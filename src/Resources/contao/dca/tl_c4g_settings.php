<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 10
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2025, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

//Palettes
// only add field if maps is installed
if (\Contao\System::getContainer()->hasParameter('kernel.packages')){
    $packages = \Contao\System::getContainer()->getParameter('kernel.packages');
    if (array_key_exists('con4gis/maps', $packages)) {
        Contao\CoreBundle\DataContainer\PaletteManipulator::create()
            ->addLegend('projects_legend', 'expert_legend', Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_BEFORE, true)
            ->addField('position_map', 'projects_legend', Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
            ->applyToPalette('default', 'tl_c4g_settings');


        $GLOBALS['TL_DCA']['tl_c4g_settings']['fields']['position_map'] = array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_settings']['position_map'],
            'exclude'                 => true,
            'options_callback'        => array('\con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon','getContentId'),
            'inputType'               => 'select',
            'eval'                    => array('tl_class'=>'w50 wizard', 'includeBlankOption' => true),
            'sql'                     => "varchar(128) NULL"
        );
    }
}

