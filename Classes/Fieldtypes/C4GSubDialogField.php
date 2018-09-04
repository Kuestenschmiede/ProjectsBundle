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
use con4gis\ProjectsBundle\Classes\Database\C4GBrickDatabaseType;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;

class C4GSubDialogField extends C4GBrickField
{
    private $table = '';
    private $fieldList = array();
    private $keyField = null;
    private $addButton = '';
    private $addButtonLabel = '';
    private $removeButton = '';
    private $databaseType = C4GBrickDatabaseType::DCA_MODEL;
    private $entityClass = '';
    private $modelClass = '';
    private $findBy = array();
    private $database = null;
    private $brickDatabase = null;
    private $pidField = 'pid';


    public function __construct() {
        $this->database = \Database::getInstance();
        $this->setTableColumn(false);
        $this->setFormField(true);
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
        $this->keyField->setFieldName($this->getFieldName().'_'.$fieldName.'_?');
        $fieldsHtml .= $this->keyField->getC4GDialogField($this->getFieldList(), $data, $dialogParams, $additionalParams = array());
        $this->keyField->setFieldName($fieldName);
        foreach ($this->fieldList as $field) {
            $fieldName = $field->getFieldName();
            $field->setFieldName($this->getFieldName().'_'.$fieldName.'_?');
            $fieldsHtml .= $field->getC4GDialogField($this->getFieldList(), $data, $dialogParams, $additionalParams = array());
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
                $propertyName = $this->getFieldName() . '_' . $this->keyField->getFieldName() . '_' . $numLoadedDataSets;
                if ($data->$propertyName) {
                    $setData = new \stdClass();
                    foreach ($data as $key => $value) {
                        $start = C4GUtils::startsWith($key,$this->getFieldName());
                        $end = C4GUtils::endsWith($key,(string)$numLoadedDataSets);
                        if ($start && $end) {
//                            $keyArray = explode('_',$key);
//                            $propertyName = $keyArray[1];
//                            $setData->$propertyName = $value;
                            $setData->$key = $value;
                        }
                    }

                    $loadedDataHtml = "<div class='c4g_sub_dialog_set'>";
                    $fieldName = $this->keyField->getFieldName();
                    $this->keyField->setFieldName($this->getFieldName().'_'.$fieldName. '_' . $numLoadedDataSets);
                    $loadedDataHtml .= $this->keyField->getC4GDialogField($this->getFieldList(), $setData, $dialogParams, $additionalParams = array());
                    $this->keyField->setFieldName($fieldName);
                    foreach ($this->fieldList as $field) {
                        $fieldName = $field->getFieldName();
                        $field->setFieldName($this->getFieldName().'_'.$fieldName. '_' . $numLoadedDataSets);
                        $loadedDataHtml .= $field->getC4GDialogField($this->getFieldList(), $setData, $dialogParams, $additionalParams = array());
                        $field->setFieldName($fieldName);
                    }
                    $loadedDataHtml .= '</div>';
                    $loadedDataHtml .= "<span class='ui-button ui-corner-all c4g_sub_dialog_remove_button' onclick='removeSubDialog(this,event);'>$removeButton</span>";
                    $loadedDataHtml = str_replace('"', "'", $loadedDataHtml);

                } else {
                    break;
                }
            }
        }

        $html = "<div class='c4g_sub_dialog_container' id='c4g_$name'>";
        $this->setAdditionalLabel("<span class='ui-button ui-corner-all c4g_sub_dialog_add_button' onclick='addSubDialog(this,event);' data-form=\"$fieldsHtml\" data-target='c4g_dialog_$name' data-field='$name' data-index='$numLoadedDataSets'>$addButton</span><span class='c4g_sub_dialog_add_button_label'>$this->addButtonLabel</span>");
        $html .= $this->addC4GFieldLabel("c4g_$name", $title, $this->isMandatory(), $this->createConditionData($fieldList, $data), $fieldList, $data, $dialogParams);
//        $html .= "<span class='c4g_sub_dialog_title'>$title</span>";
//        $html .= "<span class='c4g_sub_dialog_add_button' onclick='addSubDialog(this,event)' data-form=\"$fieldsHtml\" data-target='c4g_dialog_$name' data-field='$name'>$addButton</span>";
        $html .= "<div class='c4g_sub_dialog' id='c4g_dialog_$name'>";

        $html .= $loadedDataHtml;

        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }

    public function compareWithDB($dbValue, $dlgvalue) {}

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
    public function setDatabase($database): C4GSubDialogField
    {
        $this->database = $database;
        return $this;
    }

    /**
     * @return C4GBrickDatabase
     */
    public function getBrickDatabase(): C4GBrickDatabase
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
    public function getPidField(): string
    {
        return $this->pidField;
    }

    /**
     * @param string $pidField
     * @return C4GSubDialogField
     */
    public function setPidField(string $pidField): C4GSubDialogField
    {
        $this->pidField = $pidField;
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

}