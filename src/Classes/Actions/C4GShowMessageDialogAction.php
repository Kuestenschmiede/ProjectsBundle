<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Actions;

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialog;

class C4GShowMessageDialogAction extends C4GBrickDialogAction
{
    public function run()
    {
        $dlgValues = $this->getPutVars();
        $dialogParams = $this->getDialogParams();

        return C4GBrickDialog::showC4GMessageDialog(
            $dialogParams->getId(),
            $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_CLOSE_DIALOG_QUESTION'],
            $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_CLOSE_DIALOG_TEXT'],
            C4GBrickActionType::ACTION_CONFIRMMESSAGE,
            $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_CLOSE_DIALOG_YES'],
            C4GBrickActionType::ACTION_CANCELMESSAGE,
            $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_CLOSE_DIALOG_NO'],
            $dlgValues);
    }

    public function isReadOnly()
    {
        return true;
    }
}
