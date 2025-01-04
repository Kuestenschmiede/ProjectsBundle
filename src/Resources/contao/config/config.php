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

/**
 * Kartenstrukturelemente
 */

use con4gis\CoreBundle\Classes\C4GUtils;
use con4gis\CoreBundle\Classes\C4GVersionProvider;
use Contao\System;
use Contao\RequestToken;
use Symfony\Component\HttpFoundation\Request;

$apiBaseUrl = 'con4gis';

//ToDO deprecated
//$GLOBALS['TL_HOOKS']['postLogout'][] = array('con4gis\ProjectsBundle\Classes\Framework\C4GMaintenance', 'onLogoutClearSessions');
//$GLOBALS['TL_HOOKS']['postLogin'][] = array('con4gis\ProjectsBundle\Classes\Framework\C4GMaintenance', 'onLoginClearSessions');

/**
 * API MODULES
 */
$GLOBALS['TL_API']['c4g_brick_ajax'] = 'C4GBrickAjaxApi';

/**
 * MODELS
 */
$GLOBALS['TL_MODELS']['tl_c4g_projects'] = 'con4gis\ProjectsBundle\Classes\Models\C4gProjectsModel';
$GLOBALS['TL_MODELS']['tl_c4g_projects_logbook'] = 'con4gis\ProjectsBundle\Classes\Models\C4gProjectsLogbookModel';

//needed with upload fields
if (C4GUtils::isFrontend()) {
    try {
        // TODO replace with symfony csrf token
        // $requestStack = System::getContainer()->get('request_stack');
        // $rq = RequestToken::get();
        $rq = System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue();  
        $GLOBALS['TL_HEAD'][] = "<script>var c4g_rq = '" . $rq . "';</script>";
    } catch (\LogicException $exception) {}
}