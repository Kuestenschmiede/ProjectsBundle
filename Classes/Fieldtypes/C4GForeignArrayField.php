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

class C4GForeignArrayField extends C4GBrickField
{
    // private $fieldName = '';

    private $foreignTable = '';             //Source table for the data (frontend display). May be empty if $foreignFieldList is empty.
    private $foreignKey = '';               //Key by which the data will be matched (foreign key comparison). May be empty if $foreignFieldList is empty.
    private $foreignFieldList = array();    //The fields which display the data. If no fields are give, no frontend output is created.

    private $autoAdd = false;   //Automatically add a value to the array in the database if it does not exist yet.
    private $autoAddData = '';  //The data to add automatically. "member" = The current member id.

    public function __construct() {
        $this->setComparable(false);
    }

    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        if (!$this->foreignFieldList) {
            return '';
        } else {
            return '';
        }
    }

    public function compareWithDB($dbValue, $dlgvalue) {}

    public function createValue($dbValues, $dialogParams, $dialogValues, $useDoctrine) {
        $index = $this->getFieldName();
        $dataArray = array();
        if ($useDoctrine) {
            $dataArray = $dbValues->$index;
        } elseif ($dbValues->$index) {
            $dataArray = unserialize($dbValues->$index);
        }
        if ($this->autoAdd) {
            switch ($this->autoAddData) {
                case 'member':
                    if (!in_array($dialogParams->getMemberId(), $dataArray)) {
                        $dataArray[] = intval($dialogParams->getMemberId());
                    }
                    break;
                default:
                    break;
            }
        }
        if ($useDoctrine) {
            return $dataArray;
        } else {
            $test = serialize($dataArray);
            return $test;
        }
    }

    /**
     * @return mixed
     */
    public function getForeignTable()
    {
        return $this->foreignTable;
    }

    /**
     * @param mixed $foreignTable
     * @return C4GForeignArrayField
     */
    public function setForeignTable($foreignTable)
    {
        $this->foreignTable = $foreignTable;
        return $this;
    }

    /**
     * @return string
     */
    public function getForeignKey(): string
    {
        return $this->foreignKey;
    }

    /**
     * @param string $foreignKey
     * @return C4GForeignArrayField
     */
    public function setForeignKey(string $foreignKey): C4GForeignArrayField
    {
        $this->foreignKey = $foreignKey;
        return $this;
    }

    /**
     * @return array
     */
    public function getForeignFieldList(): array
    {
        return $this->foreignFieldList;
    }

    /**
     * @param array $foreignFieldList
     * @return C4GForeignArrayField
     */
    public function setForeignFieldList(array $foreignFieldList): C4GForeignArrayField
    {
        $this->foreignFieldList = $foreignFieldList;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAutoAdd(): bool
    {
        return $this->autoAdd;
    }

    /**
     * @param bool $autoAdd
     * @return C4GForeignArrayField
     */
    public function setAutoAdd(bool $autoAdd): C4GForeignArrayField
    {
        $this->autoAdd = $autoAdd;
        return $this;
    }

    /**
     * @return string
     */
    public function getAutoAddData(): string
    {
        return $this->autoAddData;
    }

    /**
     * @param string $autoAddData
     * @return C4GForeignArrayField
     */
    public function setAutoAddData(string $autoAddData): C4GForeignArrayField
    {
        $this->autoAddData = $autoAddData;
        return $this;
    }



}