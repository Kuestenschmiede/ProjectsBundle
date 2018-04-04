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

class C4GShowRedirectDialogAction extends C4GBrickDialogAction
{
    public function run()
    {
        $dlgValues = $this->getPutVars();
        $dialogParams = $this->getDialogParams();
        $redirects = $dialogParams->getRedirects();
        $redirectDialogMessage = '';
        $redirectDialogTitle = '';
        $redirectDialogSite = '';
        $redirectWithDialog = false;

        if ($redirects) {
            foreach ($redirects as $redirect) {
                if ($redirect->isActive()) {
                    $redirectDialogTitle   = $redirect->getTitle();
                    $redirectDialogMessage = $redirect->getMessage();
                    $redirectDialogSite    = $redirect->getSite();
                    //$redirectWithDialog    = $redirect->isShowDialog();
                    break;
                }
            }
        }

        if (!$redirectDialogTitle) {
            $redirectDialogTitle = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_REDIRECT_TITLE'];
        }

        if ($redirectWithDialog) {

            return C4GBrickDialog::showC4GMessageDialog(
                $dialogParams->getId(),
                $redirectDialogTitle,
                $redirectDialogMessage,
                C4GBrickActionType::ACTION_REDIRECTDIALOGACTION,
                $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_REDIRECT_OK'],
                C4GBrickActionType::ACTION_CANCELMESSAGE,
                $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_REDIRECT_CANCEL'],
                $dlgValues);

        } else {
            if ( $redirectDialogSite && (($jumpTo = \PageModel::findByPk($redirectDialogSite)) !== null)) {
                $return['title'] = $redirectDialogTitle;
                $return['usermessage'] = $redirectDialogMessage;
                $return['jump_after_message'] = $jumpTo->getFrontendUrl();
            } else {
                $return['title'] = $redirectDialogTitle;
                $return['usermessage'] = $redirectDialogMessage;
            }

            return $return;
        }
    }

    public function isReadOnly()
    {
        return true;
    }
}
