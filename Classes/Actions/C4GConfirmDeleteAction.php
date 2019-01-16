<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2019
 * @link      https://www.kuestenschmiede.de
 */
namespace con4gis\ProjectsBundle\Classes\Actions;

use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialog;
use con4gis\ProjectsBundle\Classes\Logs\C4GLogEntryType;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;

class C4GConfirmDeleteAction extends C4GBrickDialogAction
{
    public function run()
    {
        $dlgValues = $this->getPutVars();
        $dialogParams = $this->getDialogParams();
        $dialogId = $dialogParams->getId();
        $memberId = $dialogParams->getMemberId();
        $groupId  = $dialogParams->getGroupId();
        $projectId = $dialogParams->getProjectId();
        $viewType = $dialogParams->getViewType();
        $brickKey = $dialogParams->getBrickKey();
        $projectKey = $dialogParams->getProjectKey();
        $captionField = $dialogParams->getCaptionField();
        $sendEmails = $dialogParams->getSendEmails();
        $fieldList = $this->getFieldList();
        $brickDatabase = $this->getBrickDatabase();

        $database  = $brickDatabase->getParams()->getDatabase();
        $tableName = $brickDatabase->getParams()->getTableName();

        $dbValues   = null;
        if ($dialogId != "") {
            $dbValues  = $brickDatabase->findByPk($dialogId);
        }

        C4GBrickDialog::deleteC4GTableDataById($dialogId, $tableName, $database, $fieldList, $dbValues, $dlgValues, $memberId);

        if ($viewType == C4GBrickViewType::PROJECTBASED) {
            if ($projectId == $dialogId) {
                \Session::getInstance()->set("c4g_brick_project_id", '');
                \Session::getInstance()->set("c4g_brick_project_uuid", '');
            }
        }

        $elementName = $captionField;
        C4GBrickCommon::logEntry(
            $dialogId,
            C4GLogEntryType::DELETE_DATASET,
            $dlgValues[$elementName].$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_DELETED'],
            $brickKey,
            $viewType,
            $groupId,
            $memberId);

        if ($sendEmails) {
            $recipient = $sendEmails->getRecipient();
            $senderName = C4GBrickCommon::getNameForMember($memberId);
            if (($viewType == C4GBrickViewType::MEMBERBOOKING) && ($GLOBALS['con4gis']['booking']['installed'])) {
                $senderName = C4GBrickCommon::getNameForMember($memberId).' ('.$dbValues->caption.')';
            }

            $fields = $this->sendEMails->getFields();

            $text = '';

            foreach($fields as $field)
            {
                $text .= ' '.$dlgValues[$field].' ';
            }

            $action = new C4GSendEmailAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());
            $action->setRecipient($recipient);
            $action->setSenderName($senderName);
            $action->setText($this->brickCaption.': '.$text.$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_DELETED']);
            return $action->run();
        }

        $notifications = C4GBrickDialog::getButtonNotifications(
            C4GBrickActionType::ACTION_DELETEDIALOG,
            $dialogParams, $dbValues);
        if ($dialogParams->isWithNotification()) {
            $this->sendNotifications($notifications, $dlgValues, $fieldList, $memberId, $dbValues);
        }

        //if a project was deleted we have to change the project booking count
        if (($projectKey != '') && ($GLOBALS['con4gis']['booking']['installed'])) {
            \con4gis\BookingBundle\Resources\contao\models\C4gBookingGroupsModel::checkProjectCount($groupId);
        }

        $action = new C4GShowListAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());
        $return = $action->run();

        $url = $dlgValues['c4g_uploadURL'];
        if ($url) {
            C4GBrickCommon::deleteFile($url);
        }

        return $return;

    }

    public function isReadOnly()
    {
        return false;
    }
}
