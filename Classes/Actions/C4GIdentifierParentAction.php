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


class C4GIdentifierParentAction extends C4GBrickAction
{
    public function run() {
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
