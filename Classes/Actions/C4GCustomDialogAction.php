<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    6
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Actions;

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialog;

class C4GCustomDialogAction extends C4GBrickDialogAction
{
    protected $messageTitle = '';
    protected $messageText = '';
    protected $confirmButtonText = '';
    protected $cancelButtonText = '';

    public function run()
    {
        $dlgValues = $this->getPutVars();
        $dialogParams = $this->getDialogParams();
        $dialogId  = $dialogParams->getId();

        if ($dialogId == '') {
            $dialogParams->setId(-1);
            $action = new C4GCloseDialogAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());
            return $action->run();
        }

        $result = C4GBrickDialog::showC4GMessageDialog(
            $dialogId,
            $this->messageTitle,
            $this->messageText,
            C4GBrickActionType::ACTION_CONFIRM_CUSTOM_DIALOG,
            $this->confirmButtonText,
            C4GBrickActionType::ACTION_CANCELMESSAGE,
            $this->cancelButtonText,
            $dlgValues
        );

        return $result;
    }

    /**
     * @return string
     */
    public function getMessageTitle(): string
    {
        return $this->messageTitle;
    }

    /**
     * @param string $messageTitle
     * @return C4GCustomDialogAction
     */
    public function setMessageTitle(string $messageTitle): C4GCustomDialogAction
    {
        $this->messageTitle = $messageTitle;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessageText(): string
    {
        return $this->messageText;
    }

    /**
     * @param string $messageText
     * @return C4GCustomDialogAction
     */
    public function setMessageText(string $messageText): C4GCustomDialogAction
    {
        $this->messageText = $messageText;
        return $this;
    }

    /**
     * @return string
     */
    public function getConfirmButtonText(): string
    {
        return $this->confirmButtonText;
    }

    /**
     * @param string $confirmButtonText
     * @return C4GCustomDialogAction
     */
    public function setConfirmButtonText(string $confirmButtonText): C4GCustomDialogAction
    {
        $this->confirmButtonText = $confirmButtonText;
        return $this;
    }

    /**
     * @return string
     */
    public function getCancelButtonText(): string
    {
        return $this->cancelButtonText;
    }

    /**
     * @param string $cancelButtonText
     * @return C4GCustomDialogAction
     */
    public function setCancelButtonText(string $cancelButtonText): C4GCustomDialogAction
    {
        $this->cancelButtonText = $cancelButtonText;
        return $this;
    }

    public function isReadOnly()
    {
        return false;
    }
}
