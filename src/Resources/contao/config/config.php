<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2022, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

/**
 * Kartenstrukturelemente
 */

use con4gis\CoreBundle\Classes\C4GVersionProvider;

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
