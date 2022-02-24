<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2022, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Actions;

use con4gis\CoreBundle\Classes\C4GUtils;
use con4gis\CoreBundle\Classes\C4GVersionProvider;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GDateTimeLocationField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GGeopickerField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GGridField;
use con4gis\ProjectsBundle\Classes\Framework\C4GBaseController;
use con4gis\ProjectsBundle\Classes\Models\C4gProjectsModel;
use con4gis\ProjectsBundle\Classes\Permission\C4GTablePermission;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickView;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;
use con4gis\GroupsBundle\Resources\contao\models\MemberGroupModel;
use con4gis\GroupsBundle\Resources\contao\models\MemberModel;
use Symfony\Component\HttpFoundation\Session\Session;

abstract class C4GBrickAction
{
    protected $dialogParams;
    protected $listParams;
    protected $fieldList;
    protected $putVars;
    protected $brickDatabase;
    protected $module = null;
    /**
     * C4GBrickAction constructor.
     */
    public function __construct($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase)
    {
        $this->dialogParams = $dialogParams;
        $this->listParams = $listParams;
        $this->fieldList = $fieldList;
        $this->putVars = $putVars;
        $this->brickDatabase = $brickDatabase;
    }

    abstract public function run();

    /**
     * @return mixed
     */
    public function getDialogParams()
    {
        return $this->dialogParams;
    }

    /**
     * @param $dialogParams
     * @return $this
     */
    public function setDialogParams($dialogParams)
    {
        $this->dialogParams = $dialogParams;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFieldList()
    {
        return $this->fieldList;
    }

    /**
     * @param $fieldList
     * @return $this
     */
    public function setFieldList($fieldList)
    {
        $this->fieldList = $fieldList;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPutVars()
    {
        return $this->putVars;
    }

    /**
     * @param $putVars
     * @return $this
     */
    public function setPutVars($putVars)
    {
        $this->putVars = $putVars;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBrickDatabase()
    {
        return $this->brickDatabase;
    }

    /**
     * @param $brickDatabase
     * @return $this
     */
    public function setBrickDatabase($brickDatabase)
    {
        $this->brickDatabase = $brickDatabase;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getListParams()
    {
        return $this->listParams;
    }

    /**
     * @param $listParams
     * @return $this
     */
    public function setListParams($listParams)
    {
        $this->listParams = $listParams;

        return $this;
    }

    /**
     * @return null
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param $module
     * @return $this
     */
    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * @param $action
     * @param $module
     * @return null
     */
    public static function performAction(&$action, &$module)
    {
        $dialogParams = $module->getDialogParams();
        $brickDatabase = $module->getBrickDatabase();
        $listParams = $module->getListParams();
        $putVars = $module->getPutVars();
        $fieldList = $module->getFieldList();
        $viewParams = $dialogParams->getViewParams();

        $values = explode(':', $action, 5);
        $brickAction = $values[0];
        if ($brickAction == C4GBrickActionType::IDENTIFIER_LIST) {
            $brickAction = C4GBrickActionType::IDENTIFIER_LIST_ACTION;
        } elseif (($brickAction == C4GBrickActionType::IDENTIFIER_DIALOG) || ($brickAction == C4GBrickActionType::IDENTIFIER_BRICKDIALOG)) {
            $brickAction = C4GBrickActionType::IDENTIFIER_DIALOG_ACTION;
        } elseif ($brickAction == C4GBrickActionType::IDENTIFIER_PARENT) {
            $brickAction = C4GBrickActionType::IDENTIFIER_PARENT_ACTION;
        } elseif ($brickAction == C4GBrickActionType::IDENTIFIER_MESSAGE) {
            $brickAction = C4GBrickActionType::IDENTIFIER_MESSAGE_ACTION;
        }
        $id = $values[1];

        if ($id == null) {
            $id = -1;
        }

        $dialogParams->setId($id);

        if ($brickAction != C4GBrickActionType::ACTION_BUTTONCLICK) {
            if (key_exists(2,$values) && $values[2]) {
                $groupId = $values[2];
            } else {
                $groupId = $dialogParams->getSession()->getSessionValue('c4g_brick_group_id');
            }

            if ($groupId && MemberGroupModel::isMemberOfGroup($groupId, $dialogParams->getMemberId())) {
                if (MemberModel::hasRightInGroup($dialogParams->getMemberId(), $groupId, $dialogParams->getBrickKey())) {
                    $dialogParams->setGroupId($groupId);
                    $dialogParams->getSession()->setSessionValue('c4g_brick_group_id', $groupId);
                }
            }

            if (key_exists(3,$values) && $values[3]) {
                $project_id = $values[3];
            } else {
                $project_id = $dialogParams->getSession()->getSessionValue('c4g_brick_project_id');
            }

            if (C4gProjectsModel::checkProjectId($project_id, $dialogParams->getProjectKey(),$dialogParams->getSession())) {
                $dialogParams->setProjectId($project_id);
                $dialogParams->getSession()->setSessionValue('c4g_brick_project_id', $project_id);
                $dialogParams->setProjectUuid($dialogParams->getSession()->getSessionValue('c4g_brick_project_uuid'));
            }

            if (key_exists(4,$values) && $values[4]) {
                $parent_id = $values[4];
            } else {
                $parent_id = $dialogParams->getSession()->getSessionValue('c4g_brick_parent_id');
            }

            $dialogParams->setParentId($parent_id);
        }

        $viewType = $dialogParams->getViewType();
        if (($fieldList == null) && ($values[0] == C4GBrickActionType::IDENTIFIER_LIST)) {
            $fieldList = [];

            if (!C4GBrickView::isPublicBased($viewType) && ($viewType != C4GBrickViewType::MEMBERBOOKING)) {
                //ToDo ab hier aufräumen und C4GBrickView nutzen.
                //Gruppe setzen / prüfen
                if (($viewType == C4GBrickViewType::GROUPFORM) ||
                    ($viewType == C4GBrickViewType::GROUPFORMCOPY) ||
                    ($viewType == C4GBrickViewType::GROUPVIEW)) {
                    $element = $brickDatabase->findByPk($id);

                    $groupKeyField = $viewParams->getGroupKeyField();

                    if (($element) && ($element->$groupKeyField)) {
                        if (static::checkGroupId($element->$groupKeyField, $dialogParams->getMemberId(), $dialogParams->getBrickKey())) {
                            $dialogParams->setGroupId($element->$groupKeyField);
                            $dialogParams->getSession()->setSessionValue('c4g_brick_group_id', $dialogParams->getGroupId());
                        }
                    }
                } else {
                    if (($dialogParams->getGroupId() == null) || ($dialogParams->getGroupId() == -1)) {
                        $onlyGroup_id = static::getOnlyOneGroupId($dialogParams->getMemberId(), $dialogParams->getBrickKey());
                        if ($onlyGroup_id == -1) {
                            $action = new C4GSelectGroupDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);

                            return $action->run();
                        }
                        $dialogParams->setGroupId($onlyGroup_id);
                        $dialogParams->getSession()->setSessionValue('c4g_brick_group_id', $dialogParams->getGroupId());
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
                    ($viewType == C4GBrickViewType::GROUPPARENTBASED) ||
                    ($viewType == C4GBrickViewType::PROJECTPARENTFORM)) {
                    if (!($viewType == C4GBrickViewType::GROUPPARENTVIEW)) {
                        $project_id = static::checkProjectId($dialogParams->getProjectId(), $dialogParams->getGroupId());

                        if (($project_id == null) || ($project_id == -1)) {
                            $action = new C4GSelectProjectDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);

                            return $action->run();
                        }
                    }

                    $parent_id = $dialogParams->getParentId();

                    if ($listParams->checkButtonVisibility(C4GBrickConst::BUTTON_PARENT) &&
                        (($parent_id == null) || ($parent_id == -1))) {
                        if (!$dialogParams->isWithCommonParentOption()) {
                            $action = new C4GSelectParentDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);

                            return $action->run();
                        }
                    }
                }

                if (($viewType == C4GBrickViewType::PROJECTPARENTFORMCOPY) ||
                    ($viewType == C4GBrickViewType::PROJECTPARENTVIEW)) {
                    static::setIdValuesByParent($id, $dialogParams, $module);
                }
            }
        }

        if ($viewType == C4GBrickViewType::MEMBERBOOKING) {
            $element = $brickDatabase->findByPk($id);

            $groupKeyField = $viewParams->getGroupKeyField();

            if (($element) && ($element->$groupKeyField)) {
                $dialogParams->setGroupId($element->$groupKeyField);
                $dialogParams->getSession()->setSessionValue('c4g_brick_group_id', $element->$groupKeyField);
            } else {
                $dialogParams->setGroupId(null);
                $dialogParams->getSession()->setSessionValue('c4g_brick_group_id', null);
            }
        }

        $className = '';
        $namespaces = $viewParams->getActionNamespaces();
        foreach ($namespaces as $namespace) {
            if (C4GUtils::endsWith($namespace, $brickAction)) {
                $className = $namespace;
            }
        }
        if ($className === '') {
            $className = 'con4gis\ProjectsBundle\Classes\Actions\\' . $brickAction;
        }
        if (class_exists($className)) {
            $action = new $className($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
            $action->setModule($module);

            if (
                (!$action instanceof C4GConfirmGroupSelectAction) &&
                (!$action instanceof C4GSetProjectIdAction) &&
                (!$action instanceof C4GSetParentIdAction) &&
                (!$action instanceof C4GSetFilterAction) &&
                (!(C4GBrickView::isPublicBased($dialogParams->getViewType()))) &&
                (!(C4GBrickView::isPublicParentBased($dialogParams->getViewType())))
                ) {
                if ($module->isWithPermissionCheck()) {
                    $table = $module->getC4GTablePermissionTable();
                    if ($table) {
                        $permission = new C4GTablePermission($table, [$dialogParams->getId()], $dialogParams->getSession());
                        if ($action->isReadOnly()) {
                            $permission->setLevel(1);
                        } else {
                            $permission->setLevel(2);
                        }
                        $permission->setAction($brickAction);
                        $permission->check();
                    }
                }
            }

            return $action->run();  //If the class does not exist, an exception will be thrown.
        }

        return null;
    }

    /**
     * @param $groupId
     * @param $memberId
     * @param $brickKey
     * @return bool
     *
     * ToDo funktion hier nur zwischengelagert (PerformAction Umbau)
     */
    public static function checkGroupId($groupId, $memberId, $brickKey)
    {
        if (C4GVersionProvider::isInstalled('con4gis/groups')) {
            $groups = C4GBrickCommon::getGroupListForBrick($memberId, $brickKey);
            if ($groups) {
                foreach ($groups as $group) {
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
    public static function getOnlyOneGroupId($memberId, $brickKey)
    {
        $result = -1;
        if (C4GVersionProvider::isInstalled('con4gis/groups')) {
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
    public static function checkProjectId($projectId, $groupId)
    {
        $result = -1;
        if (($groupId) && ($projectId) &&
            (C4gProjectsModel::checkProjectGroup($projectId, $groupId))) {
            $result = $projectId;
        };

        return $result;
    }

    /**
     * @param $parent_id
     * ToDo funktion hier nur zwischengelagert (PerformAction Umbau)
     */
    public static function setIdValuesByParent($parent_id, $dialogParams, C4GBaseController &$module)
    {
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

        $dialogParams->getSession()->setSessionValue('c4g_brick_group_id', $dialogParams->getGroupId());
        $dialogParams->getSession()->setSessionValue('c4g_brick_project_id', $dialogParams->getProjectId());
        $dialogParams->getSession()->setSessionValue('c4g_brick_project_uuid', $dialogParams->getProjectUuid());
        $dialogParams->getSession()->setSessionValue('c4g_brick_parent_id', $dialogParams->getParentId());
    }

    public function withMap($fieldList, $contentId)
    {
        foreach ($fieldList as $field) {
            if ($field instanceof C4GGeopickerField || $field instanceof C4GDateTimeLocationField) {
                if ($field->getContentId()) {
                    return $field->getContentId();
                }

                return $contentId;
            }
        }

        return false;
    }

    /**
     * some fields combined in dialog, but for compare or saving we have to merge the fields in one list.
     * @param $fieldList
     * @return array
     */
    public function makeRegularFieldList($fieldList)
    {
        $resultList = [];
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

    /**
     * Needed for the permission check. If the action does more than simply show data to the user, it should not be considered read only.
     * @return bool
     */
    abstract public function isReadOnly();
}
