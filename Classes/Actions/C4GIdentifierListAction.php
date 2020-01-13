<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    7
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Actions;

use con4gis\ProjectsBundle\Classes\Views\C4GBrickView;

class C4GIdentifierListAction extends C4GBrickAction
{
    protected $module = null;

    public function run()
    {
        $dialogParams = $this->getDialogParams();
        $listParams = $this->getListParams();
        $fieldList = $this->getFieldList();
        $putVars = $this->getPutVars();
        $brickDatabase = $this->getBrickDatabase();

        $viewType = $dialogParams->getViewType();
        $id = $dialogParams->getId();
        $module = $this->getModule();

        if ((C4GBrickView::isWithoutList($viewType) || ($id > 0)) && ($listParams->isForceShowListAction() == false)) {
            $action = new C4GShowDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
            $action->setModule($this->module);
            $return = $action->run();
            if ($module->getDialogChangeHandler()) {
                $module->getDialogChangeHandler()->clearSession($module->getBrickKey());
            }

            return $return;
        }
        $action = new C4GShowListAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
        $return = $action->run();
        if ($module->getDialogChangeHandler()) {
            $module->getDialogChangeHandler()->clearSession($module->getBrickKey());
        }

        return $return;
    }

    /**
     * @return null
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param null $module
     */
    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }

    public function isReadOnly()
    {
        return true;
    }
}
