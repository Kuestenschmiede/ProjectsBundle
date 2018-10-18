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

$GLOBALS['TL_DCA']['tl_module']['palettes']['C4GSearchModule'] = '{title_legend},name,type,headline,listModule,searchFieldCaption,searchButtonCaption';

$GLOBALS['TL_DCA']['tl_module']['fields']['searchFieldCaption'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_projects']['fields']['searchFieldCaption'],
    'exclude'                 => true,
    'default'                 => $GLOBALS['TL_LANG']['tl_module']['c4g_projects']['fields']['DEFAULT_FIELD_CAPTION'],
    'inputType'               => 'text',
    'eval'                    => array('tl_class'=>'w50','mandatory'=>false),
    'sql'                     => "varchar(255) default '".$GLOBALS['TL_LANG']['tl_module']['c4g_projects']['fields']['DEFAULT_FIELD_CAPTION']."'"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['searchButtonCaption'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_projects']['fields']['searchButtonCaption'],
    'exclude'                 => true,
    'default'                 => $GLOBALS['TL_LANG']['tl_module']['c4g_projects']['fields']['DEFAULT_BUTTON_CAPTION'],
    'inputType'               => 'text',
    'eval'                    => array('tl_class'=>'w50','mandatory'=>false),
    'sql'                     => "varchar(255) default '".$GLOBALS['TL_LANG']['tl_module']['c4g_projects']['fields']['DEFAULT_BUTTON_CAPTION']."'"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['listModule'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_projects']['fields']['listModule'],
    'exclude'                 => true,
    'inputType'               => 'pageTree',
    'foreignKey'              => 'tl_page.title',
    'eval'                    => array('tl_class'=>'w50 wizard','mandatory'=>true, 'fieldType'=>'radio'),
    'sql'                     => "int(10) unsigned NOT NULL default '0'",
    'relation'                => array('type'=>'hasOne', 'load'=>'eager')
);