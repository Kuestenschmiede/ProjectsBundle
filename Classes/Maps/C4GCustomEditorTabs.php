<?php

namespace con4gis\ProjectBundle\Classes\Maps;

use c4g\Maps\C4gMapLocstylesModel;
use c4g\Maps\C4gMapsModel;

class C4GCustomEditorTabs
{
    // TODO: maybe move this loader function to another class, which then calls the load function of this and other possible classes
    public function load()
    {
        $GLOBALS['TL_JAVASCRIPT']['c4g-maps-plugin-customeditortab'] = 'system/modules/con4gis_projects/assets/js/c4g-maps-plugin-customeditortab.js';
        $GLOBALS['TL_JAVASCRIPT']['c4g-maps-plugin-projects']        = 'system/modules/con4gis_projects/assets/js/c4g-maps-plugin-projects.js';
        $GLOBALS['TL_JAVASCRIPT']['c4g-maps-plugin-starboardcontrol'] = 'system/modules/con4gis_projects/assets/js/c4g-maps-plugin-starboardcontrol.js';
        $GLOBALS['TL_JAVASCRIPT']['c4g-maps-plugin-tabcontrol'] = 'system/modules/con4gis_projects/assets/js/c4g-maps-plugin-tabcontrol.js';
        $GLOBALS['TL_JAVASCRIPT']['c4g-maps-plugin-projects-constant'] = 'system/modules/con4gis_projects/assets/js/c4g-maps-plugin-projects-constant.js';

        $GLOBALS['TL_CSS']['c4g-maps-plugin-projects']  = 'system/modules/con4gis_projects/assets/css/c4g-maps-plugin-projects.css';
    }
}
