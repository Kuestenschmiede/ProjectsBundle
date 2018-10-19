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

class C4GResetParentAction extends C4GBrickDialogAction
{
    public function run()
    {
        \Session::getInstance()->set("c4g_brick_parent_id", '');
        $this->getDialogParams()->setParentId('');
        $action = new C4GShowListAction($this->getDialogParams(), $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());
        return $action->run();
    }

    public function isReadOnly()
    {
        return true;
    }
}
