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

    if (!isset($_POST['Lat']) || !isset($_POST['Lon']) || !isset($_POST['Profile']) ) {
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

    $return = '';

    $id = \Contao\Input::post('Profile');
    $arrParams = array(
        'format' => 'xml',
        'lat'    => \Contao\Input::post('Lat'),
        'lon'    => \Contao\Input::post('Lon'),
        'addressdetails' => 1
    );

    try {
        $nominatimApi = new \con4gis\MapsBundle\Resources\contao\modules\api\ReverseNominatimApi();
        $xmlOutput = $nominatimApi->getReverseNominatimResponse($id, $arrParams);
        $xml = simplexml_load_string($xmlOutput);

        if ($xml && $xml->addressparts[0]) {
            $housenumber  = $xml->addressparts[0]->house_number; //Hausnummer
            $road         = $xml->addressparts[0]->road; //Straße
            $pedestrian   = $xml->addressparts[0]->pedestrian; //Fussweg
            $suburb       = $xml->addressparts[0]->suburb; //Ortsteil
            $town         = $xml->addressparts[0]->town; //Ort
            $county       = $xml->addressparts[0]->county; //Landkreis
            $state        = $xml->addressparts[0]->state; //Bundesland
            $postcode     = $xml->addressparts[0]->postcode; //Postleitzahl
            $country      = $xml->addressparts[0]->country; //Land
            $country_code = $xml->addressparts[0]->country_code; //Länderschlüssel

            $return = $road;
            if (!$return) {
                $return = $pedestrian;
            }

            if ($return && $housenumber) {
                $return .= ' '.$housenumber;
            }

            if ( ($postcode || $town) && ($road || $pedestrian) ) {
                $return .= ',';
            }

            if ($postcode) {
                $return .= ' '.$postcode;
            }

            if ($town) {
                $return .= ' '.$town;
            }
        }

    }catch(Exception $e) {
        $return = 'Umwandlungsfehler'; //ToDo Language
    }

    echo trim($return);