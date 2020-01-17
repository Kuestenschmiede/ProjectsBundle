<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    7
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Actions;

use con4gis\CoreBundle\Classes\C4GVersionProvider;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialog;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Logs\C4GLogEntryType;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickView;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;

class C4GSaveDialogAction extends C4GBrickDialogAction
{
    use C4GTraitCheckMandatoryFields;
    private $withRedirect = false;
    private $andNew = false;
    private $setParentIdAfterSave = false;
    private $setSessionIdAfterInsert = '';

    public function run()
    {
        $dlgValues = $this->getPutVars();
        $fieldList = $this->getFieldList();
        $dialogParams = $this->getDialogParams();
        $dialogId = $dialogParams->getId();
        $memberId = $dialogParams->getMemberId();
        $groupId = $dialogParams->getGroupId();
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

        if ((!$dialogParams->isSaveOnMandatory() || ($dialogParams->isMandatoryCheckOnActivate() && ($dlgValues['published'] === 'true' || $dlgValues['published'] === true))) && !$dialogParams->isSaveWithoutMessages()) {
            $check = $this->checkMandatoryFields($fieldList, $dlgValues);
            if (!empty($check)) {
                return $check;
            }
        }

        if ((!$dialogParams->isSaveOnMandatory() || ($dialogParams->isMandatoryCheckOnActivate() && ($dlgValues['published'] === 'true' || $dlgValues['published'] === true))) && !$dialogParams->isSaveWithoutMessages()) {
            $validate_result = C4GBrickDialog::validateFields($this->makeRegularFieldList($fieldList), $dlgValues);
            if ($validate_result && !$dialogParams->isSaveWithoutMessages()) {
                return ['usermessage' => $validate_result, 'title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['INVALID_INPUT']];
            }
        }

        $dbValues = null;
        $newId = false;

        if ($dialogId && ($dialogId != '') && ($dialogId != '-1')) {
            $dbValues = $brickDatabase->findByPk($dialogId);
        } else {
            $newId = true;
        }

        $changes = C4GBrickDialog::compareWithDB($this->makeRegularFieldList($fieldList), $dlgValues, $dbValues, $viewType, false);

        if ($newId || count($changes) > 0) {
            $validate_result = C4GBrickDialog::validateUnique($this->makeRegularFieldList($fieldList), $dlgValues, $brickDatabase, $dialogParams);
            $validate_title = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['validate_title'];
            if ($validate_result && (!$dialogParams->isSaveWithoutMessages())) {
                if ($dialogParams->getUniqueMessage()) {
                    $validate_result = $dialogParams->getUniqueMessage();
                }
                if ($dialogParams->getUniqueTitle()) {
                    $validate_title = $dialogParams->getUniqueTitle();
                }

                return ['usermessage' => $validate_result, 'title' => $validate_title];
            }

            $result = C4GBrickDialog::saveC4GDialog($dialogId, '', $this->makeRegularFieldList($fieldList),
                $dlgValues, $brickDatabase, $dbValues, $dialogParams, $memberId);

            if ($result['insertId']) {
                if ($this->setSessionIdAfterInsert) {
                    \Session::getInstance()->set($this->setSessionIdAfterInsert, $result['insertId']);
                }
                //if a project was added we have to change the project booking count
                if ((empty($dbValues)) && ($projectKey != '') && (C4GVersionProvider::isInstalled('con4gis/booking'))) {
                    \con4gis\BookingBundle\Resources\contao\models\C4gBookingGroupsModel::checkProjectCount($groupId);
                }
                if ($this->setParentIdAfterSave) {
                    $dialogParams->setParentId($result['insertId']);
                    \Session::getInstance()->set('c4g_brick_parent_id', $result['insertId']);
                }
                $dialogId = $result['insertId'];
                $dbValues = $brickDatabase->findByPk($dialogId);
                \Session::getInstance()->set('c4g_brick_dialog_id', $dialogId);
            } elseif (($dialogId) && (C4GVersionProvider::isInstalled('con4gis/booking'))) {
                \con4gis\BookingBundle\Resources\contao\models\C4gBookingGroupsModel::log($dbValues);
            }
        }
        if ($withNotification) {
            $this->module->sendNotifications($newId, $notifyOnChanges, $notification_type, $dlgValues, $fieldList, $changes);
        }

        if ($this->module && $result) {
            $addition = $this->module->afterSaveAction($changes, $result['insertId']);
            if ($addition && $addition instanceof C4GBrickDialogParams) {
                $dialogParams = $addition;
            }
        }

        C4GBrickCommon::logEntry($dialogId,
            C4GLogEntryType::SAVE_DATASET,
            $dlgValues[$captionField] . $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_SAVED'],
            $brickKey,
            $viewType,
            $groupId,
            $memberId);

        if ($sendEMails) {
            $recipient = $sendEMails->getRecipient();
            $senderName = C4GBrickCommon::getNameForMember($memberId);
            if (($viewType == C4GBrickViewType::MEMBERBOOKING) && (C4GVersionProvider::isInstalled('con4gis/booking'))) {
                $senderName = C4GBrickCommon::getNameForMember($memberId) . ' (' . $dbValues->caption . ')';
            }

            $text = '';

            foreach ($changes as $change) {
                $field = $change->getField();
                $dbValue = $field->translateFieldValue($change->getDbValue());
                $dlgValue = $field->translateFieldValue($change->getDlgValue());

                if ($field) {
                    if ($newId) {
                        $text .= $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_CHANGES_FIELD'] . '[' . $field->getTitle() . ']' . $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_CHANGES_VALUE'] . '<' . $dlgValue . '>. ' . "\r\n";
                    } else {
                        $text .= $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_CHANGES_FIELD'] . '[' . $field->getTitle() . ']' . $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_CHANGES_FROM'] . '<' . $dbValue . '>' . $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_CHANGES_TO'] . '<' . $dlgValue . '>' . $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_CHANGES_CHANGED'] . "\r\n";
                    }
                }
            }

            if ($newId || !empty($changes)) {
                $action = new C4GSendEmailAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());
                $action->setRecipient($recipient);
                $action->setSenderName($senderName);
                $action->setText($brickCaption . ': ' . "\r\n" . $text);
                $action->run();
            }
        }

        if ($withBackup) {
            //      begin C4GStreamerBackup
            if ($viewType == C4GBrickViewType::GROUPPROJECT) {
//                    $archiv = new \c4g\projects\C4GBrickBackup($dialogId, $dlgValues["c4g_project_uuid"], $dlgValues["c4g_group_id"], $this->brickDatabase, $this->brickKey);
                $archiv = new C4GStreamerBackup($dialogId, $dlgValues['c4g_project_uuid'], $dlgValues['c4g_group_id'], $brickDatabase, $brickKey, C4GBrickConst::PATH_GROUP_DATA);
                $archiv->projectsBackup();
            } elseif ($viewType == C4GBrickViewType::PROJECTBASED) { // ($this->project_uuid != null)
//                    $archiv = new \c4g\projects\C4GBrickBackup($dialogId, $this->project_uuid, $this->group_id, $this->brickDatabase, $this->brickKey);
                $archiv = new C4GStreamerBackup($dialogId, $projectUuid, $groupId, $brickDatabase, $brickKey, C4GBrickConst::PATH_GROUP_DATA);
                $archiv->projectsBackup();
            } else {
//                    $archiv = new \c4g\projects\C4GBrickBackup($dialogId, $this->project_uuid, $this->group_id, $this->brickDatabase, $this->brickKey, "basedata");
                $archiv = new C4GStreamerBackup($dialogId, $projectUuid, $groupId, $brickDatabase, $brickKey, C4GBrickConst::PATH_GROUP_DATA, 'basedata');
                $archiv->projectsBackup();
            }
//        end C4GStreamerBackup
        }

        if ((C4GBrickView::isWithoutList($viewType))) {
            if ($this->isWithRedirect()) {
                if ($dialogParams->getRedirectSite() && (($jumpTo = \PageModel::findByPk($dialogParams->getRedirectSite())) !== null)) {
                    $return['jump_to_url'] = $jumpTo->getFrontendUrl();

                    return $return;
                }
            }

            if (!$dialogParams->isSaveWithoutMessages() && !$dialogParams->isSaveWithoutSavingMessage()) {
                if ($isPopup) {
                    //return $this->performAction(C4GBrickActionType::ACTION_CLOSEPOPUPDIALOG);
                    return ['usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_SAVED']];
                }

                return ['usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_SAVED']];
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
                } elseif (C4GBrickView::isWithoutList($viewType)) {
                    $action = new C4GShowDialogAction($dialogParams, $this->getListParams(), $fieldList, $dlgValues, $brickDatabase);
                    $return = $action->run();
                } else {
                    $action = new C4GShowListAction($dialogParams, $this->getListParams(), $fieldList, $dlgValues, $brickDatabase);
                    $return = $action->run();
                }
            }

            $activation_button = $dialogParams->getButton(C4GBrickConst::BUTTON_ACTIVATION);
            $archive_button = $dialogParams->getButton(C4GBrickConst::BUTTON_ARCHIVE);
            if ($activation_button && $archive_button && $activation_button->isEnabled() && $archive_button->isEnabled()) {
                if (($dlgValues['published'] == false || $dlgValues['published'] == 'false') && $isWithActivationInfo && (!$dialogParams->isSaveWithoutMessages())) {
                    $return['usermessage'] = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_DATA_NOT_ACTIVATED'];
                    $return['title'] = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_TITLE_DATA_NOT_ACTIVATED'];
                }
            }
            if (!$dialogParams->isSaveWithoutClose() && $module && $module->getDialogChangeHandler()) {
                $module->getDialogChangeHandler()->clearSession($module->getBrickKey());
            }

            if ($dialogParams->isShowSuccessfullySavedMessage() && $changes) {
                if (!$return['usermessage'] && !$return['title']) {
                    $return['usermessage'] = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_SUCCESSFULLY_SAVED'];
                    $return['title'] = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_SUCCESSFULLY_SAVED_TITLE'];
                }
            } elseif ($dialogParams->isShowSuccessfullySavedMessage()) {
                if (!$return['usermessage'] && !$return['title']) {
                    $return['usermessage'] = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_SUCCESSFULLY_SAVED_NO_NEW_DATA'];
                    $return['title'] = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_SUCCESSFULLY_SAVED_NO_NEW_DATA_TITLE'];
                }
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
     * @param bool $withRedirect
     * @return $this
     */
    public function setWithRedirect($withRedirect = true)
    {
        $this->withRedirect = $withRedirect;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isAndNew()
    {
        return $this->andNew;
    }

    /**
     * @param bool $andNew
     * @return $this
     */
    public function setAndNew($andNew = true)
    {
        $this->andNew = $andNew;

        return $this;
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
     * @return $this
     */
    public function setSetParentIdAfterSave($setParentIdAfterSave = true)
    {
        $this->setParentIdAfterSave = $setParentIdAfterSave;

        return $this;
    }

    /**
     * @return string
     */
    public function getSetSessionIdAfterInsert()
    {
        return $this->setSessionIdAfterInsert;
    }

    /**
     * @param bool $setSessionIdAfterInsert
     * @return $this
     */
    public function setSetSessionIdAfterInsert($setSessionIdAfterInsert = true)
    {
        $this->setSessionIdAfterInsert = $setSessionIdAfterInsert;

        return $this;
    }

    public function isReadOnly()
    {
        return true;
    }
}
