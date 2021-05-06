<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
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
    //OK! ( ͡° ͜ʖ ͡°)
}
