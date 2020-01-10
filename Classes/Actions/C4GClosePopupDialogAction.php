<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    7
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Actions;

class C4GClosePopupDialogAction extends C4GBrickDialogAction
{
    public function run()
    {
        //ToDo Lösung finden den Dialog zu schließen
        //echo '<script type="text/javascript">closePopupWindow();</script>';
    }

    public function isReadOnly()
    {
        return true;
    }
}
