<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @author con4gis contributors (see "authors.md")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2026, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Actions;

class C4GCancelDialogAction extends C4GBrickDialogAction
{
    public function run()
    {
        $dialogId = $this->getDialogParams()->getId();
        $module = $this->getModule();

        $return = [
            'dialogclose' => C4GBrickActionType::IDENTIFIER_MESSAGE . $dialogId,
        ];
        if ($module->getDialogChangeHandler()) {
            $module->getDialogChangeHandler()->clearSession($module->getBrickKey());
        }

        return $return;
    }

    public function isReadOnly()
    {
        return true;
    }
}
