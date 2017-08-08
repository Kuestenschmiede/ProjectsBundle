<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\ProjectBundle\Classes\Actions;

class C4GConfirmGroupSelectAction extends C4GBrickDialogAction
{
    public function run()
    {
        $dlgValues = $this->getPutVars();
        $dialogParams = $this->getDialogParams();
        $viewParams = $dialogParams->getViewParams();

        $groupKeyField = $viewParams->getGroupKeyField();
        $dialogParams->setId(-1);
        $dialogParams->setGroupId($dlgValues[$groupKeyField]);
        $dialogParams->setProjectId('');
        $dialogParams->setProjectUuid('');
        $dialogParams->setParentId('');

        \Session::getInstance()->set("c4g_brick_group_id", $dlgValues[$groupKeyField]);
        \Session::getInstance()->set("c4g_brick_project_id", '');
        \Session::getInstance()->set("c4g_brick_project_uuid", '');
        \Session::getInstance()->set("c4g_brick_parent_id", '');

        $this->setPutVars(null);
        //$this->setFieldList(null);

        $action = new C4GShowListAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());
        return $action->run();
    }
}
