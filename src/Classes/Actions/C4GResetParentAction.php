<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2022, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Actions;

class C4GResetParentAction extends C4GBrickDialogAction
{
    public function run()
    {
        $dialogParams->getSession()->setSessionValue('c4g_brick_parent_id', '');
        $this->getDialogParams()->setParentId('');
        $action = new C4GShowListAction($this->getDialogParams(), $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());

        return $action->run();
    }

    public function isReadOnly()
    {
        return true;
    }
}
