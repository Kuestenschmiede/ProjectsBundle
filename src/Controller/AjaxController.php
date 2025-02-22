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
namespace con4gis\ProjectsBundle\Controller;

use con4gis\CoreBundle\Controller\ApiController;
use con4gis\MapsBundle\Resources\contao\modules\api\ReverseNominatimApi;
use con4gis\ProjectsBundle\Classes\Documents\C4GPrintoutPDF;
use con4gis\ProjectsBundle\Classes\Framework\C4GModuleManager;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Database;
use Contao\FrontendUser;
use Contao\Input;
use Contao\Module;
use Contao\ModuleModel;
use Contao\System;
use Psr\Container\ContainerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\HttpFoundation\RequestStack;

class AjaxController extends ApiController
{
    protected $rootDir;
    protected $session;
    protected $framework;
    protected $requestStack;

    public function __construct(string $rootDir, RequestStack $requestStack, ContaoFramework $framework)
    {
        $this->rootDir      = $rootDir;
        // $this->session      = $session = $requestStack->getCurrentRequest()->getSession();
        $this->requestStack = $requestStack;
        $this->framework    = $framework;

        $this->framework->initialize(true);
    }

    public function ajaxAction(Request $request, $language, $classname, $module, $action)
    {
        $moduleManager = new C4GModuleManager();
        if ($request->getMethod() === "PUT") {
            $arrData = $request->request->all();
            $actionString = explode(":", $action);

            //ToDo Print Service
            if ($actionString[0] === "C4GPrintDialogAction") {
                $id = $actionString[1];
                $database = Database::getInstance();
                $objModule = ModuleModel::findByPk($module);
                $strClass = Module::findClass($objModule->type);

                if ($classname) {
                    if ($strClass && class_exists($strClass)) {
                        // $objModule = new $classname($this->rootDir, $this->session, $this->framework, $objModule);
                        $objModule = new $classname($this->rootDir, $this->srequestStack, $this->framework, $objModule);
                        $printoutPDF = new C4GPrintoutPDF($database, $language);
                        return $printoutPDF->printAction($objModule, $arrData, $id, true);
                    }
                } else {
                    if ($strClass && class_exists($strClass)) {
                        $objModule = new $strClass($objModule);
                        $printoutPDF = new C4GPrintoutPDF($database, $language);
                        return $printoutPDF->printAction($objModule, $arrData, $id, true);
                    }
                }
            }

            if ($classname) {
                // $returnData = $moduleManager->getC4gFrontendController($this->rootDir, $this->session, $this->framework, $module, $language, $classname, $action, $arrData);
                $returnData = $moduleManager->getC4gFrontendController($this->rootDir, $this->requestStack, $this->framework, $module, $language, $classname, $action, $arrData);
            } else {
                $returnData = $moduleManager->getC4gFrontendModule($module, $language, $action, $arrData);
            }
        } else {
            if ($classname) {
                // $returnData = $moduleManager->getC4gFrontendController($this->rootDir, $this->session, $this->framework, $module, $language, $classname, $action);
                $returnData = $moduleManager->getC4gFrontendController($this->rootDir, $this->requestStack, $this->framework, $module, $language, $classname, $action);
            } else {
                $returnData = $moduleManager->getC4gFrontendModule($module, $language, $action);
            }
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
                $housenumber  = $xml->addressparts[0]->house_number;
                $road         = $xml->addressparts[0]->road;
                $pedestrian   = $xml->addressparts[0]->pedestrian;
                $town         = $xml->addressparts[0]->town;
                $postcode     = $xml->addressparts[0]->postcode;

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
            $return = 'Umwandlungsfehler';
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
        $user = FrontendUser::getInstance();
        if ($user->id < 1) {
            $response->setStatusCode(401);
            return $response;
        }
        $_FILES = Input::xssClean($_FILES);
        $sTempname        = $_FILES['File']['tmp_name'];
        $sFileName        = $_FILES['File']['name'];
        if ($sFileName == "blob") {
            $sFileName = Input::post('name');
        }
        $sFileType        = $_FILES['File']['type'];
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

        $rootDir = System::getContainer()->getParameter('kernel.project_dir');
        $sSystemPath = str_replace('\\','/',$rootDir) . "/" . trim($sDestinationPath,'/');
        $sDestination = $sSystemPath . "/" . $sFileName;

        if (!is_dir($sSystemPath)) {
            mkdir($sSystemPath, 0777, true);
        }

        if (move_uploaded_file($sTempname, $sDestination)) {
            $response->setData([trim($sDestinationPath,'/') . "/" . $sFileName]);
        } else {
            $response->setData([]);
        }
        return $response;
    }
}