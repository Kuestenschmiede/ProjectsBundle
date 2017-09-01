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
}
