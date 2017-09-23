<?php

/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

/**
 * Global settings
 */
$GLOBALS['con4gis']['projects']['installed'] = true;


/**
 * Kartenstrukturelemente
 */
$GLOBALS['c4g_locationtypes'][] = \con4gis\ProjectsBundle\Classes\Maps\C4GProjectsFrontend::STARBOARD_TAB;

/**
 * REST-API
 */
$GLOBALS['TL_API']['editorTabService']      = 'C4GEditorTabApi';
$GLOBALS['TL_API']['starboardTabService']      = 'C4GStarboardTabApi';

$apiBaseUrl = 'src/con4gis/CoreBundle/Resources/contao/api/index.php';

$GLOBALS['con4gis_projects_extension']['api']['editorTab']      = $apiBaseUrl . '/editorTabService';
$GLOBALS['con4gis_projects_extension']['api']['starboardTab']      = $apiBaseUrl . '/starboardTabService';

/**
 * Load Editor Plugin
 */
//$GLOBALS['TL_HOOKS']['C4gMapsLoadPlugins']['projects'] = array('C4GCustomEditorTabs','load');

/**
 * API MODULES
 */
$GLOBALS['TL_API']['c4g_brick_ajax'] = 'C4GBrickAjaxApi';

