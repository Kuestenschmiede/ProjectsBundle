<?php
/**
 *
 */

namespace con4gis\ProjectsBundle\Controller;

use con4gis\MapsBundle\Resources\contao\modules\api\ReverseNominatimApi;
use con4gis\ProjectsBundle\Classes\Framework\C4GModuleManager;
use con4gis\ProjectsBundle\Resources\contao\modules\api\C4GEditorTabApi;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AjaxController extends Controller
{
    public function ajaxAction(Request $request, $module, $action)
    {
        $moduleManager = new C4GModuleManager();
        $returnData = $moduleManager->getC4gFrontendModule($module, $action);
        $response = new JsonResponse();
        $response->setData($returnData);
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
}

