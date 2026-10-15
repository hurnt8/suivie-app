<?php

namespace App\Mail;

use App\Enums\ShipmentStatus;
use App\Models\Settings;
use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * A single responsive HTML email used for every shipment lifecycle
 * notification (creation and every subsequent status change). The
 * introductory paragraph adapts to the status; the shipment summary,
 * tracking number and "Track my package" button stay consistent.
 */
class ShipmentStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Shipment $shipment,
        public ShipmentStatus $status,
        public string $audience = 'sender',
    ) {}

    public function envelope(): Envelope
    {
        $company = Settings::current()->company_name;

        $subject = $this->status === ShipmentStatus::Registered
            ? __('emails.shipment_created_subject', ['tracking_code' => $this->shipment->tracking_code, 'company' => $company])
            : __('emails.shipment_status_subject', ['status' => $this->status->label(), 'tracking_code' => $this->shipment->tracking_code]);

        $replyTo = Settings::current()->email;

        return new Envelope(
            subject: $subject,
            replyTo: filled($replyTo) ? [new Address($replyTo, $company)] : [],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.shipments.status',
            // Plain-text alternative: HTML-only mail scores worse with spam filters.
            text: 'emails.shipments.status-text',
            with: [
                'shipment' => $this->shipment->loadMissing(['sender', 'recipient']),
                'status' => $this->status,
                'audience' => $this->audience,
                'settings' => Settings::current(),
                'trackingUrl' => $this->shipment->trackingUrl(),
            ],
        );
    }
}
