<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\ProjectsBundle\Classes\Actions;

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickSelectParentDialog;

class C4GSelectParentDialogAction extends C4GBrickDialogAction
{
    public function run()
    {
        $dialog = new C4GBrickSelectParentDialog($this->getDialogParams(), $this->brickDatabase, $this->module);
        return $dialog->show();
    }

    public function isReadOnly()
    {
        return true;
    }
}
