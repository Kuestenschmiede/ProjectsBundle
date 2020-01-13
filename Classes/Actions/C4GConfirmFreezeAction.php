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

class C4GConfirmFreezeAction extends C4GBrickDialogAction
{
    public function run()
    {
        $brickDatabase = $this->getBrickDatabase();
        $dialogParams = $this->getDialogParams();
        $dialogId = $dialogParams->getId();
        $object = $brickDatabase->findByPk($dialogId);
        if ($object) {
            $object->is_frozen = true;
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
