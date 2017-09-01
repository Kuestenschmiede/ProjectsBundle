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

class C4GTimeField extends C4GBrickField
{
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
        $id = $this->createFieldID();
        $title = $this->getTitle();
        $required = $this->generateRequiredString($data, $dialogParams);
        $value = $this->generateInitialValue($data);
        if ($value > 0) {
            $value = date($GLOBALS['TL_CONFIG']['timeFormat'], $value);
        } else {
            $value = "";
        }

        $result = '';

        if ($this->isShowIfEmpty() || !empty($value)) {

            $condition = $this->createConditionData($fieldList, $data);

            $result =
                $this->addC4GField($condition,$dialogParams,$fieldList,$data,
                '<div class="c4g_timefield">' .
                '<input ' . $required . ' type="text" class="timepicker input formdata" id="' . $id . '" value="' . $value . '" name="' . $fieldName . '" ' . $condition['conditionPrepare'] . ' maxlength="5" size="5" placeholder="__:__">' .
                '<a class="gettime" onclick="C4GTimePicker(\'' . $id . '\', \'gettime\', this)"></a>' .
                '</div>');
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
        $date = \DateTime::createFromFormat($GLOBALS['TL_CONFIG']['timeFormat'], $dlgvalue);
        if ($date) {
            $date->Format($GLOBALS['TL_CONFIG']['timeFormat']);
            $dlgValue = $date->getTimestamp();
            if (strcmp($dbValue, $dlgValue) != 0) {
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
        $date = \DateTime::createFromFormat($GLOBALS['TL_CONFIG']['timeFormat'], $fieldData);
        if ($date) {
            $date->Format($GLOBALS['TL_CONFIG']['timeFormat']);
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
            return date($GLOBALS['TL_CONFIG']['timeFormat'], $date);
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
        return $fieldTitle . '<div class="c4g_tile value">' . date($GLOBALS['TL_CONFIG']['timeFormat'],$date) . '</div>';
    }

    /**
     * Public method that will be called in translateFieldValues in C4GBrickModuleParent
     * @param $value
     * @return mixed
     */
    public function translateFieldValue($value)
    {
        $timestamp = 0; //sollte nicht vorkommen.
        if ($value) {
            $timestamp = strtotime($value);
        }

        return date($GLOBALS['TL_CONFIG']['timeFormat'], $timestamp);
    }
}