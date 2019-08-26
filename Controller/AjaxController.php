<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    6
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Controller;

use con4gis\DocumentsBundle\Classes\Stack\PdfManager;
use con4gis\MapsBundle\Resources\contao\modules\api\ReverseNominatimApi;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialog;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GForeignArrayField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GSelectField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GSubDialogField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GTextareaField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GTextField;
use con4gis\ProjectsBundle\Classes\Framework\C4GModuleManager;
use Contao\Database;
use Contao\Input;
use Contao\Module;
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
                return $this->printAction($request, $module, $arrData, $action);
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

    private function checkSubFields(&$field, $data)
    {
        if ($field instanceof C4GSubDialogField) {
            $subFieldList = array();
            if ($field->getFieldList()) {
                foreach ($field->getFieldList() as $subField) {
                    $subField->setDescription("");
                    $subField->setEditable(false);
                    $subField->setShowIfEmpty(false);
                    if (($subField instanceof C4GTextField) || ($subField instanceof C4GTextareaField)) {
                        $newField = new C4GTextField();
                        $newField->setFieldName($subField->getFieldName());
                        $newField->setTitle($subField->getTitle());
                        $newField->setSimpleTextWithoutEditing(true);
                        $newField->setShowIfEmpty(false);
                        $newField->setPrintable($subField->isPrintable());
                        $newField->setTableRow(true);
                        $subField = $newField;
                    }
                    if ($subField instanceof C4GSelectField) {
                        $subField->setSimpleTextWithoutEditing(true);
                        $subField->setInitialValue($data[$field->getFieldName()]);
                        $subField->setTableRow(true);
                    }
                    if ($subField->isPrintable() && (trim($data[$subField->getFieldName()]) || (($field instanceof C4GSubDialogField) || ($field instanceof C4GForeignArrayField)))) {
                        $this->checkSubFields($subField, $data);
                        $subFieldList[] = $subField;
                    }
                }
                $field->setFieldList($subFieldList);
            }
        }

        if ($field instanceof C4GForeignArrayField) {
            $subFieldList = array();
            if ($field->getForeignFieldList()) {
                foreach ($field->getForeignFieldList() as $subField) {
                    $subField->setDescription("");
                    $subField->setEditable(false);
                    $subField->setShowIfEmpty(false);

                    if (($subField instanceof C4GTextField) || ($subField instanceof C4GTextareaField)) {
                        $newField = new C4GTextField();
                        $newField->setFieldName($subField->getFieldName());
                        $newField->setTitle($subField->getTitle());
                        $newField->setSimpleTextWithoutEditing(true);
                        $newField->setShowIfEmpty(false);
                        $newField->setPrintable($subField->isPrintable());
                        $newField->setTableRow(true);
                        $subField = $newField;
                    }
                    if ($subField instanceof C4GSelectField) {
                        $subField->setSimpleTextWithoutEditing(true);
                        $subField->setInitialValue($data[$field->getFieldName()]);
                        $subField->setTableRow(true);
                    }
                    if ($subField->isPrintable() && (trim($data[$subField->getFieldName()]) || (($field instanceof C4GSubDialogField) || ($field instanceof C4GForeignArrayField)))) {
                        $this->checkSubFields($subField, $data);
                        $subFieldList[] = $subField;
                    }
                }
                $field->setForeignFieldList($subFieldList);
            }
        }

    }

    public function printAction(Request $request, $module, $data, $action)
    {
        $this->get('contao.framework')->initialize();
        $objModule = Database::getInstance()->prepare("SELECT * FROM tl_module WHERE id=?")
            ->limit(1)
            ->execute($module);
        $strClass = Module::findClass($objModule->type);
        $objModule = new $strClass($objModule);
        $arrAction = explode(":", $action);
        $id = $arrAction[1];
        if (method_exists($objModule, 'printPdf')) {
            return $objModule->printPdf($id);
        }
        $objModule->initBrickModule($id);

        $objModule->getDialogParams()->setTabContent(false);
        $objModule->getDialogParams()->setAccordion(false);

        $fieldList = $objModule->getFieldList();
        $printFieldList = array();
        foreach ($fieldList as $field) {
            $field->setDescription("");
            $field->setEditable(false);
            $field->setShowIfEmpty(false);
            if (($field instanceof C4GTextField) || ($field instanceof C4GTextareaField)) {
                $newField = new C4GTextField();
                $newField->setFieldName($field->getFieldName());
                $newField->setTitle($field->getTitle());
                $newField->setSimpleTextWithoutEditing(true);
                $newField->setShowIfEmpty(false);
                $newField->setPrintable($field->isPrintable());
                $newField->setTableRow(true);
                $field = $newField;
            }
            if ($field instanceof C4GSelectField) {
                $field->setSimpleTextWithoutEditing(true);
                $field->setInitialValue($data[$field->getFieldName()]);
                $field->setTableRow(true);
            }
            if ($field->isPrintable() && (trim($data[$field->getFieldName()]) || (($field instanceof C4GSubDialogField) || ($field instanceof C4GForeignArrayField)))) {
                $this->checkSubFields($field, $data);
                $printFieldList[] = $field;
            }
        }

        $content = C4GBrickDialog::buildDialogView(
            $printFieldList,
            $objModule->getBrickDatabase(),
            $data,
            null,
            $objModule->getDialogParams()
        );

        $pdfManager = new PdfManager();
        $style = TL_ROOT.'bundles/con4gisprojects/css/c4g_brick_print.css';
        $pdfManager->style = $style;

        $pdfData = array();
        $pdfData['template'] = 'c4g_pdftemplate';
        $pdfData['filename'] = '{{date::Y_m_d-H_i_s}}_document.pdf';
        $pdfData['filepath'] = C4GBrickConst::PATH_BRICK_DOCUMENTS;
        $pdfManager->setData($pdfData);

        $captionField = $objModule->getDialogParams()->getCaptionField();
        $pdfManager->headline = $objModule->getDialogParams()->getBrickCaption().': '.$data[$captionField];
        $pdfManager->hl = 'h1';

        $pdfManager->content = $content;
        $pdfManager->save();

        $path = $pdfManager->getPdfDocument()->getPath() . $pdfManager->getPdfDocument()->getFilename();
        // cut out the local path before "files"
        $path = substr($path, strpos($path, "files"));
        $response = new JsonResponse([
            "filePath" => $path,
            "fileName" => $pdfManager->getPdfDocument()->getFilename()
        ]);
        return $response;
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
            $response->setStatusCode(403);
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
        $mimeTypes = explode(',', Input::post('MimeTypes'));
        $found = false;
        foreach ($mimeTypes as $mimeType) {
            if ($sFileType == $mimeType) {
                $found = true;
            } elseif ($mimeType === 'image/*') {
                switch ($sFileType) {
                    case 'image/png':
                    case 'image/jpg':
                    case 'image/jpeg':
                        $found = true;
                        break;
                }
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

