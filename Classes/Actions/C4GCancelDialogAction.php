<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2019
 * @link      https://www.kuestenschmiede.de
 */
namespace con4gis\ProjectsBundle\Classes\Actions;

class C4GCancelDialogAction extends C4GBrickDialogAction
{
    public function run()
    {
        $dialogId = $this->getDialogParams()->getId();
        $module = $this->getModule();

        $return = array(
            'dialogclose' => C4GBrickActionType::IDENTIFIER_MESSAGE.$dialogId,
        );
        if ($module->getDialogChangeHandler()) {
            $module->getDialogChangeHandler()->clearSession($module->getBrickKey());
        }
        return $return;
    }

    public function isReadOnly()
    {
        return true;
    }
}
