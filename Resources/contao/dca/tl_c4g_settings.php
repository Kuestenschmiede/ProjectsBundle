<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    6
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
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

$GLOBALS['TL_DCA']['tl_c4g_settings']['fields']['uploadPathImages']['eval']['mandatory'] = true;
$GLOBALS['TL_DCA']['tl_c4g_settings']['fields']['uploadPathDocuments']['eval']['mandatory'] = true;
