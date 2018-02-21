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

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialog;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;

class C4GArchiveDialogAction extends C4GBrickDialogAction
{
    public function run()
    {
        $dlgValues    = $this->getPutVars();
        $dialogParams = $this->getDialogParams();
        $dialogId     = $dialogParams->getId();

        if ($dialogId == '') {
            $dialogParams->setId(-1);
            $action = new C4GCloseDialogAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());
            return $action->run();
        }

        if ($dialogParams->getViewType() == C4GBrickViewType::MEMBERBOOKING) {
            $result = C4GBrickDialog::showC4GMessageDialog(
                $dialogId,
                $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_BOOKING_ARCHIVE_DIALOG_QUESTION'],
                $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_BOOKING_ARCHIVE_DIALOG_TEXT'],
                C4GBrickActionType::ACTION_CONFIRMARCHIVE,
                $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_BOOKING_ARCHIVE_DIALOG_YES'],
                C4GBrickActionType::ACTION_CANCELARCHIVE,
                $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_BOOKING_ARCHIVE_DIALOG_NO'],
                $dlgValues
            );
        } else {
            $result = C4GBrickDialog::showC4GMessageDialog(
                $dialogId,
                $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_ARCHIVE_DIALOG_QUESTION'],
                $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_ARCHIVE_DIALOG_TEXT'],
                C4GBrickActionType::ACTION_CONFIRMARCHIVE,
                $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_ARCHIVE_DIALOG_YES'],
                C4GBrickActionType::ACTION_CANCELARCHIVE,
                $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_ARCHIVE_DIALOG_NO'],
                $dlgValues
            );
        }

        return $result;

    }
}
