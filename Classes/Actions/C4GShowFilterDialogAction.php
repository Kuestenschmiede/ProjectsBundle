<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    6
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
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
