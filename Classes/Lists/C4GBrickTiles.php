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

namespace con4gis\ProjectBundle\Classes\Lists;;


class C4GBrickTiles
{
    public static function addButtonArray(C4GBrickButton $button, $parentCaption = null)
    {
        if ($button->getType() == C4GBrickConst::BUTTON_PARENT && $parentCaption) {
            return array(
                'action'  => $button->getAction() . ':-1',
                'class' => 'c4gGuiAction',
                'type'  => 'send',
                'text'  => sprintf($button->getCaption(), $parentCaption)
            );
        }
        else {
            return array(
                'action'  => $button->getAction() . ':-1',
                'class' => 'c4gGuiAction',
                'type'  => 'send',
                'text' => $button->getCaption()
            );
        }
    }
    /**
     * @param $showGroupButton
     * @param $showAddButton
     * @return array
     */
    private static function getDialogButtons(C4GBrickListParams $listParams, $parentCaption) {
        $result = array();

        if ($listParams->checkButtonVisibility(\c4g\projects\C4GBrickConst::BUTTON_GROUP)) {
            $group_button = $listParams->getButton(\c4g\projects\C4GBrickConst::BUTTON_GROUP);
            $result[] = static::addButtonArray($group_button);
        }

        if ($listParams->checkButtonVisibility(\c4g\projects\C4GBrickConst::BUTTON_PROJECT)) {
            $project_button = $listParams->getButton(\c4g\projects\C4GBrickConst::BUTTON_PROJECT);
            $result[] = static::addButtonArray($project_button);
        }

        if ($listParams->checkButtonVisibility(\c4g\projects\C4GBrickConst::BUTTON_PARENT)) {
            $parent_button = $listParams->getButton(\c4g\projects\C4GBrickConst::BUTTON_PARENT);
            $result[] = static::addButtonArray($parent_button, $parentCaption);
        }

        if ($listParams->checkButtonVisibility(\c4g\projects\C4GBrickConst::BUTTON_ADD)) {
            $add_button = $listParams->getButton(\c4g\projects\C4GBrickConst::BUTTON_ADD);
            $result[] = static::addButtonArray($add_button);
        }

        if ($listParams->checkButtonVisibility(\c4g\projects\C4GBrickConst::BUTTON_FILTER)) {
            $filter_button = $listParams->getButton(\c4g\projects\C4GBrickConst::BUTTON_FILTER);
            $result[] = static::addButtonArray($filter_button);
        }

        if ($listParams->checkButtonVisibility(\c4g\projects\C4GBrickConst::BUTTON_CLICK)) {
            $filter_button = $listParams->getButton(\c4g\projects\C4GBrickConst::BUTTON_CLICK);
            $result[] = static::addButtonArray($filter_button);
        }

        return $result;
    }

    public static function translateBool($bool)
    {
        if ($bool == '1') {
            return $GLOBALS['TL_LANG']['FE_C4G_LIST']['TRUE'];
        } else {
            return $GLOBALS['TL_LANG']['FE_C4G_LIST']['FALSE'];
        }
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
            foreach($conditions as $condition) {
                $conditionFieldName = $condition->getFieldName();
                $conditionValue     = $condition->getValue();
                $conditionType      = $condition->getType();

                if ($conditionType == C4GBrickConditionType::VALUESWITCH) {
                    if ($conditionValue != $element->$conditionFieldName) {
                        foreach ($fieldList as $field) {
                            $fieldConditions = $field->getCondition();
                            if ($fieldConditions) {
                                foreach($fieldConditions as $fieldCondition) {
                                    $fieldConditionFieldName = $fieldCondition->getFieldName();
                                    $fieldConditionValue     = $fieldCondition->getValue();
                                    $fieldConditionType      = $fieldCondition->getType();

                                    if ( ($fieldConditionType == C4GBrickConditionType::VALUESWITCH) && ($fieldConditionFieldName == $conditionFieldName)) {
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
     * deletes all elements with delete flag
     * @param $fieldList
     * @param $tableElements
     */
    public function deleteElementsPerFlag($fieldList, $tableElements) {
        $newTableElements = array();

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

        if ( ($deleteFlag) || ($publishedFlag)) {
            foreach ($tableElements as $key=>$element) {
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
        if($filterParams && $fieldList && $tableElements)
        {
            $result = '';
            $filterName = $filterParams->getHeadtext();
            $filterType = 'checkbox';
            //ToDo: $filterParams muss um checkboxfilter erweitert werden
            $tableFields = $filterParams;

            $result .= '<div class="c4g_filter '.$filterType.'"><label>'.$filterName.'</label>';

            foreach($tableFields as $tableName => $translate)
            {
                foreach($fieldList as $field)
                {
                    if($field->getFieldName() == $tableName AND $field->isSortColumn() == true)
                    {
                        $result .= '<label for="c4g_'. $filterType .'">'.$translate.'</label>'.'<input type="checkbox" name="c4g_'. $filterType .'">';
                    }
                }
            }

            $result .= '</div>';
        }
        else
        {
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
    public static function showC4GTableList($listCaption, $database, $listHeadline, $fieldList, $tableElements, $key, C4GBrickListParams $listParams , $captionField, $parentCaption, $withLabels)
    {
        if($withLabels)
        {
            if (!$tableElements)
            {
                $tableElements = array();
            }
            else
            {
                $tableElements = C4GBrickTiles::deleteElementsPerFlag($fieldList, $tableElements);
            }

            $view = '<div class="' . C4GBrickConst::CLASS_TILES . ' ui-widget ui-widget-content ui-corner-bottom" style="display: inline-flex; justify-content: center; flex-wrap: wrap; flex-flow: row wrap;">'.\c4g\C4GHTMLFactory::lineBreak();

            $elementTimer = 1;

            $button = '';
            $search = '';
            $filterResult = '';

            foreach ($tableElements as $element)
            {
                $fields = array();
                $cnt = 0;
                if ($listParams->isWithFunctionCallOnClick()) {
                    $action = \c4g\projects\C4GBrickActionType::ACTION_BUTTONCLICK . ':' . $listParams->getOnClickFunction() . ':' . $element->id;
                } else if ($listParams->isWithDetails()) {
                    $action = \c4g\projects\C4GBrickActionType::ACTION_CLICK . ':' . $element->id; //hier wird in der versteckten Spalte 1 die ClickAction gesetzt
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
                $view .= '<a class="c4gGuiAction c4gGuiButton c4g_tile_button" title="'. $hovertext .'"   data-action="'. $action .'" role="button" style="order: '.$elementTimer.';">';

                $elementTimer++;
                foreach ($fieldList as $column)
                {
                    $fieldName = $column->getFieldName();
                    $fieldType = $column->getType();
                    $fieldTitle = '<label class="c4g_label '.$fieldType.'" >'.$column->getTitle().'</label>';
                    if ($element && $column->getExternalModel() && $column->getExternalIdField() && (!$listParams->isWithModelListFunction())) {
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
                                    $element = $database->prepare("SELECT * FROM `$tableName` WHERE `$extFieldName`='$extSearchValue' AND `$fieldName`='$extId' ORDER BY `$tableName`.`$sortField` DESC LIMIT 1 ")->execute();
                                } else {
                                    $element = $extModel::findBy($extFieldName, $extId);
                                }
                            } else {
                                $element = $extModel::findByPk($extId);
                            }
                        }
                        if ($extCallbackFunction) {
                            $extModel::$extCallbackFunction($element, $database/*, $column->getFieldName(), $extId, $listParams*/);
                        }
                    }
                    if(!$listParams->isWithModelListFunction() && $element->$fieldName)
                    {
                        $view .= '<div class="c4g_tile '.$fieldType.' field '.$fieldName.'">';

                        if ($cnt == 0)
                        {
                            $cnt++;
                        }
                        else
                        {
                            if ($column->isTableColumn())
                            {
                                if ($column instanceof C4GSelectField) {
                                    $view .= $fieldTitle .  '<div class="c4g_tile value">' . \c4g\projects\C4GBrickCommon::translateSelectOption($element->$fieldName, C4GBrickTiles::getOptions($fieldList, $element, $column)) . '</div>';
                                } else {
                                    $view .= $column->getC4GTileField($fieldTitle, $element);
                                }
                                $cnt++;
                            }

                        }
                        $view .= '</div>';
                    } else if ($listParams->isWithModelListFunction()) {
                        //Check if $fieldName is a property of $element and is not the id-Field
                        if ($column->isTableColumn()) {
                            if (property_exists($element, $fieldName) && $fieldName != 'id' && $element->$fieldName) {
                                $view .= $fieldTitle . '<div class="c4g_tile value">' . $element->$fieldName . '</div>';
                            } else if ($column->isShowIfEmpty()) {
                                $view .= $fieldTitle . '<div class="c4g_tile value"></div>';
                            }
                        }
                    }
                }

                $view .= '</a>';
            }

            $view .= '</div>';
        }
        else /*WITHOUT LABELS*/
        {
            if (!$tableElements)
            {
                $tableElements = array();
            }
            else
            {
                $tableElements = C4GBrickTiles::deleteElementsPerFlag($fieldList, $tableElements);
            }

            $view = '<div class="' . C4GBrickConst::CLASS_TILES . ' ui-widget ui-widget-content ui-corner-bottom" style="display: flex; justify-content: center; flex-wrap: wrap; flex-flow: row wrap;">'.\c4g\C4GHTMLFactory::lineBreak();

            $elementTimer = 1;

            $button = '';
            $search = '';
            $filterResult = '';

            foreach ($tableElements as $element)
            {
                $fields = array();
                $cnt = 0;
                $view .= '<a class="c4gGuiAction c4gGuiButton c4g_tile_button" data-action="'.C4GBrickActionType::ACTION_CLICK . ':' . $element->id.'" role="button" style="order: '.$elementTimer.';">';

                $elementTimer++;
                foreach ($fieldList as $column)
                {
                    $fieldName = $column->getFieldName();
                    $fieldType = $column->getType();

                    if($element->$fieldName)
                    {
                        $view .= '<div class="c4g_tile '.$fieldType.' field '.$fieldName.'">';

                        if ($cnt == 0)
                        {
                            $cnt++;
                        }
                        else
                        {
                            if ($column->isTableColumn())
                            {
                                // since this is the same as above just without labels, this should do the job
                                if ($column instanceof C4GSelectField) {
                                    $view .= '<div class="c4g_tile value">' . \c4g\projects\C4GBrickCommon::translateSelectOption($element->$fieldName, C4GBrickTiles::getOptions($fieldList, $element, $column)) . '</div>';
                                } else {
                                    $view .= $column->getC4GTileField('', $element);
                                }

//                                switch ($fieldType) {
//                                    case C4GBrickFieldType::FILE:
//                                    case C4GBrickFieldType::IMAGE:
//                                        $file = $element->$fieldName;
//                                        if (!is_string($file)) {
//                                            $file = '';
//                                        }
//                                        $fileObject = C4GBrickCommon::loadFile($file);
//                                        if ($fileObject) {
//                                            switch($column->getFileTypes())
//                                            {
//                                                case C4GBrickFileType::IMAGES_ALL:
//                                                case C4GBrickFileType::IMAGES_JPG:
//                                                case C4GBrickFileType::IMAGES_PNG:
//                                                case C4GBrickFileType::IMAGES_PNG_JPG:
//                                                    if($fileObject->path[0] == '/')
//                                                    {
//
//                                                        $view .= '<div class="c4g_tile value">' . '<img src="' .substr ($fileObject->path, 1 ). '" width="'.$column->getSize().'" height="'.$column->getSize().'">' . '</div>';
//                                                    }
//                                                    else
//                                                    {
//                                                        $view .= '<div class="c4g_tile value">' . '<img src="' .$fileObject->path. '" width="'.$column->getSize().'" height="'.$column->getSize().'">' . '</div>';
//
//                                                    }
//                                            }
//                                        }
//                                        else
//                                        {
//                                            switch($column->getFileTypes())
//                                            {
//                                                case C4GBrickFileType::IMAGES_ALL:
//                                                case C4GBrickFileType::IMAGES_JPG:
//                                                case C4GBrickFileType::IMAGES_PNG:
//                                                case C4GBrickFileType::IMAGES_PNG_JPG:
//                                                    $view .= '<div class="c4g_tile value">' . '<img src="system/modules/con4gis_projects/assets/missing.png">' . '</div>';
//                                                    break;
//                                                default:
//                                                    $view .= '<div class="c4g_tile value">' . '<div class="error"></div>' . '</div>';
//                                            }
//                                        }
//                                        break;
//                                    case C4GBrickFieldType::SELECT:
//                                        $view .= '<div class="c4g_tile value">' . \c4g\projects\C4GBrickCommon::translateSelectOption($element->$fieldName, C4GBrickTiles::getOptions($fieldList, $element, $column)) . '</div>';
//                                        break;
//                                    case C4GBrickFieldType::TIMESTAMP:
//                                        $view .= '<div class="c4g_tile value">' . $element->$fieldName . ' ('.date('d.m.Y H:m:s',$element->$fieldName).')' . '</div>';
//                                        break;
//                                    case C4GBrickFieldType::DATE:
//                                        $date = $element->$fieldName;
//                                        $view .= '<div class="c4g_tile value">' . date('d.m.Y',$date) . '</div>';
//                                        break;
//                                    case C4GBrickFieldType::TIME:
//                                        $date = $element->$fieldName;
//                                        $view .= '<div class="c4g_tile value">' . date('H:i',$date) . '</div>';
//                                        break;
//                                    case C4GBrickFieldType::HEADLINE:
//                                        $view .= '<div class="c4g_tile value">' . '<h3>'.$column->getTitle().'</h3>' . '</div>';
//                                        break;
//                                    case C4GBrickFieldType::BOOL:
//                                        $view .= '<div class="c4g_tile value">' . C4GBrickTiles::translateBool($element->$fieldName) . '</div>';
//                                        break;
//                                    case C4GBrickFieldType::TEXTAREA:
//                                        $view .= '<div class="c4g_tile value">' . $element->$fieldName . '</div>';
//                                        break;
//                                    Default:
//                                        $view .= $element->$fieldName;
//                                        break;
//                                }
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
        if(count($tableElements) >= $listParams->getDisplayLength())
        {
            $search = '<div class="c4g_tile search" ><input class="c4g_filter search" type="search" name="tilesearch" onblur="C4GSearchTiles(this)" onkeyup="C4GSearchTiles(this)"><label>'.$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['LOG_ENTRY_TYPE']['search'].'</label></div>';
            $button .= '<div class="c4g_tile sorter"><button data-lang-asc="'.$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['LOG_ENTRY_TYPE']['asc'].'" data-lang-desc="'.$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['LOG_ENTRY_TYPE']['desc'].'" type="button" onclick="tileSort(this)">'.$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['LOG_ENTRY_TYPE']['asc'].'</button></div>';
        }

//        if($listParams->getFilterParams())
//        {
//            $filterParams = $listParams->getFilterParams();
//            if ($filterParams->ifWithRangeFilter()) {
//                $filterResult .= C4GBrickTiles::addC4GRangeFilter($filterParams, $fieldList, $tableElements);
//            }
//            if ($filterParams->isWithCheckboxFilter()) {
//                $filterResult .= C4GBrickTiles::addC4GCheckboxFilter($filterParams, $fieldList, $tableElements);
//            }
//            if ($filterParams->isWithSelectFilter()) {
//                $filterResult .= C4GBrickTiles::addC4GSelectFilter($filterParams, $fieldList, $tableElements);
//            }
//
//        }

        return array
        (
            'dialogtype'    => 'html',
            'dialogdata'    => $view,
            'dialogoptions' => \c4g\C4GUtils::addDefaultDialogOptions(array
            (
                'title' => '<div class="c4g_tile headline" >'.\c4g\C4GHTMLFactory::headline($listCaption). '</div>' . $button . $search . '<div class="c4g_tile filter items">' . $filterResult . '</div>',
                'modal' => true
            )),
            'dialogid'      => C4GBrickActionType::IDENTIFIER_LIST.$key,
            'dialogstate'   => C4GBrickActionType::IDENTIFIER_LIST .$key,
            'dialogbuttons' => C4GBrickTiles::getDialogButtons($listParams, $parentCaption),
        );
    }
}