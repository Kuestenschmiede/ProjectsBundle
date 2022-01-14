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

/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2019
 * @link      https://www.kuestenschmiede.de
 */
namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldType;

class C4GTimeSelectTableField extends C4GBrickField
{
    private $type = C4GBrickFieldType::TIME;

    protected $begin = 0;
    protected $end = 0;
    protected $interval = 0;
    protected $dateFormat = '';
    protected $jsCallback = '';

    /**
     * @param C4GBrickField[] $fieldList
     * @param $data
     * @param C4GBrickDialogParams $dialogParams
     * @param array $additionalParams
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $id = 'c4g_' . $this->getFieldName();
        $required = $this->generateRequiredString($data, $dialogParams);
        $value = $this->generateInitialValue($data);
        $condition = $this->createConditionData($fieldList, $data);

        if ($this->initInvisible === true) {
            $style = 'style="display: none;"';
        } else {
            $style = '';
        }

        $fieldData = '<input ' . $required . ' ' . $condition['conditionPrepare'] . ' type="hidden" id="' . $id . '" class="formdata c4g__form-time-select ' . $id . '" name="' . $this->getFieldName() . '" value="' . $value . '">';
        $fieldData .= '<div class="c4g__form-time-select-table ' . $this->getStyleClass() . '" ' . $style . '>';

        $index = 0;
        while (($this->begin + ($index * $this->interval)) <= $this->end) {
            $begin = $this->begin + ($index * $this->interval);
            $javascript = "document.getElementById('$id').value = " . $begin . ';';

            if ($this->jsCallback !== '') {
                $javascript .= $this->jsCallback;
            }

            $fieldData .= "<button id=\"$id\" class=\"ui-button ui-corner-all ui-widget\" onclick=\"$javascript\">" . date($this->dateFormat, $begin) . '</button>';
            $index += 1;
        }

        $fieldData .= '</div>';

        $html = $this->addC4GField($condition, $dialogParams, $fieldList, $data, $fieldData);

        return $html;
    }

    /**
     * @param $dbValues
     * @param $dlgValues
     * @return array|C4GBrickFieldCompare|null
     */
    public function compareWithDB($dbValues, $dlgValues)
    {
        $fieldName = $this->getFieldName();
        $dbValue = $dbValues->$fieldName;
        $dlgValue = $dlgValues[$fieldName];

        if ($dbValue != $dlgValue) {
            return new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
        }

        return null;
    }

    /**
     * @return int
     */
    public function getBegin(): int
    {
        return $this->begin;
    }

    /**
     * @param int $begin
     * @return C4GTimeSelectTableField
     */
    public function setBegin(int $begin): C4GTimeSelectTableField
    {
        $this->begin = $begin;

        return $this;
    }

    /**
     * @return int
     */
    public function getEnd(): int
    {
        return $this->end;
    }

    /**
     * @param int $end
     * @return C4GTimeSelectTableField
     */
    public function setEnd(int $end): C4GTimeSelectTableField
    {
        $this->end = $end;

        return $this;
    }

    /**
     * @return int
     */
    public function getInterval(): int
    {
        return $this->interval;
    }

    /**
     * @param int $interval
     * @return C4GTimeSelectTableField
     */
    public function setInterval(int $interval): C4GTimeSelectTableField
    {
        $this->interval = $interval;

        return $this;
    }

    /**
     * @return string
     */
    public function getDateFormat(): string
    {
        return $this->dateFormat;
    }

    /**
     * @param string $dateFormat
     * @return C4GTimeSelectTableField
     */
    public function setDateFormat(string $dateFormat): C4GTimeSelectTableField
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }

    /**
     * @return string
     */
    public function getJsCallback(): string
    {
        return $this->jsCallback;
    }

    /**
     * @param string $jsCallback
     * @return C4GTimeSelectTableField
     */
    public function setJsCallback(string $jsCallback): C4GTimeSelectTableField
    {
        $this->jsCallback = $jsCallback;

        return $this;
    }
}
