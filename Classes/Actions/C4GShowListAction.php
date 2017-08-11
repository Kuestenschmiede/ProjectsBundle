<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\ProjectBundle\Classes\Actions;

use c4g\projects\C4gProjectsModel;
use con4gis\ProjectBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectBundle\Classes\Lists\C4GBrickList;
use con4gis\ProjectBundle\Classes\Lists\C4GBrickRenderMode;
use con4gis\ProjectBundle\Classes\Lists\C4GBrickTiles;
use con4gis\ProjectBundle\Classes\Views\C4GBrickView;
use con4gis\ProjectBundle\Classes\Views\C4GBrickViewType;
use Symfony\Component\Config\Definition\Exception\Exception;

class C4GShowListAction extends C4GBrickDialogAction
{
    public function run()
    {
        $dialogParams = $this->getDialogParams();
        $id = $dialogParams->getId();
        $memberId = $dialogParams->getMemberId();
        $groupId  = $dialogParams->getGroupId();
        $projectId = $dialogParams->getProjectId();
        $projectKey = $dialogParams->getProjectKey();
        $parentId = $dialogParams->getParentId();
        $parentIdField = $dialogParams->getParentIdField();
        $parentModel = $dialogParams->getParentModel();
        $parentCaption = $dialogParams->getParentCaption();
        $parentCaptionFields = $dialogParams->getParentCaptionFields();
        $brickKey = $dialogParams->getBrickKey();
        $brickCaptionPlural = $dialogParams->getBrickCaptionPlural();
        $captionField = $dialogParams->getCaptionField();
        $withLabels = $dialogParams->isWithLabels();
        $viewType = $dialogParams->getViewType();
        $viewParams = $dialogParams->getViewParams();
        $modelListFunction = $viewParams->getModelListFunction();
        $listParams = $this->getListParams();
        $fieldList = $this->getFieldList();
        $putVars   = $this->getPutVars();
        $brickDatabase = $this->getBrickDatabase();
        $database = $this->brickDatabase->getParams()->getDatabase();
        $modelClass = $brickDatabase->getParams()->getModelClass();

        $groupCount = -1;
        if ($GLOBALS['con4gis_groups_extension']['installed']) {
            $groupCount = count(C4GBrickCommon::getGroupListForBrick( $memberId, $brickKey ));
        }

        $group_headline = '';
        $project_headline = '';
        $parent_headline = '';


        if ((C4GBrickView::isGroupBased($viewType)) ||
            (C4GBrickView::isProjectBased($viewType)) ||
            (C4GBrickView::isProjectParentBased($viewType)))
        {
            $onlyGroupId = $this->getOnlyOneGroupId($memberId, $brickKey);
            if ($onlyGroupId == -1) {
                $action = new C4GSelectGroupDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
            } else {
                $groupId = $onlyGroupId;
                $this->dialogParams->setGroupId($onlyGroupId);
                \Session::getInstance()->set("c4g_brick_group_id", $onlyGroupId);
            }

//ToDo checkButtonVisibility prüfen
//            if ( $listParams->checkButtonVisibility(C4GBrickConst::BUTTON_GROUP) && ( ($groupId == null) || ($groupId == -1))) {
//                if ($onlyGroupId == -1) {
//                    $action = new C4GSelectGroupDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
//                    return $action->run();
//                } else {
//                    $groupId = $onlyGroupId;
//                    $this->dialogParams->setGroupId($onlyGroupId);
//                    \Session::getInstance()->set("c4g_brick_group_id", $onlyGroupId);
//                }
//            }

            $group = \MemberGroupModel::findByPk($groupId);
            if ($group) {
                $group_headline = '<div class="c4g_brick_headtext">'.$GLOBALS['TL_LANG']['FE_C4G_LIST']['USERMESSAGE_ACTIVE_GROUP'].'<b>'.$group->name.'</b></div>';
            }

            if ( $listParams->checkButtonVisibility(C4GBrickConst::BUTTON_PROJECT) && ( ($projectId == null) || ($projectId == -1))) {
                $action = new C4GSelectProjectDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                return $action->run();
            } else if (($viewType == C4GBrickViewType::PROJECTBASED) || ($viewType == C4GBrickViewType::PROJECTPARENTBASED) || ($viewType == C4GBrickViewType::PROJECTPARENTVIEW)){
                $project = C4gProjectsModel::findByPk($projectId);
                if ($project) {
                    $project_headline = '<div class="c4g_brick_headtext">'.$GLOBALS['TL_LANG']['FE_C4G_LIST']['USERMESSAGE_ACTIVE_PROJECT'].'<b>'.$project->caption.'</b></div>';
                } else {
                    \Session::getInstance()->set("c4g_brick_project_id", '');
                    \Session::getInstance()->set("c4g_brick_project_uuid", '');

                    return array(
                        'usermessage' => $GLOBALS['TL_LANG']['FE_C4G_LIST']['USERMESSAGE_FIRST_CREATE_PROJECT']
                    );
                }
            }

            if ($listParams->checkButtonVisibility(C4GBrickConst::BUTTON_PARENT) &&
                !$dialogParams->isWithEmptyParentOption() &&
                (($parentId == null) || ($parentId == -1))) {
                $action = new C4GSelectParentDialogAction(
                    $dialogParams,
                    $listParams,
                    $fieldList,
                    $putVars,
                    $brickDatabase
                );
                return $action->run();
            } elseif ($listParams->checkButtonVisibility(C4GBrickConst::BUTTON_PARENT)
                    && (($viewType == C4GBrickViewType::PROJECTPARENTBASED)
                    || ($viewType == C4GBrickViewType::GROUPPARENTVIEW)
                    || ($viewType == C4GBrickViewType::PROJECTPARENTVIEW)
                    || ($viewType == C4GBrickViewType::GROUPPARENTBASED))) {
                $parent = $parentModel::findByPk($parentId);
                if ($parent) {
                    $caption = $parent->caption;
                    if (!$caption) {
                        $caption = $parent->name;
                    }
                    if ($parentCaptionFields && is_array($parentCaptionFields)) {
                        $caption = '';
                        foreach ($parentCaptionFields as $key => $value) {
                            if (strlen($value) == 1) {
                                if ($value == ')') {
                                    //if there is no bracketed value remove brackets
                                    if (substr(trim($caption), -1, 1) == '(') {
                                        $caption = substr(trim($caption), 0, -1);
                                    } else {
                                        $caption = trim($caption).$value;
                                    }
                                } else {
                                    $caption .= $value;
                                }
                            } else {
                                $caption .= $parent->$value . ' ';
                            }
                        }
                    }

                    $parent_headline = '<div class="c4g_brick_headtext"> '.$parentCaption.': <b>'.$caption.'</b></div>';
                } elseif (!$dialogParams->isWithEmptyParentOption()) {
                    \Session::getInstance()->set("c4g_brick_parent_id", '');

                    //ToDo language
                    return array(
                        'usermessage' => 'Bitte erzeugen Sie zuerst ein(en) '. $parentCaption . '.' //ToDo Language
                    );
                }
            }


        }

        try
        {
            $tableName = $brickDatabase->getParams()->getTableName();
            switch($viewType) {
                case C4GBrickView::isGroupBased($viewType):
                    if ($tableName == 'tl_c4g_projects') {
                        $t = $tableName;
                        $arrColumns = array("$t.group_id=? AND $t.brick_key=?");
                        $arrValues = array($groupId, $projectKey);
                        $arrOptions = array(
                            'order' => "$t.tstamp DESC"
                        );

                        $elements = $brickDatabase->findBy($arrColumns, $arrValues, $arrOptions);
                    } else {
                        if($viewType == C4GBrickViewType::GROUPPARENTVIEW || $viewType == C4GBrickViewType::GROUPPARENTBASED) {
                            $pid_field = 'pid';
                            if ($parentIdField) {
                                $pid_field = $parentIdField;
                            }

                            if ($modelListFunction) {
                                $function = $modelListFunction;
                                $database = $brickDatabase->getParams()->getDatabase();

                                //ToDo Umbau brickDatabase
                                $model = $modelClass ? $modelClass : $brickDatabase->getParams()->getModelClass();
                                $elements = $model::$function($groupId, $pid_field, $parentId, $database, $listParams);

                                if ($elements->headline) {
                                    $list_headline = '<div class="c4g_brick_headtext_highlighted">' . $elements->headline . '</div>';
                                    unset($elements->headline);
                                }
                            } else {
                                if ($dialogParams->isWithEmptyParentOption() && $parentId == -1) {
                                    $elements = $brickDatabase->findBy($viewParams->getGroupKeyField(), $groupId);
                                } else {
                                    $elements = $brickDatabase->findBy($pid_field, $parentId);
                                }
                            }
                        }
                        else {
                            if ($modelListFunction) {
                                $function = $modelListFunction;
                                $database = $brickDatabase->getParams()->getDatabase();

                                //ToDo Umbau brickDatabase
                                $model = $modelClass ? $modelClass : $brickDatabase->getParams()->getModelClass();
                                $elements = $model::$function($groupId, $database, $listParams);
                                if ($elements->headline) {
                                    $list_headline = '<div class="c4g_brick_headtext_highlighted">' . $elements->headline . '</div>';
                                    unset($elements->headline);
                                }
                            } else {
                                $groupKeyField = $viewParams->getGroupKeyField();
                                $elements = $brickDatabase->findBy($groupKeyField, $groupId);
                            }
                        }
                    }
                    break;
                case C4GBrickView::isProjectBased($viewType):
                    $elements = $brickDatabase->findBy('project_id', $projectId);
                    break;
                case C4GBrickView::isProjectParentBased($viewType):
                    $pid_field = 'pid';
                    if ($parentIdField) {
                        $pid_field = $parentIdField;
                    }
                    $elements = $brickDatabase->findBy($pid_field, $parentId);
                    break;
                case C4GBrickView::isMemberBased($viewType):
                    if($modelListFunction) {
                        $function = $modelListFunction;
                        $database = $brickDatabase->getParams()->getDatabase();
                        //ToDo Umbau brickDatabase
                        $model = $modelClass;
                        $elements = $model::$function($memberId, $tableName, $database, $fieldList, $listParams);
                        if ($elements->headline) {
                            $list_headline = '<div class="c4g_brick_headtext_highlighted">' . $elements->headline . '</div>';
                            unset($elements->headline);
                        }
                    }
                    else {
                        $memberKeyField = $viewParams->getMemberKeyField();
                        $elements = $brickDatabase->findBy($memberKeyField, $memberId);
                    }
                    break;
                case C4GBrickView::isPublicBased($viewType):
                    if ($modelListFunction && $parentIdField) {
                        $function = $modelListFunction;
                        $pid = $parentIdField;
                        //ToDo Umbau brickDatabase
                        $model = $modelClass;

                        //ToDo umbauen -> kann so nicht mehr funktionieren ($this-> ursprünglich ModuleParent)
                        $elements = $model::$function($this->$pid);
//ToDo prüfen evtl. wieder einbauen
//                        if ($id == "-1") {
//                            $this->initBrickModule($id); //set initial values with new function params
//                        }
                    } else {
                        $elements = $brickDatabase->findAll();
                    }
                    break;
                default:
                    break;
            }
        } catch (Exception $e) {
            $elements = null;
        }

        if (!$elements) {
            $elements = array();
        }
        $content = '';
        if (!$dialogParams->getC4gMap()) {
            $result = $this->withMap($fieldList, $dialogParams->getContentId());
            if ($result) {
                $content = \Controller::replaceInsertTags('{{insert_content::'.$result.'}}');
            }
        } else {
            $content = $dialogParams->getC4gMap();
        }

        $headtext = $dialogParams->getHeadline();
        if ($listParams->getHeadline()) {
            $headtext = $listParams->getHeadline();
        } elseif (($group_headline) && ($project_headline) && ($parent_headline)) {
            $headtext = $headtext . \c4g\C4GHTMLFactory::lineBreak() .
                $group_headline . $project_headline . $parent_headline;
        } elseif (($group_headline) && ($project_headline)) {
            $headtext = $headtext.\c4g\C4GHTMLFactory::lineBreak().$group_headline.$project_headline;
        } elseif (($group_headline) && ($parent_headline)) {
            $headtext = $headtext.\c4g\C4GHTMLFactory::lineBreak().$group_headline.$parent_headline;
        } elseif ($group_headline) {
            $headtext = $headtext.\c4g\C4GHTMLFactory::lineBreak().$group_headline;
        }
        if ($list_headline) {
            $headtext .= \c4g\C4GHTMLFactory::lineBreak().$list_headline;

        }


        $renderMode = $listParams->getRenderMode();
        switch ($renderMode) {
            case C4GBrickRenderMode::LISTBASED:
                $result = C4GBrickList::showC4GTableList(
                    $brickCaptionPlural,
                    $database,
                    $content,
                    $headtext,
                    $fieldList,
                    $elements,
                    $id,
                    $captionField,
                    $parentCaption,
                    $listParams
                );
                break;
            case C4GBrickRenderMode::TILEBASED:
                //ToDo Der Funktionsumfang von TILEBASED muss immer an LISTBASED angepasst sein. Bitte nachziehen.
                $result = C4GBrickTiles::showC4GTableList(
                    $headtext,
                    $database,
                    $brickCaptionPlural,
                    $fieldList,
                    $elements,
                    $id,
                    $listParams,
                    $captionField,
                    $parentCaption,
                    $withLabels
                );
                break;
        }
        return $result;
    }
}
