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

class C4GReloadAction extends C4GBrickDialogAction
{
    public function run()
    {
        $jumpTo = \Controller::replaceInsertTags('{{link_url::back}}');
        $return['jump_to_url'] = $jumpTo;

        return $return;
    }

    public function isReadOnly()
    {
        return true;
    }
}
