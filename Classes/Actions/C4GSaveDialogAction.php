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

use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialog;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Logs\C4GLogEntryType;
use con4gis\ProjectsBundle\Classes\Notifications\C4GBrickNotification;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickView;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;
use NotificationCenter\Model\Notification;

class C4GSaveDialogAction extends C4GBrickDialogAction
{
    private $withRedirect = false;
    private $andNew = false;
    private $setParentIdAfterSave = false;
    private $setSessionIdAfterInsert = "";

    public function run()
    {
        $dlgValues = $this->getPutVars();
        $fieldList = $this->getFieldList();
        $dialogParams = $this->getDialogParams();
        $dialogId = $dialogParams->getId();
        $memberId = $dialogParams->getMemberId();
        $groupId  = $dialogParams->getGroupId();
        $projectKey = $dialogParams->getProjectKey();
        $projectUuid = $dialogParams->getProjectUuid();
        $viewType = $dialogParams->getViewType();
        $brickDatabase = $this->getBrickDatabase();
        $withNotification = $dialogParams->isWithNotification();
        $notifyOnChanges = $dialogParams->isNotifyOnChanges();
        $notification_type = $dialogParams->getNotificationType();
        $brickKey = $dialogParams->getBrickKey();
        $brickCaption = $dialogParams->getBrickCaption();
        $captionField = $dialogParams->getCaptionField();
        $sendEMails = $dialogParams->getSendEMails();
        $withBackup = $dialogParams->isWithBackup();
        $isPopup = $dialogParams->isPopup();
        $isWithActivationInfo = $dialogParams->isWithActivationInfo();
        $module = $this->getModule();

        $mandatoryCheckResult = C4GBrickDialog::checkMandatoryFields($fieldList, $dlgValues);
        if ($mandatoryCheckResult !== true) {
            if ((!$dialogParams->isSaveOnMandatory() || ($dialogParams->isMandatoryCheckOnActivate() && ($dlgValues['published'] === 'true' || $dlgValues['published'] === true))) && !$dialogParams->isSaveWithoutMessages()) {
                if ($mandatoryCheckResult instanceof C4GBrickField) {
                    if ($mandatoryCheckResult->getSpecialMandatoryMessage() != '') {
                        return array('usermessage' => $mandatoryCheckResult->getSpecialMandatoryMessage(),
                            'title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MANDATORY_TITLE'],
                            'callback' => array('function' => 'focusOnElement', 'params' => 'c4g_'. $mandatoryCheckResult->getFieldName()));
                    } elseif ($mandatoryCheckResult->getTitle() != '') {
                        return array('usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MANDATORY_FIELD'].'"'. $mandatoryCheckResult->getTitle().'".',
                            'title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MANDATORY_TITLE'],
                            'callback' => array('function' => 'focusOnElement', 'params' => 'c4g_'. $mandatoryCheckResult->getFieldName()));
                    }
                }
                return array('usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MANDATORY'],
                    'title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MANDATORY_TITLE']);
            }
        }

        $validate_result = C4GBrickDialog::validateFields($this->makeRegularFieldList($fieldList), $dlgValues);
        if ($validate_result && !$dialogParams->isSaveWithoutMessages()) {
            return array('usermessage' => $validate_result, 'title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['INVALID_INPUT']);
        }

        $dbValues = null;
        $newId = false;

        if ($dialogId && ($dialogId != "") && ($dialogId != "-1")) {
            $dbValues = $brickDatabase->findByPk($dialogId);
        } else {
            $newId = true;
        }

        $changes = C4GBrickDialog::compareWithDB($this->makeRegularFieldList($fieldList), $dlgValues, $dbValues, $viewType, false);

        if ($newId || count($changes) > 0) {
            $validate_result = C4GBrickDialog::validateUnique($this->makeRegularFieldList($fieldList), $dlgValues, $brickDatabase, $dialogParams);
            $validate_title  = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['validate_title'];
            if ($validate_result && (!$dialogParams->isSaveWithoutMessages())) {
                if ($dialogParams->getUniqueMessage()) {
                    $validate_result = $dialogParams->getUniqueMessage();
                }
                if ($dialogParams->getUniqueTitle()) {
                    $validate_title = $dialogParams->getUniqueTitle();
                }
                return array('usermessage' => $validate_result, 'title' => $validate_title);
            }

            $result = C4GBrickDialog::saveC4GDialog($dialogId, $this->tableName, $this->makeRegularFieldList($fieldList),
                $dlgValues, $brickDatabase, $dbValues, $dialogParams, $memberId);

            if ($result['insertId']) {
                if ($this->setSessionIdAfterInsert) {
                    \Session::getInstance()->set($this->setSessionIdAfterInsert, $result['insertId']);
                }
                //if a project was added we have to change the project booking count
                if ((empty($dbValues)) && ($projectKey != '') && ($GLOBALS['con4gis']['booking']['installed'])) {
                    \con4gis\BookingBundle\Resources\contao\models\C4gBookingGroupsModel::checkProjectCount($groupId);
                }
                if ($this->setParentIdAfterSave) {
                    $dialogParams->setParentId($result['insertId']);
                    \Session::getInstance()->set("c4g_brick_parent_id", $result['insertId']);
                }
                $dialogId = $result['insertId'];
                $dbValues = $brickDatabase->findByPk($dialogId);
                \Session::getInstance()->set("c4g_brick_dialog_id", $dialogId);
            } else if (($dialogId) && ($GLOBALS['con4gis']['booking']['installed'])) {
                \con4gis\BookingBundle\Resources\contao\models\C4gBookingGroupsModel::log($dbValues);
            }
        }

        if ($withNotification && ($newId || $notifyOnChanges)) {
            $notification_array = unserialize($notification_type);
            if(sizeof($notification_array) == 1 ) {
                $objNotification = Notification::findByPk($notification_array);
                if ($objNotification !== null) {
                    $arrTokens = C4GBrickNotification::getArrayTokens($dlgValues,$fieldList);
                    $objNotification->send($arrTokens);
                }
            } else {
                foreach ($notification_array as $notification) {
                    $objNotification = Notification::findByPk($notification);
                    if ($objNotification !== null) {
                        $arrTokens = C4GBrickNotification::getArrayTokens($dlgValues, $fieldList);
                        $objNotification->send($arrTokens);
                    }
                }
            }
        }

        if ($this->module && $result) {
            $addition = $this->module->afterSaveAction($changes, $result['insertId']);
            if ($addition && $addition instanceof C4GBrickDialogParams) {
                $dialogParams = $addition;
            }
        }

        C4GBrickCommon::logEntry($dialogId,
            C4GLogEntryType::SAVE_DATASET,
            $dlgValues[$captionField].$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_SAVED'],
            $brickKey,
            $viewType,
            $groupId,
            $memberId);


        if ($sendEMails) {
            $recipient = $sendEMails->getRecipient();
            $senderName = C4GBrickCommon::getNameForMember($memberId);
            if (($viewType == C4GBrickViewType::MEMBERBOOKING) && ($GLOBALS['con4gis']['booking']['installed'])) {
                $senderName = C4GBrickCommon::getNameForMember($memberId).' ('.$dbValues->caption.')';
            }

            $text = '';

            foreach($changes as $change)
            {
                $field = $change->getField();
                $dbValue = $field->translateFieldValue($change->getDbValue());
                $dlgValue = $field->translateFieldValue($change->getDlgValue());

                if ($field) {
                    if ($newId) {
                        $text .= $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_CHANGES_FIELD'].'['.$field->getTitle().']'.$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_CHANGES_VALUE'].'<'.$dlgValue.'>. '."\r\n";
                    } else {
                        $text .= $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_CHANGES_FIELD'].'['.$field->getTitle().']'.$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_CHANGES_FROM'].'<'.$dbValue.'>'.$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_CHANGES_TO'].'<'.$dlgValue.'>'.$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_CHANGES_CHANGED']."\r\n";
                    }
                }
            }

            if ($newId || !empty($changes)) {
                $action = new C4GSendEmailAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());
                $action->setRecipient($recipient);
                $action->setSenderName($senderName);
                $action->setText($brickCaption.': '."\r\n".$text);
                $action->run();
            }
        }

        if ($withBackup) {
            //      begin C4GStreamerBackup
            if ($viewType == C4GBrickViewType::GROUPPROJECT) {
//                    $archiv = new \c4g\projects\C4GBrickBackup($dialogId, $dlgValues["c4g_project_uuid"], $dlgValues["c4g_group_id"], $this->brickDatabase, $this->brickKey);
                $archiv = new C4GStreamerBackup($dialogId, $dlgValues["c4g_project_uuid"], $dlgValues["c4g_group_id"], $brickDatabase, $brickKey, C4GBrickConst::PATH_GROUP_DATA);
                $archiv->projectsBackup();
            } elseif ($viewType == C4GBrickViewType::PROJECTBASED) { // ($this->project_uuid != null)
//                    $archiv = new \c4g\projects\C4GBrickBackup($dialogId, $this->project_uuid, $this->group_id, $this->brickDatabase, $this->brickKey);
                $archiv = new C4GStreamerBackup($dialogId, $projectUuid, $groupId, $brickDatabase, $brickKey, C4GBrickConst::PATH_GROUP_DATA);
                $archiv->projectsBackup();
            } else {
//                    $archiv = new \c4g\projects\C4GBrickBackup($dialogId, $this->project_uuid, $this->group_id, $this->brickDatabase, $this->brickKey, "basedata");
                $archiv = new C4GStreamerBackup($dialogId, $projectUuid, $groupId, $brickDatabase, $brickKey, C4GBrickConst::PATH_GROUP_DATA, "basedata");
                $archiv->projectsBackup();
            }
//        end C4GStreamerBackup
        }

        if ((C4GBrickView::isWithoutList($viewType))){
            if ($this->isWithRedirect()) {
                if ($dialogParams->getRedirectSite() && (($jumpTo = \PageModel::findByPk($dialogParams->getRedirectSite())) !== null)) {
                    $return['jump_to_url'] = $jumpTo->getFrontendUrl();
                    return $return;
                }
            }

            if (!$dialogParams->isSaveWithoutMessages() && !$dialogParams->isSaveWithoutSavingMessage()) {
                if ($isPopup) {
                    //return $this->performAction(C4GBrickActionType::ACTION_CLOSEPOPUPDIALOG);
                    return array('usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_SAVED']);
                } else {
                    return array('usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_SAVED']);
                }
            }
        } else {
            if ($this->isAndNew()) {
                $this->getDialogParams()->setId(-1);
                $action = new C4GShowDialogAction($dialogParams, $this->getListParams(), $fieldList, $dlgValues, $brickDatabase);
                $return = $action->run();
            } else {
                if (($dialogParams->isSaveWithoutClose())) {
                    $action = new C4GShowDialogAction($dialogParams, $this->getListParams(), $fieldList, $dlgValues, $brickDatabase);
                    $return = $action->run();
                } else if (C4GBrickView::isWithoutList($viewType)) {
                    $action = new C4GShowDialogAction($dialogParams, $this->getListParams(), $fieldList, $dlgValues, $brickDatabase);
                    $return = $action->run();
                } else {
                    $action = new C4GShowListAction($dialogParams, $this->getListParams(), $fieldList, $dlgValues, $brickDatabase);
                    $return = $action->run();
                }
            }

            $activation_button = $dialogParams->getButton(C4GBrickConst::BUTTON_ACTIVATION);
            $archive_button = $dialogParams->getButton(C4GBrickConst::BUTTON_ARCHIVE);
            if($activation_button && $archive_button && $activation_button->isEnabled() && $archive_button->isEnabled()){
                if(($dlgValues['published'] == false || $dlgValues['published'] == 'false') && $isWithActivationInfo && (!$dialogParams->isSaveWithoutMessages())){
                    $return['usermessage'] =  $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_DATA_NOT_ACTIVATED'];
                    $return['title'] = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_TITLE_DATA_NOT_ACTIVATED'];
                }
            }
            if (!$dialogParams->isSaveWithoutClose() && $module->getDialogChangeHandler()) {
                $module->getDialogChangeHandler()->clearSession($module->getBrickKey());
            }
            return $return;
        }

    }

    /**
     * @return boolean
     */
    public function isWithRedirect()
    {
        return $this->withRedirect;
    }

    /**
     * @param boolean $withRedirect
     */
    public function setWithRedirect($withRedirect)
    {
        $this->withRedirect = $withRedirect;
    }

    /**
     * @return boolean
     */
    public function isAndNew()
    {
        return $this->andNew;
    }

    /**
     * @param boolean $andNew
     */
    public function setAndNew($andNew)
    {
        $this->andNew = $andNew;
    }

    /**
     * @return bool
     */
    public function isSetParentIdAfterSave()
    {
        return $this->setParentIdAfterSave;
    }

    /**
     * @param bool $setParentIdAfterSave
     */
    public function setSetParentIdAfterSave($setParentIdAfterSave)
    {
        $this->setParentIdAfterSave = $setParentIdAfterSave;
    }

    /**
     * @return string
     */
    public function getSetSessionIdAfterInsert()
    {
        return $this->setSessionIdAfterInsert;
    }

    /**
     * @param string $setSessionIdAfterInsert
     */
    public function setSetSessionIdAfterInsert($setSessionIdAfterInsert)
    {
        $this->setSessionIdAfterInsert = $setSessionIdAfterInsert;
    }

}
