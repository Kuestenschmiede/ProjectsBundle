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

namespace con4gis\ProjectsBundle\Classes\Actions;

use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GDateTimeLocationField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GGeopickerField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GGridField;
use con4gis\ProjectsBundle\Classes\Models\C4gProjectsModel;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickView;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialog;
use con4gis\ProjectsBundle\Classes\Actions\C4GBrickActionType;
use con4gis\GroupsBundle\Resources\contao\models\MemberGroupModel;
use con4gis\GroupsBundle\Resources\contao\models\MemberModel;
use con4gis\DocumentsBundle\Classes\Pdf\PdfDocument\PdfManager;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickSelectProjectDialog;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickFilterDialog;

abstract class C4GBrickAction
{
    protected $dialogParams;
    protected $listParams;
    protected $fieldList;
    protected $putVars;
    protected $brickDatabase;

    /**
     * C4GBrickAction constructor.
     */
    public function __construct($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase)
    {
        $this->dialogParams  = $dialogParams;
        $this->listParams    = $listParams;
        $this->fieldList     = $fieldList;
        $this->putVars       = $putVars;
        $this->brickDatabase = $brickDatabase;
    }


    public abstract function run();


    /**
     * @return mixed
     */
    public function getDialogParams()
    {
        return $this->dialogParams;
    }

    /**
     * @param mixed $dialogParams
     */
    public function setDialogParams($dialogParams)
    {
        $this->dialogParams = $dialogParams;
    }

    /**
     * @return mixed
     */
    public function getFieldList()
    {
        return $this->fieldList;
    }

    /**
     * @param mixed $fieldList
     */
    public function setFieldList($fieldList)
    {
        $this->fieldList = $fieldList;
    }

    /**
     * @return mixed
     */
    public function getPutVars()
    {
        return $this->putVars;
    }

    /**
     * @param mixed $putVars
     */
    public function setPutVars($putVars)
    {
        $this->putVars = $putVars;
    }

    /**
     * @return mixed
     */
    public function getBrickDatabase()
    {
        return $this->brickDatabase;
    }

    /**
     * @param mixed $brickDatabase
     */
    public function setBrickDatabase($brickDatabase)
    {
        $this->brickDatabase = $brickDatabase;
    }

    /**
     * @return mixed
     */
    public function getListParams()
    {
        return $this->listParams;
    }

    /**
     * @param mixed $listParams
     */
    public function setListParams($listParams)
    {
        $this->listParams = $listParams;
    }



    /**
     * execute functions for "actionstrings"
     * @param string $action
     */
    public static function performAction(&$action, &$module)
    {
        $dialogParams  = $module->getDialogParams();
        $brickDatabase = $module->getBrickDatabase();
        $listParams    = $module->getListParams();
        $putVars       = $module->getPutVars();
        $fieldList     = $module->getFieldList();
        $viewParams    = $dialogParams->getViewParams();

        $values = explode(':', $action, 5);
        $brickAction = $values[0];
        $id = $values[1];

        if ($id == null) {
            $id = -1;
        }

        $dialogParams->setId($id);

        if ($brickAction != C4GBrickActionType::ACTION_BUTTONCLICK) {

            if ($values[2]) {
                $groupId = $values[2];
            } else {
                $groupId = \Session::getInstance()->get("c4g_brick_group_id");
            }

            if ($groupId && MemberGroupModel::isMemberOfGroup($groupId, $dialogParams->getMemberId())) {
                if (MemberModel::hasRightInGroup($dialogParams->getMemberId(), $groupId, $dialogParams->getBrickKey())) {
                    $dialogParams->setGroupId($groupId);
                    \Session::getInstance()->set("c4g_brick_group_id", $groupId);
                }
            }

            if ($values[3]) {
                $project_id = $values[3];
            } else {
                $project_id = \Session::getInstance()->get("c4g_brick_project_id");
            }

            if (C4gProjectsModel::checkProjectId($project_id, $dialogParams->getProjectKey())) {
                $dialogParams->setProjectId($project_id);
                \Session::getInstance()->set("c4g_brick_project_id", $project_id);
                $dialogParams->setProjectUuid(\Session::getInstance()->get("c4g_brick_project_uuid"));
            }

            if ($values[4]) {
                $parent_id = $values[4];
            } else {
                $parent_id = \Session::getInstance()->get("c4g_brick_parent_id");
            }

            $dialogParams->setParentId($parent_id);
        }

        $viewType  = $dialogParams->getViewType();
        if ( ($fieldList == null) && ($values[0] == C4GBrickActionType::IDENTIFIER_LIST)) {
            $fieldList = array();

            if (!C4GBrickView::isPublicBased($viewType) && ($viewType != C4GBrickViewType::MEMBERBOOKING))
            {
                //ToDo ab hier aufräumen und C4GBrickView nutzen.
                //Gruppe setzen / prüfen
                if ( ($viewType == C4GBrickViewType::GROUPFORM) ||
                    ($viewType == C4GBrickViewType::GROUPFORMCOPY) ||
                    ($viewType == C4GBrickViewType::GROUPVIEW)) {
                    $element = $brickDatabase->findByPk($id);

                    $groupKeyField = $viewParams->getGroupKeyField();

                    if (($element) && ($element->$groupKeyField)) {
                        if (static::checkGroupId($element->$groupKeyField, $dialogParams->getMemberId(), $dialogParams->getBrickKey())) {
                            $dialogParams->setGroupId($element->$groupKeyField);
                            \Session::getInstance()->set("c4g_brick_group_id", $dialogParams->getGroupId());
                        }
                    }
                } else {
                    if (($dialogParams->getGroupId() == null) || ($dialogParams->getGroupId() == -1)) {
                        $onlyGroup_id = static::getOnlyOneGroupId($dialogParams->getMemberId(), $dialogParams->getBrickKey());
                        if ($onlyGroup_id == -1) {
                            $action = new C4GSelectGroupDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                            return $action->run();
                        } else {
                            $dialogParams->setGroupId($onlyGroup_id);
                            \Session::getInstance()->set("c4g_brick_group_id", $dialogParams->getGroupId());
                        }
                    }
                }

                if (($viewType == C4GBrickViewType::PROJECTBASED) ||
                    ($viewType == C4GBrickViewType::PROJECTFORM)) {
                    $project_id = static::checkProjectId($dialogParams->getProjectId(), $dialogParams->getGroupId());

                    if (($project_id == null) || ($project_id == -1)) {
                        $action = new C4GSelectProjectDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                        return $action->run();
                    }
                }

                if (($viewType == C4GBrickViewType::PROJECTPARENTBASED) ||
                    ($viewType == C4GBrickViewType::GROUPPARENTVIEW) ||
                    //($viewType == C4GBrickViewType::PROJECTPARENTVIEW) ||
                    ($viewType == C4GBrickViewType::PROJECTPARENTFORM)) {

                    if (!($viewType == C4GBrickViewType::GROUPPARENTVIEW)) {
                        $project_id = static::checkProjectId($dialogParams->getProjectId(),$dialogParams->getGroupId());

                        if (($project_id == null) || ($project_id == -1)) {
                            $action = new C4GSelectProjectDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                            return $action->run();
                        }
                    }

                    $parent_id = $dialogParams->getParentId();

                    if ( $listParams->checkButtonVisibility(C4GBrickConst::BUTTON_PARENT) &&
                        (($parent_id == null) || ($parent_id == -1))) {
                        $action = new C4GSelectParentDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                        return $action->run();
                    }
                }

                if (($viewType == C4GBrickViewType::PROJECTPARENTFORMCOPY) ||
                    ($viewType == C4GBrickViewType::PROJECTPARENTVIEW)) {
                    static::setIdValuesByParent($id, $dialogParams);
                }

            }
        }

        if ($viewType == C4GBrickViewType::MEMBERBOOKING) {
            $element = $brickDatabase->findByPk($id);

            $groupKeyField = $viewParams->getGroupKeyField();

            if (($element) && ($element->$groupKeyField)) {
                $dialogParams->setGroupId($element->$groupKeyField);
                \Session::getInstance()->set("c4g_brick_group_id", $element->$groupKeyField);
            } else {
                $dialogParams->setGroupId(null);
                \Session::getInstance()->set("c4g_brick_group_id", null);
            }
        }


        switch ($values[0]) {
            case C4GBrickActionType::IDENTIFIER_DIALOG:
            case C4GBrickActionType::ACTION_CLICK:
            case C4GBrickActionType::ACTION_SHOWDIALOG:
                $action = new C4GShowDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
            case C4GBrickActionType::IDENTIFIER_MESSAGE:
            case C4GBrickActionType::ACTION_SHOWMESSAGEDIALOG:
                $action = new C4GShowMessageDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
            case C4GBrickActionType::IDENTIFIER_PARENT:
                $dialogParams->setId(-1);
                $dialogParams->setParentId($id);
                $action = new C4GShowDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
            case C4GBrickActionType::ACTION_DELETEDIALOG:
                $action = new C4GDeleteDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
            case C4GBrickActionType::ACTION_ARCHIVEDIALOG:
                $action = new C4GArchiveDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
            case C4GBrickActionType::ACTION_ACTIVATIONDIALOG:
                $action = new C4GActivationDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
            case C4GBrickActionType::ACTION_SEND_NOTIFICATION:
                $action = new C4GSendNotificationAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
            case C4GBrickActionType::ACTION_EMAILNOTIFICATIONDIALOG:
                try {
                    $modelClass = $brickDatabase->getParams()->getModelClass();
                    $dialogParams->setId($modelClass::getId($putVars, $brickDatabase->getParams()->getDatabase()));
                } catch (Exception $e) {
                    //ToDo Fehlerbehandlung
                }

                $action = new C4GShowEmailNotificationDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
            case C4GBrickActionType::ACTION_CONFIRMEMAILNOTIFICATION:
                $action = new C4GSendEmailNotificationAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
            case C4GBrickActionType::ACTION_CANCELMESSAGE:
            case C4GBrickActionType::ACTION_CANCELARCHIVE:
            case C4GBrickActionType::ACTION_CANCELACTIVATION:
            case C4GBrickActionType::ACTION_CANCELFREEZE:
            case C4GBrickActionType::ACTION_CANCELDEFROST:
            case C4GBrickActionType::ACTION_CANCELEMAILNOTIFICATION:
            case C4GBrickActionType::ACTION_CANCELGROUPSELECT:
            case C4GBrickActionType::ACTION_CANCELPROJECTSELECT:
            case C4GBrickActionType::ACTION_CANCELPARENTSELECT:
            case C4GBrickActionType::ACTION_CANCELPARENTFILTER:
                $action = new C4GCancelDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                $return = $action->run();
                if ($module->getDialogChangeHandler()) {
                    $module->getDialogChangeHandler()->clearSession($module->getBrickKey());
                }
                return $return;
            case C4GBrickActionType::ACTION_FREEZEDIALOG:
                $action = new C4GFreezeDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
            case C4GBrickActionType::ACTION_DEFROSTDIALOG:
                $action = new C4GDefrostDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
            case C4GBrickActionType::ACTION_CONFIRMDELETE:
                $action = new C4GConfirmDeleteAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
//            case C4GBrickActionType::ACTION_BUTTONCLICK:
//                //Im Fall der Click-Action ist die ID ide aufzurufende Funktion.
//                $function = strval($id);
//                $return = $this->$function($this->putVars);
//                break;
            case C4GBrickActionType::ACTION_CLOSEDIALOG:
                $action = new C4GCloseDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
            case C4GBrickActionType::ACTION_CLOSEPOPUPDIALOG:
                //ToDo Lösung finden den Dialog zu schließen
                //echo '<script type="text/javascript">closePopupWindow();</script>';
                break;
            case C4GBrickActionType::ACTION_SAVEDIALOG:
                $action = new C4GSaveDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                $action->setModule($module);
                $return = $action->run();
                if (!$dialogParams->isSaveWithoutClose() && $module->getDialogChangeHandler()) {
                    $module->getDialogChangeHandler()->clearSession($module->getBrickKey());
                }
                return $return;
            case C4GBrickActionType::ACTION_SAVEANDNEWDIALOG:
                $action = new C4GSaveDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                $action->setAndNew(true);
                $return = $action->run();
                if ($module->getDialogChangeHandler()) {
                    $module->getDialogChangeHandler()->clearSession($module->getBrickKey());
                }
                return $return;
            case C4GBrickActionType::ACTION_SAVEANDREDIRECTDIALOG:
                $action = new C4GSaveDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                $action->setWithRedirect(true);
                return $action->run();
            case C4GBrickActionType::ACTION_TICKET:
                $action = new C4GTicketDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                $action->setModule($module);
                return $action->run();
            case C4GBrickActionType::ACTION_CONFIRMMESSAGE:
                $action = new C4GConfirmMessageAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
            case C4GBrickActionType::ACTION_CONFIRMARCHIVE:
                $action = new C4GConfirmArchiveAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
            case C4GBrickActionType::ACTION_CONFIRMACTIVATION:
                $action = new C4GConfirmActivationAction(
                    $dialogParams,
                    $listParams,
                    $fieldList,
                    $putVars,
                    $brickDatabase
                );
                $action->setModule($module);
                return $action->run();
            case C4GBrickActionType::ACTION_CONFIRMFREEZE:
                $action = new C4GConfirmFreezeAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
            case C4GBrickActionType::ACTION_CONFIRMDEFROST:
                $action = new C4GConfirmDefrostAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
            case C4GBrickActionType::IDENTIFIER_LIST:
                if (C4GBrickView::isWithoutList($viewType) || ($id > 0)) {
                    $action = new C4GShowDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                    $return = $action->run();
                    if ($module->getDialogChangeHandler()) {
                        $module->getDialogChangeHandler()->clearSession($module->getBrickKey());
                    }
                    return $return;
                    break;
                } else {
                    $action = new C4GShowListAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                    $return = $action->run();
                    if ($module->getDialogChangeHandler()) {
                        $module->getDialogChangeHandler()->clearSession($module->getBrickKey());
                    }
                    return $return;
                    break;
                }
                break;
            case C4GBrickActionType::ACTION_SELECTGROUP:
                $action = new C4GSelectGroupDialogAction(
                    $dialogParams,
                    $listParams,
                    $fieldList,
                    $putVars,
                    $brickDatabase
                );
                return $action->run();
                break;
            case C4GBrickActionType::ACTION_SELECTPROJECT:
                $action = new C4GSelectProjectDialogAction(
                    $dialogParams,
                    $listParams,
                    $fieldList,
                    $putVars,
                    $brickDatabase
                );
                return $action->run();
                break;
            case C4GBrickActionType::ACTION_SELECTPARENT:
                $action = new C4GSelectParentDialogAction(
                    $dialogParams,
                    $listParams,
                    $fieldList,
                    $putVars,
                    $brickDatabase
                );
                return $action->run();
                break;
            case C4GBrickActionType::ACTION_FILTER:
                $action = new C4GShowFilterDialogAction(
                    $dialogParams,
                    $listParams,
                    $fieldList,
                    $putVars,
                    $brickDatabase
                );
                return $action->run();
                break;
            case C4GBrickActionType::ACTION_CONFIRMSELECT:
                //TODO [MEi, 10.03.2015] Mit Leben füllen
                break;
            case C4GBrickActionType::ACTION_CONFIRMGROUPSELECT:
                $action = new C4GConfirmGroupSelectAction(
                    $dialogParams,
                    $listParams,
                    $fieldList,
                    $putVars,
                    $brickDatabase
                );
                return $action->run();
                break;
            case C4GBrickActionType::ACTION_CONFIRMPROJECTSELECT:
                $action = new C4GSetProjectIdAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
            case C4GBrickActionType::ACTION_CONFIRMPARENTSELECT:
                $action = new C4GSetParentIdAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                $listAction = $action->run();
                $module->addFields();
                $listAction->setFieldList($module->getFieldList());
                return $listAction->run();
            case C4GBrickActionType::ACTION_CONFIRMPARENTFILTER:
                $action = new C4GSetFilterAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                $action->setModule($module);
                return $action->run();
            case C4GBrickActionType::ACTION_EXPORT:
                $action = new C4GExportDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
            case C4GBrickActionType::ACTION_PRINT:
                $action = new C4GPrintDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
            case C4GBrickActionType::ACTION_REDIRECT:
                $action = new C4GRedirectAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
            case C4GBrickActionType::ACTION_REDIRECTBACK:
                $action = new C4GRedirectBackAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
            case C4GBrickActionType::ACTION_REDIRECTDIALOGACTION:
                $action = new C4GRedirectDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
            case C4GBrickActionType::ACTION_RELOAD:
                $action = new C4GReloadAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
            case C4GBrickActionType::ACTION_LOGINREDIRECT:
                $action = new C4GLoginRedirectAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
            case C4GBrickActionType::ACTION_CHANGEFIELD:
                $action = new C4GChangeFieldAction(
                    $dialogParams,
                    $listParams,
                    $fieldList,
                    $putVars,
                    $brickDatabase,
                    $module
                );
                $newFieldList = $action->run();
                $module->setFieldList($newFieldList);
                return $newFieldList;
            default:
                break;
        }

        if (isset($return)) {
            return $return;
        } else {
            return;
        }
    }


    /**
     * @param $groupId
     * @param $memberId
     * @param $brickKey
     * @return bool
     *
     * ToDo funktion hier nur zwischengelagert (PerformAction Umbau)
     */
    public static function checkGroupId($groupId, $memberId, $brickKey){
        if ($GLOBALS['con4gis']['groups']['installed']) {
            $groups = C4GBrickCommon::getGroupListForBrick( $memberId, $brickKey );
            if ($groups) {
                foreach($groups as $group) {
                    if ($group->id == $groupId) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @param $memberId
     * @param $brickKey
     * @return int
     *
     * ToDo funktion hier nur zwischengelagert (PerformAction Umbau)
     */
    public static function getOnlyOneGroupId($memberId, $brickKey) {
        $result = -1;
        if ($GLOBALS['con4gis']['groups']['installed']) {
            $groups = C4GBrickCommon::getGroupListForBrick($memberId, $brickKey);
            $count = count($groups);

            if ($count == 1) {
                $result = $groups[0]->id;
            }
        }
        return $result;
    }


    /**
     * @param $projectId
     * @param $groupId
     * @return int
     *
     * ToDo funktion hier nur zwischengelagert (PerformAction Umbau)
     */
    public static function checkProjectId($projectId, $groupId) {
        $result = -1;
        if ( ( $groupId ) && ($projectId) &&
            (C4gProjectsModel::checkProjectGroup($projectId, $groupId))) {
            $result = $projectId;
        };

        return $result;
    }


    /**
     * @param $parent_id
     * ToDo funktion hier nur zwischengelagert (PerformAction Umbau)
     *
     */
    public static function setIdValuesByParent($parent_id, $dialogParams) {
        $dialogParams->setParentId($parent_id);
        $dialogParams->setProjectId('');
        $dialogParams->setProjectUuid('');
        $dialogParams->setGroupId('');

        if ($parent_id) {
            //ToDo überarbeiten brickDatabase
            $model = $dialogParams->getParentModel();
            $parent = $model::findByPk($parent_id);
            if ($parent) {
                $dialogParams->setProjectId($parent->project_id);
                $project = C4gProjectsModel::findByPk($parent->project_id);
                if ($project) {
                    $dialogParams->setProjectUuid($project->uuid);
                    $dialogParams->setGroupId($project->group_id);
                }

            }
        }

        \Session::getInstance()->set("c4g_brick_group_id", $dialogParams->getGroupId());
        \Session::getInstance()->set("c4g_brick_project_id", $dialogParams->getProjectId());
        \Session::getInstance()->set("c4g_brick_project_uuid", $dialogParams->getProjectUuid());
        \Session::getInstance()->set("c4g_brick_parent_id", $dialogParams->getParentId());
    }

    function withMap($fieldList, $contentId) {
        foreach ($fieldList as $field) {
            if ($field instanceof C4GGeopickerField || $field instanceof C4GDateTimeLocationField) {
                if($field->getContentId()) {
                    return $field->getContentId();
                }else{
                    return $contentId;
                }
            }
        }

        return false;
    }

    /**
     * some fields combined in dialog, but for compare or saving we have to merge the fields in one list.
     * @param $fieldList
     * @return array
     */
    public function makeRegularFieldList($fieldList) {
        $resultList = array();
        foreach ($fieldList as $field) {
            if ($field->getExtTitleField()) {
                $resultList[] = $field->getExtTitleField();
            }
            if ($field instanceof C4GGridField) {
                $grid = $field->getGrid();
                if ($grid) {
                    foreach ($grid->getElements() as $element) {
                        $elementField = $element->getField();
                        if ($elementField) {
                            $resultList[] = $elementField;
                        }
                    }
                }
            }

            $resultList[] = $field;
        }

        return $resultList;
    }

}
