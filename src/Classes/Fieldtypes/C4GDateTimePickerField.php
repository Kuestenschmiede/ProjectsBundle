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
namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\CoreBundle\Resources\contao\models\C4gLogModel;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;

class C4GDateTimePickerField extends C4GBrickField
{
    // customize single date fields
    private $customFormat = null;
    private $dateField = ''; //merged with time if necessary
    private $timeField = ''; //merged with date if necessary

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
        $id = 'c4g_' . $fieldName;
        $title = $this->getTitle();
        $required = $this->generateRequiredString($data, $dialogParams);
        $value = $this->generateInitialValue($data);

        if (is_int($value) && $this->dateField) {
            $dateField = $this->dateField;
            $date = $data->$dateField;
            if ($date) {
                //ToDo move to helper classes
                $hour = date('h', $value);
                $minute = date('i', $value);
                $second = date('s', $value);
                $year = date('Y', $date);
                $month = date('m', $date);
                $day = date('d', $date);

                $value = mktime(
                    intval($hour),
                    intval($minute),
                    intval($second),
                    intval($month),
                    intval($day),
                    intval($year)
                );
            }
        } elseif (is_int($value) && $this->timeField) {
            $timeField = $this->timeField;
            $time = $data->$timeField;
            if ($time) {
                //ToDo move to helper classes
                $hour = date('h', $time);
                $minute = date('i', $time);
                $second = date('s', $time);
                $year = date('Y', $value);
                $month = date('m', $value);
                $day = date('d', $value);

                $value = mktime(
                    intval($hour),
                    intval($minute),
                    intval($second),
                    intval($month),
                    intval($day),
                    intval($year)
                );
            }
        }

        if ($this->customFormat) {
            $dateFormat = $this->customFormat;
        } else {
            $dateFormat = $GLOBALS['TL_CONFIG']['datimFormat'];
        }
        if ($value > 0) {
            $value = date($dateFormat, $value);
        } else {
            $value = '';
        }
        $result = '';

        if ($this->isShowIfEmpty() || !empty($value)) {
            $condition = $this->createConditionData($fieldList, $data);
            $description = $this->getC4GDescriptionLabel($this->getDescription(), $condition);

            $result =
                $this->addC4GField($condition,$dialogParams,$fieldList,$data,
                '<input ' . $required . ' type="text" id="' . $id . '" class="formdata ' . $id . '" onmousedown="C4GDateTimePicker(\'' . $id . '\')" name="' . $fieldName . '" value="' . $value . '" ' . $condition['conditionPrepare'] . '>');
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
        $date = \DateTime::createFromFormat($GLOBALS['TL_CONFIG']['datimFormat'], $dlgvalue);
        if ($date) {
            $date->Format($GLOBALS['TL_CONFIG']['datimFormat']);
            $dlgValue = $date->getTimestamp();
            if (strcmp($dbValue, $dlgValue) != 0) {
                $result = new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
            }
        } else {
            $dlgValue = '';
            $result = new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
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
        if (strpos($fieldData, '-') === 9) {
            // special case. format "dd.mm.yy - H:i:s"
            $arrDate = explode('-', $fieldData);
            $date = trim($arrDate[0]);
            // fill the year to 4 digits
            $tmpDate = explode('.', $date);
            if (strlen($tmpDate[2]) === 2) {
                // ToDo filtern ob 19 oder 20 vorgehängt werden muss
                $tmpDate[2] = '20' . $tmpDate[2];
            }
            $date = implode('.', $tmpDate);
            $time = $arrDate[1];
            // split into hour, minute and seconds
            $arrTime = explode(':', $time);
            $objDate = new \DateTime($date);

            try {
                $diff = new \DateInterval('PT' . trim($arrTime[0]) . 'H' . trim($arrTime[1]) . 'M');
            } catch (\Exception $exception) {
                // fallback, this results in $diff being a time difference of 0
                $diff = \DateInterval::createFromDateString($time);
                C4gLogModel::addLogEntry('projects', $exception->getMessage());
            }
            $objDate->add($diff);
            $fieldData = $objDate->getTimestamp();

            return $fieldData;
        }
        $date = strtotime($fieldData);
        $datetime = new \DateTime($fieldData);
        if ($date) {
            $fieldData = $date;
        } elseif ($datetime) {
            $fieldData = $datetime->getTimestamp();
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
        $value = $rowData->$fieldName;
        if ($value) {
            if ($this->dateField) {
                $dateField = $this->dateField;
                $date = $rowData->$dateField;
                if ($date) {
                    //ToDo move to helper classes
                    $hour = date('H', $value);
                    $minute = date('i', $value);
                    $second = date('s', $value);
                    $year = date('Y', $date);
                    $month = date('m', $date);
                    $day = date('d', $date);

                    $value = mktime(
                        intval($hour),
                        intval($minute),
                        intval($second),
                        intval($month),
                        intval($day),
                        intval($year)
                    );
                }
            } elseif ($this->timeField) {
                $timeField = $this->timeField;
                $time = $rowData->$timeField;
                if ($time) {
                    //ToDo move to helper classes
                    $hour = date('H', $time);
                    $minute = date('i', $time);
                    $second = date('s', $time);
                    $year = date('Y', $value);
                    $month = date('m', $value);
                    $day = date('d', $value);

                    $value = mktime(
                        intval($hour),
                        intval($minute),
                        intval($second),
                        intval($month),
                        intval($day),
                        intval($year)
                    );
                }
            }

            //ToDo check old version. This won't work because the span isn't printable and sortable.
            //return "<span style='display:none;'>$value</span>" . '<span>' . date($GLOBALS['TL_CONFIG']['datimFormat'], $value) . '</span>';
            return date($GLOBALS['TL_CONFIG']['datimFormat'], $value);
        }

        return '';
    }

    /**
     * @return null
     */
    public function getCustomFormat()
    {
        return $this->customFormat;
    }

    /**
     * @param null $customFormat
     */
    public function setCustomFormat($customFormat)
    {
        $this->customFormat = $customFormat;
    }

    /**
     * @return string
     */
    public function getDateField(): string
    {
        return $this->dateField;
    }

    /**
     * @param string $dateField
     */
    public function setDateField(string $dateField): void
    {
        $this->dateField = $dateField;
    }

    /**
     * @return string
     */
    public function getTimeField(): string
    {
        return $this->timeField;
    }

    /**
     * @param string $timeField
     */
    public function setTimeField(string $timeField): void
    {
        $this->timeField = $timeField;
    }
}
