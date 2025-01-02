<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 10
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2025, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
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
