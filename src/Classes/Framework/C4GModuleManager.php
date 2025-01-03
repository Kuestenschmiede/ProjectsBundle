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
namespace con4gis\ProjectsBundle\Classes\Framework;

use con4gis\CoreBundle\Classes\Helper\ArrayHelper;
use con4gis\ProjectsBundle\Classes\Buttons\C4GMoreButton;
use con4gis\ProjectsBundle\Classes\Buttons\C4GMoreButtonEntry;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GMoreButtonField;
use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Database;
use Contao\Frontend;
use Contao\Module;
use Contao\ModuleModel;
use Contao\System;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RequestStack;
use Contao\FrontendUser;
use Contao\StringUtil;

class C4GModuleManager
{
    /**
     * A hashmap which stores all actively used Frontend modules. This way they are not reinitialized on
     * every ajax call, optimizing performance and avoiding errors.
     * @var array
     */
    private $moduleMap = [];

    public function getC4gFrontendController(string $rootDir, RequestStack $requestStack, ContaoFramework $framework, $id, $language, $classname, $request, $putVars = [])
    {
        $hasBackendUser = System::getContainer()->get('contao.security.token_checker')->hasBackendUser();
        $hasFrontendUser = System::getContainer()->get('contao.security.token_checker')->hasFrontendUser();
        if (!strlen($id) || $id < 1) {
            header('HTTP/1.1 412 Precondition Failed');

            return 'Missing frontend module ID';
        }

        $objModule = ModuleModel::findByPk($id);

        if (!$objModule) {
            header('HTTP/1.1 404 Not Found');

            return 'Frontend module not found';
        }

        // Show to guests only
        if ($objModule->guests && $hasFrontendUser && !$hasBackendUser && !$objModule->protected) {
            header('HTTP/1.1 403 Forbidden');

            return 'Forbidden';
        }

        // Protected element
        if (!$hasBackendUser && $objModule->protected) {
            if (!$hasFrontendUser) {
                header('HTTP/1.1 403 Forbidden');

                return 'Forbidden';
            }

            $this-import(FrontendUser::class, 'User');
            $groups = StringUtil::deserialize($objModule->groups);

            if (!is_array($groups) || count($groups) < 1 || count(array_intersect($groups, $this->User->groups)) < 1) {
                header('HTTP/1.1 403 Forbidden');

                return 'Forbidden';
            }
        }
        $strClass = Module::findClass($objModule->type);

        if (!class_exists($strClass)) {
            $this->log(
                'Controller class "' . $GLOBALS['FE_MOD'][$objModule->type] .
                '" (module "' . $objModule->type . '") does not exist',
                'Ajax getFrontendModule()',
                TL_ERROR
            );

            header('HTTP/1.1 404 Not Found');

            return 'Frontend controller class does not exist';
        }

        if ($strClass && !$this->moduleMap[$id]) {
            $objModule->typePrefix = 'mod_';
            $controllerModule = new $classname($rootDir, $requestStack, $framework, $objModule);
            $this->moduleMap[$id] = $controllerModule;
        } else {
            $controllerModule = $this->moduleMap[$id];
        }

        if (strpos($request, 'morebutton') === 0) {
            $arrRequest = explode(':', $request);
            // 0 is the morebutton string, 1 is the element id and 2 is the index of the more button option
            $controllerModule->initBrickModule($arrRequest[1]);
            $arrMorebutton = explode('_', $arrRequest[0]);
            if ($arrMorebutton && count($arrMorebutton) == 2) {
                $fieldList = $controllerModule->getFieldList();
                $moreButton = null;
                foreach ($fieldList as $field) {
                    if ($field instanceof C4GMoreButtonField && $field->getFieldName() === $arrMorebutton[1]) {
                        $moreButton = $field->getMoreButton();

                        break;
                    }
                }
                if ($moreButton && $moreButton instanceof C4GMoreButton) {
                    $callable = $moreButton->getEntryByIndex($arrRequest[2]);
                    if ($callable instanceof C4GMoreButtonEntry) {
                        $params = [$arrRequest[1]];
                        if (count($arrRequest) > 3) {
                            for ($i = 3; $i < count($arrRequest); $i++) {
                                $params[] = $arrRequest[$i];
                            }
                        }
                        $arrData = $callable->call($params);

                        return json_encode($arrData);
                    }
                }
            }
        }

        $controllerModule->setLanguage($language);
        return $controllerModule->generateAjax($request);
    }

    public function getC4gFrontendModule($id, $language, $request, $putVars = [])
    {
        if (!strlen($id) || $id < 1) {
            header('HTTP/1.1 412 Precondition Failed');

            return 'Missing frontend module ID';
        }

        $objModule = Database::getInstance()->prepare('SELECT * FROM tl_module WHERE id=?')
            ->limit(1)
            ->execute($id);

        if ($objModule->numRows < 1) {
            header('HTTP/1.1 404 Not Found');

            return 'Frontend module not found';
        }

        $hasBackendUser = System::getContainer()->get('contao.security.token_checker')->hasBackendUser();
        $hasFrontendUser = System::getContainer()->get('contao.security.token_checker')->hasFrontendUser();

        // Show to guests only
        if ($objModule->guests && $hasFrontendUser && !$hasBackendUser && !$objModule->protected) {
            header('HTTP/1.1 403 Forbidden');

            return 'Forbidden';
        }

        // Protected element
        if (!$hasBackendUser && $objModule->protected) {
            if (!$hasFrontendUser) {
                header('HTTP/1.1 403 Forbidden');

                return 'Forbidden';
            }

            $this->import(FrontendUser::class, 'User' );
            $groups = deserialize($objModule->groups);

            if (!is_array($groups) || count($groups) < 1 || count(array_intersect($groups, $this->User->groups)) < 1) {
                header('HTTP/1.1 403 Forbidden');

                return 'Forbidden';
            }
        }

        $strClass = Module::findClass($objModule->type);
        if ($strClass === "Contao\ModuleProxy") {
            $this->log(
                'Module class "' . $GLOBALS['FE_MOD'][$objModule->type] .
                '" (module "' . $objModule->type . '") does not exist',
                'Ajax getFrontendModule()',
                TL_ERROR
            );

            header('HTTP/1.1 404 Not Found');

            return 'Projects Controller incorrectly integrated';
        } else {
            // Return if the class does not exist
            if (!class_exists($strClass)) {
                $this->log(
                    'Module class "' . $GLOBALS['FE_MOD'][$objModule->type] .
                    '" (module "' . $objModule->type . '") does not exist',
                    'Ajax getFrontendModule()',
                    TL_ERROR
                );

                header('HTTP/1.1 404 Not Found');

                return 'Frontend module class does not exist';
            }

            if (!$this->moduleMap[$id]) {
                $objModule->typePrefix = 'mod_';
                $objModule = new $strClass($objModule);
                $this->moduleMap[$id] = $objModule;
            } else {
                $objModule = $this->moduleMap[$id];
            }
        }


        if (strpos($request, 'morebutton') === 0) {
            $arrRequest = explode(':', $request);
            // 0 is the morebutton string, 1 is the element id and 2 is the index of the more button option
            $objModule->setLanguage($language);
            $objModule->initBrickModule($arrRequest[1]);
            $arrMorebutton = explode('_', $arrRequest[0]);
            if ($arrMorebutton && count($arrMorebutton) == 2) {
                $fieldList = $objModule->getFieldList();
                $moreButton = null;
                foreach ($fieldList as $field) {
                    if ($field instanceof C4GMoreButtonField && $field->getFieldName() === $arrMorebutton[1]) {
                        $moreButton = $field->getMoreButton();

                        break;
                    }
                }
                if ($moreButton && $moreButton instanceof C4GMoreButton) {
                    $callable = $moreButton->getEntryByIndex($arrRequest[2]);
                    if ($callable instanceof C4GMoreButtonEntry) {
                        $params = [$arrRequest[1]];
                        if (count($arrRequest) > 3) {
                            for ($i = 3; $i < count($arrRequest); $i++) {
                                $params[] = $arrRequest[$i];
                            }
                        }
                        $arrData = $callable->call($params);

                        return json_encode($arrData);
                    }
                }
            }
        }

        $objModule->setLanguage($language);

        return $objModule->generateAjax($request);
    }
}