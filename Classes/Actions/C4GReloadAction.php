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
