<?php
/**
 *
 */

namespace con4gis\ProjectBundle\Controller;

use con4gis\ProjectBundle\Classes\Framework\C4GModuleManager;
use Contao\Database;
use Contao\Module;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AjaxController extends Controller
{
    public function ajaxAction(Request $request, $module, $action)
    {
        $returnData = C4GModuleManager::getInstance()->getC4gFrontendModule($module, $action);
        $response = new JsonResponse();
        $response->setData($returnData);
        return $response;
    }
}

