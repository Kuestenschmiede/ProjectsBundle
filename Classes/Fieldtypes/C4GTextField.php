<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    6
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldText;

class C4GTextField extends C4GBrickFieldText
{
    protected $size = 255;
    protected $maxLength = 255;
    protected $simpleTextWithoutEditing = false; //Renders HTML tags, never use this to display user-generated data

    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        $id = "c4g_" . $this->getFieldName();
        $required = $this->generateRequiredString($data, $dialogParams);
        $value = $this->generateInitialValue($data);
        $result = '';

        if ($this->isShowIfEmpty() || !empty(trim($value))) {

            $condition = $this->createConditionData($fieldList, $data);

            if ($this->isSimpleTextWithoutEditing()) {
                $result = $this->addC4GField($condition, $dialogParams, $fieldList, $data,
                    "<div $required ".$condition['conditionPrepare']." id=\"$id\" class=\"c4g_non_input\">$value</div>");

            } else {
                $result = $this->addC4GField($condition, $dialogParams, $fieldList, $data,
                    '<input ' . $required . ' ' . $condition['conditionPrepare'] . ' type="text" id="' . $id . '" class="formdata ' . $id . '" size="'.$this->size.'"  maxLength="'.$this->maxLength.'" name="' . $this->getFieldName() . '" value="' . $value . '">');
            }
        }

        return $result;
    }

    /**
     * @param $dbValues
     * @param $dlgValues
     * @return array|C4GBrickFieldCompare|null
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
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param $size
     * @return $this|C4GBrickFieldText
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * @param $maxLength
     * @return $this|C4GBrickFieldText
     */
    public function setMaxLength($maxLength)
    {
        $this->maxLength = $maxLength;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSimpleTextWithoutEditing(): bool
    {
        return $this->simpleTextWithoutEditing;
    }

    /**
     * @param bool $simpleTextWithoutEditing
     */
    public function setSimpleTextWithoutEditing(bool $simpleTextWithoutEditing): void
    {
        $this->simpleTextWithoutEditing = $simpleTextWithoutEditing;
    }

}