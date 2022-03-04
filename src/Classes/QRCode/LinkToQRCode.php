<?php

namespace con4gis\ProjectsBundle\Classes\QRCode;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
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
            $renderer = new ImageRenderer(
                new RendererStyle(400),
                new ImagickImageBackEnd()
            );
            $writer = new Writer($renderer);
            $writer->writeFile($link, $fileName);
        } catch (\Throwable $exception) {
            C4gLogModel::addLogEntry('projects', $throwable->getMessage());

            return false;
        }

        return true;
    }
}
