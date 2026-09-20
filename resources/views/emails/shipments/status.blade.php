<x-emails.layout :settings="$settings">
    @php
        $accent = '#4f46e5';
        $ink = '#0f172a';
        $muted = '#64748b';
        $isCreation = $status === \App\Enums\ShipmentStatus::Registered;
    @endphp

    <p style="margin:0 0 16px; font-size:15px; color:{{ $ink }};">
        {{ __('emails.greeting', ['name' => $shipment->sender->name]) }}
    </p>

    <p style="margin:0 0 24px; font-size:15px; color:{{ $ink }}; line-height:1.6;">
        {{ $isCreation ? __('emails.intro_created', ['company' => $settings->company_name]) : __('emails.intro_status_updated') }}
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#eef2ff; border-radius:10px; margin-bottom:24px;">
        <tr>
            <td style="padding:20px 24px;">
                <p style="margin:0 0 4px; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; color:{{ $accent }};">
                    {{ __('emails.current_status') }}
                </p>
                <p style="margin:0 0 6px; font-size:18px; font-weight:700; color:{{ $ink }};">
                    {{ $status->label() }}
                </p>
                <p style="margin:0; font-size:14px; color:{{ $muted }}; line-height:1.5;">
                    {{ $status->description() }}
                </p>
            </td>
        </tr>
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0; border-radius:10px; margin-bottom:28px;">
        <tr>
            <td style="padding:20px 24px;">
                <p style="margin:0 0 2px; font-size:12px; color:{{ $muted }};">{{ __('emails.tracking_number') }}</p>
                <p style="margin:0 0 16px; font-size:20px; font-weight:700; letter-spacing:0.04em; color:{{ $ink }}; font-family:'SFMono-Regular',Consolas,Menlo,monospace;">
                    {{ $shipment->tracking_code }}
                </p>

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="50%" style="padding-bottom:12px; vertical-align:top;">
                            <p style="margin:0 0 2px; font-size:12px; color:{{ $muted }};">{{ __('emails.recipient') }}</p>
                            <p style="margin:0; font-size:14px; color:{{ $ink }};">{{ $shipment->recipient->name }}</p>
                        </td>
                        <td width="50%" style="padding-bottom:12px; vertical-align:top;">
                            <p style="margin:0 0 2px; font-size:12px; color:{{ $muted }};">{{ __('emails.estimated_delivery') }}</p>
                            <p style="margin:0; font-size:14px; color:{{ $ink }};">
                                {{ $shipment->estimated_delivery_date?->translatedFormat('d M Y') ?? __('emails.not_available') }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="vertical-align:top;">
                            <p style="margin:0 0 2px; font-size:12px; color:{{ $muted }};">{{ __('emails.origin') }}</p>
                            <p style="margin:0; font-size:14px; color:{{ $ink }};">{{ $shipment->origin }}</p>
                        </td>
                        <td width="50%" style="vertical-align:top;">
                            <p style="margin:0 0 2px; font-size:12px; color:{{ $muted }};">{{ __('emails.destination') }}</p>
                            <p style="margin:0; font-size:14px; color:{{ $ink }};">{{ $shipment->destination }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto 28px;">
        <tr>
            <td style="border-radius:8px; background-color:{{ $accent }};">
                <a href="{{ $trackingUrl }}" style="display:inline-block; padding:14px 32px; font-size:15px; font-weight:600; color:#ffffff; text-decoration:none;">
                    {{ __('emails.track_button') }}
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 4px; font-size:13px; color:{{ $muted }}; line-height:1.6;">
        {{ __('emails.link_fallback') }}
    </p>
    <p style="margin:0; font-size:13px; word-break:break-all;">
        <a href="{{ $trackingUrl }}" style="color:{{ $accent }};">{{ $trackingUrl }}</a>
    </p>
</x-emails.layout>
