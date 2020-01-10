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

class C4GConfirmCustomDialogAction extends C4GBrickDialogAction
{
    public function run()
    {
        return $this->dialogParams->getCustomDialogCallback()->call();
    }

    public function isReadOnly()
    {
        return false;
    }
}
