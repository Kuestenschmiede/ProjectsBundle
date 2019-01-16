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

use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;

class C4GConfirmMessageAction extends C4GBrickDialogAction
{
    public function run()
    {
        $dlgValues = $this->getPutVars();

        $url = $dlgValues['c4g_uploadURL'];
        if ($url) {
            C4GBrickCommon::deleteFile($url);
        }

        $action = new C4GShowListAction($this->getDialogParams(), $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());
        return $action->run();
    }

    public function isReadOnly()
    {
        return true;
    }
}
