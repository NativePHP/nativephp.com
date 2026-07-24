<?php

namespace App\Support;

use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode as QRCodeGenerator;
use chillerlan\QRCode\QROptions;

/**
 * Thin wrapper around chillerlan/php-qrcode (available via filament/filament)
 * that renders scannable, inline SVG QR codes.
 */
class QrCode
{
    /**
     * Render the given data as inline SVG markup.
     *
     * Dark modules are filled with #000, so the SVG must be placed on a light
     * background (a white container) to stay scannable in both colour schemes.
     */
    public static function svg(string $data): string
    {
        $options = new QROptions([
            'outputType' => QROutputInterface::MARKUP_SVG,
            'outputBase64' => false,
            'eccLevel' => EccLevel::M,
            'addQuietzone' => true,
            'quietzoneSize' => 2,
            'drawLightModules' => false,
            'svgAddXmlHeader' => false,
            'cssClass' => 'jump-qr',
        ]);

        $svg = (new QRCodeGenerator($options))->render($data);

        // chillerlan emits a viewBox-only <svg> (no width/height) so it can scale;
        // pin it to fill its container as a crisp square.
        return str_replace('<svg ', '<svg style="display:block;width:100%;height:auto" ', $svg);
    }
}
