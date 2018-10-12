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
use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Filter\C4GBrickFilterParams;
use con4gis\ProjectsBundle\Classes\Lists\C4GBrickList;
use con4gis\ProjectsBundle\Classes\Lists\C4GBrickRenderMode;
use con4gis\ProjectsBundle\Classes\Lists\C4GBrickTiles;
use con4gis\ProjectsBundle\Classes\Models\C4gProjectsModel;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickView;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;
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
        $parentId = $dialogParams->getParentId();
        $parentIdField = $dialogParams->getParentIdField();
        $parentModel = $dialogParams->getParentModel();
        $parentCaption = $dialogParams->getParentCaption();
        $parentCaptionFields = $dialogParams->getParentCaptionFields();
        $brickKey = $dialogParams->getBrickKey();
        $brickCaptionPlural = $dialogParams->getBrickCaptionPlural();
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
        $viewFormatFunction = $listParams->getViewFormatFunction();

        $group_headline = '';
        $project_headline = '';
        $parent_headline = '';

        if ((C4GBrickView::isGroupBased($viewType)) ||
            (C4GBrickView::isProjectBased($viewType)) ||
            (C4GBrickView::isProjectParentBased($viewType))) {
            $onlyGroupId = $this->getOnlyOneGroupId($memberId, $brickKey);
            if ($listParams->checkButtonVisibility(C4GBrickConst::BUTTON_GROUP) && (($groupId == null)
                    || ($groupId == -1)) && ($onlyGroupId == -1)) {
                $action = new C4GSelectGroupDialogAction(
                    $dialogParams,
                    $listParams,
                    $fieldList,
                    $putVars,
                    $brickDatabase
                );
                return $action->run();
            } else {
                if ($onlyGroupId != -1) {
                    $groupId = $onlyGroupId;
                    $this->dialogParams->setGroupId($onlyGroupId);
                    $this->listParams->deleteButton(C4GBrickConst::BUTTON_GROUP);
                }
                \Session::getInstance()->set("c4g_brick_group_id", $groupId);
            }

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

                    $redirects = $dialogParams->getRedirects();
                    if ($redirects) {
                        foreach($redirects as $redirect) {
                            $redirect->setActive($redirect->getType() == C4GBrickConst::REDIRECT_PROJECT);
                        }

                        $action = new C4GShowRedirectDialogAction(
                            $dialogParams,
                            $listParams,
                            $fieldList,
                            $putVars,
                            $brickDatabase
                        );
                        return $action->run();
                    } else {
                        return array('usermessage' => $GLOBALS['TL_LANG']['FE_C4G_LIST']['USERMESSAGE_FIRST_CREATE_PROJECT']);
                    }
                }
            }

            if ($listParams->checkButtonVisibility(C4GBrickConst::BUTTON_PARENT) &&
                !$dialogParams->isWithCommonParentOption() &&
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
                    } elseif ($parentCaptionCallback = $dialogParams->getParentCaptionCallback()) {
                        $class = $parentCaptionCallback[0];
                        $function = $parentCaptionCallback[1];
                        $arrCaptions = $class::$function(
                            [$parent],
                            $this->brickDatabase->getEntityManager()
                        );
                        $caption = $arrCaptions[$parentId];
                    }
                    $parent_headline = '<div class="c4g_brick_headtext"> '.$parentCaption.': <b>'.$caption.'</b></div>';
                } elseif (!$dialogParams->isWithCommonParentOption()) {
                    \Session::getInstance()->set("c4g_brick_parent_id", '');

                    return array('title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MISSING_PARENT_TITLE'] .$parentCaption,
                        'usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MISSING_PARENT']. $parentCaption . '.'
                    );
                }
            }


        } elseif (C4GBrickView::isPublicParentBased($viewType)) {
            if ($dialogParams->getParentId() < 0) {
                $action = new C4GSelectPublicParentDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                $action->setModule($this->module);
                return $action->run();
                } else {
                    if ($listParams->checkButtonVisibility(C4GBrickConst::BUTTON_PUBLIC_PARENT)) {
                        $parent = $parentModel::findByPk($parentId);
                        if ($parent) {
                            if (is_array($parent)) {
                                $caption = $parent['name'];
                            } elseif ($parent instanceof \stdClass) {
                                $caption = $parent->name;
                            } else {
                                $caption = 'NULL';
                            }
                            $parent_headline = '<div class="c4g_brick_headtext"> '.$parentCaption.': <b>'.$caption.'</b></div>';
                    }
                }
            }
        }

        $listDataObject = $this->module->getListDataObject();
        $listDataObject->loadListElements();
        $listElements = $listDataObject->getListElements();

        $filterObject = $listParams->getFilterObject();
        if ($filterObject) {
            $listElements = $filterObject->filter($listElements, $dialogParams);
            $filterObject->addButton($listParams);
        } else {
            /** DEPRECATED; use a C4GListFilter object. */
            $filterParams =  $listParams->getFilterParams();
            if ($filterParams instanceof C4GBrickFilterParams) {
                if ($filterParams->isWithRangeFilter() && !$modelListFunction) {
                    $dateFrom = $filterParams->getRangeFrom();
                    $dateTo = $filterParams->getRangeTo();
                    $rangeFrom = strtotime($filterParams->getRangeFrom());
                    $rangeTo = strtotime($filterParams->getRangeTo());
                    $highlightSpan = '<span class="c4g_brick_headtext_highlighted">';
                    $highlightSpanEnd = '</span>';
                    if ($filterParams->isWithoutFiltertext()) {
                        $filterText = "";
                    } else {
                        $filterText = "Zeitraum von " . $highlightSpan . $dateFrom . $highlightSpanEnd . ' bis zum ' .
                            $highlightSpan . $dateTo . $highlightSpanEnd;
                    }
                    $filterField = $filterParams->getFilterField();
                    if ($filterField) {
                        foreach ($listElements as $key => $element) {
                            if (is_array($element)) {
                                if ($element[$filterField] < $rangeFrom || $rangeTo < $element[$filterField]) {
                                    if ($listElements instanceof \stdClass) {
                                        unset($listElements->$key);
                                    } else {
                                        unset($listElements[$key]);
                                    }
                                }
                            } else {
                                if ($element->$filterField < $rangeFrom || $rangeTo < $element->$filterField) {
                                    if ($listElements instanceof \stdClass) {
                                        unset($listElements->$key);
                                    } else {
                                        unset($listElements[$key]);
                                    }
                                }
                            }
                        }
                    }
                } elseif ($filterParams->isWithMethodFilter() && $listElements) {
                    if ($filterParams->getUseMethodFilter()) {
                        $class = $filterParams->getFilterMethod()[0];
                        $method = $filterParams->getFilterMethod()[1];
                        $listElements = $class::$method($listElements, $dialogParams);
                        setcookie($dialogParams->getBrickKey().'_methodFilter', '1', time()+3600, '/');
                    } else {
                        setcookie($dialogParams->getBrickKey().'_methodFilter', '0', time()+3600, '/');
                    }
                }
            }
        }

        // call formatter if set
        if ($viewFormatFunction && $modelClass) {
            $listElements = $modelClass::$viewFormatFunction($listElements);
        }

        if (!$listElements) {
            $listElements = array();
        }
        $content = '';
        if (!$dialogParams->getC4gMap()) {
            $result = $this->withMap($fieldList, $dialogParams->getContentId());
            if ($result) {
//                $content = \Controller::replaceInsertTags('{{insert_content::'.$result.'}}');
                $content = $result;
            }
        } else {
            $content = $dialogParams->getC4gMap();
        }
        $headlineTag = $dialogParams->getHeadlineTag();
        // ignore default headlines if set
        if ($listParams->isCustomHeadline() && $list_headline) {
            $headtext = "";
            if ($listParams->getHeadline()) {
                $headtext = '<'.$headlineTag.'>'.$listParams->getHeadline().'</'.$headlineTag.'>';
            }
            $headtext .= C4GHTMLFactory::lineBreak() . $list_headline;
            if ($group_headline) {
                $headtext .= $group_headline;
            }
            if ($project_headline) {
                $headtext .= $project_headline;
            }
            if ($parent_headline) {
                $headtext .= $parent_headline;
            }
        } else {
            $headtext = '<'.$headlineTag.'>'.$dialogParams->getHeadline().'</'.$headlineTag.'>';
            if ($listParams->getHeadline()) {
                $headtext = '<'.$headlineTag.'>'.$listParams->getHeadline().'</'.$headlineTag.'>';
            } elseif (($group_headline) && ($project_headline) && ($parent_headline)) {
                $headtext = $headtext .
                    $group_headline . $project_headline . $parent_headline;
            } elseif (($group_headline) && ($project_headline)) {
                $headtext = $headtext.$group_headline.$project_headline;
            } elseif (($group_headline) && ($parent_headline)) {
                $headtext = $headtext.$group_headline.$parent_headline;
            } elseif ($group_headline) {
                $headtext = $headtext.$group_headline;
            } elseif ($parent_headline) {
                $headtext = $headtext.$parent_headline;
            }
            if ($list_headline) {
                $headtext .= C4GHTMLFactory::lineBreak().$list_headline;
            }
            if ($filterText) {
                $headtext .= $filterText;
            }
        }


        $renderMode = $listParams->getRenderMode();
        switch ($renderMode) {
            case C4GBrickRenderMode::LISTBASED:
                $result = C4GBrickList::showC4GList(
                    $brickCaptionPlural,
                    $database,
                    $content,
                    $headtext,
                    $fieldList,
                    $listElements,
                    $id,
                    $parentCaption,
                    $listParams
                );
                break;
            case C4GBrickRenderMode::TABLEBASED:
                $result = C4GBrickList::showC4GTableList(
                    $brickCaptionPlural,
                    $database,
                    $content,
                    $headtext,
                    $fieldList,
                    $listElements,
                    $id,
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
                    $listElements,
                    $id,
                    $listParams,
                    $parentCaption,
                    $withLabels
                );
                break;
        }
        return $result;
    }

    public function isReadOnly()
    {
        return true;
    }
}
