<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\ProjectsBundle\Classes\Actions;


use con4gis\ProjectsBundle\Classes\Views\C4GBrickView;

class C4GIdentifierListAction extends C4GBrickAction
{
    protected $module = null;

    public function run() {
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
            $return = $action->run();
            if ($module->getDialogChangeHandler()) {
                $module->getDialogChangeHandler()->clearSession($module->getBrickKey());
            }
            return $return;
        } else {
            $action = new C4GShowListAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
            $return = $action->run();
            if ($module->getDialogChangeHandler()) {
                $module->getDialogChangeHandler()->clearSession($module->getBrickKey());
            }
            return $return;
        }
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
