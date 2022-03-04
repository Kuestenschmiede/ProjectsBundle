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

class C4GIdentifierParentAction extends C4GBrickAction
{
    public function run()
    {
        $dialogParams = $this->getDialogParams();
        $listParams = $this->getListParams();
        $fieldList = $this->getFieldList();
        $putVars = $this->getPutVars();
        $brickDatabase = $this->getBrickDatabase();
        $id = $dialogParams->getId();

        $dialogParams->setId(-1);
        $dialogParams->setParentId($id);
        $action = new C4GShowDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);

        return $action->run();
    }

    public function isReadOnly()
    {
        return true;
    }
}
