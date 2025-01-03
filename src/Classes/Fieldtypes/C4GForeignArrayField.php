<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 10
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2025, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\CoreBundle\Classes\C4GUtils;
use con4gis\CoreBundle\Classes\Helper\ArrayHelper;
use con4gis\ProjectsBundle\Classes\Database\C4GBrickDatabase;
use con4gis\ProjectsBundle\Classes\Database\C4GBrickDatabaseType;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldType;

class C4GForeignArrayField extends C4GBrickField
{
    private $foreignTable = '';             //Source table for the data (frontend display). May be empty if $foreignFieldList is empty.
    private $foreignKey = '';               //Key by which the data will be matched (foreign key comparison). May be empty if $foreignFieldList is empty.
    private $foreignFieldList = [];    //The fields which display the data. If no fields are give, no frontend output is created.
    private $databaseType = C4GBrickDatabaseType::DCA_MODEL;
    private $entityClass = '';
    private $modelClass = '';
    private $findBy = [];
    private $database = null;
    private $brickDatabase = null;
    private $where = [];
    private $delimiter = '#';
    private $identifier = '';
    private $parentFieldName = '';
    private $parentFieldDelimiter = '';

    private $autoAdd = false;   //Automatically add a value to the array in the database if it does not exist yet.
    private $autoAddData = '';  //The data to add automatically. "member" = The current member id.

    /**
     * @param string $type
     */
    public function __construct(string $type = C4GBrickFieldType::FOREIGNARRAY)
    {
        $this->setComparable(false);
        parent::__construct($type);
    }

    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        if (!$this->foreignFieldList) {
            return '';
        }
        $loadedDataHtml = '';
        $subData = [];
        foreach ($data as $key => $value) {
            if (C4GUtils::startsWith($key, $this->parentFieldName)) {
                $base = explode($this->parentFieldDelimiter, $key)[1];
            }
            $keyArray = explode($this->delimiter, $base ?? $key);
            if ($keyArray && $keyArray[0] == $this->identifier && $keyArray[1] && $keyArray[2]) {
                $subData[$keyArray[0] . $this->delimiter . $keyArray[2]][$keyArray[1]] = $value;
            }
        }

        foreach ($subData as $key => $dbVals) {
            $dbVals = ArrayHelper::arrayToObject($dbVals);

            foreach ($this->foreignFieldList as $field) {
                $loadedDataHtml .= $field->getC4GDialogField($this->foreignFieldList, $dbVals, $dialogParams, $additionalParams);
            }
        }

        $html = '';
        if ($this->isEditable() || $loadedDataHtml) {
            $name = $this->getFieldName();
            $title = $this->getTitle();
            $html = "<div class='c4g_array_field_container' id='c4g_$name'>";
            $html .= $this->addC4GFieldLabel("c4g_$name", $title, $this->isMandatory(), $this->createConditionData($fieldList, $data), $fieldList, $data, $dialogParams);
            $html .= "<div class='c4g_array_field' id='c4g_dialog_$name'>";

            $loadedDataHtml = str_replace('"', "'", $loadedDataHtml);
            $html .= $loadedDataHtml;

            $html .= '</div>';
            $html .= '</div><br>';
        }

        return $html;
    }

    public function compareWithDB($dbValue, $dlgvalue)
    {
    }

    public function createValue($dbValues, $dialogParams, $dialogValues, $useDoctrine)
    {
        $index = $this->getFieldName();
        $dataArray = [];
        if ($useDoctrine) {
            $dataArray = $dbValues->$index;
        } elseif ($dbValues->$index) {
            $dataArray = \Contao\StringUtil::deserialize($dbValues->$index);
        }
        if ($this->autoAdd) {
            switch ($this->autoAddData) {
                case 'member':
                    if (!in_array($dialogParams->getMemberId(), $dataArray)) {
                        $dataArray[] = intval($dialogParams->getMemberId());
                    } else {
                        unset($dataArray[array_search($dialogParams->getMemberId(), $dataArray)]);
                        $dataArray[] = intval($dialogParams->getMemberId());
                    }

                    break;
                default:
                    break;
            }
        }
        if ($useDoctrine) {
            return $dataArray;
        }
        $test = serialize($dataArray);

        return $test;
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
    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    /**
     * @param string $delimiter
     * @return C4GForeignArrayField
     * @throws \Exception
     */
    public function setDelimiter(string $delimiter): C4GForeignArrayField
    {
        if (($delimiter === '_') || ($delimiter === '?')) {
            throw new \Exception('C4GForeignArrayField::delimiter must not be _ or ?.');
        }
        $this->delimiter = $delimiter;

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
    public function getParentFieldName(): string
    {
        return $this->parentFieldName;
    }

    /**
     * @param string $parentFieldName
     */
    public function setParentFieldName(string $parentFieldName): void
    {
        $this->parentFieldName = $parentFieldName;
    }

    /**
     * @return string
     */
    public function getParentFieldDelimiter(): string
    {
        return $this->parentFieldDelimiter;
    }

    /**
     * @param string $parentFieldDelimiter
     */
    public function setParentFieldDelimiter(string $parentFieldDelimiter): void
    {
        $this->parentFieldDelimiter = $parentFieldDelimiter;
    }
}
