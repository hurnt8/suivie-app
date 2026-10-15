<?php

namespace App\Support;

use App\Models\Settings;
use App\Models\Shipment;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Renders the shipment waybill/invoice to a PDF string, in the current app locale
 * (the notification sets it to the shipment's language).
 */
class WaybillPdf
{
    public static function available(): bool
    {
        return class_exists(Dompdf::class);
    }

    /**
     * QR code (SVG, no GD needed) encoding the given URL, as a data URI usable
     * in both the browser page and the PDF. Null when the library is missing.
     */
    public static function qrDataUri(string $url): ?string
    {
        if (! class_exists(\BaconQrCode\Writer::class)) {
            return null;
        }

        try {
            $renderer = new \BaconQrCode\Renderer\ImageRenderer(
                new \BaconQrCode\Renderer\RendererStyle\RendererStyle(240, 0),
                new \BaconQrCode\Renderer\Image\SvgImageBackEnd,
            );

            return 'data:image/svg+xml;base64,'.base64_encode((new \BaconQrCode\Writer($renderer))->writeString($url));
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    public static function render(Shipment $shipment): string
    {
        $shipment->loadMissing(['sender', 'recipient']);

        $html = view('shipments.waybill', [
            'shipment' => $shipment,
            'settings' => Settings::current(),
            'pdf' => true,
        ])->render();

        $options = new Options;
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4');
        $dompdf->render();

        return $dompdf->output();
    }
}
