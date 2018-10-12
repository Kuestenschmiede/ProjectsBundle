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

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialog;
use con4gis\ProjectsBundle\Classes\Models\C4gProjectsModel;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;

class C4GCloseDialogAction extends C4GBrickDialogAction
{
    private $ignoreChanges = false;

    public function run()
    {
        $dialogDataObject = $this->module->getDialogDataObject();
        $diffs = $dialogDataObject->getDifferences();

        if ($diffs && $this->ignoreChanges === false) {
            $action = new C4GShowMessageChangesDialogAction($this->dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());
            $action->setDiffs($diffs);
            $action->setModule($this->module);
            return $action->run();
        } else {
            $this->dialogParams->setId(-1);
            $action = new C4GShowListAction($this->dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());
            $action->setModule($this->module);
            return $action->run();
        }
    }

    /**
     * @param bool $ignoreChanges
     * @return $this
     */
    public function setIgnoreChanges($ignoreChanges = true)
    {
        $this->ignoreChanges = $ignoreChanges;
        return $this;
    }

    public function isReadOnly()
    {
        return true;
    }
}
