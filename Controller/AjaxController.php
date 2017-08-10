<?php
/**
 *
 */

namespace con4gis\ProjectBundle\Controller;

use con4gis\ProjectBundle\Classes\Framework\C4GModuleManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
}

