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

use con4gis\CoreBundle\Classes\C4GVersionProvider;

class C4GExportDialogAction extends C4GBrickDialogAction
{
    public function run()
    {
        $dlgValues = $this->getPutVars();
        $dialogParams = $this->getDialogParams();
        $dialogId = $dialogParams->getId();
        $memberId = $dialogParams->getMemberId();
        $groupId = $dialogParams->getGroupId();
        $viewType = $dialogParams->getViewType();
        $fieldList = $this->getFieldList();
        $brickDatabase = $this->getBrickDatabase();
//        $dbValue = $model::findByPk($dialogId);

        //save data
        if (!C4GBrickDialog::checkMandatoryFields($fieldList, $dlgValues)) {
            return ['usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MANDATORY']];
        }

        $validate_result = C4GBrickDialog::validateFields($fieldList, $dlgValues);
        if ($validate_result) {
            return ['usermessage' => htmlspecialchars_decode($validate_result)];
        }

        $database = $brickDatabase->getParams()->getDatabase();
        $dbValues = $brickDatabase->findByPk($dialogId);
        $tableName = $brickDatabase->getParams()->getTableName();

        $changes = C4GBrickDialog::compareWithDB($this->makeRegularFieldList($fieldList), $dlgValues, $dbValues, $viewType, false);

        if (count($changes) > 0) {
            $validate_result = C4GBrickDialog::validateUnique($this->makeRegularFieldList($fieldList), $dlgValues, $brickDatabase, $dialogParams);
            if ($validate_result) {
                return ['usermessage' => $validate_result, 'title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['validate_title']];
            }

            $result = C4GBrickDialog::saveC4GDialog($dialogId, $tableName, $this->makeRegularFieldList($fieldList),
                $dlgValues, $brickDatabase, $dbValues, $viewType,
                $memberId);
            if ($result['insertId']) {
                //if a project was added we have to change the project booking count
                if ((empty($dbValues)) && ($this->projectKey != '') && (C4GVersionProvider::isInstalled('con4gis/booking'))) {
                    \con4gis\BookingBundle\Resources\contao\models\C4gBookingGroupsModel::checkProjectCount($this->group_id);
                }
                $dialogId = $result['insertId'];
                $dbValues = $brickDatabase->findByPk($dialogId);
            } elseif (($dialogId) && (C4GVersionProvider::isInstalled('con4gis/booking'))) {
                \con4gis\BookingBundle\Resources\contao\models\C4gBookingGroupsModel::log($dbValues);
            }
        }

//        $elementName = $this->captionField;
//        $this->afterSaveAction($changes);

        // generate export file
        $dbValues->refresh();

        $selection = \Contao\StringUtil::deserialize($dbValues->selection);
        $filename = C4GStreamerExport::exportBasedata($groupId, $selection, $dbValues, $memberId);
//        $dir = C4GBrickConst::PATH_GROUP_DATA."/".$this->group_id."/export/";
//        $dir2 = C4GBrickConst::PATH_GROUP_DATA."/".$this->group_id."/basedata/";

//        ============================================================
//         download export file

//        Controller::sendFileToBrowser($dir."test.pdf");
//        Controller::sendFileToBrowser($dir2."basedata.xml");

//        $tmpFile = new \File($dir2."basedata.xml");
//        $tmp1 = $tmpFile->mime;
//        $tmp2 = $tmpFile->basename;
//        $tmp3 = $tmpFile->filename;
//        $tmp4 = $tmpFile->filesize;
        ////        $tmp5 = $tmpFile->sendToBrowser();
//
//
//        // Open the "save as …" dialogue
//        header('Content-Type: ' . $tmpFile->mime);
//        header('Content-Transfer-Encoding: binary');
//        header('Content-Disposition: attachment; filename="' . ($filename ?: $tmpFile->basename) . '"');
//        header('Content-Length: ' . $tmpFile->filesize);
//        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
//        header('Pragma: public');
//        header('Expires: 0');
//        header('Connection: close');
//
//        // Output the file
//        $resFile = fopen(TL_ROOT . '/' . $tmpFile->strFile, 'rb');
//        fpassthru($resFile);
//        fclose($resFile);

//        $tmp->sendToBrowser();

//        header('Content-Type: ' . $this->mime);
//        header('Content-Transfer-Encoding: binary');
//        header('Content-Disposition: attachment; filename="' . ($filename ?: $this->basename) . '"');
//        header('Content-Length: ' . $this->filesize);
//        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
//        header('Pragma: public');
//        header('Expires: 0');
//        header('Connection: close');

//        $file = "datei/pfad/datei123.xml";
//        header("Content-Type: text/xml");
//        header("Content-Disposition: attachment; filename=datei.xml");
//        header("Content-Length: ". filesize($file));
//        readfile($file);

//        $result = C4GBrickDialog::showC4GMessageDialog(
//            $dialogId,
//            $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_ACTIVATION_DIALOG_QUESTION'],
//            $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_ACTIVATION_DIALOG_TEXT'],
//            C4GBrickActionType::ACTION_CONFIRMACTIVATION,
//            $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_ACTIVATION_DIALOG_YES'],
//            C4GBrickConst::ACTION_CANCELACTIVATION,
//            $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_ACTIVATION_DIALOG_NO'],
//            $dlgValues
//        );
//
//        return $result;

//        header("Content-Type: application/zip");
//        header("Content-Disposition: attachment; filename=\"$filename\"");
//        header("Content-Length: ". filesize($filename));
//        readfile($filename, $dir);
//        ============================================================

        // zurueck zur Uebersicht
        $action = new C4GShowListAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());

        return $action->run();
    }

    public function isReadOnly()
    {
        return true;
    }
}
