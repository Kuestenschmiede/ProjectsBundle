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

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickSelectProjectDialog;

class C4GSelectProjectDialogAction extends C4GBrickDialogAction
{
    public function run()
    {
        $dialog = new C4GBrickSelectProjectDialog($this->getDialogParams());

        return $dialog->show();
    }

    public function isReadOnly()
    {
        return true;
    }
}
