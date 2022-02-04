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

class C4GTextareaField extends C4GBrickField
{
    /**
     * @param string $type
     */
    public function __construct(string $type = C4GBrickFieldType::TEXTAREA)
    {
        parent::__construct($type);
        $this->setSize(3);
    }

    /**
     * @param $field
     * @param $data
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $id = 'c4g_' . $this->getFieldName();
        $required = $this->generateRequiredString($data, $dialogParams, $fieldList);
        $value = $this->generateInitialValue($data);
        $result = '';

        if (!($this->getSize())) {
            $size = 15;
        } else {
            $size = $this->getSize();
        }
        //onkeydown="if (event.keyCode == 13) { String.fromCharCode(13); return false; }"

        if ($this->isShowIfEmpty() || !empty(trim($value))) {
            $condition = $this->createConditionData($fieldList, $data);
            if ($this->getMaxLength() > 0) {
                $maxlength = 'maxlength="' . $this->getMaxLength() . '"';
            } else {
                $maxlength = '';
            }
            $result =
                $this->addC4GField($condition,$dialogParams,$fieldList,$data,
                '<textarea ' . $required . ' ' . $condition['conditionPrepare'] . ' id="' . $id . '" class="formdata c4g__form-control c4g__form-textarea ' . $id . '" name="' . $this->getFieldName() . '" rows="' . $size . '"' . $maxlength . '" >' . $value . ' </textarea>');
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
        $dbValue = trim($dbValue);
        $dlgValue = trim($dlgvalue);
        $result = null;
        if (strcmp($dbValue, $dlgValue) != 0) {
            $result = new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
        }

        return $result;
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

        return $fieldTitle . '<div class="c4g_tile_value">' . $element->$fieldName . '</div>';
    }
}
