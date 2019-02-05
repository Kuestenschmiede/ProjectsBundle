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
