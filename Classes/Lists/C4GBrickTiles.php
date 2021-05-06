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
namespace con4gis\ProjectsBundle\Classes\Lists;

use con4gis\CoreBundle\Classes\C4GHTMLFactory;
use con4gis\CoreBundle\Classes\C4GUtils;
use con4gis\ProjectsBundle\Classes\Actions\C4GBrickActionType;
use con4gis\ProjectsBundle\Classes\Buttons\C4GBrickButton;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Conditions\C4GBrickConditionType;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldType;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GMoreButtonField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GSelectField;
use con4gis\ProjectsBundle\Classes\Filter\C4GBrickFilterParams;
use Contao\Database;

;

class C4GBrickTiles
{
    public static function addButtonArray(C4GBrickButton $button, $parentCaption = null)
    {
        if ($button->getType() == C4GBrickConst::BUTTON_PARENT && $parentCaption) {
            return [
                'action' => $button->getAction() . ':-1',
                'class' => 'c4gGuiAction',
                'type' => 'send',
                'text' => sprintf($button->getCaption(), $parentCaption),
            ];
        }

        return [
                'action' => $button->getAction() . ':-1',
                'class' => 'c4gGuiAction',
                'type' => 'send',
                'text' => $button->getCaption(),
            ];
    }
    /**
     * @param $showGroupButton
     * @param $showAddButton
     * @return array
     */
    private static function getDialogButtons(C4GBrickListParams $listParams, $parentCaption)
    {
        $result = [];

        if ($listParams->checkButtonVisibility(C4GBrickConst::BUTTON_GROUP)) {
            $group_button = $listParams->getButton(C4GBrickConst::BUTTON_GROUP);
            $result[] = static::addButtonArray($group_button);
        }

        if ($listParams->checkButtonVisibility(C4GBrickConst::BUTTON_PROJECT)) {
            $project_button = $listParams->getButton(C4GBrickConst::BUTTON_PROJECT);
            $result[] = static::addButtonArray($project_button);
        }

        if ($listParams->checkButtonVisibility(C4GBrickConst::BUTTON_PARENT)) {
            $parent_button = $listParams->getButton(C4GBrickConst::BUTTON_PARENT);
            $result[] = static::addButtonArray($parent_button, $parentCaption);
        }

        if ($listParams->checkButtonVisibility(C4GBrickConst::BUTTON_ADD)) {
            $add_button = $listParams->getButton(C4GBrickConst::BUTTON_ADD);
            $result[] = static::addButtonArray($add_button);
        }

        if ($listParams->checkButtonVisibility(C4GBrickConst::BUTTON_FILTER)) {
            $filter_button = $listParams->getButton(C4GBrickConst::BUTTON_FILTER);
            $result[] = static::addButtonArray($filter_button);
        }

        if ($listParams->checkButtonVisibility(C4GBrickConst::BUTTON_CLICK)) {
            $filter_button = $listParams->getButton(C4GBrickConst::BUTTON_CLICK);
            $result[] = static::addButtonArray($filter_button);
        }

        return $result;
    }

    public static function translateBool($bool)
    {
        if ($bool == '1') {
            return $GLOBALS['TL_LANG']['FE_C4G_LIST']['TRUE'];
        }

        return $GLOBALS['TL_LANG']['FE_C4G_LIST']['FALSE'];
    }

    /**
     * @param $fieldList
     * @param $data
     * @param $condition
     * @return bool
     */
    public function getOptions($fieldList, $element, $column)
    {
        //$fieldName = $column->getFieldName();
        $conditions = $column->getCondition();
        if ($conditions) {
            foreach ($conditions as $condition) {
                $conditionFieldName = $condition->getFieldName();
                $conditionValue = $condition->getValue();
                $conditionType = $condition->getType();

                if ($conditionType == C4GBrickConditionType::VALUESWITCH) {
                    if ($conditionValue != $element->$conditionFieldName) {
                        foreach ($fieldList as $field) {
                            $fieldConditions = $field->getCondition();
                            if ($fieldConditions) {
                                foreach ($fieldConditions as $fieldCondition) {
                                    $fieldConditionFieldName = $fieldCondition->getFieldName();
                                    $fieldConditionValue = $fieldCondition->getValue();
                                    $fieldConditionType = $fieldCondition->getType();

                                    if (($fieldConditionType == C4GBrickConditionType::VALUESWITCH)
                                        && ($fieldConditionFieldName == $conditionFieldName)) {
                                        if ($fieldConditionValue == $element->$conditionFieldName) {
                                            return $field->getOptions();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $column->getOptions();
    }

    /**
     * @param $fieldList
     */
    public function getTileClasses($fieldList, $values)
    {
        $result = '';
        foreach ($fieldList as $field) {
            if ($field->isTileClass()) {
                if ($field->getTileClassTable() && $field->getTileClassField()) {
                    // get dataset from other table
                    $tablename = $field->getTileClassTable();
                    $fieldName = $field->getFieldName();
                    $otherFieldName = $field->getTileClassField();
                    $db = Database::getInstance();
                    $dbResult = $db->prepare("SELECT $otherFieldName FROM $tablename WHERE id = ?")
                        ->execute($values->$fieldName)
                        ->fetchAllAssoc();
                    $dataset = $dbResult[0];
                    $result .= $dataset[$otherFieldName] . ' ';
                } else {
                    $fieldName = $field->getFieldName();
                    $result .= $values->$fieldName . ' ';
                }
            }
        }

        return $result;
    }

    /**
     * deletes all elements with delete flag
     * @param $fieldList
     * @param $tableElements
     */
    public function deleteElementsPerFlag($fieldList, $tableElements)
    {
        $newTableElements = [];

        $deleteFlag = null;
        $publishedFlag = null;
        foreach ($fieldList as $field) {
            $fieldName = $field->getFieldName();
            if ($field->getType() == C4GBrickFieldType::FLAG_DELETE) {
                $deleteFlag = $fieldName;

                break;
            }
            if ($field->getType() == C4GBrickFieldType::FLAG_PUBLISHED) {
                $publishedFlag = $fieldName;

                break;
            }
        }

        if (($deleteFlag) || ($publishedFlag)) {
            foreach ($tableElements as $key => $element) {
                if ($deleteFlag) {
                    if ($element->$fieldName == false) {
                        $newTableElements[] = $element;
                    }
                } else {
                    if ($element->$fieldName == true) {
                        $newTableElements[] = $element;
                    }
                }
            }
        } else {
            $newTableElements = $tableElements;
        }

        return $newTableElements;
    }

    private function addC4GCheckboxFilter(C4GBrickFilterParams $filterParams, $fieldList, $tableElements)
    {
        if ($filterParams && $fieldList && $tableElements) {
            $result = '';
            $filterName = $filterParams->getHeadtext();
            $filterType = 'checkbox';
            //ToDo: $filterParams muss um checkboxfilter erweitert werden
            $tableFields = $filterParams;

            $result .= '<div class="c4g_filter ' . $filterType . '"><label>' . $filterName . '</label>';

            foreach ($tableFields as $tableName => $translate) {
                foreach ($fieldList as $field) {
                    if (($field->getFieldName() == $tableName) && ($field->isSortColumn() == true)) {
                        $result .= '<label for="c4g_' . $filterType . '">' . $translate . '</label>'
                            . '<input type="checkbox" name="c4g_' . $filterType . '">';
                    }
                }
            }
            $result .= '</div>';
        } else {
            return '';
        }

        return $result;
    }

    private function addC4GSelectFilter($filter, $fieldList, $tableElements)
    {
        return '';
    }

    private function addC4GRangeFilter($filter, $fieldList, $tableElements)
    {
        return '';
    }

    /**
     * @param $listCaption
     * @param C4GBrickField[] $fieldList
     * @param $tableElements
     * @param $key
     * @return array
     */
    public static function showC4GTableList(
        $listCaption,
        $database,
        $listHeadline,
        $fieldList,
        $tableElements,
        $key,
        C4GBrickListParams $listParams,
        $parentCaption,
        $withLabels
    ) {
        if ($withLabels) {
            if (!$tableElements) {
                $tableElements = [];
            } else {
                $tableElements = C4GBrickTiles::deleteElementsPerFlag($fieldList, $tableElements);
            }

            $view = '<div class="' . C4GBrickConst::CLASS_TILES . ' ui-widget ui-widget-content ui-corner-bottom" style="display: inline-flex; justify-content: center; flex-wrap: wrap; flex-flow: row wrap;">' . C4GHTMLFactory::lineBreak();

            $elementTimer = 1;

            $button = '';
            $search = '';
            $filterResult = '';

            foreach ($tableElements as $element) {
                $fields = [];
                $cnt = 0;
                if ($listParams->isWithFunctionCallOnClick()) {
                    $action = C4GBrickActionType::ACTION_BUTTONCLICK . ':'
                        . $listParams->getOnClickFunction() . ':' . $element->id;
                } elseif ($listParams->isWithDetails()) {
                    $action = C4GBrickActionType::ACTION_CLICK . ':'
                        . $element->id; //hier wird in der versteckten Spalte 1 die ClickAction gesetzt
                } else {
                    $action = '';
                }
                $cnt++;
//                $action = $listParams->isWithDetails() ? C4GBrickActionType::ACTION_CLICK . ':' . $element->id : '';
                if ($listParams->isWithHoverText()) {
                    // currently only supported when coming from the module data
                    $hovertext = $element->hovertext;
                } else {
                    $hovertext = '';
                }

                $tileClasses = C4GBrickTiles::getTileClasses($fieldList, $element);

                $view .= '<a class="' . $tileClasses . 'c4gGuiAction c4gGuiButton c4g_tile_button" title="' . $hovertext .
                    '"   data-action="' . $action . '" role="button" style="order: ' . $elementTimer . ';">';

                $elementTimer++;
                foreach ($fieldList as $column) {
                    $fieldName = $column->getFieldName();
                    $fieldType = $column->getType();

                    $fieldTitle = '<label class="c4g_label ' . $fieldType . '" >' . $column->getTitle() . '</label>';
                    if ($element && $column->getExternalModel() &&
                        $column->getExternalIdField() && (!$listParams->isWithModelListFunction())) {
                        $extModel = $column->getExternalModel();
                        $extIdFieldName = $column->getExternalIdField();
                        $extFieldName = $column->getExternalFieldName();
                        $extId = $element->$extIdFieldName;
                        $extCallbackFunction = $column->getExternalCallBackFunction();
                        if ($extId && ($extId > 0)) {
                            if ($extFieldName && ($extFieldName != '')) {
                                $extSearchValue = $column->getExternalSearchValue();
                                if ($extSearchValue) {
                                    $tableName = $extModel::getTableName();
                                    $fieldName = $column->getFieldName();
                                    $sortField = $column->getExternalSortField();
                                    $element = $database->prepare("SELECT * FROM `$tableName` WHERE " .
                                        "`$extFieldName`='$extSearchValue' AND `$fieldName`='$extId' ORDER BY " .
                                        " `$tableName`.`$sortField` DESC LIMIT 1 ")->execute();
                                } else {
                                    $element = $extModel::findBy($extFieldName, $extId);
                                }
                            } else {
                                $element = $extModel::findByPk($extId);
                            }
                        }
                        if ($extCallbackFunction) {
                            $extModel::$extCallbackFunction($element, $database);
                        }
                    }
                    if (!$listParams->isWithModelListFunction() && $element->$fieldName) {
                        $view .= '<div class="c4g_tile ' . $fieldType . ' field ' . $fieldName . '">';

                        if ($cnt == 0) {
                            $cnt++;
                        } else {
                            if ($column->isTableColumn()) {
                                if ($column instanceof C4GSelectField) {
                                    $view .= $fieldTitle . '<div class="c4g_tile value">' .
                                        C4GBrickCommon::translateSelectOption(
                                            $element->$fieldName,
                                            C4GBrickTiles::getOptions($fieldList, $element, $column)) . '</div>';
                                } else {
                                    $view .= $column->getC4GTileField($fieldTitle, $element);
                                }
                                $cnt++;
                            }
                        }
                        $view .= '</div>';
                    } elseif ($listParams->isWithModelListFunction()) {
                        //Check if $fieldName is a property of $element and is not the id-Field
                        if ($column->isTableColumn()) {
                            $view .= '<div class="c4g_tile_style">';
                            if (property_exists($element, $fieldName) && $fieldName != 'id' && $element->$fieldName) {
                                $view .= $fieldTitle . '<div class="c4g_tile value">' . $element->$fieldName . '</div>';
                            } elseif ($column->isShowIfEmpty() && !($column instanceof C4GMoreButtonField)) {
                                $view .= $fieldTitle . '<div class="c4g_tile value"></div>';
                            } elseif ($column instanceof C4GMoreButtonField) {
                                $view .= $column->getC4GTileField($fieldTitle, $element);
                            }
                            $view .= '</div>';
                        }
                    }
                }
                $view .= '</a>';
            }

            $view .= '</div>';
        } else /*WITHOUT LABELS*/ {
            if (!$tableElements) {
                $tableElements = [];
            } else {
                $tableElements = C4GBrickTiles::deleteElementsPerFlag($fieldList, $tableElements);
            }

            $view = '<div class="' . C4GBrickConst::CLASS_TILES . ' ui-widget ui-widget-content ui-corner-bottom" style="display: flex; justify-content: center; flex-wrap: wrap; flex-flow: row wrap;">' . C4GHTMLFactory::lineBreak();

            $elementTimer = 1;

            $button = '';
            $search = '';
            $filterResult = '';

            foreach ($tableElements as $element) {
                $fields = [];
                $cnt = 0;
                $tileClasses = C4GBrickTiles::getTileClasses($fieldList, $element);
                $view .= '<a class="' . $tileClasses . 'c4gGuiAction c4gGuiButton c4g_tile_button" data-action="' .
                    C4GBrickActionType::ACTION_CLICK . ':' . $element->id . '" role="button" style="order: '
                    . $elementTimer . ';">';

                $elementTimer++;
                foreach ($fieldList as $column) {
                    $fieldName = $column->getFieldName();
                    $fieldType = $column->getType();

                    if ($element->$fieldName) {
                        $view .= '<div class="c4g_tile ' . $fieldType . ' field ' . $fieldName . '">';

                        if ($cnt == 0) {
                            $cnt++;
                        } else {
                            if ($column->isTableColumn()) {
                                // since this is the same as above just without labels, this should do the job
                                if ($column instanceof C4GSelectField) {
                                    $view .= '<div class="c4g_tile value">' . C4GBrickCommon::translateSelectOption(
                                        $element->$fieldName,
                                        C4GBrickTiles::getOptions($fieldList, $element, $column)) . '</div>';
                                } else {
                                    $view .= $column->getC4GTileField('', $element);
                                }
                                $cnt++;
                            }
                        }
                        $view .= '</div>';
                    }
                }
                $view .= '</a>';
            }
            $view .= '</div>';
        }

        //count($tableElements) ist immer 1, da $tableElements ein Objekt ist! (bei Auswertung über modellistfunction)
        if (count($tableElements) >= $listParams->getDisplayLength()) {
            $search = '<div class="c4g_tile search" ><input class="c4g_filter search" type="search" name="tilesearch" onblur="C4GSearchTiles(this)" onkeyup="C4GSearchTiles(this)"><label>' . $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['LOG_ENTRY_TYPE']['search'] . '</label></div>';
            $button .= '<div class="c4g_tile sorter"><button data-lang-asc="' . $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['LOG_ENTRY_TYPE']['asc'] . '" data-lang-desc="' . $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['LOG_ENTRY_TYPE']['desc'] . '" type="button" onclick="tileSort(this)">' . $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['LOG_ENTRY_TYPE']['asc'] . '</button></div>';
        }

        return [
            'dialogtype' => 'html',
            'dialogdata' => $view,
            'dialogoptions' => C4GUtils::addDefaultDialogOptions([
                'title' => '<div class="c4g_tile_headline" >' .
                    $listCaption .
                    '</div>' . $button . $search . '<div class="c4g_tile filter items">' . $filterResult . '</div>',
                'modal' => true,
            ]),
            'dialogid' => C4GBrickActionType::IDENTIFIER_LIST . $key,
            'dialogstate' => C4GBrickActionType::IDENTIFIER_LIST . $key,
            'dialogbuttons' => C4GBrickTiles::getDialogButtons($listParams, $parentCaption),
        ];
    }
}
