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

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldType;
use Contao\Date;

class C4GTimeField extends C4GBrickField
{
    /**
     * @param string $type
     */
    public function __construct(string $type = C4GBrickFieldType::TIME)
    {
        parent::__construct($type);
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
        $id = $this->createFieldID();
        $title = $this->getTitle();
        $required = $this->generateRequiredString($data, $dialogParams, $fieldList);
        $value = $this->generateInitialValue($data);
        if ($value > 0) {
            $value = date($GLOBALS['TL_CONFIG']['timeFormat'], $value);
        } else {
            $value = '';
        }

        $result = '';

        if ($this->isShowIfEmpty() || !empty($value)) {
            $condition = $this->createConditionData($fieldList, $data);

            $result =
                $this->addC4GField($condition,$dialogParams,$fieldList,$data,
                '<div class="c4g_timefield" ' . $required . '>' .
                '<input ' . $required . ' type="text" class="timepicker input formdata c4g__form-control c4g__form-text-input" id="' . $id . '" value="' . $value . '" name="' . $fieldName . '" ' . $condition['conditionPrepare'] . ' maxlength="5" size="5" placeholder="__:__">' .
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
        $timezone = new \DateTimeZone($GLOBALS['TL_CONFIG']['timeZone']);
        $date = \DateTime::createFromFormat($GLOBALS['TL_CONFIG']['timeFormat'], $dlgvalue, $timezone);
        if ($date) {
            $date->Format($GLOBALS['TL_CONFIG']['timeFormat']);

            $objDate = new Date(date($GLOBALS['TL_CONFIG']['timeFormat'], $date->getTimestamp()), Date::getFormatFromRgxp('time'));
            $dlgValue = $objDate->tstamp;

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
        $timezone = new \DateTimeZone($GLOBALS['TL_CONFIG']['timeZone']);
        $date = \DateTime::createFromFormat($GLOBALS['TL_CONFIG']['timeFormat'], $fieldData, $timezone);
        if ($date) {
            $date->Format($GLOBALS['TL_CONFIG']['timeFormat']);

            $objDate = new Date(date($GLOBALS['TL_CONFIG']['timeFormat'], $date->getTimestamp()), Date::getFormatFromRgxp('time'));
            $fieldData = $objDate->tstamp;
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

        return $fieldTitle . '<div class="c4g_tile_value">' . date($GLOBALS['TL_CONFIG']['timeFormat'], $date) . '</div>';
    }

    /**
     * Public method that will be called to view the value
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
