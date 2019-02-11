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

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickFilterDialog;

class C4GShowFilterDialogAction extends C4GBrickDialogAction
{
    public function run()
    {
        $filterParams = $this->getListParams()->getFilterParams();
        $dialogParams = $this->getDialogParams();
        $dialogParams->setMemberId($dialogParams->getMemberId());
        $dialogParams->setFilterParams($filterParams);

        $dialog = new C4GBrickFilterDialog($dialogParams);
        if ($this->listParams->getFilterObject() !== null) {
            $dialog->setFilter($this->listParams->getFilterObject());
        }
        return $dialog->show();
    }

    public function isReadOnly()
    {
        return true;
    }
}
