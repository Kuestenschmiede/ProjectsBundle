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

use con4gis\CoreBundle\Classes\C4GUtils;
use con4gis\CoreBundle\Classes\Callback\C4GCallback;
use con4gis\ProjectsBundle\Classes\Conditions\C4GBrickCondition;
use con4gis\ProjectsBundle\Classes\Database\C4GBrickDatabase;
use con4gis\ProjectsBundle\Classes\Database\C4GBrickDatabaseParams;
use con4gis\ProjectsBundle\Classes\Database\C4GBrickDatabaseType;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldType;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickView;

class C4GSubDialogField extends C4GBrickField
{
    private $table = '';
    private $fieldList = [];
    private $keyField = null;
    private $foreignKeyField = null;
    private $addButton = '';
    private $addButtonLabel = '';
    private $removeButton = '';
    private $removeButtonClass = 'c4g_right_neg_99';
    private $removeButtonMessage = '';
    private $editButton = '';
    private $databaseType = C4GBrickDatabaseType::DCA_MODEL;
    private $entityClass = '';
    private $modelClass = '';
    private $findBy = [];
    private $database = null;
    private $brickDatabase = null;
    private $where = [];
    private $delimiter = '#';
    private $wildcard = '?';
    private $showButtons = true;
    private $finishEditingCaption = '';
    private $allowDelete = true;
    private $saveInNewDataset = false;
    private $originalIdName = '';
    private $saveInNewDataSetIfCondition = null;
    private $insertNewCondition = null;
    private $deleteCondition = null;
    private $orderBy = '';
    private $insertBefore = false;
    private $showFirstDataSet = false;
    private $showDataSetsByCount = 0;
    private $showDataSetsByCountField = '';
    private $parentFieldList = [];

    /**
     * @param string $type
     */
    public function __construct(string $type = C4GBrickFieldType::SUBDIALOG)
    {
        parent::__construct($type);
        $this->database = \Database::getInstance();
        $this->setTableColumn(false);
        $this->setFormField(true);
    }

    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $name = $this->getFieldName();
        if ($this->getAdditionalID()) {
            $name .= '_' . $this->getAdditionalId();
        }
        $title = $this->getTitle();
        $addButton = $this->addButton;
        $removeButton = $this->removeButton;
        $editButton = $this->editButton;
        $this->generateRequiredString($data, $dialogParams, $fieldList);

        $fieldsHtml = '';
        $keyFieldName = $this->keyField->getFieldName();

        $this->keyField->setFieldName($name . $this->delimiter . $keyFieldName . $this->delimiter . $this->wildcard);
        $fieldsHtml .= $this->keyField->getC4GDialogField($this->getFieldList(), $data, $dialogParams, $additionalParams = []);
        $this->keyField->setFieldName($keyFieldName);

        foreach ($this->fieldList as $field) {
            $fieldName = $field->getFieldName();
            if ($field instanceof C4GForeignArrayField) {
                continue;
            }
            if ($field instanceof C4GFileField) {
                $uploadURL = $field->getUploadURL();
                $deleteURL = $field->getDeleteURL();
                $filenameColumn = $field->getFilenameColumn();
                $field->setUploadURL($name . $this->delimiter . $uploadURL . $this->delimiter . $this->wildcard);
                $field->setDeleteURL($name . $this->delimiter . $deleteURL . $this->delimiter . $this->wildcard);
                $field->setFilenameColumn($name . $this->delimiter . $filenameColumn . $this->delimiter . $this->wildcard);
            }
            $templateData = new \stdClass();
            if ($data) {
                foreach ($data as $key => $value) {
                    $templateData->$key = '';
                }
            }

            $field->setFieldName($name . $this->delimiter . $fieldName . $this->delimiter . $this->wildcard);
            if (!$field->hasStyleClass($this->getFieldName())) {
                $field->addStyleClass($this->getFieldName());
            }
            $fieldsHtml .= $field->getC4GDialogField($this->getFieldList(), $templateData, $dialogParams, $additionalParams = []);
            $field->setFieldName($fieldName);
            if ($field instanceof C4GFileField) {
                $field->setUploadURL($uploadURL);
                $field->setDeleteURL($deleteURL);
                $field->setFilenameColumn($filenameColumn);
            }
        }

        if ($this->showButtons && !C4GBrickView::isWithoutEditing($dialogParams->getViewType())) {
            $removeButtonClass = $this->removeButtonClass;
            $message = $this->removeButtonMessage;
            $fieldsHtml .= "<span class='c4g__btn c4g__btn-secondary c4g_sub_dialog_remove_button js-sub-dialog-button $removeButtonClass' onclick='removeSubDialog(this,event);' data-message='$message' title='$message'>$removeButton</span>";
        } else {
            $fieldsHtml .= ''; //<br>
        }

        /** Generate html for already loaded data sets if there are any */
        $numLoadedDataSets = 0;
        $loadedDataHtml = '';
        if ($data) {
            while (true) {
                /* We break manually if the condition is not met. */
                $numLoadedDataSets += 1;
                $propertyName = $name . $this->delimiter . $keyFieldName . $this->delimiter . $numLoadedDataSets;
                if ($data->$propertyName) {
                    /* skip data sets where any where clause is not met  */
                    foreach ($this->where as $key => $clause) {
                        $field = $name . $this->delimiter . $clause[0] . $this->delimiter . $numLoadedDataSets;
                        $value = $clause[1];
                        if (!is_array($data->$field) && $data->$field == $value) {
                            if ($clause[2] === 'or') {
                                break;
                            }
                        } elseif (!is_array($data->$field) && $data->$field != $value) {
                            if ($clause[2] === 'and' || !$this->where[$key + 1]) {
                                continue 2;
                            } elseif ($clause[2] === 'or') {
                                continue 1;
                            }
                        } elseif (is_array($data->$field) && in_array($value, $data->$field)) {
                            if ($clause[2] === 'or') {
                                break;
                            }
                        } elseif (is_array($data->$field) && !in_array($value, $data->$field)) {
                            if ($clause[2] === 'and' || !$this->where[$key + 1]) {
                                continue 2;
                            } elseif ($clause[2] === 'or') {
                                continue 1;
                            }
                        }
                    }
                    $setData = new \stdClass();
                    foreach ($data as $key => $value) {
                        $start = C4GUtils::startsWith($key, $name);
                        $end = C4GUtils::endsWith($key, (string) $numLoadedDataSets);
                        if ($start && $end) {
                            $setData->$key = $value;
                        }
                    }

                    foreach ($setData as $key => $value) {
                        $data->$key = $value;
                    }

                    if ($fieldsHtml) {
                        if ($editButton) {
                            $dataSetClass = 'c4g_sub_dialog_set c4g_sub_dialog_set_uneditable';
                        } else {
                            $dataSetClass = 'c4g_sub_dialog_set';
                        }
                        $loadedDataHtml .= "<div class='$dataSetClass'>";
                        $fieldName = $keyFieldName;
                        $this->keyField->setFieldName($name . $this->delimiter . $fieldName . $this->delimiter . $numLoadedDataSets);
                        $loadedDataHtml .= $this->keyField->getC4GDialogField($this->getFieldList(), $data, $dialogParams, $additionalParams = []);
                        $this->keyField->setFieldName($fieldName);
                        $dataFieldNamesArray = [];
                        foreach ($this->fieldList as $field) {
                            $fieldName = $field->getFieldName();
                            if ($field->getAdditionalID()) {
                                $fieldName .= '_' . $field->getAdditionalId();
                            }
                            if ($editButton || !$this->isEditable()) {
                                $editable = $field->isEditable();
                                $field->setEditable(false);
                            }
                            if ($field instanceof C4GFileField) {
                                $uploadURL = $field->getUploadURL();
                                $deleteURL = $field->getDeleteURL();
                                $filenameColumn = $field->getFilenameColumn();
                                $field->setUploadURL($name . $this->delimiter . $uploadURL . $this->delimiter . $numLoadedDataSets);
                                $field->setDeleteURL($name . $this->delimiter . $deleteURL . $this->delimiter . $numLoadedDataSets);
                                $field->setFilenameColumn($name . $this->delimiter . $filenameColumn . $this->delimiter . $numLoadedDataSets);
                            }
                            $field->setFieldName($name . $this->delimiter . $fieldName . $this->delimiter . $numLoadedDataSets);
                            if (!$field instanceof C4GForeignArrayField) {
                                if ((!$editButton) || ($editable)) {
                                    $dataFieldNamesArray[] = $field->getFieldName();
                                }
                            }
                            if (!$field->hasStyleClass($this->getFieldName())) {
                                $field->addStyleClass($this->getFieldName());
                            }
                            $loadedDataHtml .= $field->getC4GDialogField($this->getFieldList(), $data, $dialogParams, $additionalParams = []);
                            $field->setFieldName($fieldName);
                            if ($editButton) {
                                $field->setEditable($editable);
                            }
                            if ($field instanceof C4GFileField) {
                                $field->setUploadURL($uploadURL);
                                $field->setDeleteURL($deleteURL);
                                $field->setFilenameColumn($filenameColumn);
                            }
                        }
                        $dataFieldNames = implode(',', $dataFieldNamesArray);
                        if ($this->showButtons && !C4GBrickView::isWithoutEditing($dialogParams->getViewType())) {
                            if ($editButton) {
                                $captionFinish = $this->finishEditingCaption;
                                $editButtonHtml = "<span class='c4g__btn c4g__btn-secondary c4g_sub_dialog_edit_button js-sub-dialog-button' onclick='editSubDialog(this,event);' data-fields='$dataFieldNames'  data-captionFinishEditing='$captionFinish' data-captionBeginEditing='$editButton'>$editButton</span>";
                            } else {
                                $editButtonHtml = '';
                            }
                            if ($this->allowDelete) {
                                $deleteButtonClass = $this->removeButtonClass;
                                $message = $this->removeButtonMessage;
                                $deleteButtonHtml = "<span class='c4g__btn c4g__btn-secondary c4g_sub_dialog_remove_button js-sub-dialog-button $deleteButtonClass' onclick='removeSubDialog(this,event)'; data-message='$message' title='$message'>$removeButton</span>";
                            } else {
                                $deleteButtonHtml = '';
                            }
                            $loadedDataHtml .= "$editButtonHtml$deleteButtonHtml";
                        } else {
                            $loadedDataHtml .= ''; //<br>
                        }

                        $loadedDataHtml .= '</div>';
                    }
                } else {
                    break;
                }
            }
        } elseif ($this->showDataSetsByCountField || $this->showDataSetsByCount || $this->showFirstDataSet) {
            $initialCount = $this->showDataSetsByCount ?: 1;

            if ($this->showDataSetsByCountField) {
                $countField = $this->showDataSetsByCountField;
                foreach ($this->getParentFieldList() as $fieldListItem) {
                    if ($fieldListItem->getFieldName() == $countField) {
                        $initialCount = $fieldListItem->getInitialValue();

                        break;
                    }
                }
            }

            $numLoadedDataSets = intval($initialCount);

            for ($i = 0; $i < $numLoadedDataSets; $i++) {
                if ($fieldsHtml) {
                    if ($editButton) {
                        $dataSetClass = 'c4g_sub_dialog_set c4g_sub_dialog_set_uneditable';
                    } else {
                        $dataSetClass = 'c4g_sub_dialog_set';
                    }
                    $loadedDataHtml .= "<div class='$dataSetClass'>";
                    $fieldName = $keyFieldName;
                    $this->keyField->setFieldName($name . $this->delimiter . $fieldName . $this->delimiter . $i);
                    $loadedDataHtml .= $this->keyField->getC4GDialogField($this->getFieldList(), $data, $dialogParams, $additionalParams = []);
                    $this->keyField->setFieldName($fieldName);
                    $dataFieldNamesArray = [];
                    foreach ($this->fieldList as $field) {
                        $fieldName = $field->getFieldName();
                        if ($field->getAdditionalID()) {
                            $fieldName .= '_' . $field->getAdditionalId();
                        }
                        if ($editButton || !$this->isEditable()) {
                            $editable = $field->isEditable();
                            $field->setEditable(false);
                        }
                        if ($field instanceof C4GFileField) {
                            $uploadURL = $field->getUploadURL();
                            $deleteURL = $field->getDeleteURL();
                            $filenameColumn = $field->getFilenameColumn();
                            $field->setUploadURL($name . $this->delimiter . $uploadURL . $this->delimiter . $i);
                            $field->setDeleteURL($name . $this->delimiter . $deleteURL . $this->delimiter . $i);
                            $field->setFilenameColumn($name . $this->delimiter . $filenameColumn . $this->delimiter . $i);
                        }
                        $field->setFieldName($name . $this->delimiter . $fieldName . $this->delimiter . $i);
                        if (!$field instanceof C4GForeignArrayField) {
                            if ((!$editButton) || ($editable)) {
                                $dataFieldNamesArray[] = $fieldName;
                            }
                        }
                        if (!$field->hasStyleClass($this->getFieldName())) {
                            $field->addStyleClass($this->getFieldName());
                        }
                        $loadedDataHtml .= $field->getC4GDialogField($this->getFieldList(), $data, $dialogParams, $additionalParams = []);
                        $field->setFieldName($fieldName);
                        if ($editButton) {
                            $field->setEditable($editable);
                        }
                        if ($field instanceof C4GFileField) {
                            $field->setUploadURL($uploadURL);
                            $field->setDeleteURL($deleteURL);
                            $field->setFilenameColumn($filenameColumn);
                        }
                    }

                    $dataFieldNames = implode(',', $dataFieldNamesArray);
                    if ($this->showButtons && !C4GBrickView::isWithoutEditing($dialogParams->getViewType())) {
                        if ($editButton) {
                            $captionFinish = $this->finishEditingCaption;
                            $editButtonHtml = "<span class='c4g__btn c4g__btn-secondary c4g_sub_dialog_edit_button js-sub-dialog-button' onclick='editSubDialog(this,event);' data-fields='$dataFieldNames'  data-captionFinishEditing='$captionFinish' data-captionBeginEditing='$editButton'>$editButton</span>";
                        } else {
                            $editButtonHtml = '';
                        }
                        if ($this->allowDelete) {
                            $deleteButtonClass = $this->removeButtonClass;
                            $message = $this->removeButtonMessage;
                            $deleteButtonHtml = "<span class='c4g__btn c4g__btn-secondary c4g_sub_dialog_remove_button js-sub-dialog-button $deleteButtonClass' onclick='removeSubDialog(this,event)'; data-message='$message' title='$message'>$removeButton</span>";
                        } else {
                            $deleteButtonHtml = '';
                        }
                        $loadedDataHtml .= "$editButtonHtml$deleteButtonHtml";
                    } else {
                        $loadedDataHtml .= ''; //<br>
                    }

                    $loadedDataHtml .= '</div>';
                }
            }
        }

        if (($this->showButtons && !C4GBrickView::isWithoutEditing($dialogParams->getViewType())) || $loadedDataHtml) {
            $condition = $this->createConditionData($fieldList, $data);
            $styleClass = 'c4g__form-'.$this->getType().' '.'c4g__form-'.$this->getType().'--'.$this->getFieldName();
            $class = 'class="c4g__form-group formdata '.$styleClass.'"';

            $conditionStart =
                '<div '
                . $class
                . $condition['conditionName']
                . $condition['conditionType']
                . $condition['conditionValue']
                . $condition['conditionDisable']
                . '>';

            $html = $conditionStart . "<div class='c4g_sub_dialog_container formdata' " . $condition['conditionPrepare'] . " id='c4g_$name'>";
            $html .= "<template id='c4g_$name" . '_template' . "'>$fieldsHtml</template>";
            $html .= $this->addC4GFieldLabel("c4g_$name", $title, $this->isMandatory(), '', $fieldList, $data, $dialogParams);
            if ($this->showButtons && !C4GBrickView::isWithoutEditing($dialogParams->getViewType())) {
                $insert = $this->insertBefore === true ? 'before' : 'after';
                $max = $this->getMax();
                $html .= "<span class='c4g__btn c4g__btn-secondary c4g_sub_dialog_add_button js-sub-dialog-button' onclick='addSubDialog(this,event,$max);' data-template='c4g_$name" . '_template' . "' data-target='c4g_dialog_$name' data-field='$name' data-index='$numLoadedDataSets' data-wildcard='" . $this->wildcard . "' data-insert='$insert'>$addButton</span><span class='c4g_sub_dialog_add_button_label'>$this->addButtonLabel</span>";
            }
            $html .= "<div class='c4g_sub_dialog formdata' id='c4g_dialog_$name'>";
            $html .= $loadedDataHtml;

            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        } else {
            $html = ''; //<br>
        }

        return $html;
    }

    public function compareWithDB($dbValues, $dlgValues, $name = '')
    {
        $changes = [];

        $subDlgValues = [];
        $indexList = [];
        $indexListIndex = 0;
        foreach ($dlgValues as $key => $value) {
            $keyArray = explode($this->delimiter, $key);
            $fieldName = $this->getFieldName();
            if ($this->getAdditionalID()) {
                $fieldName .= '_' . $this->getAdditionalId();
            }

            if ($keyArray && $keyArray[0] == $fieldName) {
                $subDlgValues[$keyArray[0] . $this->delimiter . $keyArray[2]][$keyArray[1]] = $value;
                $indexList[] = $keyArray[0] . $this->delimiter . $keyArray[2];
            } else {
                foreach ($this->fieldList as $field) {
                    if ($field instanceof C4GSubDialogField) {
                        if (strpos($key, $field->getDelimiter()) !== false) {
                            $subDlgValues[$indexList[$indexListIndex]][$key] = $value;                  //Sub Dialog Values aus den Dialog Values holen
                            $indexListIndex += 1;
                        } else {
                            $subDlgValues[$indexList[$indexListIndex]][$key] = $value;                  //Sub Dialog Values aus den Dialog Values holen
                            $indexListIndex += 1;
                        }
                    }
                }
            }
        }

        foreach ($this->fieldList as $field) {
            if ($field instanceof C4GSubDialogField) {
                foreach ($subDlgValues as $key => $values) {
                    $keyArray = explode($field->delimiter, $key);
                    if ($keyArray && $keyArray[0] && $keyArray[1] && $keyArray[2]) {
                        foreach ($subDlgValues[$key] as $k => $v) {
                            $index = $k . $field->getDelimiter() . $keyArray[1] . $field->getDelimiter() . $keyArray[2];
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
                $class = new \ReflectionClass($this->entityClass);
                $namespace = $class->getNamespaceName();
                $dbClass = str_replace($namespace . '\\', '', $this->entityClass);
                $dbClass = str_replace('\\', '', $dbClass);
            } else {
                $class = new \ReflectionClass(get_called_class());
                $namespace = str_replace('contao\\modules', 'database', $class->getNamespaceName());
                $dbClass = $this->modelClass;
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
                                $compare = $field->compareWithDB([], $sDlgvalues);
                            } else {
                                $compare = $field->compareWithDB([], $sDlgvalues);
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
                    $compare = $field->compareWithDB($dbVals, []);
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

    public function checkMandatory($dlgValues)
    {
        $subDlgValues = [];
        $indexList = [];
        $indexListIndex = 0;
        $fieldName = $this->getFieldName();
        if ($this->getAdditionalID()) {
            $fieldName .= '_' . $this->getAdditionalId();
        }

        foreach ($dlgValues as $key => $value) {
            $keyArray = explode($this->delimiter, $key);
            if ($keyArray && $keyArray[0] == $fieldName) {
                $subDlgValues[$keyArray[0] . $this->delimiter . $keyArray[2]][$keyArray[1]] = $value;
                $indexList[] = $keyArray[0] . $this->delimiter . $keyArray[2];
                array_unique($indexList);
            } else {
                foreach ($this->fieldList as $field) {
                    if ($field instanceof C4GSubDialogField) {
                        if (strpos($key, $field->getDelimiter()) !== false) {
                            $subDlgValues[$indexList[$indexListIndex]][$key] = $value;
                            $indexListIndex += 1;
                        } else {
                            $subDlgValues[$indexList[$indexListIndex]][$key] = $value;
                            $indexListIndex += 1;
                        }
                    }
                }
            }
        }
        foreach ($subDlgValues as $key => $subDlgVals) {
            if ($key === '') {
                continue;
            }
            foreach ($this->fieldList as $field) {
                $result = $field->checkMandatory($subDlgVals);
                if ($result instanceof C4GBrickField) {
                    return $result;
                }
            }
        }

        return false;
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
     * @return string
     */
    public function getEditButtton(): string
    {
        return $this->editButton;
    }

    /**
     * @param string $editButtton
     * @return C4GSubDialogField
     */
    public function setEditButton(string $editButtton): C4GSubDialogField
    {
        $this->editButton = $editButtton;

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
     * @param $fieldList
     */
    public function setFieldList(array $fieldList)
    {
        $this->fieldList = $fieldList;
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
     * @param String $field
     * @param $value
     * @param String $type
     * @return $this
     */
    public function addWhere(String $field, $value, String $type = 'and')
    {
        $this->where[] = [$field, $value, $type];

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
        if (($delimiter === '_') || ($delimiter === '?') || ($delimiter === ',')) {
            throw new \Exception('C4GSubDialogField::delimiter must not be _or ? or ,.');
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

    /**
     * @return bool
     */
    public function isShowButtons(): bool
    {
        return $this->showButtons;
    }

    /**
     * @param bool $showButtons
     * @return C4GSubDialogField
     */
    public function setShowButtons(bool $showButtons): C4GSubDialogField
    {
        $this->showButtons = $showButtons;

        return $this;
    }

    /**
     * @return string
     */
    public function getFinishEditingCaption(): string
    {
        return $this->finishEditingCaption;
    }

    /**
     * @param string $finishEditingCaption
     * @return C4GSubDialogField
     */
    public function setFinishEditingCaption(string $finishEditingCaption): C4GSubDialogField
    {
        $this->finishEditingCaption = $finishEditingCaption;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAllowDelete(): bool
    {
        return $this->allowDelete;
    }

    /**
     * @param bool $allowDelete
     * @return C4GSubDialogField
     */
    public function setAllowDelete(bool $allowDelete): C4GSubDialogField
    {
        $this->allowDelete = $allowDelete;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSaveInNewDataset(): bool
    {
        return $this->saveInNewDataset;
    }

    /**
     * @param bool $saveInNewDataset
     * @return C4GSubDialogField
     */
    public function setSaveInNewDataset(bool $saveInNewDataset): C4GSubDialogField
    {
        $this->saveInNewDataset = $saveInNewDataset;

        return $this;
    }

    /**
     * @return string
     */
    public function getOriginalIdName(): string
    {
        return $this->originalIdName;
    }

    /**
     * @param string $originalIdName
     * @return C4GSubDialogField
     */
    public function setOriginalIdName(string $originalIdName): C4GSubDialogField
    {
        $this->originalIdName = $originalIdName;

        return $this;
    }

    /**
     * @return null
     */
    public function getSaveInNewDataSetIfCondition(): ?C4GBrickCondition
    {
        return $this->saveInNewDataSetIfCondition;
    }

    /**
     * @param C4GBrickCondition $saveInNewDataSetIfCondition
     * @return $this
     */
    public function setSaveInNewDataSetIfCondition(C4GBrickCondition $saveInNewDataSetIfCondition = null)
    {
        $this->saveInNewDataSetIfCondition = $saveInNewDataSetIfCondition;

        return $this;
    }

    /**
     * @return C4GCallback
     */
    public function getInsertNewCondition(): ?C4GCallback
    {
        return $this->insertNewCondition;
    }

    /**
     * @param C4GCallback $callback
     * @return C4GSubDialogField
     */
    public function setInsertNewCondition(C4GCallback $callback): C4GSubDialogField
    {
        $this->insertNewCondition = $callback;

        return $this;
    }

    /**
     * @return C4GCallback
     */
    public function getDeleteCondition(): ?C4GCallback
    {
        return $this->deleteCondition;
    }

    /**
     * @param C4GCallback $callback
     * @return C4GSubDialogField
     */
    public function setDeleteCondition(C4GCallback $callback): C4GSubDialogField
    {
        $this->deleteCondition = $callback;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    /**
     * @param string $orderBy
     * @return C4GSubDialogField
     */
    public function setOrderBy(string $orderBy): C4GSubDialogField
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * @return string
     */
    public function getRemoveButtonClass(): string
    {
        return $this->removeButtonClass;
    }

    /**
     * @param string $removeButtonClass
     * @return $this
     * @return $this
     */
    public function setRemoveButtonClass(string $removeButtonClass)
    {
        $this->removeButtonClass = $removeButtonClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getRemoveButtonMessage(): string
    {
        return $this->removeButtonMessage;
    }

    /**
     * @param string $removeButtonMessage
     */
    public function setRemoveButtonMessage(string $removeButtonMessage)
    {
        $this->removeButtonMessage = $removeButtonMessage;
    }

    /**
     * @return bool
     */
    public function isInsertBefore(): bool
    {
        return $this->insertBefore;
    }

    /**
     * @param bool $insertBefore
     * @return $this
     */
    public function setInsertBefore(bool $insertBefore = true)
    {
        $this->insertBefore = $insertBefore;

        return $this;
    }

    /**
     * @return bool
     */
    public function isShowFirstDataSet(): bool
    {
        return $this->showFirstDataSet;
    }

    /**
     * @param bool $showFirstDataSet
     */
    public function setShowFirstDataSet(bool $showFirstDataSet): void
    {
        $this->showFirstDataSet = $showFirstDataSet;
    }

    /**
     * @return string
     */
    public function getShowDataSetsByCountField(): string
    {
        return $this->showDataSetsByCountField;
    }

    /**
     * @param string $showDataSetsByCountField
     */
    public function setShowDataSetsByCountField(string $showDataSetsByCountField): void
    {
        $this->showDataSetsByCountField = $showDataSetsByCountField;
    }

    /**
     * @return array
     */
    public function getParentFieldList(): array
    {
        return $this->parentFieldList;
    }

    /**
     * @param array $parentFieldList
     */
    public function setParentFieldList(array $parentFieldList): void
    {
        $this->parentFieldList = $parentFieldList;
    }

    /**
     * @return int
     */
    public function getShowDataSetsByCount(): int
    {
        return $this->showDataSetsByCount;
    }

    /**
     * @param int $showDataSetsByCount
     */
    public function setShowDataSetsByCount(int $showDataSetsByCount): void
    {
        $this->showDataSetsByCount = $showDataSetsByCount;
    }
}
