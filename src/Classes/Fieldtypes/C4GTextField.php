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
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldText;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldType;
use Contao\Controller;

class C4GTextField extends C4GBrickFieldText
{
    protected $size = 255;
    protected $maxLength = 255;
    protected $simpleTextWithoutEditing = false; //Renders HTML tags, never use this to display user-generated data
    protected $ariaLabel = '';

    /**
     * @param string $type
     */
    public function __construct(string $type = C4GBrickFieldType::TEXT)
    {
        parent::__construct($type);
    }

    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $id = 'c4g_' . $this->getFieldName();
        if ($this->getAdditionalID()) {
            $id .= '_' . $this->getAdditionalID();
        }
        $required = $this->generateRequiredString($data, $dialogParams, $fieldList);
        $value = $this->generateInitialValue($data);
        if ($this->replaceInsertTag) {
            $value = Controller::replaceInsertTags($value);
        }
        $result = '';

        if ($this->isShowIfEmpty() || !empty(trim($value))) {
            $condition = $this->createConditionData($fieldList, $data);

            if ($this->isSimpleTextWithoutEditing()) {
                $result = $this->addC4GField($condition, $dialogParams, $fieldList, $data,
                    '<div ' . $condition['conditionPrepare'] . " id=\"$id\" class=\"c4g_non_input formdata\">$value</div>");
            } else {
                if ($this->placeholder !== '') {
                    $placeholder = ' placeholder="' . $this->placeholder . '"';
                } else {
                    $placeholder = '';
                }

                if ($this->ariaLabel !== '') {
                    $aria = 'aria-label="' . $this->ariaLabel . '"';
                }

                $result = $this->addC4GField($condition, $dialogParams, $fieldList, $data,
                    '<input ' . $required . ' ' . $condition['conditionPrepare'] . ' type="text" id="' . $id . '" class="formdata c4g__form-control c4g__form-text-input ' . $id . '" size="' . $this->size . '"  maxLength="' . $this->maxLength . '" name="' . $this->getFieldName() . '" value="' . $value . '"' . $placeholder . $aria . '>');
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
        $dbValue = str_replace(['&#40;', '&#41;'], ['(', ')'], trim($dbValue));
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
     * @return C4GTextField
     */
    public function setSimpleTextWithoutEditing(bool $simpleTextWithoutEditing = true): C4GTextField
    {
        $this->simpleTextWithoutEditing = $simpleTextWithoutEditing;

        return $this;
    }

    /**
     * @return string
     */
    public function getAriaLabel(): string
    {
        return $this->ariaLabel;
    }

    /**
     * @param string $ariaLabel
     * @return C4GTextField
     */
    public function setAriaLabel(string $ariaLabel): C4GTextField
    {
        $this->ariaLabel = $ariaLabel;

        return $this;
    }
}
