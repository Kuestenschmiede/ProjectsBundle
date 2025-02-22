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

    if (!isset($_POST['Path']) || !isset($_FILES['File']['tmp_name'])) {
        die();
    }

    define("TL_MODE","FE");
//    $sRootPath = dirname($_SERVER['SCRIPT_FILENAME']) . "/../../../../../";
//    require_once($sRootPath . "system/initialize.php");

    $initialize = $_SERVER["DOCUMENT_ROOT"].'../system/initialize.php';
    if (!file_exists($initialize)) {
        $initialize = '../../../system/initialize.php';
    }

    // Initialize the system
    require_once($initialize);

    // User not logged in...
    $hasFrontendUser = System::getContainer()->get('contao.security.token_checker')->hasFrontendUser();
    if (!$hasFrontendUser) {
        header('HTTP/1.0 403 Forbidden');
        echo "Forbidden";
        die();
    }

    // xss cleanup
    $_FILES = \Contao\Input::xssClean($_FILES);

    $sTempname        = $_FILES['File']['tmp_name'];
    $sFileName        = $_FILES['File']['name'];
    $sFileType        = $_FILES['File']['type'];
    //$sDestinationFile = \Contao\Input::post('File');
    $sDestinationPath = \Contao\Input::post('Path');

    $file = explode('.', $sFileName);
    $sFileExt = strtolower($file[count($file)-1]);

    $mimeTypes = explode(',', \Contao\Input::post('MimeTypes'));
    $found = false;
    foreach ($mimeTypes as $mimeType) {
        if ($sFileType == $mimeType) {
            $found = true;
        }
    }

    if (!$mimeTypes || !$found) {
        die();
    }

    $sUniqID   = uniqid();
    $sFileName = $sUniqID . "." . $sFileExt;

    $contaoPath = substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'],'system', false) -1);
    if($contaoPath == false)
    {
        $contaoPath = '';
    }

    if (empty($sError)) {
        $rootDir = System::getContainer()->getParameter('kernel.project_dir');
        $sSystemPath = str_replace('\\','/',$rootDir) . "/" . trim($sDestinationPath,'/');
        $sDestination = $sSystemPath . "/" . $sFileName;

        if (!is_dir($sSystemPath)) {
            mkdir($sSystemPath, 0777, true);
        }

        if (move_uploaded_file($sTempname, $sDestination)) {
            echo $contaoPath . '/' . trim($sDestinationPath,'/') . "/" . $sFileName;
        } else {
            echo 0;
        }
    }