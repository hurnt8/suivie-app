@php
    $company = $settings->company_name ?: config('app.name');
    $pdf = $pdf ?? false;
    $logoPath = filled($settings->logo_path) ? public_path('storage/'.$settings->logo_path) : null;
    $logoSrc = null;
    if ($logoPath && is_file($logoPath)) {
        $logoSrc = $pdf
            ? 'data:'.(mime_content_type($logoPath) ?: 'image/png').';base64,'.base64_encode((string) file_get_contents($logoPath))
            : \Illuminate\Support\Facades\Storage::disk('public')->url($settings->logo_path);
    }
    $weight = $shipment->weight !== null ? rtrim(rtrim((string) $shipment->weight, '0'), '.').' kg' : '—';
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('waybill.title') }} {{ $shipment->tracking_code }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: {{ $pdf ? '#fff' : '#f4f4f5' }}; font-family: "DejaVu Sans", Arial, sans-serif; font-size: 12px; color: #111; }
        .toolbar { max-width: 760px; margin: 16px auto 0; padding: 0 16px; text-align: right; }
        .toolbar button, .toolbar a { font: 600 13px Arial, sans-serif; padding: 8px 14px; border-radius: 6px; border: 1px solid #d4d4d8; background: #fff; color: #111; text-decoration: none; cursor: pointer; }
        .toolbar button { background: #111; color: #fff; border-color: #111; }
        .sheet { max-width: 760px; margin: 16px auto; background: #fff; padding: {{ $pdf ? '0' : '32px 36px' }}; {{ $pdf ? '' : 'border: 1px solid #e4e4e7;' }} }
        table { width: 100%; border-collapse: collapse; }
        .head td { vertical-align: top; padding-bottom: 12px; border-bottom: 2px solid #111; }
        .brand { font-size: 20px; font-weight: bold; }
        .doc-title { text-align: right; font-weight: bold; font-size: 14px; text-transform: uppercase; }
        .tracking-box { margin: 16px 0; }
        .tracking-box td { vertical-align: middle; }
        .qr { width: 130px; text-align: right; }
        .tracking { padding: 12px; border: 2px dashed #111; text-align: center; }
        .tracking small { display: block; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; }
        .tracking strong { font-size: 24px; letter-spacing: 2px; }
        .parties td { width: 50%; vertical-align: top; padding-right: 14px; }
        h2 { margin: 16px 0 6px; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #111; padding-bottom: 3px; }
        p { margin: 0 0 3px; }
        .rows td { padding: 5px 0; border-bottom: 1px dotted #999; vertical-align: top; }
        .rows td:first-child { width: 40%; color: #444; }
        .total { border: 2px solid #111; margin-top: 6px; }
        .total td { vertical-align: middle; padding: 12px 14px; }
        .total .amt { text-align: right; font-size: 20px; font-weight: bold; }
        .note { font-size: 10px; color: #444; margin-top: 14px; }
        footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #111; text-align: center; font-size: 11px; }
        @media print { body { background: #fff; } .toolbar { display: none; } .sheet { border: 0; margin: 0; padding: 0; max-width: none; } }
    </style>
</head>
<body>
    @unless ($pdf)
        <div class="toolbar">
            <a href="{{ route('admin.shipments.show', $shipment) }}">← {{ __('waybill.back') }}</a>
            <button type="button" onclick="window.print()">{{ __('waybill.print') }}</button>
        </div>
    @endunless

    <main class="sheet">
        <table class="head">
            <tr>
                <td>
                    @if ($logoSrc)
                        <img src="{{ $logoSrc }}" alt="{{ $company }}" style="max-height:60px; max-width:200px;">
                    @else
                        <div class="brand">{{ $company }}</div>
                    @endif
                    <p style="margin-top:6px">{{ $settings->address }}</p>
                    <p>{{ $settings->phone }}{{ $settings->phone && $settings->email ? ' · ' : '' }}{{ $settings->email }}</p>
                </td>
                <td class="doc-title">
                    {{ __('waybill.title') }}<br>
                    <span style="font-weight:normal">{{ $shipment->created_at->translatedFormat('d/m/Y H:i') }}</span>
                </td>
            </tr>
        </table>

        @php $qr = \App\Support\WaybillPdf::qrDataUri($shipment->trackingUrl()); @endphp
        <table class="tracking-box">
            <tr>
                <td class="tracking">
                    <small>{{ __('waybill.tracking_number') }}</small>
                    <strong>{{ $shipment->tracking_code }}</strong>
                </td>
                @if ($qr)
                    <td class="qr">
                        <img src="{{ $qr }}" alt="QR" width="110" height="110">
                    </td>
                @endif
            </tr>
        </table>

        <table class="parties">
            <tr>
                @foreach ([['waybill.sender', $shipment->sender], ['waybill.recipient', $shipment->recipient]] as [$label, $p])
                    <td>
                        <h2 style="margin-top:0">{{ __($label) }}</h2>
                        <p><strong>{{ $p->name }}</strong></p>
                        @if ($p->company)<p>{{ $p->company }}</p>@endif
                        <p>{{ $p->address }}</p>
                        <p>{{ $p->postal_code }} {{ $p->city }}</p>
                        <p>{{ $p->country }}</p>
                        @if ($p->phone)<p>{{ __('waybill.phone') }} : {{ $p->phone }}</p>@endif
                    </td>
                @endforeach
            </tr>
        </table>

        <h2>{{ __('waybill.details') }}</h2>
        <table class="rows">
            <tr><td>{{ __('waybill.origin') }}</td><td>{{ $shipment->origin }}</td></tr>
            <tr><td>{{ __('waybill.destination') }}</td><td>{{ $shipment->destination }}</td></tr>
            <tr><td>{{ __('waybill.shipment_type') }}</td><td>{{ $shipment->shipment_type->label() }}</td></tr>
            <tr><td>{{ __('waybill.service') }}</td><td>{{ $shipment->service_type->label() }}</td></tr>
            <tr><td>{{ __('waybill.packages') }}</td><td>{{ $shipment->package_count }}</td></tr>
            <tr><td>{{ __('waybill.weight') }}</td><td>{{ $weight }}</td></tr>
            <tr><td>{{ __('waybill.content') }}</td><td>{{ $shipment->description ?: '—' }}</td></tr>
            <tr><td>{{ __('waybill.estimated_delivery') }}</td><td>{{ $shipment->estimated_delivery_date?->translatedFormat('d/m/Y') ?? '—' }}</td></tr>
            <tr><td>{{ __('waybill.status') }}</td><td>{{ $shipment->current_status->label() }}</td></tr>
            @if ($shipment->special_instructions)
                <tr><td>{{ __('waybill.instructions') }}</td><td>{{ $shipment->special_instructions }}</td></tr>
            @endif
        </table>

        <h2>{{ __('waybill.billing') }}</h2>
        <table class="total">
            <tr>
                <td>{{ __('waybill.amount_due') }}<br><strong>{{ $shipment->recipient->name }}</strong></td>
                <td class="amt">{{ $shipment->formattedAmount() ?? '—' }}</td>
            </tr>
        </table>

        <p class="note">{{ __('waybill.tracking_online') }} : {{ $shipment->trackingUrl() }}</p>

        <footer>{{ $company }} — {{ __('waybill.thanks') }}</footer>
    </main>
</body>
</html>
