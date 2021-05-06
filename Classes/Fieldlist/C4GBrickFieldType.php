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
namespace con4gis\ProjectsBundle\Classes\Fieldlist;

class C4GBrickFieldType
{
    const EMAIL = 'email';
    const KEY = 'key';
    const POSTAL = 'postal';
    const TEXT = 'text';
    const INT = 'int';
    const FLOAT = 'float';
    const BOOL = 'bool';
    const TEXTAREA = 'textarea';
    const NOMINATIM_ADDRESS = 'nominatim_address';
    const TEXTEDITOR = 'texteditor';
    const MULTICHECKBOX = 'multicheckbox';
    const MULTISELECT = 'multiselect';
    const SELECT = 'select';
    const FILE = 'file';
    const IMAGE = 'image';
    const GALLERY = 'gallery';
    const COLOR = 'color';
    const DATE = 'date';
    const DATETIMEPICKER = 'datetimepicker';
    const DATETIMELOCATION = 'datetimelocation';
    const NUMBER = 'number';
    const TIME = 'time';
    const TIMESTAMP = 'timestamp';
    const URL = 'url';
    const RADIOGROUP = 'radio';
    const TEL = 'tel';
    const TIMEPICKER = 'timepicker';// Nicht kompatibel in FF und IE
    const RANGE = 'range';
    const GEOPICKER = 'geopicker';
    const FLAG_DELETE = 'deleteflag';
    const FLAG_PUBLISHED = 'publishedflag'; //Achtung! wird noch nicht unterstützt
    const DATATABLE = 'datatable';
    const HEADLINE = 'headline';

    //const MOREBUTTON        = 'morebutton';
}
