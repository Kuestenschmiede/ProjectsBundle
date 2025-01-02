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

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialog;

class C4GDeleteDialogAction extends C4GBrickDialogAction
{
    public function run()
    {
        $dlgValues = $this->getPutVars();
        $dialogParams = $this->getDialogParams();
        $dialogId = $dialogParams->getId();

        if ($dialogId == '') {
            $dialogParams->setId(-1);
            $action = new C4GCloseDialogAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());

            return $action->run();
        }

        $result = C4GBrickDialog::showC4GMessageDialog(
            $dialogId,
            $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_DELETE_DIALOG_QUESTION'],
            $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_DELETE_DIALOG_TEXT'],
            C4GBrickActionType::ACTION_CONFIRMDELETE,
            $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_DELETE_DIALOG_YES'],
            C4GBrickActionType::ACTION_CANCELMESSAGE,
            $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_DELETE_DIALOG_NO'],
            $dlgValues
        );

        return $result;
    }

    public function isReadOnly()
    {
        return false;
    }
}
