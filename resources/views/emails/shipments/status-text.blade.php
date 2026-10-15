@php
    $isCreation = $status === \App\Enums\ShipmentStatus::Registered;
    $isRecipient = ($audience ?? 'sender') === 'recipient';
    $addressee = $isRecipient ? $shipment->recipient : $shipment->sender;
    $counterpart = $isRecipient ? $shipment->sender : $shipment->recipient;
@endphp
{{ __('emails.greeting', ['name' => $addressee->name]) }}

@if ($isRecipient)
{{ __('emails.intro_recipient', ['sender' => $shipment->sender->name, 'company' => $settings->company_name]) }}
@else
{{ $isCreation ? __('emails.intro_created', ['company' => $settings->company_name]) : __('emails.intro_status_updated') }}
@endif


{{ __('emails.current_status') }}: {{ $status->label() }}
{{ $status->description() }}

{{ __('emails.tracking_number') }}: {{ $shipment->tracking_code }}
{{ $isRecipient ? __('emails.sender') : __('emails.recipient') }}: {{ $counterpart->name }}
{{ __('emails.estimated_delivery') }}: {{ $shipment->estimated_delivery_date?->translatedFormat('d M Y') ?? __('emails.not_available') }}
{{ __('emails.origin') }}: {{ $shipment->origin }}
{{ __('emails.destination') }}: {{ $shipment->destination }}
@if ($shipment->hasAmount())
{{ __('emails.amount') }}: {{ $shipment->formattedAmount() }}
@endif

{{ __('emails.track_button') }}: {{ $trackingUrl }}

{{ $settings->company_name }}
