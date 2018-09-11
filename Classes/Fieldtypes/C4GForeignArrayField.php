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


use con4gis\CoreBundle\Resources\contao\classes\C4GUtils;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectsBundle\Classes\Database\C4GBrickDatabase;
use con4gis\ProjectsBundle\Classes\Database\C4GBrickDatabaseType;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;

class C4GForeignArrayField extends C4GBrickField
{

    private $foreignTable = '';             //Source table for the data (frontend display). May be empty if $foreignFieldList is empty.
    private $foreignKey = '';               //Key by which the data will be matched (foreign key comparison). May be empty if $foreignFieldList is empty.
    private $foreignFieldList = array();    //The fields which display the data. If no fields are give, no frontend output is created.
    private $databaseType = C4GBrickDatabaseType::DCA_MODEL;
    private $entityClass = '';
    private $modelClass = '';
    private $findBy = array();
    private $database = null;
    private $brickDatabase = null;
    private $where = array();
    private $identifier = '';               //Don't ask why, just set this to the same value as the field name.
    private $delimiter = '#';

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
            $loadedDataHtml = '';
            $subData = array();
            foreach ($data as $key => $value) {
                $keyArray = explode($this->delimiter,$key);
                if ($keyArray && $keyArray[0] == $this->identifier) {
                    $subData[$keyArray[0].$this->delimiter.$keyArray[2]][$keyArray[1]] = $value;
                }
            }

            foreach ($subData as $dbVals) {
                $dbVals = C4GBrickCommon::arrayToObject($dbVals);
                foreach ($this->foreignFieldList as $field) {
                    $loadedDataHtml .= $field->getC4GDialogField($this->foreignFieldList, $dbVals, $dialogParams, $additionalParams);
                }
            }

            $name = $this->getFieldName();
            $title = $this->getTitle();
            $html = "<div class='c4g_array_field_container' id='c4g_$name'>";
            $html .= $this->addC4GFieldLabel("c4g_$name", $title, $this->isMandatory(), $this->createConditionData($fieldList, $data), $fieldList, $data, $dialogParams);
            $html .= "<div class='c4g_array_field' id='c4g_dialog_$name'>";

            $loadedDataHtml = str_replace('"', "'", $loadedDataHtml);
            $html .= $loadedDataHtml;

            $html .= "</div>";
            $html .= "</div>";

            return $html;
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
     * @return mixed
     */
    public function getDatabaseType()
    {
        return $this->databaseType;
    }

    /**
     * @param mixed $databaseType
     * @return C4GForeignArrayField
     */
    public function setDatabaseType($databaseType)
    {
        $this->databaseType = $databaseType;
        return $this;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * @param string $entityClass
     * @return C4GForeignArrayField
     */
    public function setEntityClass(string $entityClass): C4GForeignArrayField
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return $this->modelClass;
    }

    /**
     * @param string $modelClass
     * @return C4GForeignArrayField
     */
    public function setModelClass(string $modelClass): C4GForeignArrayField
    {
        $this->modelClass = $modelClass;
        return $this;
    }

    /**
     * @return array
     */
    public function getFindBy(): array
    {
        return $this->findBy;
    }

    /**
     * @param array $findBy
     * @return C4GForeignArrayField
     */
    public function setFindBy(array $findBy): C4GForeignArrayField
    {
        $this->findBy = $findBy;
        return $this;
    }

    /**
     * @return null
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param \Contao\Database $database
     * @return $this
     */
    public function setDatabase(\Contao\Database $database)
    {
        $this->database = $database;
        return $this;
    }

    /**
     * @return null
     */
    public function getBrickDatabase()
    {
        return $this->brickDatabase;
    }

    /**
     * @param C4GBrickDatabase $brickDatabase
     * @return C4GForeignArrayField
     */
    public function setBrickDatabase(C4GBrickDatabase $brickDatabase)
    {
        $this->brickDatabase = $brickDatabase;
        return $this;
    }

    /**
     * @return array
     */
    public function getWhere(): array
    {
        return $this->where;
    }

    /**
     * @param array $where
     * @return C4GForeignArrayField
     */
    public function setWhere(array $where): C4GForeignArrayField
    {
        $this->where = $where;
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

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     * @return C4GForeignArrayField
     */
    public function setIdentifier(string $identifier): C4GForeignArrayField
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * @return string
     */
    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    /**
     * @param string $delimiter
     * @return C4GForeignArrayField
     */
    public function setDelimiter(string $delimiter): C4GForeignArrayField
    {
        $this->delimiter = $delimiter;
        return $this;
    }



}