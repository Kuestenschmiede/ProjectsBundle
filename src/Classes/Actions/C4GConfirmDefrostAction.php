<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @author con4gis contributors (see "authors.md")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2026, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Actions;

class C4GConfirmDefrostAction extends C4GBrickDialogAction
{
    public function run()
    {
        $brickDatabase = $this->getBrickDatabase();
        $dialogParams = $this->getDialogParams();
        $dialogId = $dialogParams->getId();
        $object = $brickDatabase->findByPk($dialogId);
        if ($object) {
            $object->is_frozen = false;
            $object->save();
        }

        $action = new C4GShowListAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());

        return $action->run();
    }

    public function isReadOnly()
    {
        return false;
    }
}
