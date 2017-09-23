<?php

namespace con4gis\ProjectsBundle\Classes\Maps;


class C4GCustomEditorTabs
{
    // TODO: maybe move this loader function to another class, which then calls the load function of this and other possible classes
    public function load()
    {
        $GLOBALS['TL_JAVASCRIPT']['c4g-maps-plugin-customeditortab'] = 'bundles/con4gisprojects/js/c4g-maps-plugin-customeditortab.js';
        $GLOBALS['TL_JAVASCRIPT']['c4g-maps-plugin-projects']        = 'bundles/con4gisprojects/js/c4g-maps-plugin-projects.js';
        $GLOBALS['TL_JAVASCRIPT']['c4g-maps-plugin-starboardcontrol'] = 'bundles/con4gisprojects/js/c4g-maps-plugin-starboardcontrol.js';
        $GLOBALS['TL_JAVASCRIPT']['c4g-maps-plugin-tabcontrol'] = 'bundles/con4gisprojects/js/c4g-maps-plugin-tabcontrol.js';
        $GLOBALS['TL_JAVASCRIPT']['c4g-maps-plugin-projects-constant'] = 'bundles/con4gisprojects/js/c4g-maps-plugin-projects-constant.js';

        $GLOBALS['TL_CSS']['c4g-maps-plugin-projects']  = 'bundles/con4gisprojects/css/c4g-maps-plugin-projects.css';
    }
}
