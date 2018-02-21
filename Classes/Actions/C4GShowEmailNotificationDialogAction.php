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

use con4gis\CoreBundle\Resources\contao\classes\C4GHTMLFactory;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialog;

class C4GShowEmailNotificationDialogAction extends C4GBrickDialogAction
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

        $additional_email_text = new C4GTextareaField();
        $additional_email_text->setFieldName('email_text');
        $additional_email_text->setTitle($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['additional_email_text']);
        $additional_email_text->setDescription($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['desc_additional_email_text']);
        $additional_email_text->setTableColumn(false);
        $additional_email_text->setEditable(true);
        $additional_email_text->setIgnoreViewType(true);

        $result = C4GBrickDialog::showC4GMessageDialog(
            $dialogId,
            $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_EMAIL_NOTIFICATION_DIALOG_QUESTION'],
            $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_EMAIL_NOTIFICATION_DIALOG_TEXT'],
            C4GBrickActionType::ACTION_CONFIRMEMAILNOTIFICATION,
            $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_EMAIL_NOTIFICATION_DIALOG_YES'],
            C4GBrickActionType::ACTION_CANCELEMAILNOTIFICATION,
            $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_EMAIL_NOTIFICATION_DIALOG_NO'],
            $dlgValues,
            C4GHTMLFactory::lineBreak().$additional_email_text->getC4GDialogField(array($additional_email_text), null, $dialogParams)
        );
        return $result;

    }
}
