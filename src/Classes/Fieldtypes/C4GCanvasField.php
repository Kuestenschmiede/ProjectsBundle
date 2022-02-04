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

class C4GCanvasField extends C4GBrickField
{
    private $width = 256;
    private $height = 192;

    /**
     * @param string $type
     */
    public function __construct(string $type = C4GBrickFieldType::CANVAS)
    {
        parent::__construct($type);
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

        $additionalClasses = '';
        if ($this->isDatabaseField()) {
            $additionalClasses = ' formdata';
        }

        if ($this->isShowIfEmpty() || !empty($value)) {
            $condition = $this->createConditionData($fieldList, $data);
            $result =
                $this->addC4GField($condition,$dialogParams,$fieldList,$data,
                '<canvas ' . $required . ' ' . $condition['conditionPrepare'] . ' id="' . $id . '" class="' . $id . $additionalClasses . ' c4g__form-canvas" name="' . $this->getFieldName() . '" width="' . $this->getWidth() . '" height="' . $this->getHeight() . '" >' . $value . ' </canvas>');
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
//        $fieldname = $this->getFieldName();
//        $dbValue = $dbValues->$fieldname;
//        $dlgvalue = $dlgValues[$this->getFieldName()];
//        $dbValue = trim($dbValue);
//        $dlgValue = trim($dlgvalue);
//        $result = null;
//        if (strcmp($dbValue, $dlgValue) != 0) {
//            $result = new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
//        }
//        return $result;

        return null;
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

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth(int $width): void
    {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight(int $height): void
    {
        $this->height = $height;
    }
}
