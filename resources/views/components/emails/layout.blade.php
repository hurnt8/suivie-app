@props(['settings'])
@php
    $inkColor = '#0f172a';
    $mutedColor = '#64748b';
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $settings->company_name ?? 'Livrion' }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f1f5f9; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9; padding:32px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 1px 3px rgba(15,23,42,0.08);">
                <tr>
                    <td style="background-color:{{ $inkColor }}; padding:28px 32px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="color:#ffffff; font-size:20px; font-weight:700; letter-spacing:0.02em;">
                                    {{ $settings->company_name ?? 'Livrion' }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:32px;">
                        {{ $slot }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:24px 32px; background-color:#f8fafc; border-top:1px solid #e2e8f0;">
                        <p style="margin:0 0 4px; font-size:12px; color:{{ $mutedColor }}; line-height:1.6;">
                            {{ $settings->company_name ?? 'Livrion' }}@if($settings->address ?? null) &middot; {{ $settings->address }}@endif
                        </p>
                        <p style="margin:0; font-size:12px; color:{{ $mutedColor }}; line-height:1.6;">
                            {{ __('emails.automated_notice') }}
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
