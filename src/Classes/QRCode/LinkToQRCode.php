<?php

namespace con4gis\ProjectsBundle\Classes\QRCode;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use con4gis\CoreBundle\Resources\contao\models\C4gLogModel;

/**
 * Class LinkToQRCode
 * @package con4gis\ProjectsBundle\Classes\QRCode
 */
class LinkToQRCode
{
    /**
     * @param $link
     */
    public static function linkToQRCode($link, $fileName)
    {
        try {
            // SvgImageBackEnd is chosen because PngImageBackEnd/Imagick might be missing in this BaconQrCode version
            $renderer = new ImageRenderer(
                new RendererStyle(400),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            
            $writer->writeFile($link, $fileName);
        } catch (\Throwable $throwable) {
            C4gLogModel::addLogEntry('projects', $throwable->getMessage());

            return false;
        }

        return true;
    }
}
