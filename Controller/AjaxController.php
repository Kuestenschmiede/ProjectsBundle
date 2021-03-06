<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Controller;

use con4gis\CoreBundle\Resources\contao\models\C4gLogModel;
use con4gis\DocumentsBundle\Classes\Stack\PdfManager;
use con4gis\MapsBundle\Resources\contao\modules\api\ReverseNominatimApi;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialog;
use con4gis\ProjectsBundle\Classes\Documents\C4GPrintoutPDF;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GDateField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GEmailField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GForeignArrayField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GPostalField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GSelectField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GSubDialogField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GTelField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GTextareaField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GTextField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GTimeField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GTimepickerField;
use con4gis\ProjectsBundle\Classes\Framework\C4GModuleManager;
use Contao\Database;
use Contao\Input;
use Contao\Module;
use Contao\StringUtil;
use Contao\System;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AjaxController extends Controller
{
    public function ajaxAction(Request $request, $language, $module, $action)
    {
        $moduleManager = new C4GModuleManager();
        if ($request->getMethod() === "PUT") {
            $arrData = $request->request->all();
            $actionString = explode(":", $action);
            if ($actionString[0] === "C4GPrintDialogAction") {
                $id = $actionString[1];
                $database = Database::getInstance();
                $objModule = $database->prepare("SELECT * FROM tl_module WHERE id=?")
                    ->limit(1)
                    ->execute($module);
                $strClass = Module::findClass($objModule->type);
                $objModule = new $strClass($objModule);
                $printoutPDF = new C4GPrintoutPDF($database);
                return $printoutPDF->printAction($objModule, $arrData, $id);
            }
            $returnData = $moduleManager->getC4gFrontendModule($module, $language, $action, $arrData);
        } else {
            $returnData = $moduleManager->getC4gFrontendModule($module, $language, $action);
        }
        $response = new JsonResponse();
        if ($returnData === null) {
            $referer = $request->server->get('HTTP_REFERER');
            $referer = substr($referer, 0, strpos($referer, '?state'));
            $this->redirectToRoute($referer . '?state=list:-1');
        } else {
            $response->setData(json_decode($returnData));
            return $response;
        }
    }

    public function getAddressAction(Request $request, $profileId, $lat, $lon)
    {
        $arrParams = array(
            'lat' => $lat,
            'lon' => $lon,
            'addressdetails' => 1
        );
        $return = '';
        try {
            $nominatimApi = new ReverseNominatimApi();
            $xmlOutput = $nominatimApi->getReverseNominatimResponse($profileId, $arrParams);
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

        } catch(Exception $e) {
            $return = 'Umwandlungsfehler'; //ToDo Language
        }
        $response = new JsonResponse();
        $response->setData($return);
        return $response;
    }

    public function editorAction(Request $request, $id)
    {
        $method = $request->getRealMethod();
        $api = new C4GEditorTabApi();
        switch ($method) {
            case 'GET':
                // get is the initial request of tab configuration
                $data = $api->createTabs($id);
                break;
            case 'PUT':
                // an existing element has been modified
                // requires a response to complete the creation process
                $data = $api->onElementModification($id);
                break;
            case 'DELETE':
                // an existing element has been deleted
                // requires a response to complete the creation process
                $data = $api->onElementDeletion($id);
                break;
            default:
                $data = array();
                break;
        }
        return JsonResponse::create($data);
    }

    public function editorPostAction(Request $request)
    {
        $api = new C4GEditorTabApi();
        $data = $api->onElementCreation($request->request->all());
        return JsonResponse::create($data);
    }

    public function uploadAction(Request $request)
    {
        $response = new JsonResponse();
        if (!isset($_POST['Path']) || !isset($_FILES['File']['tmp_name'])) {
            $response->setStatusCode(400);
            return $response;
        }
        // User not logged in...
        if (!FE_USER_LOGGED_IN) {
            $response->setStatusCode(401);
            return $response;
        }
        // xss cleanup
        $_FILES = Input::xssClean($_FILES);
        $sTempname        = $_FILES['File']['tmp_name'];
        $sFileName        = $_FILES['File']['name'];
        if ($sFileName == "blob") {
            $sFileName = Input::post('name');
        }
        $sFileType        = $_FILES['File']['type'];
        //$sDestinationFile = \Contao\Input::post('File');
        $sDestinationPath = Input::post('Path');
        $file = explode('.', $sFileName);
        $sFileExt = strtolower($file[count($file)-1]);
        $mimeTypes = explode(',', $GLOBALS['TL_CONFIG']['uploadTypes']);
        $found = false;
        foreach ($mimeTypes as $mimeType) {
            if ($sFileType === 'image/'.$mimeType || $sFileType === 'application/'.$mimeType) {
                $found = true;
            }
        }
        if (!$mimeTypes || !$found) {
            $response->setStatusCode(400);
            return $response;
        }

        $sUniqID   = uniqid();
        $sFileName = $sUniqID . "." . $sFileExt;

//        $contaoPath = substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'],'system', false) -1);
//        if($contaoPath == false)
//        {
//            $contaoPath = '';
//        }

        $rootDir = System::getContainer()->getParameter('kernel.project_dir');
        $sSystemPath = str_replace('\\','/',$rootDir) . "/" . trim($sDestinationPath,'/');
        $sDestination = $sSystemPath . "/" . $sFileName;

        if (!is_dir($sSystemPath)) {
            mkdir($sSystemPath, 0777, true);
        }

        if (move_uploaded_file($sTempname, $sDestination)) {
            $response->setData([trim($sDestinationPath,'/') . "/" . $sFileName]);
//            $response->setData([$contaoPath . '/' . trim($sDestinationPath,'/') . "/" . $sFileName]);
//                echo $contaoPath . '/' . trim($sDestinationPath,'/') . "/" . $sFileName;
        } else {
            $response->setData([]);
        }
        return $response;
    }
}

