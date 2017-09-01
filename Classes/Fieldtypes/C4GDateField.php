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

namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;

class C4GDateField extends C4GBrickField
{
    private $minDate = null;
    private $maxDate = null;

    private $excludeWeekdays = null;
    private $excludeDates = null;

    /**
     * @param C4GBrickField[] $fieldList
     * @param $data
     * @param C4GBrickDialogParams $dialogParams
     * @param array $additionalParams
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        $fieldName = $this->getFieldName();
        $id = "c4g_" . $fieldName;
        $title = $this->getTitle();
        $required = $this->generateRequiredString($data, $dialogParams);
        $value = $this->generateInitialValue($data);

        if ($this->isCallOnChange()) {
            $changeAction = 'onchange="'.$this->getCallOnChangeFunction().'"';
        }

//        $intMonth = date("n", time());
//        $intDay   = date("j", time());
//        $intYear  = date("Y", time());
        //$intSummertime = date("I", time());

        //date_default_timezone_set('UTC');
        if (!$this->minDate || ($this->minDate == '') || ($this->minDate == 0)) {
            //$this->minDate = mktime(0, 0, 0, $intMonth, $intDay,  ($intYear - 25)/*, $intSummertime*/);
            $this->minDate = strtotime('-25 year');
        }

        if (!$this->maxDate || ($this->maxDate == '') || ($this->maxDate == 0)) {
            //$this->maxDate = mktime(23, 59, 0, $intMonth, $intDay, ($intYear + 25)/*, $intSummertime*/);
            $this->maxDate = strtotime('+25 year');
        }

        if ($value > 0) {
            $value = date($GLOBALS['TL_CONFIG']['dateFormat'], $value);
        } else {
            $value = "";
        }

        $result = '';

        $PHPFormatOptions = array('Y', 'm', 'd');
        $JSFormatOptions = array('yy', 'mm', 'dd');
        $format = str_replace($PHPFormatOptions, $JSFormatOptions, $GLOBALS['TL_CONFIG']['dateFormat']);

        if ($this->isShowIfEmpty() || !empty($value)) {

            $condition = $this->createConditionData($fieldList, $data);

            $result =
                $this->addC4GField($condition,$dialogParams,$fieldList,$data,
                    '<input ' . $required . ' type="text" id="' . $id . '" class="formdata ' . $id . '" '.$changeAction.' onmousedown="C4GDatePicker(\'' . $id . '\', \'date\', \'' .$this->minDate. '\', \'' . $this->maxDate . '\', \''.$format.'\',\'' . $GLOBALS["TL_LANGUAGE"] .'\',\'' . $this->excludeWeekdays . '\',\'' . $this->excludeDates . '\')" name="' . $fieldName . '" value="' . $value . '" ' . $condition['conditionPrepare'] . '>');
        }

        return $result;
    }

    /**
     * Method that will be called in the compareWithDB() in C4GBrickDialog
     * @param $dbValue
     * @param $dlgvalue
     * @return array
     */
    public function compareWithDB($dbValues, $dlgValues)
    {
        $fieldname = $this->getFieldName();
        $dbValue = $dbValues->$fieldname;
        $dlgvalue = $dlgValues[$this->getFieldName()];
        $result = null;
        if(!$this->isSearchField()) {
            $date = \DateTime::createFromFormat($GLOBALS['TL_CONFIG']['dateFormat'], $dlgvalue);
            if ($date) {
                $date->Format($GLOBALS['TL_CONFIG']['dateFormat']);
                $date->setTime(0,0,0);
                $dlgValue = $date->getTimestamp();
                if (strcmp($dbValue, $dlgValue) != 0) {
                    $result = new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
                }
            }
        } else {
            $dbValue = date($GLOBALS['TL_CONFIG']['dateFormat'], $dbValue);
            $dlgValue = $dlgvalue;
            //exception for C4GMatching
            if($dlgValue && strcmp($dbValue, $dlgValue) != 0) {
                $dlgValue = date($GLOBALS['TL_CONFIG']['dateFormat'], $dlgValue);
            }
            if($dlgValue && strcmp($dbValue, $dlgValue) != 0){
                $result = new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
            }
        }
        return $result;
    }

    /**
     * Method that will be called in the saveC4GDialog() in C4GBrickDialog
     * @return array
     */
    public function createFieldData($dlgValues)
    {
        $fieldData = $dlgValues[$this->getFieldName()];
        $format = $GLOBALS['TL_CONFIG']['dateFormat'];
        $date = \DateTime::createFromFormat($format, $fieldData);
        if ($date) {
            $date->Format($format);
            $date->setTime(0,0,0);
            $fieldData = $date->getTimestamp();
        } else {
            $fieldData = '';
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
        $fieldName = $this->getFieldName();
        $date = $rowData->$fieldName;
        if ($date) {
            return date($GLOBALS['TL_CONFIG']['dateFormat'], $date);
        } else {
            return '';
        }
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
        return $fieldTitle . '<div class="c4g_tile value">' . date($GLOBALS['TL_CONFIG']['dateFormat'],$date) . '</div>';
    }

    /**
     * Public method that will be called in translateFieldValues in C4GBrickModuleParent
     * @param $value
     * @return mixed
     */
    public function translateFieldValue($value)
    {
        $date = $value;
        $timestamp = strtotime($value);
        if(is_numeric($timestamp)){
            return $value;
        } else if($value == $GLOBALS['TL_LANG']['FE_C4G_DIALOG_COMPARE']['newEntry']){
            return $value;
        } else {
            return date($GLOBALS['TL_CONFIG']['dateFormat'], $date);
        }
    }

    /**
     * @return null
     */
    public function getMinDate()
    {
        return $this->minDate;
    }

    /**
     * @param null $minDate
     */
    public function setMinDate($minDate)
    {
        $this->minDate = $minDate;
    }

    /**
     * @return null
     */
    public function getMaxDate()
    {
        return $this->maxDate;
    }

    /**
     * @param null $maxDate
     */
    public function setMaxDate($maxDate)
    {
        $this->maxDate = $maxDate;
    }

    /**
     * @return null
     */
    public function getExcludeWeekdays()
    {
        return $this->excludeWeekdays;
    }

    /**
     * @param null $excludeWeekdays
     */
    public function setExcludeWeekdays($excludeWeekdays)
    {
        $this->excludeWeekdays = $excludeWeekdays;
    }

    /**
     * @return null
     */
    public function getExcludeDates()
    {
        return $this->excludeDates;
    }

    /**
     * @param null $excludeDates
     */
    public function setExcludeDates($excludeDates)
    {
        $this->excludeDates = $excludeDates;
    }
}