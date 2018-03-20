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
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use Doctrine\ORM\Mapping\Id;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;

class C4GActivationDialogAction extends C4GBrickDialogAction
{
    public function run()
    {
        $dlgValues = $this->getPutVars();
        $dialogParams = $this->getDialogParams();
        $viewType     = $dialogParams->getViewType();
        $fieldList    = $this->getFieldList();
        $dialogId = $dialogParams->getId();

        if ($this->dialogParams->isMandatoryCheckOnActivate()) {
            $mandatoryCheckResult = C4GBrickDialog::checkMandatoryFields($fieldList, $dlgValues);
            if ($mandatoryCheckResult !== true) {
               if ($mandatoryCheckResult instanceof C4GBrickField) {
                   if ($mandatoryCheckResult->getSpecialMandatoryMessage() != '') {
                       return array('usermessage' => $mandatoryCheckResult->getSpecialMandatoryMessage(), 'title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MANDATORY_TITLE'],
                            'callback' => array('function' => 'focusOnElement', 'params' => 'c4g_' . $mandatoryCheckResult->getFieldName()));
                   } elseif ($mandatoryCheckResult->getTitle() != '') {
                       return array('usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MANDATORY_FIELD'].'"'. $mandatoryCheckResult->getTitle().'".',
                           'title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MANDATORY_TITLE'],
                           'callback' => array('function' => 'focusOnElement', 'params' => 'c4g_'. $mandatoryCheckResult->getFieldName()));
                   }
                }
                return array('usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MANDATORY'], 'title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MANDATORY_TITLE']);
            }

            $validate_result = C4GBrickDialog::validateFields($this->makeRegularFieldList($fieldList), $dlgValues);
            if ($validate_result) {
                return array('usermessage' => $validate_result, 'title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['INVALID_INPUT']);
            }
        }

        if ($dialogParams->isRedirectWithSaving() && $dialogParams->isRedirectWithActivation()){
//            $action = new C4GSaveDialogAction($this->getDialogParams(), $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());
//            $action->setModule($this->module);
//            $action->run();
            $dbValues = null;
            if ($dialogId && ($dialogId != "") && ($dialogId != "-1")) {
                $dbValues = $this->getBrickDatabase()->findByPk($dialogId);
            } else {
                $result = C4GBrickDialog::saveC4GDialog($dialogId, $this->tableName, $this->makeRegularFieldList($fieldList),
                    $dlgValues, $this->getBrickDatabase(), $dbValues, $dialogParams, $dialogParams->getMemberId());
                if ($result) {
                    $dialogId = $result['insertId'];
                    $dialogParams->setId($dialogId);
                }
            }
        }

        if ($dialogId == '') {
            $dialogParams->setId(-1);
            $action = new C4GCloseDialogAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());
            return $action->run();
        }

        if(($viewType == C4GBrickViewType::PUBLICUUIDBASED) && !($this->dialogParams->getUuid())) {
            return array('usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MISSING_UUID'], 'title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MISSING_UUID_TITLE']);
            }

        if($viewType == C4GBrickViewType::MEMBERBOOKING) {
            $result = C4GBrickDialog::showC4GMessageDialog(
                $dialogId,
                $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_BOOKING_ACTIVATION_DIALOG_QUESTION'],
                $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_BOOKING_ACTIVATION_DIALOG_TEXT'],
                C4GBrickActionType::ACTION_CONFIRMACTIVATION,
                $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_BOOKING_ACTIVATION_DIALOG_YES'],
                C4GBrickActionType::ACTION_CANCELACTIVATION,
                $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_BOOKING_ACTIVATION_DIALOG_NO'],
                $dlgValues
            );
        } else if ($dialogParams->isConfirmActivation()) {
            $action = new C4GConfirmActivationAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());
            return $action->run();
        } else {
            $messageText = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_ACTIVATION_DIALOG_TEXT'];
            if ($dialogParams->isRedirectWithSaving() && $dialogParams->isRedirectWithActivation()){
                $messageText = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_ACTIVATION_DIALOG_SAVED_TEXT'];
            }

            $result = C4GBrickDialog::showC4GMessageDialog(
                $dialogId,
                $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_ACTIVATION_DIALOG_QUESTION'],
                $messageText,
                C4GBrickActionType::ACTION_CONFIRMACTIVATION,
                $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_ACTIVATION_DIALOG_YES'],
                C4GBrickActionType::ACTION_CANCELACTIVATION,
                $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_ACTIVATION_DIALOG_NO'],
                $dlgValues
            );
        }

        return $result;
    }
}
