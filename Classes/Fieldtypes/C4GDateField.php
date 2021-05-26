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
namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\ProjectsBundle\Classes\Common\C4GBrickRegEx;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickView;

class C4GDateField extends C4GBrickField
{
    protected $minDate = null;
    protected $maxDate = null;

    protected $excludeWeekdays = null;
    protected $excludeDates = null;

    // customize single date fields
    protected $customFormat = null;
    protected $customLanguage = null;
    protected $sortType = 'de_date';
    protected $pattern = C4GBrickRegEx::DATE_D_M_Y;

    protected $flipButtonPosition = true;

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
        $required = $this->generateRequiredString($data, $dialogParams);
        $value = $this->generateInitialValue($data);

        if ($this->isCallOnChange()) {
            $changeAction = 'onchange="' . $this->getCallOnChangeFunction() . '"';
        }

        if (!$this->minDate || ($this->minDate == '') || ($this->minDate == 0)) {
            $this->minDate = strtotime('-25 year');
        }

        if (!$this->maxDate || ($this->maxDate == '') || ($this->maxDate == 0)) {
            $this->maxDate = strtotime('+25 year');
        }

        //ToDo hotfix 0 is saved by default and is the 01.01.1970.
        if (is_numeric($value) && intval($value) != 0) {
            $date = new \DateTime();
            $date->setTimestamp($value);
            $value = $date->format($dateFormat);
        } else {
            $value = '';
        }

        $result = '';

        $PHPFormatOptions = ['Y', 'm', 'd'];
        $JSFormatOptions = ['yy', 'mm', 'dd'];
        $format = str_replace($PHPFormatOptions, $JSFormatOptions, $dateFormat);

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
                                $fieldValue = $data->$conField;
                            } else {
                                $fieldValue = $data->row()[$conField];
                            }
                        } elseif ($this->getInitialValue()) {
                            $fieldValue = $this->getInitialValue();
                        }
                        if (!$con->checkAgainstCondition($fieldValue)) {
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

            if (!$display) {
                $html = '<div class="c4g_date_field_container" style="display: none"  onmousedown="C4GDatePicker(\'' . $id . '\', \'date\', \'' . $this->minDate . '\', \'' . $this->maxDate . '\', \'' . $format . '\',\'' . $pickerLanguage . '\',\'' . $this->excludeWeekdays . '\',\'' . $this->excludeDates . '\')" >';
            } else {
                $html = '<div class="c4g_date_field_container" onmousedown="C4GDatePicker(\'' . $id . '\', \'date\', \'' . $this->minDate . '\', \'' . $this->maxDate . '\', \'' . $format . '\',\'' . $pickerLanguage . '\',\'' . $this->excludeWeekdays . '\',\'' . $this->excludeDates . '\')" >';
            }
            if ($this->isFlipButtonPosition()) {
                if (!$this->isIgnoreViewType() && (C4GBrickView::isWithoutEditing($dialogParams->getViewType()) || !$this->isEditable())) {
                    $html .= '<span class="ui-button ui-corner-all c4g_date_field_button c4g_date_field_button_flipped"><i class="far fa-calendar-alt"></i></span>';
                } else {
                    $html .= '<span onclick="if (document.getElementById(' . $buttonId . ')) {$(document.getElementById(' . $buttonId . ')).show(); $(document.getElementById(' . $buttonId . ')).focus();}" class="ui-button ui-corner-all c4g_date_field_button_interactive c4g_date_field_button_interactive_flipped"><i class="far fa-calendar-alt"></i></span>';
                }
                $html .= '<input readonly="true" autocomplete="off" ' . $required . ' type="text" id="' . $id . '" class="formdata c4g_date_field_input c4g_date_field_input_flipped ' . $id . '" ' . $changeAction . ' name="' . $fieldName . '" value="' . $value . '" ' . $condition['conditionPrepare'] . 'pattern="' . $this->pattern . '"' . '>';
            } else {
                $html .= '<input readonly="true" autocomplete="off" ' . $required . ' type="text" id="' . $id . '" class="formdata c4g_date_field_input ' . $id . '" ' . $changeAction . ' name="' . $fieldName . '" value="' . $value . '" ' . $condition['conditionPrepare'] . 'pattern="' . $this->pattern . '"' . '>';
                if (!$this->isIgnoreViewType() && (C4GBrickView::isWithoutEditing($dialogParams->getViewType()) || !$this->isEditable())) {
                    $html .= '<span class="ui-button ui-corner-all c4g_date_field_button"><i class="far fa-calendar-alt"></i></span>';
                } else {
                    $html .= '<span onclick="if (document.getElementById(' . $buttonId . ')) {$(document.getElementById(' . $buttonId . ')).show(); $(document.getElementById(' . $buttonId . ')).focus();" class="ui-button ui-corner-all c4g_date_field_button_interactive"><i class="far fa-calendar-alt"></i></span>';
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

        return $fieldTitle . '<div class="c4g_tile value">' . date($GLOBALS['TL_CONFIG']['dateFormat'], $date) . '</div>';
    }

    /**
     * Public method that will be called in translateFieldValues in C4GBrickModuleParent
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
            return $value;
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
    public function setCustomLanguage($customLanguage): void
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
    public function getSortType(): string
    {
        return $this->sortType;
    }

    /**
     * @param string $sortType
     */
    public function setSortType(string $sortType): void
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
    public function setFlipButtonPosition(bool $flipButtonPosition): void
    {
        $this->flipButtonPosition = $flipButtonPosition;
    }
}
