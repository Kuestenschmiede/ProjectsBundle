<?php
/**
 * Created by PhpStorm.
 * User: rro
 * Date: 30.08.18
 * Time: 09:27
 */

namespace con4gis\ProjectsBundle\Classes\Fieldtypes;


use con4gis\CoreBundle\Resources\contao\classes\C4GUtils;
use con4gis\ProjectsBundle\Classes\Database\C4GBrickDatabase;
use con4gis\ProjectsBundle\Classes\Database\C4GBrickDatabaseParams;
use con4gis\ProjectsBundle\Classes\Database\C4GBrickDatabaseType;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;

class C4GSubDialogField extends C4GBrickField
{
    private $table = '';
    private $fieldList = array();
    private $keyField = null;
    private $foreignKeyField = null;
    private $addButton = '';
    private $addButtonLabel = '';
    private $removeButton = '';
    private $databaseType = C4GBrickDatabaseType::DCA_MODEL;
    private $entityClass = '';
    private $modelClass = '';
    private $findBy = array();
    private $database = null;
    private $brickDatabase = null;
    private $where = array();
    private $delimiter = '#';
    private $wildcard = '?';

    public function __construct() {
        $this->database = \Database::getInstance();
        $this->setTableColumn(false);
        $this->setFormField(true);
//        $this->setComparable(false);
    }

    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        $name = $this->getFieldName();
        $title = $this->getTitle();
        $addButton = $this->addButton;
        $removeButton = $this->removeButton;

//        $fieldsHtml = "<div class='c4g_sub_dialog_set'>";
        $fieldsHtml = "";
        $fieldName = $this->keyField->getFieldName();
        $this->keyField->setFieldName($this->getFieldName().$this->delimiter.$fieldName.$this->delimiter.$this->wildcard);
        $fieldsHtml .= $this->keyField->getC4GDialogField($this->getFieldList(), $data, $dialogParams, $additionalParams = array());
        $this->keyField->setFieldName($fieldName);
        foreach ($this->fieldList as $field) {
            $fieldName = $field->getFieldName();
            $templateData = new \stdClass();
            foreach ($data as $key => $value) {
                $templateData->$key = '';
            }
            $field->setFieldName($this->getFieldName().$this->delimiter.$fieldName.$this->delimiter.$this->wildcard);
            $fieldsHtml .= $field->getC4GDialogField($this->getFieldList(), $templateData, $dialogParams, $additionalParams = array());
            $field->setFieldName($fieldName);
        }
//        $fieldsHtml .= '</div>';
        $fieldsHtml .= "<span class='ui-button ui-corner-all c4g_sub_dialog_remove_button' onclick='removeSubDialog(this,event);'>$removeButton</span>";
        $fieldsHtml = str_replace('"', "'", $fieldsHtml);

        /** Generate html for already loaded data sets if there are any */

        $numLoadedDataSets = 0;
        $loadedDataHtml = '';
        if ($data) {
            while (true) {  /** We break manually if the condition is not met. */
                $numLoadedDataSets += 1;
                $propertyName = $this->getFieldName() . $this->delimiter . $this->keyField->getFieldName() . $this->delimiter . $numLoadedDataSets;
                if ($data->$propertyName) {
                    /** skip data sets where any where clause is not met  */
                    foreach ($this->where as $clause) {
                        $field = $this->getFieldName() . $this->delimiter . $clause[0] . $this->delimiter . $numLoadedDataSets;
                        $value = $clause[1];
                        if ($data->$field != $value) {
                            continue 2;
                        }
                    }
                    $setData = new \stdClass();
                    foreach ($data as $key => $value) {
                        $start = C4GUtils::startsWith($key,$this->getFieldName());
                        $end = C4GUtils::endsWith($key,(string)$numLoadedDataSets);
                        if ($start && $end) {
                            $setData->$key = $value;
                        }
                    }

                    foreach ($setData as $key => $value) {
                        $data->$key = $value;
                    }

                    $loadedDataHtml .= "<div class='c4g_sub_dialog_set'>";
                    $fieldName = $this->keyField->getFieldName();
                    $this->keyField->setFieldName($this->getFieldName().$this->delimiter.$fieldName. $this->delimiter . $numLoadedDataSets);
                    $loadedDataHtml .= $this->keyField->getC4GDialogField($this->getFieldList(), $data, $dialogParams, $additionalParams = array());
                    $this->keyField->setFieldName($fieldName);
                    foreach ($this->fieldList as $field) {
                        $fieldName = $field->getFieldName();
                        $field->setFieldName($this->getFieldName().$this->delimiter.$fieldName. $this->delimiter . $numLoadedDataSets);
                        $loadedDataHtml .= $field->getC4GDialogField($this->getFieldList(), $data, $dialogParams, $additionalParams = array());
                        $field->setFieldName($fieldName);
                    }
                    $loadedDataHtml .= "<span class='ui-button ui-corner-all c4g_sub_dialog_remove_button' onclick='removeSubDialog(this,event);'>$removeButton</span>";
                    $loadedDataHtml .= '</div>';
                } else {
                    break;
                }
            }
        }

        $html = "<div class='c4g_sub_dialog_container' id='c4g_$name'>";
        $html .= "<template id='c4g_$name" . "_template"."'>$fieldsHtml</template>";
        $this->setAdditionalLabel("<span class='ui-button ui-corner-all c4g_sub_dialog_add_button' onclick='addSubDialog(this,event);' data-template='c4g_$name" . "_template"."' data-target='c4g_dialog_$name' data-field='$name' data-index='$numLoadedDataSets' data-wildcard='".$this->wildcard."'>$addButton</span><span class='c4g_sub_dialog_add_button_label'>$this->addButtonLabel</span>");
        $html .= $this->addC4GFieldLabel("c4g_$name", $title, $this->isMandatory(), $this->createConditionData($fieldList, $data), $fieldList, $data, $dialogParams);
        $html .= "<div class='c4g_sub_dialog' id='c4g_dialog_$name'>";

        $loadedDataHtml = str_replace('"', "'", $loadedDataHtml);
        $html .= $loadedDataHtml;

        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }

    public function compareWithDB($dbValues, $dlgValues, $name = '') {
        $changes = array();

        $subDlgValues = array();
        $indexList = array();
        $indexListIndex = 0;
        foreach ($dlgValues as $key => $value) {
            $keyArray = explode($this->delimiter,$key);
            if ($keyArray && $keyArray[0] == $this->getFieldName()) {
                $subDlgValues[$keyArray[0].$this->delimiter.$keyArray[2]][$keyArray[1]] = $value;
                $indexList[] = $keyArray[0].$this->delimiter.$keyArray[2];
                array_unique($indexList);
            } else {
                foreach ($this->fieldList as $field) {
                    if ($field instanceof C4GSubDialogField) {
                        if (strpos($key, $field->getDelimiter()) !== false) {
                            $subDlgValues[$indexList[$indexListIndex]][$key] = $value;
                            $indexListIndex += 1;
                        }
                    }
                }
            }
        }

        foreach ($this->fieldList as $field) {
            if ($field instanceof C4GSubDialogField) {
                foreach ($subDlgValues as $key => $values) {
                    $keyArray = explode($field->delimiter,$key);
                    if ($keyArray && $keyArray[0] && $keyArray[1] && $keyArray[2]) {
                        foreach ($subDlgValues[$key] as $k => $v) {
                            $index = $k.$field->getDelimiter().$keyArray[1].$field->getDelimiter().$keyArray[2];
                            $subDlgValues[$keyArray[0]][$index] = $v;
                            unset($subDlgValues[$key]);
                        }
                    }
                }
            }
        }

        if ($this->brickDatabase == null) {
            $databaseParams = new C4GBrickDatabaseParams($this->getDatabaseType());
            $databaseParams->setPkField('id');
            $databaseParams->setTableName($this->table);

            if (class_exists($this->entityClass)) {
                $class      = new \ReflectionClass($this->entityClass);
                $namespace  = $class->getNamespaceName();
                $dbClass    = str_replace($namespace . '\\', '', $this->entityClass);
                $dbClass    = str_replace('\\', '', $dbClass);
            } else {
                $class      = new \ReflectionClass(get_called_class());
                $namespace  = str_replace("contao\\modules", "database", $class->getNamespaceName());
                $dbClass    = $this->modelClass;
            }

            $databaseParams->setFindBy($this->findBy);
            $databaseParams->setEntityNamespace($namespace);
            $databaseParams->setDatabase($this->database);

            if ($this->databaseType == C4GBrickDatabaseType::DCA_MODEL) {
                $databaseParams->setModelClass($this->modelClass);
            } else {
                $databaseParams->setEntityClass($dbClass);
            }

            $this->brickDatabase = new C4GBrickDatabase($databaseParams);
        }

        if ($subDlgValues) {
            $foreignKey = $dbValues->id;
            $subDbValues = $this->brickDatabase->findBy($this->foreignKeyField->getFieldName(), $foreignKey);
            if (count($subDbValues) > count($subDlgValues)) {
                $changes[] = new C4GBrickFieldCompare($this, $subDbValues, $subDlgValues);
            } else {
                foreach ($subDlgValues as $sDlgValuesKey => $sDlgvalues) {
                    $idFieldName = $this->keyField->getFieldName();
                    $subDbValues = $this->brickDatabase->findBy($idFieldName, $sDlgvalues[$idFieldName]);
                    if ($subDbValues) {
                        foreach ($this->fieldList as $field) {
                            foreach ($subDbValues as $sDbValues) {
                                if ($field instanceof C4GSubDialogField) {
                                    $compare = $field->compareWithDB($sDbValues, $sDlgvalues, $sDlgValuesKey);
                                } else {
                                    $compare = $field->compareWithDB($sDbValues, $sDlgvalues);
                                }
                                if ($compare instanceof C4GBrickFieldCompare) {
                                    $changes[] = $compare;
                                } elseif (is_array($compare) && sizeof($compare) > 0) {
                                    $changes += $compare;
                                }
                            }
                        }
                    } else {
                        foreach ($this->fieldList as $field) {
                            if ($field instanceof C4GSubDialogField) {
                                $compare = $field->compareWithDB(array(), $sDlgvalues);
                            } else {
                                $compare = $field->compareWithDB(array(), $sDlgvalues);
                            }
                            if ($compare instanceof C4GBrickFieldCompare) {
                                $changes[] = $compare;
                            } elseif (is_array($compare) && sizeof($compare) > 0) {
                                $changes += $compare;
                            }
                        }
                    }
                }
            }
        } else {
            /** check for missing dialog values (i.e. they were deleted on the client)  */

            $foreignKey = $dbValues->id;
            $result = $this->brickDatabase->findBy($this->foreignKeyField->getFieldName(), $foreignKey);
            foreach ($result as $res) {
                if ($this->where) {
                    foreach ($this->where as $clause) {
                        $name = $clause[0];
                        $value = $clause[1];
                        if (!$res->$name == $value) {
                            continue 2;
                        }
                        $dbVals = $res;
                    }
                } else {
                    $dbVals = $res;
                }
                foreach ($this->fieldList as $field) {
                    $compare = $field->compareWithDB($dbVals, array());
                    if ($compare instanceof C4GBrickFieldCompare) {
                        $changes[] = $compare;
                    } elseif (is_array($compare) && sizeof($compare) > 0) {
                        $changes += $compare;
                    }
                }
            }
        }
        return $changes;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @param string $table
     * @return C4GSubDialogField
     */
    public function setTable(string $table): C4GSubDialogField
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddButton(): string
    {
        return $this->addButton;
    }

    /**
     * @param string $addButton
     * @return C4GSubDialogField
     */
    public function setAddButton(string $addButton): C4GSubDialogField
    {
        $this->addButton = $addButton;
        return $this;
    }

    /**
     * @return string
     */
    public function getRemoveButton(): string
    {
        return $this->removeButton;
    }

    /**
     * @param string $removeButton
     * @return C4GSubDialogField
     */
    public function setRemoveButton(string $removeButton): C4GSubDialogField
    {
        $this->removeButton = $removeButton;
        return $this;
    }

    /**
     * @return C4GKeyField
     */
    public function getKeyField(): C4GKeyField
    {
        return $this->keyField;
    }

    /**
     * @param C4GBrickField $keyField
     * @return C4GSubDialogField
     */
    public function setKeyField(C4GBrickField $keyField): C4GSubDialogField
    {
        $this->keyField = $keyField;
        return $this;
    }

    /**
     * @return null
     */
    public function getForeignKeyField()
    {
        return $this->foreignKeyField;
    }

    /**
     * @param null $foreignKeyField
     * @return C4GSubDialogField
     */
    public function setForeignKeyField($foreignKeyField)
    {
        $this->foreignKeyField = $foreignKeyField;
        return $this;
    }

    /**
     * @return array
     */
    public function getFieldList(): array
    {
        return $this->fieldList;
    }

    /**
     * @param C4GBrickField $field
     * @return C4GSubDialogField
     */
    public function addField(C4GBrickField $field): C4GSubDialogField
    {
        $this->fieldList[] = $field;
        return $this;
    }

    /**
     * @param array $fieldList
     * @return C4GSubDialogField
     */
    public function addFields(array $fieldList): C4GSubDialogField
    {
        foreach ($fieldList as $field) {
            if ($field instanceof C4GBrickField) {
                $this->fieldList[] = $field;
            }
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getDatabaseType(): string
    {
        return $this->databaseType;
    }

    /**
     * @param string $databaseType
     * @return C4GSubDialogField
     */
    public function setDatabaseType(string $databaseType): C4GSubDialogField
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
     * @return C4GSubDialogField
     */
    public function setEntityClass(string $entityClass): C4GSubDialogField
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
     * @return C4GSubDialogField
     */
    public function setModelClass(string $modelClass): C4GSubDialogField
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
     * @return C4GSubDialogField
     */
    public function setFindBy(array $findBy): C4GSubDialogField
    {
        $this->findBy = $findBy;
        return $this;
    }


    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param $database
     * @return C4GSubDialogField
     */
    public function setDatabase(C4GBrickDatabase $database): C4GSubDialogField
    {
        $this->database = $database;
        return $this;
    }

    /**
     * @return C4GBrickDatabase
     */
    public function getBrickDatabase()
    {
        return $this->brickDatabase;
    }

    /**
     * @param C4GBrickDatabase $brickDatabase
     * @return C4GSubDialogField
     */
    public function setBrickDatabase(C4GBrickDatabase $brickDatabase): C4GSubDialogField
    {
        $this->brickDatabase = $brickDatabase;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddButtonLabel(): string
    {
        return $this->addButtonLabel;
    }

    /**
     * @param string $addButtonLabel
     * @return C4GSubDialogField
     */
    public function setAddButtonLabel(string $addButtonLabel): C4GSubDialogField
    {
        $this->addButtonLabel = $addButtonLabel;
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
     * @return $this
     */
    public function addWhere(array $where)
    {
        if ($where[0] && $where[1]) {
            $this->where[] = array($where[0], $where[1]);
        }
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
     * @return C4GSubDialogField
     * @throws \Exception
     */
    public function setDelimiter(string $delimiter): C4GSubDialogField
    {
        if (($delimiter === '_') || ($delimiter === '?')) {
            throw new \Exception('C4GSubDialogField::delimiter must not be _ or ?.');
        }
        $this->delimiter = $delimiter;
        return $this;
    }

    /**
     * @return string
     */
    public function getWildcard(): string
    {
        return $this->wildcard;
    }

    /**
     * @param string $wildcard
     * @return C4GSubDialogField
     */
    public function setWildcard(string $wildcard): C4GSubDialogField
    {
        $this->wildcard = $wildcard;
        return $this;
    }



}