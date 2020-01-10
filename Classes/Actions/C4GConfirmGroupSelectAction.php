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
namespace con4gis\ProjectsBundle\Classes\Actions;

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

        \Session::getInstance()->set('c4g_brick_group_id', $dlgValues[$groupKeyField]);
        \Session::getInstance()->set('c4g_brick_project_id', '');
        \Session::getInstance()->set('c4g_brick_project_uuid', '');
        \Session::getInstance()->set('c4g_brick_parent_id', '');

        $this->setPutVars(null);
        //$this->setFieldList(null);

        $action = new C4GShowListAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());

        return $action->run();
    }

    public function isReadOnly()
    {
        return true;
    }
}
