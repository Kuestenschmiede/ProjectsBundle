<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Actions;

use con4gis\CoreBundle\Classes\C4GHTMLFactory;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldText;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GKeyField;
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
        $groupId = $dialogParams->getGroupId();
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
        $putVars = $this->getPutVars();
        $brickDatabase = $this->getBrickDatabase();
        $database = $this->brickDatabase->getParams()->getDatabase();
        $modelClass = $brickDatabase->getParams()->getModelClass();
        $viewFormatFunction = $listParams->getViewFormatFunction();

        if ($listParams->getRedirectListPage() !== 0) {
            $redirectAction = new C4GRedirectAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
            $redirectAction->setRedirectWithSaving(false);
            $redirectAction->setRedirectSite($listParams->getRedirectListPage());

            return $redirectAction->run();
        }

        // strip fieldlist to avoid iterating irrelevant fields
        if ($listParams->isStripFieldList() === true) {
            $listFieldlist = [];
            foreach ($fieldList as $field) {
                if ($field->isTableColumn() || $field instanceof C4GKeyField) {
                    $listFieldlist[] = $field;
                }
            }
            $fieldList = $listFieldlist;
        }

        if ($this->module) {
            $this->module->onShowListAction();
        }

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
            }
            if ($onlyGroupId != -1) {
                $groupId = $onlyGroupId;
                $this->dialogParams->setGroupId($onlyGroupId);
                $this->listParams->deleteButton(C4GBrickConst::BUTTON_GROUP);
            }
            \Session::getInstance()->set('c4g_brick_group_id', $groupId);

            $group = \MemberGroupModel::findByPk($groupId);
            if ($group) {
                $group_headline = '<div class="c4g_brick_headtext">' . $GLOBALS['TL_LANG']['FE_C4G_LIST']['USERMESSAGE_ACTIVE_GROUP'] . '<b>' . $group->name . '</b></div>';
            }

            if ($listParams->checkButtonVisibility(C4GBrickConst::BUTTON_PROJECT) && (($projectId == null) || ($projectId == -1))) {
                $action = new C4GSelectProjectDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);

                return $action->run();
            } elseif (($viewType == C4GBrickViewType::PROJECTBASED) || ($viewType == C4GBrickViewType::PROJECTPARENTBASED) || ($viewType == C4GBrickViewType::PROJECTPARENTVIEW)) {
                $project = C4gProjectsModel::findByPk($projectId);
                if ($project) {
                    $project_headline = '<div class="c4g_brick_headtext">' . $GLOBALS['TL_LANG']['FE_C4G_LIST']['USERMESSAGE_ACTIVE_PROJECT'] . '<b>' . $project->caption . '</b></div>';
                } else {
                    \Session::getInstance()->set('c4g_brick_project_id', '');
                    \Session::getInstance()->set('c4g_brick_project_uuid', '');

                    $redirects = $dialogParams->getRedirects();
                    if ($redirects) {
                        foreach ($redirects as $redirect) {
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
                    }

                    return ['usermessage' => $GLOBALS['TL_LANG']['FE_C4G_LIST']['USERMESSAGE_FIRST_CREATE_PROJECT']];
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
                                        $caption = trim($caption) . $value;
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
                    $parent_headline = '<div class="c4g_brick_headtext"> ' . $parentCaption . ': <b>' . $caption . '</b></div>';
                } elseif (!$dialogParams->isWithCommonParentOption()) {
                    \Session::getInstance()->set('c4g_brick_parent_id', '');

                    return ['title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MISSING_PARENT_TITLE'] . $parentCaption,
                        'usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MISSING_PARENT'] . $parentCaption . '.',
                    ];
                }
            }
        } elseif (C4GBrickView::isPublicParentBased($viewType)) {
            if ($dialogParams->getParentId() < 0) {
                $action = new C4GSelectPublicParentDialogAction($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
                $action->setModule($this->module);

                return $action->run();
            }
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
                    $parent_headline = '<div class="c4g_brick_headtext"> ' . $parentCaption . ': <b>' . $caption . '</b></div>';
                    $listParams->addButton(C4GBrickConst::BUTTON_RESET_PARENT);
                } else {
                    $listParams->deleteButton(C4GBrickConst::BUTTON_RESET_PARENT);
                }
            }
        }

        try {
            $tableName = $brickDatabase->getParams()->getTableName();
            switch ($viewType) {
                case C4GBrickView::isGroupBased($viewType):
                    if ($viewType == C4GBrickViewType::GROUPPARENTVIEW || $viewType == C4GBrickViewType::GROUPPARENTBASED) {
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
                            if ($dialogParams->isWithCommonParentOption() && $parentId == -1) {
                                $elements = $brickDatabase->findBy($viewParams->getGroupKeyField(), $groupId);
                                $this->listParams->deleteButton(C4GBrickConst::BUTTON_ADD);
                            } else {
                                $elements = $brickDatabase->findBy($pid_field, $parentId);
                            }
                        }
                    } else {
                        if ($modelListFunction) {
                            $function = $modelListFunction;
                            $database = $brickDatabase->getParams()->getDatabase();

                            //ToDo Umbau brickDatabase
                            $model = $modelClass ?: $brickDatabase->getParams()->getModelClass();
                            $elements = $model::$function($groupId, $database, $listParams, $brickDatabase);
                            if ($elements->headline) {
                                $list_headline = '<div class="c4g_brick_headtext_highlighted">' . $elements->headline . '</div>';
                                unset($elements->headline);
                            }
                        } else {
                            $groupKeyField = $viewParams->getGroupKeyField();
                            $elements = $brickDatabase->findBy($groupKeyField, $groupId);
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
                case C4GBrickViewType::ADMINBASED:
                    $elements = $brickDatabase->findAll();

                    break;
                case C4GBrickView::isMemberBased($viewType):
                    if ($modelListFunction) {
                        $function = $modelListFunction;
                        $database = $brickDatabase->getParams()->getDatabase();
                        $model = $modelClass;
                        $elements = $model::$function($memberId, $tableName, $database, $fieldList, $listParams);
                        if ($elements->headline) {
                            $list_headline = '<div class="c4g_brick_headtext_highlighted">' . $elements->headline . '</div>';
                            unset($elements->headline);
                        }
                    } else {
                        $memberKeyField = $viewParams->getMemberKeyField();
                        $elements = $brickDatabase->findBy($memberKeyField, $memberId);
                    }

                    break;
                case C4GBrickView::isPublicBased($viewType):
                    if ($modelListFunction) {
                        $function = $modelListFunction;
                        $class = $modelClass;
                        if ($parentIdField) {
                            $elements = $class::$function($parentId);
                        } else {
                            $elements = $class::$function();
                        }

                        if ($elements->headline) {
                            $list_headline = '<div class="c4g_brick_headtext_highlighted">' . $elements->headline . '</div>';
                            unset($elements->headline);
                        }
                    } else {
                        if ($brickDatabase->getParams()->getFindBy() && (count($brickDatabase->getParams()->getFindBy()) > 0)) {
                            $elements = call_user_func_array([$brickDatabase,'findBy'], $brickDatabase->getParams()->getFindBy());
                        } else {
                            $elements = $brickDatabase->findAll();
                        }
                    }

                    break;
                case C4GBrickView::isPublicUUIDBased($viewType):
                    if ($modelListFunction) {
                        $function = $modelListFunction;
                        $database = $brickDatabase->getParams()->getDatabase();
                        $model = $modelClass;
                        $elements = $model::$function($this->dialogParams->getUuid(), $tableName, $database, $fieldList, $listParams);
                        if ($elements->headline) {
                            $list_headline = '<div class="c4g_brick_headtext_highlighted">' . $elements->headline . '</div>';
                            unset($elements->headline);
                        }
                    } else {
                        $uuid = $this->dialogParams->getUuid();
                        $elements = $brickDatabase->findBy('uuid', $uuid);
                    }

                    break;
                case C4GBrickView::isPublicParentBased($viewType):
                    if ($modelListFunction) {
                        $function = $modelListFunction;
                        $database = $brickDatabase->getParams()->getDatabase();
                        $model = $modelClass;
                        $elements = $model::$function($parentId, $tableName, $database, $fieldList, $listParams);
                        if ($elements->headline) {
                            $list_headline = '<div class="c4g_brick_headtext_highlighted">' . $elements->headline . '</div>';
                            unset($elements->headline);
                        }
                    } else {
                        if ($parentId === 0 || $parentId === '0') {
                            $elements = $brickDatabase->findAll();
                        } else {
                            $elements = $brickDatabase->findBy($dialogParams->getParentIdField(), $parentId);
                        }
                    }

                    break;
                default:

                    break;
            }
        } catch (Exception $e) {
            $elements = null;
        }

        $filterObject = $listParams->getFilterObject();
        if ($filterObject) {
            $elements = $filterObject->filter($elements, $dialogParams);
            $filterObject->addButton($listParams);
            $filterText = $filterObject->getFilterHeadline();
        } else {
            /** DEPRECATED; use a C4GListFilter object. */
            $filterParams = $listParams->getFilterParams();
            if ($filterParams instanceof C4GBrickFilterParams) {
                if ($filterParams->isWithRangeFilter() && !$modelListFunction) {
                    $dateFrom = $filterParams->getRangeFrom();
                    $dateTo = $filterParams->getRangeTo();
                    $rangeFrom = strtotime($filterParams->getRangeFrom());
                    $rangeTo = strtotime($filterParams->getRangeTo());
                    $highlightSpan = '<span class="c4g_brick_headtext_highlighted">';
                    $highlightSpanEnd = '</span>';
                    if ($filterParams->isWithoutFiltertext()) {
                        $filterText = '';
                    } else {
                        $filterText = 'Zeitraum von ' . $highlightSpan . $dateFrom . $highlightSpanEnd . ' bis zum ' .
                            $highlightSpan . $dateTo . $highlightSpanEnd;
                    }
                    $filterField = $filterParams->getFilterField();
                    if ($filterField) {
                        foreach ($elements as $key => $element) {
                            if (is_array($element)) {
                                if ($element[$filterField] < $rangeFrom || $rangeTo < $element[$filterField]) {
                                    if ($elements instanceof \stdClass) {
                                        unset($elements->$key);
                                    } else {
                                        unset($elements[$key]);
                                    }
                                }
                            } else {
                                if ($element->$filterField < $rangeFrom || $rangeTo < $element->$filterField) {
                                    if ($elements instanceof \stdClass) {
                                        unset($elements->$key);
                                    } else {
                                        unset($elements[$key]);
                                    }
                                }
                            }
                        }
                    }
                } elseif ($filterParams->isWithMethodFilter() && $elements) {
                    if ($filterParams->getUseMethodFilter()) {
                        $class = $filterParams->getFilterMethod()[0];
                        $method = $filterParams->getFilterMethod()[1];
                        $elements = $class::$method($elements, $dialogParams);
                        setcookie($dialogParams->getBrickKey() . '_methodFilter', '1', time() + 3600, '/');
                    //$listParams->addButton(C4GBrickConst::BUTTON_RESET_FILTER);
                    } else {
                        setcookie($dialogParams->getBrickKey() . '_methodFilter', '0', time() + 3600, '/');
                        //$listParams->deleteButton(C4GBrickConst::BUTTON_RESET_FILTER);
                    }
                }
            }
        }

        // call formatter if set
        if ($viewFormatFunction && $modelClass) {
            $elements = $modelClass::$viewFormatFunction($elements);
        }

        foreach ($fieldList as $field) {
            if ($field instanceof C4GBrickFieldText && $field->isTableColumn() && $field->isTableAutoCut()) {
                $fieldName = $field->getFieldName();
                if ($elements != null) {
                    foreach ($elements as $element) {
                        if ($element instanceof \stdClass) {
                            $width = $field->getColumnWidth();
                            if ($width > 0 && (strlen($element->$fieldName) > $width)) {
                                $element->$fieldName = $field->cutFieldValue($element->$fieldName, $width);
                            }
                        } else {
                            $e = $element->row();
                            $width = $field->getColumnWidth();
                            if ($width > 0 && (strlen($e[$fieldName]) > $width)) {
                                $e[$fieldName] = $field->cutFieldValue($e[$fieldName], $width);
                                $element->setRow($e);
                            }
                        }
                    }
                }
            }
        }

        if (!$elements) {
            $elements = [];
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

        //ToDo rebuild headline meachnism (list && dialog)

        // ignore default headlines if set
        if ($listParams->isCustomHeadline() && $list_headline) {
            $headtext = '';
            if ($listParams->getHeadline()) {
                $headtext = '<' . $headlineTag . '>' . $listParams->getHeadline() . '</' . $headlineTag . '>';
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
            if ($dialogParams->getHeadline()) {
                $headtext = '<' . $headlineTag . '>' . $dialogParams->getHeadline() . '</' . $headlineTag . '>';
            }
            if ($listParams->getHeadline()) {
                $headtext = '<' . $headlineTag . '>' . $listParams->getHeadline() . '</' . $headlineTag . '>';
            } elseif (($group_headline) && ($project_headline) && ($parent_headline)) {
                $headtext = $headtext .
                    $group_headline . $project_headline . $parent_headline;
            } elseif (($group_headline) && ($project_headline)) {
                $headtext = $headtext . $group_headline . $project_headline;
            } elseif (($group_headline) && ($parent_headline)) {
                $headtext = $headtext . $group_headline . $parent_headline;
            } elseif ($group_headline) {
                $headtext = $headtext . $group_headline;
            } elseif ($parent_headline) {
                $headtext = $headtext . $parent_headline;
            }
            if ($list_headline) {
                $headtext .= C4GHTMLFactory::lineBreak() . $list_headline;
            }
            if ($filterText) {
                $headtext .= $filterText;
            }
        }

        $brickCaptionPlural = "<span class=\"c4g_list_headline\">$brickCaptionPlural</span>";
        $filterButtons = $listParams->getFilterButtons();
        if ($filterButtons !== null) {
            $brickCaptionPlural .= '<div class="c4g_list_filter_buttons">';
            foreach ($filterButtons as $filterButton) {
                $brickCaptionPlural .= $filterButton->getButtonHtml();
            }
            $brickCaptionPlural .= '</div>';
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
                    $elements,
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
                    $elements,
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
                    $elements,
                    $id,
                    $listParams,
                    $parentCaption,
                    $withLabels
                );

                break;
        }
        if (!$elements && $dialogParams->getEmptyListMessage()) {
            $result['usermessage'] = $dialogParams->getEmptyListMessage()[1];
            $result['title'] = $dialogParams->getEmptyListMessage()[0];
        }

        return $result;
    }

    public function isReadOnly()
    {
        return true;
    }
}
