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

namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;

class C4GCanvasField extends C4GBrickField
{
    private $width = 256;
    private $height = 192;

    /**
     * @param $field
     * @param $data
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {

        $id = "c4g_" . $this->getFieldName();
        $required = $this->generateRequiredString($data, $dialogParams);
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
                '<canvas ' . $required . ' ' . $condition['conditionPrepare'] . ' id="' . $id . '" class="' . $id . $additionalClasses . ' ui-corner-all" name="' . $this->getFieldName() . '" width="'.$this->getWidth().'" height="'.$this->getHeight().'" >' . $value . ' </canvas>');
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
        return $fieldTitle . '<div class="c4g_tile value">' . $element->$fieldName . '</div>';
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