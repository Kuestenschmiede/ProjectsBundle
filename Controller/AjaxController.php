<?php
/**
 *
 */

namespace con4gis\ProjectBundle\Controller;

use Contao\Database;
use Contao\Module;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AjaxController extends Controller
{
    public function ajaxAction(Request $request, $module, $action)
    {
        $returnData = $this->getC4gFrontendModule($module, $action);
        $response = new JsonResponse();
        $response->setData($returnData);
        return $response;
    }

    // Todo Auslagern in ModuleManager
    private function getC4gFrontendModule($id, $request)
    {
        if (!strlen($id) || $id < 1) {
            header('HTTP/1.1 412 Precondition Failed');
            return 'Missing frontend module ID';
        }

        $objModule = Database::getInstance()->prepare("SELECT * FROM tl_module WHERE id=?")
            ->limit(1)
            ->execute($id);

        if ($objModule->numRows < 1) {
            header('HTTP/1.1 404 Not Found');
            return 'Frontend module not found';
        }

        // Show to guests only
        if ($objModule->guests && FE_USER_LOGGED_IN && !BE_USER_LOGGED_IN && !$objModule->protected) {
            header('HTTP/1.1 403 Forbidden');
            return 'Forbidden';
        }

        // Protected element
        if (!BE_USER_LOGGED_IN && $objModule->protected) {
            if (!FE_USER_LOGGED_IN) {
                header('HTTP/1.1 403 Forbidden');
                return 'Forbidden';
            }

            $this->import('FrontendUser', 'User');
            $groups = deserialize($objModule->groups);

            if (!is_array($groups) || count($groups) < 1 || count(array_intersect($groups, $this->User->groups)) < 1) {
                header('HTTP/1.1 403 Forbidden');
                return 'Forbidden';
            }
        }

        $strClass = Module::findClass($objModule->type);
//        $strClass = $this->findFrontendModule($objModule->type);

        // Return if the class does not exist
        if (!class_exists($strClass)) {
            $this->log(
                'Module class "'.$GLOBALS['FE_MOD'][$objModule->type].
                '" (module "'.$objModule->type.'") does not exist',
                'Ajax getFrontendModule()',
                TL_ERROR
            );

            header('HTTP/1.1 404 Not Found');
            return 'Frontend module class does not exist';
        }

        $objModule->typePrefix = 'mod_';
        $objModule = new $strClass($objModule);

        return $objModule->generateAjax($request);
    }
}

