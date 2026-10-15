<?php

namespace App\Providers;

use App\Models\ShipmentNotification;
use App\Models\User;
use App\Notifications\Shipment\ShipmentStatusNotification;
use App\Services\ActivityLogService;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Events\Login;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureRateLimiting();
        $this->configureActivityLogging();
        $this->configureNotificationLogging();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    /**
     * Public tracking lookups are rate limited to keep the endpoint from
     * being used to brute-force tracking codes.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('tracking', fn (Request $request) => Limit::perMinute(30)->by($request->ip()));
    }

    protected function configureActivityLogging(): void
    {
        Event::listen(function (Login $event): void {
            if (! $event->user instanceof User) {
                return;
            }

            app(ActivityLogService::class)->log(
                action: 'login',
                description: __('messages.activity_login', ['name' => $event->user->name]),
            );
        });
    }

    /**
     * Reflects the outcome of queued shipment notifications back onto their
     * `shipment_notifications` audit row.
     */
    protected function configureNotificationLogging(): void
    {
        Event::listen(function (NotificationSent $event): void {
            if ($event->notification instanceof ShipmentStatusNotification) {
                ShipmentNotification::whereKey($event->notification->shipmentNotificationId)
                    ->update(['status' => 'sent', 'sent_at' => now()]);
            }
        });

        Event::listen(function (NotificationFailed $event): void {
            if ($event->notification instanceof ShipmentStatusNotification) {
                ShipmentNotification::whereKey($event->notification->shipmentNotificationId)
                    ->update(['status' => 'failed']);
            }
        });
    }
}
