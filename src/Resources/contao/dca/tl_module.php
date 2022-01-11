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

$GLOBALS['TL_DCA']['tl_module']['palettes']['C4GSearchModule'] =
    '{title_legend},name,type,headline,listModule,searchFieldCaption,'.
    'hideSearchFieldCaption,searchFieldPlaceholder,searchButtonCaption';

$GLOBALS['TL_DCA']['tl_module']['fields']['searchFieldCaption'] = array
(
    'exclude'                 => true,
    'default'                 => &$GLOBALS['TL_LANG']['tl_module']['DEFAULT_FIELD_CAPTION'],
    'inputType'               => 'text',
    'eval'                    => array('tl_class'=>'w50','mandatory'=>true),
    'sql'                     => "varchar(100) default '".$GLOBALS['TL_LANG']['tl_module']['c4g_projects']['fields']['DEFAULT_FIELD_CAPTION']."'"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['hideSearchFieldCaption'] = array
(
    'exclude'                 => true,
    'default'                 => false,
    'inputType'               => 'checkbox',
    'eval'                    => array('tl_class'=>'w50 m12','mandatory'=>false),
    'sql'                     => "char(1) default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['searchFieldPlaceholder'] = array
(
    'exclude'                 => true,
    'default'                 => &$GLOBALS['TL_LANG']['tl_module']['DEFAULT_FIELD_CAPTION'],
    'inputType'               => 'text',
    'eval'                    => array('tl_class'=>'clr w50','mandatory'=>false),
    'sql'                     => "varchar(255) default '".$GLOBALS['TL_LANG']['tl_module']['c4g_projects']['fields']['DEFAULT_FIELD_CAPTION']."'"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['searchButtonCaption'] = array
(
    'exclude'                 => true,
    'default'                 => &$GLOBALS['TL_LANG']['tl_module']['DEFAULT_BUTTON_CAPTION'],
    'inputType'               => 'text',
    'eval'                    => array('tl_class'=>'w50','mandatory'=>false),
    'sql'                     => "varchar(100) default '".$GLOBALS['TL_LANG']['tl_module']['c4g_projects']['fields']['DEFAULT_BUTTON_CAPTION']."'"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['listModule'] = array
(
    'exclude'                 => true,
    'inputType'               => 'pageTree',
    'foreignKey'              => 'tl_page.title',
    'eval'                    => array('tl_class'=>'w50 wizard','mandatory'=>true, 'fieldType'=>'radio'),
    'sql'                     => "int(10) unsigned NOT NULL default '0'",
    'relation'                => array('type'=>'hasOne', 'load'=>'eager')
);