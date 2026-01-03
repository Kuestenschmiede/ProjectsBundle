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
