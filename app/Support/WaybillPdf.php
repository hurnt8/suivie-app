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
