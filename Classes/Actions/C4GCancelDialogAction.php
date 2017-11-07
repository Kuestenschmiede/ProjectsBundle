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

class C4GCancelDialogAction extends C4GBrickDialogAction
{
    public function run()
    {
        $dialogId = $this->getDialogParams()->getId();

        $return = array(
            'dialogclose' => C4GBrickActionType::IDENTIFIER_MESSAGE.$dialogId,
        );
        $action = new C4GShowListAction(
            $this->dialogParams,
            $this->listParams,
            $this->fieldList,
            $this->putVars,
            $this->brickDatabase
        );
        return $action->run();
//        return $return;
    }
}
