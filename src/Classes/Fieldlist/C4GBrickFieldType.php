<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2022, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Fieldlist;

class C4GBrickFieldType
{
    public const EMAIL = 'email';
    public const KEY = 'key';
    public const POSTAL = 'postal';
    public const TEXT = 'text';
    public const INT = 'int';
    public const FLOAT = 'float';
    public const BOOL = 'bool';
    public const TEXTAREA = 'textarea';
    public const NOMINATIM_ADDRESS = 'nominatim_address';
    public const TEXTEDITOR = 'texteditor';
    public const MULTICHECKBOX = 'multicheckbox';
    public const MULTISELECT = 'multiselect';
    public const SELECT = 'select';
    public const FILE = 'file';
    public const IMAGE = 'image';
    public const GALLERY = 'gallery';
    public const COLOR = 'color';
    public const DATE = 'date';
    public const DATETIMEPICKER = 'datetimepicker';
    public const DATETIMELOCATION = 'datetimelocation';
    public const NUMBER = 'number';
    public const TIME = 'time';
    public const TIMESTAMP = 'timestamp';
    public const URL = 'url';
    public const RADIOGROUP = 'radio';
    public const TEL = 'tel';
    public const TIMEPICKER = 'timepicker';// Nicht kompatibel in FF und IE
    public const RANGE = 'range';
    public const GEOPICKER = 'geopicker';
    public const FLAG_DELETE = 'deleteflag';
    public const FLAG_PUBLISHED = 'publishedflag'; //Achtung! wird noch nicht unterstützt
    public const DATATABLE = 'datatable';
    public const HEADLINE = 'headline';
    public const AUDIO = 'audio';
    public const BUTTON = 'button';
    public const CANVAS = 'canvas';
    public const CHECKBOX = 'check';
    public const DATACLASS = 'dataclass';
    public const DUMMY = 'dummy';
    public const FOREIGNARRAY = 'foreignarray';
    public const FOREIGNKEY = 'foreignkey';
    public const GRID = 'grid';
    public const ICON = 'icon';
    public const INCLUDE = 'include';
    public const LINK = 'link';
    public const USER = 'user';
    public const MULTICOLUMN = 'multicolumn';
    public const MULTILINK = 'multilink';
    public const PERMALINK = 'permalink';
    public const SIGNATURE = 'signature';
    public const STOPWATCH = 'stopwatch';
    public const SUBDIALOG = 'subdialog';
    public const TAB = 'tab';
}
