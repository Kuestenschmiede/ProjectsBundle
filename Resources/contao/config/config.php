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
$apiBaseUrl = 'con4gis';

$GLOBALS['TL_HOOKS']['postLogout'][] = array('con4gis\ProjectsBundle\Classes\Framework\C4GMaintenance', 'onLogoutClearSessions');
$GLOBALS['TL_HOOKS']['postLogin'][] = array('con4gis\ProjectsBundle\Classes\Framework\C4GMaintenance', 'onLoginClearSessions');

/**
 * API MODULES
 */
$GLOBALS['TL_API']['c4g_brick_ajax'] = 'C4GBrickAjaxApi';

/**
 * MODELS
 */
$GLOBALS['TL_MODELS']['tl_c4g_projects'] = 'con4gis\ProjectsBundle\Classes\Models\C4gProjectsModel';
$GLOBALS['TL_MODELS']['tl_c4g_projects_logbook'] = 'con4gis\ProjectsBundle\Classes\Models\C4gProjectsLogbookModel';
