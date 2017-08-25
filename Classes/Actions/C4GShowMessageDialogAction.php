<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\ProjectBundle\Classes\Actions;

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
}
