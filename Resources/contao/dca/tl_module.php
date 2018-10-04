<?php
/**
 * Created by PhpStorm.
 * User: cro
 * Date: 04.10.18
 * Time: 13:04
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['C4GSearchModule'] = '{title_legend},name,type,headline,listModule';

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