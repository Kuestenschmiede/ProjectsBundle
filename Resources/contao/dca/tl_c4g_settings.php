<?php

/**
 *  con4gis for Contao Open Source CMS
 *
 * @version   php 7
 * @package   con4gis-Projects (ProjectsBundle)
 * @author    con4gis contributors
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018 - 2018
 * @link      https://www.kuestenschmiede.de
 */


//Palettes
$GLOBALS['TL_DCA']['tl_c4g_settings']['palettes']['default'] .= '{projects_legend},position_map;';

$GLOBALS['TL_DCA']['tl_c4g_settings']['fields']['position_map'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_settings']['position_map'],
    'exclude'                 => true,
    'options_callback'        => array('\con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon','getContentId'),
    'inputType'               => 'select',
    'eval'                    => array('tl_class'=>'w50 wizard', 'includeBlankOption' => true),
    'sql'                     => "varchar(128) NULL"
);
