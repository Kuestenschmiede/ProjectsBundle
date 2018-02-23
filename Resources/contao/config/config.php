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

/**
 * Global settings
 */
$GLOBALS['con4gis']['projects']['installed'] = true;


/**
 * Kartenstrukturelemente
 */
$GLOBALS['c4g_locationtypes'][] = \con4gis\ProjectsBundle\Classes\Maps\C4GProjectsFrontend::STARBOARD_TAB;

$apiBaseUrl = 'con4gis';

$GLOBALS['con4gis']['projects']['api']['editorTab']      = $apiBaseUrl . '/editorTabService';
$GLOBALS['con4gis']['projects']['api']['starboardTab']      = $apiBaseUrl . '/starboardTabService';

/**
 * Load Editor Plugin
 */
$GLOBALS['TL_HOOKS']['C4gMapsLoadPlugins']['projects'] = array('con4gis\ProjectsBundle\Classes\Maps\C4GCustomEditorTabs','load');

/**
 * API MODULES
 */
$GLOBALS['TL_API']['c4g_brick_ajax'] = 'C4GBrickAjaxApi';

/**
 * MODELS
 */
$GLOBALS['TL_MODELS']['tl_c4g_projects'] = 'con4gis\ProjectsBundle\Classes\Models\C4gProjectsModel';
$GLOBALS['TL_MODELS']['tl_c4g_projects_logbook'] = 'con4gis\ProjectsBundle\Classes\Models\C4gProjectsLogbookModel';
