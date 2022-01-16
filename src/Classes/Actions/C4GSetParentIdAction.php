<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2022, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Actions;

class C4GSetParentIdAction extends C4GBrickDialogAction
{
    public function run()
    {
        $dlgValues = $this->getPutVars();
        $dialogParams = $this->getDialogParams();
        $dialogParams->setParentId($dlgValues['parent_id']);
        $module = $this->getModule();

        $dialogParams->getSession()->setSessionValue('c4g_brick_parent_id', $dlgValues['parent_id']);
        $this->setPutVars(null);

        $dialogParams->setId(-1);
        $action = new C4GShowListAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());
        $module->setFieldList($this->fieldList);
        $action->setFieldList($this->fieldList);

        return $action->run();
    }

    public function isReadOnly()
    {
        return true;
    }
}
