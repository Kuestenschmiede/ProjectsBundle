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

namespace con4gis\ProjectsBundle\Classes\Lists;


use con4gis\CoreBundle\Resources\contao\classes\C4GUtils;
use con4gis\MapsBundle\Resources\contao\models\C4gMapsModel;
use con4gis\ProjectsBundle\Classes\Actions\C4GBrickActionType;
use con4gis\ProjectsBundle\Classes\Buttons\C4GBrickButton;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Conditions\C4GBrickConditionType;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GDateTimeLocationField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GDecimalField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GGeopickerField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GSelectField;
use con4gis\CoreBundle\Resources\contao\classes\C4GHTMLFactory;
use Contao\ContentModel;

class C4GBrickList
{
    public static function addButtonArray(C4GBrickButton $button, $parentCaption = null)
    {
        if ($button->getType() == C4GBrickConst::BUTTON_PARENT && $parentCaption) {
            return array(
                'id' => $button->getAction() . ':-1',
                'text' => sprintf($button->getCaption(), $parentCaption),
                'tableSelection' => false
            );
        } /*else if ($button->getType() == C4GBrickConst::BUTTON_PRINTLIST) {
                return array(
                    'extend' => 'pdf',
                    'text'  => sprintf($button->getCaption(), $parentCaption),
                    'tableSelection'    => false,
            );
        }*/ else {
            return array(
                'id' => $button->getAction() . ':-1',
                'text' => $button->getCaption(),
                'tableSelection' => false
            );
        }
    }

    /**
     * @param $showGroupButton
     * @param $showAddButton
     * @return array
     */
    public static function getDialogButtons(C4GBrickListParams $listParams, $parentCaption) {
        $result = array();

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

        if ($listParams->checkButtonVisibility(C4GBrickConst::BUTTON_PUBLIC_PARENT)) {
            $parent_button = $listParams->getButton(C4GBrickConst::BUTTON_PUBLIC_PARENT);
            $result[] = static::addButtonArray($parent_button, $parentCaption);
        }

        if ($listParams->checkButtonVisibility(C4GBrickConst::BUTTON_ADD)) {
            $add_button = $listParams->getButton(C4GBrickConst::BUTTON_ADD);
            $result[] = static::addButtonArray($add_button);
        }

        if ($listParams->checkButtonVisibility(C4GBrickConst::BUTTON_IMPORT)) {
            $import_button = $listParams->getButton(C4GBrickConst::BUTTON_IMPORT);
            $result[] = static::addButtonArray($import_button);
        }

        if ($listParams->checkButtonVisibility(C4GBrickConst::BUTTON_FILTER)) {
            $filter_button = $listParams->getButton(C4GBrickConst::BUTTON_FILTER);
            $result[] = static::addButtonArray($filter_button);
        }

        if ($listParams->checkButtonVisibility(C4GBrickConst::BUTTON_TOGGLE_METHOD_FILTER)) {
            $filter_button = $listParams->getButton(C4GBrickConst::BUTTON_TOGGLE_METHOD_FILTER);
            $result[] = static::addButtonArray($filter_button);
        }

        if ($listParams->checkButtonVisibility(C4GBrickConst::BUTTON_REDIRECT)) {
            $redirect_button = $listParams->getButton(C4GBrickConst::BUTTON_REDIRECT);
            $result[] = static::addButtonArray($redirect_button);
        }

        if ($listParams->checkButtonVisibility(C4GBrickConst::BUTTON_CLICK)) {
            $filter_button = $listParams->getButton(C4GBrickConst::BUTTON_CLICK);
            $result[] = static::addButtonArray($filter_button);
        }

        return $result;
    }

    public static function translateBool($bool) {
        if ( ($bool == '1' || $bool == true) && $bool != 'false') {
            return $GLOBALS['TL_LANG']['FE_C4G_LIST']['TRUE'];
        } else {
            return $GLOBALS['TL_LANG']['FE_C4G_LIST']['FALSE'];
        }
    }

    /**
     * deletes all elements with delete flag
     * @param $fieldList
     * @param $tableElements
     */
    public static function deleteElementsPerFlag($fieldList, $tableElements) {
        $newTableElements = array();

        $deleteFlag = null;
        $publishedFlag = null;
        foreach ($fieldList as $field) {
            $fieldName = $field->getFieldName();
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

    /**
     * Kann mit besserer DataTables-Einbindung umgebaut werden. Über die jetzige Komponente greift bVisible nicht
     * zweimal :( Das Ganze hier sorgt dafür, dass eine Spalte entfernt wird, wenn zwei Felder über eine Condition
     * geschaltet werden. Siehe auch getOptions für abhängige Selektfelder.
     *
     * @param $data
     * @return mixed
     */
    private static function hideDuplicatedColumns($data) {
        $titles = array();
        $keys = array();

        foreach ($data['aoColumnDefs'] as $columnKey => $column) {
            $columnTitle = $column['sTitle'];

            $found = false;
            foreach($titles as $title) {
                if ($title == $columnTitle) {
                    $keys[] = $columnKey;
                    break;
                }
            }

            if (!$found) {
                $titles[] = $columnTitle;
            }
        }

        foreach ($keys as $key) {
            unset($data['aoColumnDefs'][$key]);

            if ($data['aaData']) {
                foreach($data['aaData'] as $rowKey => $row) {
                    unset($data['aaData'][$rowKey][$key]);
                }
            }
        }

        //neue Indexs setzen
        $newColumnDefs = array();
        $cnt=0;
        if ($data['aoColumnDefs']) {
            foreach ($data['aoColumnDefs'] as $columnKey => $columnDef) {
                $columnDef['aTargets'] = array($cnt);
                $newColumnDefs[] = $columnDef;
                $cnt++;
            }
        }

        $data['aoColumnDefs'] = $newColumnDefs;

        if (($data) && ($data['aaData'])) {
            foreach($data['aaData'] as $rowKey => $row) {
                $newRowData = array();
                foreach($data['aaData'][$rowKey] as $columnKey => $column) {
                    $newRowData[] = $column;
                }
                $data['aaData'][$rowKey] = $newRowData;
            }

        }

        return $data;
    }
    /**
     * @param $fieldList
     * @param $data
     * @param $condition
     * @return bool
     */
    public static function getOptions($fieldList, $element, $column)
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

    //ToDo !!! Die Button als array übergeben
    /**
     * @param $listCaption
     * @param C4GBrickField[] $fieldList
     * @param $tableElements
     * @param $key
     * @return array
     */
    public static function showC4GTableList(
        $listCaption, $database, $content, $listHeadline,  $fieldList, $tableElements, $key,
        $parentCaption, $listParams)
    {
        if (!$tableElements) {
            $tableElements = array();
        } else {
            $tableElements = C4GBrickList::deleteElementsPerFlag($fieldList, $tableElements);
        }

        //Hier werden die Werte für jQuery Datatables Bibliothek gesetzt. Die Version ist leider veraltet, sodass
        //die Dokumentation nicht mehr passt.
        //der erste Buchstabe steht für den Typ -> s=string, b=boolean, i=int, usw.(ao array object?)

        // define datatable
        $data = array();
        //$data['iDisplayIndex'] = $key;
        $data['aoColumnDefs']    = array();
        $data['bJQueryUI']       = $listParams->isWithJQueryUI();
        $data['bScrollCollapse'] = true;
        $data['bStateSave']      = true;
        $data['bPaginate']       = $listParams->isPaginate();
        $data['bLengthChange']   = $listParams->isLengthChange();
        $data['bFilter']         = $listParams->isFilter();
        $data['bInfo']           = $listParams->isInfo();
        $data['bDeferRender']    = true;
        $data['bScroller']       = true;
        $data['responsive']      = true;

        if ($listParams->isWithExportButtons()) {
            if ($listParams->isWithJQueryUI()) {
                $data['sDom'] =
                    '<"fg-toolbar ui-toolbar ui-widget-header ui-helper-clearfix ui-corner-tl ui-corner-tr"lfr>'.
                    't'.
                    '<"fg-toolbar ui-toolbar ui-widget-header ui-helper-clearfix ui-corner-bl ui-corner-br"ip>B';
            } else {
                $data['sDom'] = '<"H"lfr>t<"F"ip>B';
            }

            if ($listParams->getExportButtons()) {
                $data['buttons'] = $listParams->getExportButtons()->getButtonArr();
            }

        }

        if($listParams) {
            $data['iDisplayLength'] = $listParams->getDisplayLength();
        } else {
            $data['iDisplayLength'] = 25;
        }
        $data['sPaginationType'] = 'full_numbers';

        $listCaption2 = $listCaption;

        if ($listCaption2 == $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['BRICK_CAPTION_PLURAL']) {
            $listCaption2 = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['BRICK_CAPTION_PLURAL2'];
        }

        $data['oLanguage'] = array(
            'oPaginate' => $GLOBALS['TL_LANG']['FE_C4G_LIST']['DATATABLE_CAPTION_PAGINATION'],
            'sEmptyTable' => $GLOBALS['TL_LANG']['FE_C4G_LIST']['DATATABLE_CAPTION_NONE'] . $listCaption . $GLOBALS['TL_LANG']['FE_C4G_LIST']['DATATABLE_CAPTION_EXISTS'],
            'sInfo' => '_TOTAL_ ' . $listCaption . ' (_START_'.$GLOBALS['TL_LANG']['FE_C4G_LIST']['DATATABLE_CAPTION_TO'].'_END_'.')',
            'sInfoEmpty' => '_TOTAL_ ',
            'sInfoFiltered' => ' '.$GLOBALS['TL_LANG']['FE_C4G_LIST']['DATATABLE_CAPTION_FROM'].'_MAX_ '. $listCaption2,
            'sInfoThousands' => $GLOBALS['TL_LANG']['FE_C4G_LIST']['DATATABLE_CAPTION_INFO_THOUSANDS'],
            'sLengthMenu' => $GLOBALS['TL_LANG']['FE_C4G_LIST']['DATATABLE_CAPTION_SHOW'].'_MENU_ ' . $listCaption,
            'sProcessing' => $GLOBALS['TL_LANG']['FE_C4G_LIST']['DATATABLE_CAPTION_PROCESSING'],
            'sSearch' => $GLOBALS['TL_LANG']['FE_C4G_LIST']['DATATABLE_CAPTION_SEARCH'],
            'sZeroRecords' => $GLOBALS['TL_LANG']['FE_C4G_LIST']['DATATABLE_CAPTION_NONE'] . $listCaption . $GLOBALS['TL_LANG']['FE_C4G_LIST']['DATATABLE_CAPTION_FOUND'],
        );

        $cnt = 0;

        foreach ($fieldList as $column) {
            $additionalClasses = ' c4g_list_align_left';
            if ($column->getAlign() == 'right') {
                $additionalClasses = ' c4g_list_align_right';
            } else if ($column->getAlign() == 'center') {
                $additionalClasses = ' c4g_list_align_center';
            }
            if ($column->isShowSum()) {
                $additionalClasses .= ' c4g_sum';
            }
            $priority = $cnt;
            if ($column->getTableColumnPriority() > 0) {
                $priority = $column->getTableColumnPriority();
            }
            if ($cnt == 0) {
                $data['aoColumnDefs'][] = array(
                    'sClass' => 'c4g_brick_col_'.$cnt,
                    'sTitle' => 'key',
                    'bVisible' => false,
                    'bSearchable' => false,
                    'aTargets' => array(0),
                    'responsivePriority' => array($priority));
                $cnt++;
            } else {
                if ($column->isTableColumn()) {
                    $noMouseEvents = $column->isShowSortIcons() === false ? ' nomouseevents' : '';
                    if ($column->isSortColumn()) {
                        $data['aoColumnDefs'][] = array(
                            'sClass' => 'c4g_brick_col c4g_brick_col_'.$cnt.$noMouseEvents.$additionalClasses,
                            'sTitle' => $column->getTitle(),
                            'aDataSort' => array($cnt),
                            'sWidth' => $column->getColumnWidth() . '%',
                            'aTargets' => array($cnt),
                            'sType' => $column->getSortType(),
                            'responsivePriority' => array($priority));

                        if($column->getSortSequence() == 'desc') {
                            $data['aaSorting'] = [[$cnt, 'desc']];
                        } else {
                            $data['aaSorting'] = [[$cnt, 'asc']];
                        }
                    } else {
                        $data['aoColumnDefs'][] = array(
                            'sClass' => 'c4g_brick_col c4g_brick_col_'.$cnt.$noMouseEvents.$additionalClasses,
                            'sTitle' => $column->getTitle(),
                            'sWidth' => $column->getColumnWidth() . '%',
                            'sType' => $column->getSortType(),
                            'aTargets' => array($cnt),
                            'responsivePriority' => array($priority));
                    }
                    $cnt++;
                }
            }

        }

        foreach ($tableElements as $element) {
            if ($listParams->isRemoveUnpublishedElements() && !$element->published) {
                continue;
            }

            $fields = array();
            $cnt = 0;
            $col = 1;
            $convertingCount = 0; //DateTimeLocation
            foreach ($fieldList as $column) {
                $fieldName = $column->getFieldName();
                $row_data = $element;

                if ($fieldName == $listParams->getCaptionField()) {
                    $col = $cnt;
                }
                if ($cnt == 0) {
                    if ($listParams->isWithFunctionCallOnClick()) {
                        $fields[] = C4GBrickActionType::ACTION_BUTTONCLICK . ':' . $listParams->getOnClickFunction() . ':' . $element->id;
                    } else {
                        $fields[] = C4GBrickActionType::ACTION_CLICK . ':' . $element->id; //hier wird in der versteckten Spalte 1 die ClickAction gesetzt
                    }
                    $cnt++;
                } else {
                    if ($element && $column->getExternalModel() && $column->getExternalIdField() && (!$listParams->isWithModelListFunction())) {
                        $extModel = $column->getExternalModel();
                        $extIdFieldName = $column->getExternalIdField();
                        $extFieldName = $column->getExternalFieldName();
                        $extId = $element->$extIdFieldName;
                        $extCallbackFunction = $column->getExternalCallBackFunction();
                        if ($extModel && $extId && ($extId > 0)) {
                            if ($extFieldName && ($extFieldName != '')) {
                                $extSearchValue = $column->getExternalSearchValue();
                                if ($extSearchValue) {
                                    $tableName = $extModel::getTableName();
                                    $fieldName = $column->getFieldName();
                                    $sortField = $column->getExternalSortField();
                                    $row_data = $database->prepare("SELECT * FROM `$tableName` WHERE `$extFieldName`='$extSearchValue' AND `$fieldName`='$extId' ORDER BY `$tableName`.`$sortField` DESC LIMIT 1 ")->execute();
                                } else {
                                    $row_data = $extModel::findBy($extFieldName, $extId);
                                }
                            } else {
                                $row_data = $extModel::findByPk($extId);
                            }
                        }
                        if ($extCallbackFunction) {
                            $extModel::$extCallbackFunction($row_data, $database/*, $column->getFieldName(), $extId, $listParams*/);
                        }
                    }

                    if ($column->isTableColumn()) {
                        if ($column  instanceof C4GSelectField) {
                            $fields[] = C4GBrickCommon::translateSelectOption($row_data->$fieldName, C4GBrickList::getOptions($fieldList, $row_data, $column));
                        } else if ($column instanceof C4GGeopickerField) {
                            $fields[] = $column->getC4GListField($row_data, $content, $database);
                        } else if ($column instanceof C4GDateTimeLocationField){
                            $lat = $row_data->loc_geoy;
                            $lon = $row_data->loc_geox;
                            $time = $row_data->loc_time;
                            $addressField = $column->getAddressField();
                            $idFromModel = $row_data->id;
                            $extModel = $column->getExternalModel();
                            $extDbValues = $extModel::findByPk($idFromModel);
                            $address_db = $extDbValues->$addressField;
                            $profile_id = null;

                            if ($row_data->$fieldName) {
                                // when the value is already set, i.e. in a modellistfunction
                                $fields[] = $row_data->$fieldName;
                                $cnt++;
                                continue;
                            }

                            if ($content) {
                                $find = 'profile":"';
                                $pos = strpos($content, $find);
                                if ($pos > 0) {
                                    $str_profile_id = substr($content, $pos + 10, 4);
                                    if ($str_profile_id) {
                                        $profile_id = intval($str_profile_id);
                                    }
                                }
                            }
                            $address = '';
                            if(($convertingCount == 1) && ($profile_id))
                            {
                                $lat_2 = $row_data->loc_geoy_2;
                                $lon_2 = $row_data->loc_geox_2;
                                $time_2 = $row_data->loc_time_2;
                                if($address_db) {
                                    $address = $time_2. ' ('.$address_db. ' )';
                                }
                                else if($lat_2 && $lon_2  && $time_2) {
                                    $address = $time_2 . ' (' . C4GBrickCommon::convert_coordinates_to_address($lat_2, $lon_2, $profile_id, $database) . ')';
                                }
                                else {
                                    $address = $time .' ('.C4GBrickCommon::convert_coordinates_to_address($lat, $lon, $profile_id, $database). ')';
                                }
                                $convertingCount = 0;
                            } elseif ($profile_id) {
                                if($address_db) {
                                    $address = $time. ' ('.$address_db. ' )';
                                    $convertingCount = 1;
                                } else {
                                    $address = $time . ' (' . C4GBrickCommon::convert_coordinates_to_address($lat, $lon, $profile_id, $database) . ')';
                                    $convertingCount = 1;
                                }
                            } elseif ($column->getContentId()) {
                                $contentId = $column->getContentId();
                                // get content element from table
                                $contentElement = ContentModel::findByPk($contentId);
                                if ($contentElement) {
                                    // get map from contentElement
                                    $mapId = $contentElement->c4g_map_id;
                                    $map = C4gMapsModel::findByPk($mapId);
                                    if ($map) {
                                        $address = $time . ' (' . C4GBrickCommon::convert_coordinates_to_address($lat, $lon, $map->profile, $database) . ')';
                                        $convertingCount = 1;
                                    }
                                }
                            }
                            $fields[] = $address;
                        } else {
                            $fields[] = $column->getC4GListField($row_data, $content);
                        }

                        $cnt++;
                    }
                }

            }

            //an dieser Stelle können beliebigviele Daten über das Array durchgereicht werden. Zum Beispiel
            //verschiedene Click-Actions falls notwendig.
            $data['aaData'][] = $fields;
        }

        $data = C4GBrickList::hideDuplicatedColumns($data);
        if ($listParams) {
            $withDetails = $listParams->isWithDetails();
            $selectRow   = $listParams->getSelectRow();
        } else {
            $withDetails = true;
            $selectRow = -1;
        }
        if ($selectRow == -1) {
            foreach ($tableElements as $element) {
                if (property_exists($element->selectrow) && $element->selectrow) {
                    $selectRow = $element->selectrow;
                    break;
                }
            }
        }
        if ($selectRow != -1) {
            $data['sScrollY'] = '50vh'; //ToDo calc size
        }

        $buttons = '';
        if (!$listParams->isWithoutListButtons()) {
            $buttons = C4GBrickList::getDialogButtons($listParams, $parentCaption);
        }
        $return = array
        (
            'contenttype' => 'datatable',
            'contentdata' => $data,
            'contentoptions' => array
            (
                'actioncol' => 0,
                'selectrow' => $selectRow,
                'clickAction' => $withDetails,
            ),
            'state' => C4GBrickActionType::IDENTIFIER_LIST . ':' . $key, //Listenstatus
            'headline' => $listHeadline,
            'buttons' => $buttons
        );
        if ($searchValue = \Session::getInstance()->get('c4g_list_searchValue')) {
            $return['searchValue'] = $searchValue;
            \Session::getInstance()->remove('c4g_list_searchValue');
        }


        if ($listParams->isshowToolTips()) {
            $return['contentoptions']['tooltipcol']  = $col;
        }

        return $return;
    }

    public static function showC4GList($listCaption, $database, $content, $listHeadline,  $fieldList, $tableElements, $key, $parentCaption, $listParams)
    {
        $customListViewFunction = $listParams->getCustomListViewFunction();
        if (empty($customListViewFunction)) {
            $view = C4GBrickList::buildListView($fieldList, $database, $tableElements, $content, $listParams);
        } else {
            $method = $customListViewFunction[1];
            $view = $customListViewFunction[0]->$method($fieldList, $database, $tableElements, $content, $listParams);
        }

        $buttons = '';
        if (!$listParams->isWithoutListButtons()) {
            $buttons = C4GBrickList::getDialogButtons($listParams, $parentCaption);
        }

        $result = array
        (
            'headline' => $listHeadline,
            'dialogtype' => 'html',
            'dialogdata' => $view,
            'dialogoptions' => C4GUtils::addDefaultDialogOptions(array
            (
                'title' => $listCaption,
                'modal' => true,
                'embedDialogs' => true,
            )),
            'dialogid' =>C4GBrickActionType::IDENTIFIER_LIST . ':' . $key, //Listenstatus
            'dialogstate' =>C4GBrickActionType::IDENTIFIER_LIST . ':' . $key, //Listenstatus
            'dialogbuttons' => $buttons
        );
        return $result;
    }

    public static function buildListView(
        $fieldList,
        $database,
        $tableElements,
        $content,
        $listParams
    ) {
        $view = '<div class="' . C4GBrickConst::CLASS_LIST . '">';


        $view .= '<ul class="c4g_brick_list_header">';

        $captionField = '';
        $sortColumn = '';
        $rowCount = $listParams->getRowCount();
        foreach ($fieldList as $field) {
            if ($field->isTableColumn()) {
                if ($field->getFieldName() == $listParams->getCaptionField()) {
                    $captionField = $listParams->getCaptionField();
                }
                $beforeDiv = '<li class="c4g_brick_list_column c4g_brick_list_header_column ' . $field->getFieldName() . '">';
                $afterDiv = '</li>';
                if ($field->isHidden()) {
                    $beforeDiv .= '<div class="c4g_brick_hidden_field" style="display:none">';
                    $afterDiv .= '</div>';
                }
                $view .= $beforeDiv . $field->getTitle() . $afterDiv;
            }
            if ($field->isSortColumn()) {
                $sortColumn = $field->getFieldName();
                $sortSequence = $field->getSortSequence();
            }
        }
        $view .= '</ul>';

        $i = 0;
        if ($sortColumn) {
            $sortSequenceIdentifier = 4;
            if ($sortSequence) {
                if ($sortSequence == SORT_DESC) {
                    $sortSequenceIdentifier = 3;
                }
            }
            $tableElements = C4GBrickCommon::array_collection_sort($tableElements, $sortColumn, $sortSequenceIdentifier, false, $rowCount);
        }

        foreach ($tableElements as $row) {
            $i++;
            if ($listParams->isWithFunctionCallOnClick()) {
                $href = C4GBrickActionType::ACTION_BUTTONCLICK . ':' . $listParams->getOnClickFunction() . ':' . $row->id;
            } else {
                if ($listParams->getRedirectTo()) {
                    $href = C4GBrickActionType::ACTION_REDIRECT_TO_DETAIL . ':' . $row->id;
                } else {
                    $href = C4GBrickActionType::ACTION_CLICK . ':' . $row->id;
                }
            }
            $convertingCount = 0; //DateTimeLocation
            $tooltip = '';
            if ($captionField) {
                $tooltip = $row->$captionField;
            }
            $view .= '<a class="c4gGuiAction" href="" data-action="'.$href.'"><ul class="c4g_brick_list_row c4g_brick_list_row_'.$i.'" data-tooltip="'.$tooltip.'" title="'.$tooltip.'">';
            foreach ($fieldList as $field) {
                $fieldName = $field->getFieldName();
                $additionalParameters = array();
                $additionalParameters['database'] = $database;

                $additionalParameters['content'] = $content;
                if ($field->isTableColumn()) {
                    $beforeDiv = '<li class="c4g_brick_list_column c4g_brick_list_row_column '.$field->getFieldName().'">';
                    $afterDiv = '</li>';
                    if ($field->isHidden()) {
                        $beforeDiv .= '<div class="c4g_brick_hidden_field" style="display:none">';
                        $afterDiv .= '</div>';
                    }
                    if ($field  instanceof C4GSelectField) {
                        $view .= $beforeDiv . C4GBrickCommon::translateSelectOption($row->$fieldName, C4GBrickList::getOptions($fieldList, $row, $field)) . $afterDiv;
                    } else if ($field instanceof C4GGeopickerField) {
                        $view .= $beforeDiv . $field->getC4GListField($row, $content, $database) . $afterDiv;
                    } else if ($field instanceof C4GDateTimeLocationField){
                        $lat = $row->loc_geoy;
                        $lon = $row->loc_geox;
                        $time = $row->loc_time;
                        $addressField = $field->getAddressField();
                        $idFromModel = $row->id;
                        $extModel = $field->getExternalModel();
                        $extDbValues = $extModel::findByPk($idFromModel);
                        $address_db = $extDbValues->$addressField;
                        $profile_id = null;

                        if ($content) {
                            $find = 'profile":"';
                            $pos = strpos($content, $find);
                            if ($pos > 0) {
                                $str_profile_id = substr($content, $pos + 10, 4);
                                if ($str_profile_id) {
                                    $profile_id = intval($str_profile_id);
                                }
                            }
                        }
                        $address = '';
                        if(($convertingCount == 1) && ($profile_id))
                        {
                            $lat_2 = $row->loc_geoy_2;
                            $lon_2 = $row->loc_geox_2;
                            $time_2 = $row->loc_time_2;
                            if($address_db) {
                                $address = $time_2. ' ('.$address_db. ' )';
                            }
                            else if($lat_2 && $lon_2  && $time_2) {
                                $address = $time_2 . ' (' . C4GBrickCommon::convert_coordinates_to_address($lat_2, $lon_2, $profile_id, $database) . ')';
                            }
                            else {
                                $address = $time .' ('.C4GBrickCommon::convert_coordinates_to_address($lat, $lon, $profile_id, $database). ')';
                            }
                            $convertingCount = 0;
                        }
                        else if ($profile_id) {
                            if($address_db) {
                                $address = $time. ' ('.$address_db. ' )';
                                $convertingCount = 1;
                            }
                            else {
                                $address = $time . ' (' . C4GBrickCommon::convert_coordinates_to_address($lat, $lon, $profile_id, $database) . ')';
                                $convertingCount = 1;
                            }
                        }
                        $view .= $beforeDiv . $address . $afterDiv;
                    } else {
                        $view .= $beforeDiv . $field->getC4GListField($row, $content) . $afterDiv;
                    }
                }

            }
            $view .= '</ul></a>';
        }
        return $view;
    }

}