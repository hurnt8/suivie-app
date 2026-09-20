<?php

use App\Enums\ShipmentStatus;
use App\Models\Recipient;
use App\Models\Sender;
use App\Models\Settings;
use App\Models\Shipment;
use App\Models\User;
use App\Notifications\Shipment\ShipmentStatusNotification;
use App\Services\ShipmentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

/**
 * Builds the payload exactly as the admin form submits it: untouched optional
 * inputs arrive as empty strings.
 *
 * @param  array<string, mixed>  $overrides
 * @return array<string, array<string, mixed>>
 */
function shipmentFormPayload(array $overrides = []): array
{
    return [
        'sender' => Sender::factory()->make(['company' => '', 'phone' => ''])->toArray(),
        'recipient' => Recipient::factory()->make(['company' => '', 'phone' => ''])->toArray(),
        'shipment' => array_merge([
            'description' => '',
            'weight' => '',
            'amount' => '',
            'package_count' => 1,
            'shipment_type' => 'parcel',
            'service_type' => 'standard',
            'origin' => 'Paris, France',
            'destination' => 'Lyon, France',
            'estimated_delivery_date' => '',
            'special_instructions' => '',
        ], $overrides),
    ];
}

test('blank optional fields are stored as NULL instead of empty strings', function () {
    Notification::fake();

    $shipment = app(ShipmentService::class)->create(shipmentFormPayload());

    $row = DB::table('shipments')->where('id', $shipment->id)->first();

    expect($row->estimated_delivery_date)->toBeNull()
        ->and($row->special_instructions)->toBeNull()
        ->and($row->description)->toBeNull()
        ->and($row->weight)->toBeNull()
        ->and($row->amount)->toBeNull()
        ->and($row->currency)->toBeNull();

    expect(DB::table('senders')->where('id', $shipment->sender_id)->value('company'))->toBeNull();
});

test('an amount is stored with the current currency and communicated to the recipient', function () {
    Notification::fake();
    Settings::query()->updateOrCreate(['id' => 1], ['currency' => 'USD']);

    $shipment = app(ShipmentService::class)->create(shipmentFormPayload(['amount' => '120.5']));

    expect($shipment->amount)->toBe('120.50')
        ->and($shipment->currency)->toBe('USD');

    Notification::assertSentOnDemand(
        ShipmentStatusNotification::class,
        fn (ShipmentStatusNotification $notification, array $channels, object $notifiable) => $notifiable->routes['mail'] === $shipment->recipient->email
            && $notification->audience === ShipmentStatusNotification::AUDIENCE_RECIPIENT,
    );

    Notification::assertSentOnDemand(
        ShipmentStatusNotification::class,
        fn (ShipmentStatusNotification $notification, array $channels, object $notifiable) => $notifiable->routes['mail'] === $shipment->sender->email
            && $notification->audience === ShipmentStatusNotification::AUDIENCE_SENDER,
    );
});

test('the recipient is not emailed when the shipment has no amount', function () {
    Notification::fake();

    app(ShipmentService::class)->create(shipmentFormPayload());

    Notification::assertSentOnDemandTimes(ShipmentStatusNotification::class, 1);
});

test('creating a shipment generates a tracking code, an initial event and a notification', function () {
    Notification::fake();

    $data = [
        'sender' => Sender::factory()->make()->toArray(),
        'recipient' => Recipient::factory()->make()->toArray(),
        'shipment' => [
            'description' => 'Colis test',
            'weight' => 2.5,
            'package_count' => 1,
            'shipment_type' => 'parcel',
            'service_type' => 'standard',
            'origin' => 'Paris, France',
            'destination' => 'Lyon, France',
            'estimated_delivery_date' => now()->addDays(5)->toDateString(),
        ],
    ];

    $shipment = app(ShipmentService::class)->create($data);

    expect($shipment->tracking_code)->toMatch('/^[A-Z]+-\d{4}-[A-Z0-9]{6}$/')
        ->and($shipment->current_status)->toBe(ShipmentStatus::Registered)
        ->and($shipment->events()->count())->toBe(1);

    Notification::assertSentOnDemand(ShipmentStatusNotification::class);
});

test('creating a shipment can reuse an existing sender and recipient', function () {
    Notification::fake();

    $sender = Sender::factory()->create();
    $recipient = Recipient::factory()->create();

    $shipment = app(ShipmentService::class)->create([
        'sender' => ['id' => $sender->id],
        'recipient' => ['id' => $recipient->id],
        'shipment' => [
            'package_count' => 1,
            'shipment_type' => 'parcel',
            'service_type' => 'standard',
            'origin' => 'Paris, France',
            'destination' => 'Berlin, Germany',
        ],
    ]);

    expect(Sender::count())->toBe(1)
        ->and(Recipient::count())->toBe(1)
        ->and($shipment->sender_id)->toBe($sender->id)
        ->and($shipment->recipient_id)->toBe($recipient->id);
});

test('changing status creates a new event and updates current_status', function () {
    Notification::fake();

    $shipment = Shipment::factory()->create(['current_status' => ShipmentStatus::Registered]);
    $actor = User::factory()->admin()->create();

    app(ShipmentService::class)->changeStatus($shipment, ShipmentStatus::InTransit, [
        'location' => 'Marseille, France',
    ], $actor);

    $shipment->refresh();

    expect($shipment->current_status)->toBe(ShipmentStatus::InTransit)
        ->and($shipment->events()->latest('event_date')->first()->created_by)->toBe($actor->id);
});

test('marking a shipment delivered stamps delivered_at', function () {
    Notification::fake();

    $shipment = Shipment::factory()->create(['current_status' => ShipmentStatus::OutForDelivery, 'delivered_at' => null]);

    app(ShipmentService::class)->changeStatus($shipment, ShipmentStatus::Delivered);

    expect($shipment->fresh()->delivered_at)->not->toBeNull();
});

test('the chosen email language is stored on the shipment and used for every notification', function () {
    Notification::fake();

    $shipment = app(ShipmentService::class)->create(shipmentFormPayload(['mail_locale' => 'es', 'amount' => '50']));

    expect($shipment->mail_locale)->toBe('es')
        ->and($shipment->mailLocale())->toBe('es');

    Notification::assertSentOnDemandTimes(ShipmentStatusNotification::class, 2);
    Notification::assertSentOnDemand(
        ShipmentStatusNotification::class,
        fn (ShipmentStatusNotification $notification) => $notification->locale === 'es',
    );
});

test('an unsupported or blank email language falls back to the platform language', function () {
    Notification::fake();
    Settings::query()->updateOrCreate(['id' => 1], ['default_locale' => 'de']);
    Settings::flush();

    $unsupported = app(ShipmentService::class)->create(shipmentFormPayload(['mail_locale' => 'xx']));
    $blank = app(ShipmentService::class)->create(shipmentFormPayload(['mail_locale' => '']));

    expect($unsupported->mail_locale)->toBe('de')
        ->and($blank->mail_locale)->toBe('de');
});

test('emails are rendered in the shipment language whatever the app language is', function () {
    app()->setLocale('fr');

    $shipment = app(ShipmentService::class)->create(shipmentFormPayload(['mail_locale' => 'en', 'amount' => '50']));

    $emails = collect(Mail::mailer('array')->getSymfonyTransport()->messages())
        ->map(fn ($sent) => $sent->getOriginalMessage());

    expect($emails)->toHaveCount(2);

    $expectedSubject = __('emails.shipment_created_subject', [
        'tracking_code' => $shipment->tracking_code,
        'company' => Settings::current()->company_name,
    ], 'en');

    foreach ($emails as $email) {
        expect($email->getSubject())->toBe($expectedSubject)
            ->and($email->getHtmlBody())->toContain(__('emails.amount', [], 'en'))
            ->and($email->getHtmlBody())->not->toContain(__('emails.amount', [], 'fr'));
    }

    // Sending the email must not leak the shipment's language into the rest of the request.
    expect(app()->getLocale())->toBe('fr');
});
