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
namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\ProjectsBundle\Classes\Common\C4GBrickRegEx;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldType;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickView;


//*** NOT READY !!! ***//
class C4GDateRangeField extends C4GBrickField
{
    protected $minDate = null;
    protected $maxDate = null;

    protected $excludeWeekdays = null;
    protected $excludeDates = null;

    // customize single date fields
    protected $customFormat = null;
    protected $customLanguage = null;
    protected $sortType = 'de_date';
    protected $pattern = C4GBrickRegEx::DATE_D_M_Y; //ToDo

    protected $flipButtonPosition = false;
    protected $datePickerByBrowser = false;

    protected $showInlinePicker = false;

    protected $displayEmptyInput = true;

    /**
     * @param string $type
     */
    public function __construct(string $type = C4GBrickFieldType::DATE)
    {
        parent::__construct($type);

        if ($GLOBALS['TL_CONFIG']['dateFormat'] == 'Y-m-d') {
            $this->pattern = C4GBrickRegEx::DATE_Y_M_D;
        } else if ($GLOBALS['TL_CONFIG']['dateFormat'] != 'd.m.Y') {
            $this->pattern = '';
        }
    }

    /**
     * @param C4GBrickField[] $fieldList
     * @param $data
     * @param C4GBrickDialogParams $dialogParams
     * @param array $additionalParams
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $fieldName = $this->getFieldName();
        if ($this->getAdditionalID()) {
            $fieldName = $this->getFieldName() . '_' . $this->getAdditionalID();
        }
        if ($this->customFormat) {
            $dateFormat = $this->customFormat;
        } else {
            $dateFormat = $GLOBALS['TL_CONFIG']['dateFormat'];
        }

        if ($this->customLanguage) {
            $pickerLanguage = $this->customLanguage;
        } else {
            $pickerLanguage = $GLOBALS['TL_LANGUAGE'];
        }

        $id = 'c4g_' . $fieldName;
        $title = $this->getTitle();
        $required = $this->generateRequiredString($data, $dialogParams, $fieldList);
        $value = $this->generateInitialValue($data);
        $changeAction = '';
        if ($this->isCallOnChange()) {
            $changeAction = ' onchange="' . $this->getCallOnChangeFunction() . '"';
        }

        if (!$this->minDate || ($this->minDate == '') || ($this->minDate == 0)) {
            $this->minDate = strtotime('-25 year');
        }

        if (!$this->maxDate || ($this->maxDate == '') || ($this->maxDate == 0)) {
            $this->maxDate = strtotime('+25 year');
        }

        //ToDo hotfix 0 is saved by default and is the 01.01.1970.
        if (is_numeric($value) && intval($value) != 0) {
            if ($this->isDatePickerByBrowser()) {
                $value = date('Y-m-d', $value);
            } else {
                $date = new \DateTime();
                $date->setTimestamp($value);
                $value = $date->format($dateFormat);
            }
        } else {
            $value = '';
        }

        $result = '';

        $trans = array(
            'd' => 'dd',
            'm' => 'mm',
            'Y' => 'yyyy',
            'y' => 'yy',
            'j'  => 's');

        $outputFormat = strtr($dateFormat, $trans);

        if ($this->isShowIfEmpty() || !empty($value)) {
            $buttonId = "'" . $id . "'";
            $condition = $this->createConditionData($fieldList, $data);
            //We need to check the condition here and display the field, since the JS does not do that it seems.
            $display = true;
            $conditions = $this->getCondition();
            if ($conditions) {
                if (is_array($conditions)) {
                    foreach ($conditions as $con) {
                        $conField = $con->getFieldName();
                        $conValue = $con->getValue();
                        $fieldValue = '';
                        if ($data) {
                            if ($data instanceof \stdClass) {
                                $fieldValue = $data && property_exists($data,$conField) ? $data->$conField : '';
                            } else {
                                $fieldValue = $data->row()[$conField];
                            }
                        } elseif ($this->getInitialValue()) {
                            foreach($fieldList as $field) {
                                $conFieldName = $field->getFieldName();
                                if ($field->getAdditionalID()) {
                                    $conFieldName = $conFieldName.'_'.$field->getAdditionalID();
                                }
                                if ($conFieldName == $conField) {
                                    if ($field->getInitialValue()) {
                                        $fieldValue = $field->getInitialValue();
                                        break;
                                    }
                                }
                            }
                        }
                        if ((!$this->displayEmptyInput && !$fieldValue) || !$con->checkAgainstCondition($fieldValue)) {
                            $display = false;
                        }
                    }
                } else {
                    $conField = $conditions->getFieldName();
                    $conValue = $conditions->getValue();
                    $fieldValue = $data->row()[$conField];
                    if (!$conditions->checkAgainstCondition($fieldValue)) {
                        $display = false;
                    }
                }
            }

            if ($this->isDatePickerByBrowser()) {
                $this->pattern = C4GBrickRegEx::DATE_Y_M_D;
//                if (!$display) {
//                    $html = '<div class="c4g__form-date-container c4g__input-group c4g__input-group-date formdata" style="display: none">';
//                } else {
                    $html = '<div class="c4g__form-date-container c4g__input-group c4g__input-group-date formdata" >';
//                }
            } else {
//                if (!$display) {
//                    $html = '<div class="c4g__form-date-container c4g__input-group c4g__input-group-date formdata" style="display: none">';
//                } else {
                    $html = '<div class="c4g__form-date-container c4g__input-group c4g__input-group-date formdata">';
//                }
            }

            if (!$this->isIgnoreViewType() && (C4GBrickView::isWithoutEditing($dialogParams->getViewType()) || !$this->isEditable())) {
                if (!$this->isDatePickerByBrowser()) {
                    $html .= '<input readonly="false" autocomplete="off" ' . $required . ' type="text" id="' . $id . '" class="noformdata c4g__form-control c4g__form-date-input ' . $id . '" ' . $changeAction . ' name="' . $fieldName . '" value="' . $value . '" ' . $condition['conditionPrepare'] . 'pattern="' . $this->pattern . '"' . '>';
                } else {
                    $html .= '<input readonly="false" autocomplete="off" ' . $required . ' type="text" id="' . $id . '" class="noformdata c4g__form-control c4g__form-date-input ' . $id . '" ' . $changeAction . ' name="' . $fieldName . '" value="' . $value . '" ' . $condition['conditionPrepare'] . 'pattern="' . $this->pattern . '"' . '>';
                }
            } else {
                if (!$this->isDatePickerByBrowser()) {
                    $search = "C4GDatePicker('" . $id;
                    $onLoadScript = $dialogParams->getOnloadScript();

                    if ($this->isShowInlinePicker()) {
                        if (!strpos($onLoadScript, $search)) {
                            $onLoadScript = 'C4GDateRangePicker(\'' . $id . '_picker\', \'date\', \'' . $this->minDate . '\', \'' . $this->maxDate . '\', \'' . $outputFormat . '\',\'' . $pickerLanguage . '\',\'' . $this->excludeWeekdays . '\',\'' . $this->excludeDates . '\');';
                            $dialogParams->setOnloadScript($onLoadScript);
                        }
                        $html .= '<div readonly="false" autocomplete="off" ' . $required . ' id="' . $id . '_picker" type="text" class="c4g__form-datepicker noformdata" name="' . $fieldName . '_picker" value="' . $value . '"></div>';
                        $html .= '<input style="visibility:hidden; height:0; width:0;" readonly="false" autocomplete="off" ' . $required . ' type="text" id="' . $id . '" class="formdata c4g__form-control c4g__form-date-input ' . $id . '" ' . $changeAction . ' name="' . $fieldName . '" value="' . $value . '" ' . $condition['conditionPrepare'] . 'pattern="' . $this->pattern . '"' . '>';
                    } else {
                        if (!strpos($onLoadScript, $search)) {
                            $onLoadScript = 'C4GDatePicker(\'' . $id . '\', \'date\', \'' . $this->minDate . '\', \'' . $this->maxDate . '\', \'' . $outputFormat . '\',\'' . $pickerLanguage . '\',\'' . $this->excludeWeekdays . '\',\'' . $this->excludeDates . '\');';
                            $dialogParams->setOnloadScript($onLoadScript);
                        }
                        $html .= '<button ' . $required . ' onclick="if (document.getElementById(' . $buttonId . ')) {jQuery(document.getElementById(' . $buttonId . ')).show();jQuery(document.getElementById(' . $buttonId . ')).focus();};" type="button" class="c4g__btn c4g__btn-date c4g__form-date-button-interactive"><i class="far fa-calendar-alt"></i></button>';
                        $html .= '<input readonly="false" autocomplete="off" ' . $required . ' type="text" id="' . $id . '" class="formdata c4g__form-control c4g__form-date-input ' . $id . '" ' . $changeAction . ' name="' . $fieldName . '" value="' . $value . '" ' . $condition['conditionPrepare'] . 'pattern="' . $this->pattern . '"' . '>';
                    }
                } else {
                    $html .= '<input autocomplete="on" ' . $required . ' type="date" id="' . $id . '" class="formdata c4g__form-control c4g__form-date-input ' . $id . '" ' . $changeAction . ' name="' . $fieldName . '" value="' . $value . '" ' . $condition['conditionPrepare'] . 'pattern="' . $this->pattern . '"' . '>';
                }
            }

            $html .= '</div>';
            $result =
                $this->addC4GField($condition,$dialogParams,$fieldList,$data,
                    $html);
        }

        return $result;
    }

    /**
     * Method that will be called in the compareWithDB() in C4GBrickDialog
     * @param $dbValues
     * @param $dlgValues
     * @return C4GBrickFieldCompare|null
     */
    public function compareWithDB($dbValues, $dlgValues)
    {
        $fieldname = $this->getFieldName();
        $dbValue = $dbValues->$fieldname;
        $additionalId = $this->getAdditionalID();
        if (!empty($additionalId)) {
            $dlgvalue = $dlgValues[$this->getFieldName() . '_' . $additionalId];
        } else {
            $dlgvalue = $dlgValues[$this->getFieldName()];
        }
        $result = null;
        if (!$this->isSearchField()) {
            $date = \DateTime::createFromFormat($GLOBALS['TL_CONFIG']['dateFormat'], $dlgvalue);
            if ($date) {
                $date->Format($GLOBALS['TL_CONFIG']['dateFormat']);
                $date->setTime(0, 0, 0);
                $dlgValue = $date->getTimestamp();
                if (strcmp($dbValue, $dlgValue) != 0) {
                    $result = new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
                }
            }
        } else {
            $dbValue = date($GLOBALS['TL_CONFIG']['dateFormat'], $dbValue);
            $dlgValue = $dlgvalue;
            //exception for C4GMatching
            if ($dlgValue && strcmp($dbValue, $dlgValue) != 0) {
                $dlgValue = date($GLOBALS['TL_CONFIG']['dateFormat'], $dlgValue);
            }
            if ($dlgValue && strcmp($dbValue, $dlgValue) != 0) {
                $result = new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
            }
        }

        return $result;
    }

    /**
     * Method that will be called in the saveC4GDialog() in C4GBrickDialog
     * @param $dlgValues
     * @return int|string
     */
    public function createFieldData($dlgValues)
    {
        $additionalId = $this->getAdditionalID();
        if (!empty($additionalId)) {
            $fieldData = $dlgValues[$this->getFieldName() . '_' . $additionalId];
        } else {
            $fieldData = $dlgValues[$this->getFieldName()];
        }

        $format = $GLOBALS['TL_CONFIG']['dateFormat'];
        $arrParts = explode('.', $fieldData);
        // if the year is only two characters long, check the current year
        // if given year is greater, it will be seen as 20th century
        // else it will be seen as 21st century
        if (strlen($arrParts[2]) == 2 && $GLOBALS['TL_CONFIG']['dateFormat'] == 'd.m.Y') {
            $currentYear = (new \DateTime())->format('y');
            if ($arrParts[2] > intval($currentYear)) {
                $fieldData = $arrParts[0] . '.' . $arrParts[1] . '.19' . $arrParts[2];
            } else {
                $fieldData = $arrParts[0] . '.' . $arrParts[1] . '.20' . $arrParts[2];
            }
        }
        $date = $fieldData ? new \DateTime($fieldData) : false;
        if ($date) {
            $date->Format($format);
            $date->setTime(0, 0, 0);
            $fieldData = $date->getTimestamp();
        } else {
            $fieldData = 0;
        }

        return $fieldData;
    }

    /**
     * Public method for creating the field specific list HTML
     * @param $rowData
     * @param $content
     * @return mixed
     */
    public function getC4GListField($rowData, $content)
    {
        if ($this->customFormat) {
            $dateFormat = $this->customFormat;
        } else {
            $dateFormat = $GLOBALS['TL_CONFIG']['dateFormat'];
        }
        $fieldName = $this->getFieldName();
        $date = $rowData->$fieldName;
        if ($date) {
            return date($dateFormat, $date);
        }

        return '';
    }

    /**
     * Public method for creating the field specific tile HTML
     * @param $fieldTitle
     * @param $element
     * @return mixed
     */
    public function getC4GTileField($fieldTitle, $element)
    {
        $fieldName = $this->getFieldName();
        $date = $element->$fieldName;

        return $fieldTitle . '<div class="c4g_tile_value">' . date($GLOBALS['TL_CONFIG']['dateFormat'], $date) . '</div>';
    }

    /**
     * Public method that will be called to view the value
     * @param $value
     * @return mixed
     */
    public function translateFieldValue($value)
    {
        if ($value === '') {
            $value = 0;
        }
        $date = $value;
        $timestamp = strtotime($value);
        if (is_numeric($timestamp)) {
            return date($GLOBALS['TL_CONFIG']['dateFormat'], $timestamp);
        } elseif ($value == $GLOBALS['TL_LANG']['FE_C4G_DIALOG_COMPARE']['newEntry']) {
            return $value;
        }

        return date($GLOBALS['TL_CONFIG']['dateFormat'], $date);
    }

    /**
     * @return null
     */
    public function getMinDate()
    {
        return $this->minDate;
    }

    /**
     * @param $minDate
     * @return $this
     */
    public function setMinDate($minDate)
    {
        $this->minDate = $minDate;

        return $this;
    }

    /**
     * @return null
     */
    public function getMaxDate()
    {
        return $this->maxDate;
    }

    /**
     * @param $maxDate
     * @return $this
     */
    public function setMaxDate($maxDate)
    {
        $this->maxDate = $maxDate;

        return $this;
    }

    /**
     * @return null
     */
    public function getExcludeWeekdays()
    {
        return $this->excludeWeekdays;
    }

    /**
     * @param $excludeWeekdays
     * @return $this
     */
    public function setExcludeWeekdays($excludeWeekdays)
    {
        $this->excludeWeekdays = $excludeWeekdays;

        return $this;
    }

    /**
     * @return null
     */
    public function getExcludeDates()
    {
        return $this->excludeDates;
    }

    /**
     * @param $excludeDates
     * @return $this
     */
    public function setExcludeDates($excludeDates)
    {
        $this->excludeDates = $excludeDates;

        return $this;
    }

    /**
     * @return null
     */
    public function getCustomFormat()
    {
        return $this->customFormat;
    }

    /**
     * @param $customFormat
     * @return $this
     */
    public function setCustomFormat($customFormat)
    {
        $this->customFormat = $customFormat;

        return $this;
    }

    /**
     * @return null
     */
    public function getCustomLanguage()
    {
        return $this->customLanguage;
    }

    /**
     * @param null $customLanguage
     */
    public function setCustomLanguage($customLanguage)
    {
        $this->customLanguage = $customLanguage;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param string $pattern
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @return string
     */
    public function getSortType()
    {
        return $this->sortType;
    }

    /**
     * @param string $sortType
     */
    public function setSortType($sortType)
    {
        $this->sortType = $sortType;
    }

    /**
     * @return bool
     */
    public function isFlipButtonPosition(): bool
    {
        return $this->flipButtonPosition;
    }

    /**
     * @param bool $flipButtonPosition
     */
    public function setFlipButtonPosition(bool $flipButtonPosition)
    {
        $this->flipButtonPosition = $flipButtonPosition;
    }

    /**
     * @return bool
     */
    public function isDatePickerByBrowser(): bool
    {
        return $this->datePickerByBrowser;
    }

    /**
     * @param bool $datePickerByBrowser
     */
    public function setDatePickerByBrowser(bool $datePickerByBrowser): void
    {
        $this->datePickerByBrowser = $datePickerByBrowser;
        $this->pattern = C4GBrickRegEx::DATE_Y_M_D; //ToDo
    }

    /**
     * @return bool
     */
    public function isShowInlinePicker(): bool
    {
        return $this->showInlinePicker;
    }

    /**
     * @param bool $showInlinePicker
     */
    public function setShowInlinePicker(bool $showInlinePicker): void
    {
        $this->showInlinePicker = $showInlinePicker;
    }

    /**
     * @return bool
     */
    public function isDisplayEmptyInput(): bool
    {
        return $this->displayEmptyInput;
    }

    /**
     * @param bool $displayEmptyInput
     */
    public function setDisplayEmptyInput(bool $displayEmptyInput): void
    {
        $this->displayEmptyInput = $displayEmptyInput;
    }

}
