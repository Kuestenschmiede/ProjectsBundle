<?php
/**
 * Created by PhpStorm.
 * User: rro
 * Date: 30.08.18
 * Time: 09:27
 */

namespace con4gis\ProjectsBundle\Classes\Fieldtypes;


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
        if (!$this->keyField instanceof C4GKeyField) {
            return '';
        }

        $name = $this->getFieldName();
        $title = $this->getTitle();
        $addButton = $this->addButton;
        $removeButton = $this->removeButton;

        $fieldsHtml = "<div class='c4g_sub_dialog_template'>";
        foreach ($this->fieldList as $field) {
            $fieldName = $field->getFieldName();
            $field->setFieldName($this->getFieldName().'_'.$fieldName.'_?');
            $fieldsHtml .= $field->getC4GDialogField();
            $fieldsHtml .= "<span class='c4g_sub_dialog_remove_button' onclick='removeSubDialog(this,event);'>$removeButton</span>";
            $field->setFieldName($fieldName);
        }
        $fieldsHtml .= '</div>';

        $html = "<div class='c4g_sub_dialog_container' id='c4g_$name'>";
        $html .= "<span class='c4g_sub_dialog_title'>$title</span>";
        $html .= "<span class='c4g_sub_dialog_add_button' onclick='addSubDialog(this,event)' data-form='$fieldsHtml' data-target='c4g_dialog_$name' data-field='$name'>$addButton</span>";
        $html .= "<div class='c4g_sub_dialog' id='c4g_dialog_$name'>";

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
     * @param C4GKeyField $keyField
     * @return C4GSubDialogField
     */
    public function setKeyField($keyField): C4GSubDialogField
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



}