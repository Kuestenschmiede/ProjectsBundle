<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Add palettes to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['progemisNewslist'] = str_replace(';{config_legend},news_archives', ';{config_legend},archives_by_insertag', $GLOBALS['TL_DCA']['tl_module']['palettes']['newslist']);

$GLOBALS['TL_DCA']['tl_module']['palettes']['progemisEventlist']   = str_replace(';{config_legend},cal_calendar', ';{config_legend},calendar_by_insertag', $GLOBALS['TL_DCA']['tl_module']['palettes']['eventlist']);

$GLOBALS['TL_DCA']['tl_module']['palettes']['progemisCommunelist'] = '{title_legend},name,headline,type;';

$GLOBALS['TL_DCA']['tl_module']['fields']['archives_by_insertag'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['archives_by_insertag'],
    'exclude'                 => true,
    'inputType'               => 'text',
    'eval'                    => array('mandatory'=>true,'tl_class'=>'w50'),
    'sql'                     => "varchar(100) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['calendar_by_insertag'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['calendar_by_insertag'],
    'exclude'                 => true,
    'inputType'               => 'text',
    'eval'                    => array('mandatory'=>true,'tl_class'=>'w50'),
    'sql'                     => "varchar(100) NOT NULL default ''"
);