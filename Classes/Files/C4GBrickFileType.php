<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */
namespace con4gis\ProjectsBundle\Classes\Files;

class C4GBrickFileType
{
    const IMAGES_ALL = 'image/*';
    const IMAGES_PNG_JPG = 'image/png,image/jpeg';
    const IMAGES_PNG_JPG_TIFF = 'image/png,image/jpeg,image/tiff';
    const IMAGES_PNG = 'image/png';
    const IMAGES_JPG = 'image/jpeg';
    const ALL_AUDIOS = 'audio/*';
    const ALL_VIDEOS = 'video/*';
    const PDF = 'application/pdf';
    const ZIP = 'application/zip';
    const WORD = 'application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document';

    //Beliebig erweiterbar :)
}