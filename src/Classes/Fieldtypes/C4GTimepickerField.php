<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2022, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldType;

class C4GTimepickerField extends C4GBrickField
{
    /**
     * @param string $type
     */
    public function __construct(string $type = C4GBrickFieldType::TIMEPICKER)
    {
        parent::__construct($type);
    }
    /**
     * @param $fieldList
     * @param $field
     * @param $data
     * @param $viewType
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $id = 'c4g_' . $this->getFieldName();
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
            $description = $this->getC4GDescriptionLabel($this->getDescription(), $condition);

            $result =
                $this->addC4GField($condition,$dialogParams,$fieldList,$data,
                '<input type="time"' . $required . ' ' . $condition['conditionPrepare'] . ' id="' . $id . '" class="formdata c4g__form-control c4g__form-time-input ' . $id . '" name="' . $this->getFieldName() . '" title="' . $this->getTitle() . '" value="' . $value . '">');
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
