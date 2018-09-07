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

namespace con4gis\ProjectsBundle\Classes\Actions;

use con4gis\CoreBundle\Resources\contao\classes\C4GHTMLFactory;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Database\C4GBrickDatabase;
use con4gis\ProjectsBundle\Classes\Database\C4GBrickDatabaseParams;
use con4gis\ProjectsBundle\Classes\Database\C4GBrickDatabaseType;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialog;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GForeignArrayField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GSubDialogField;
use con4gis\ProjectsBundle\Classes\Models\C4gProjectsModel;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickView;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;


class C4GShowDialogAction extends C4GBrickDialogAction
{
    public function run()
    {
        $element = null;
        $brickDatabase     = $this->getBrickDatabase();
        $dialogParams      = $this->getDialogParams();
        $viewParams        = $dialogParams->getViewParams();
        $viewType          = $viewParams->getViewType();
        $modelListFunction = $viewParams->getModelListFunction();
        $id                = $dialogParams->getId();
        $memberId          = $dialogParams->getMemberId();
        $groupId           = $dialogParams->getGroupId();
        $projectId         = $dialogParams->getProjectId();
        $parentId          = $dialogParams->getParentId();
        $parentModel       = $dialogParams->getParentModel();
        $parentIdField     = $dialogParams->getParentIdField();
        $additionalHeadtext = $dialogParams->getAdditionalHeadText();
        $parentCaptionFields = $dialogParams->getParentCaptionFields();

        if ((!$id || $id == -1 || $id == "-1" || !is_int($id)) && ($dialogParams->isWithInitialSaving())) {
            $id = $this->saveAndGetId($id);
            \Session::getInstance()->set("c4g_brick_dialog_id", $id);
            $dialogParams->setId($id);
        }

        //WURDE EINE ID ÜBERGEBEN?
        if ((!$modelListFunction) &&
            ($viewType != C4GBrickViewType::PROJECTPARENTFORMCOPY) &&
            ($viewType != C4GBrickViewType::PROJECTFORMCOPY)){
            if (($id) && ($id != -1)) {
                $element = $brickDatabase->findByPk($id);
                if (empty($element)) {
                    return;
                }

                //implemented for permalinks
                $groupKeyField = $viewParams->getGroupKeyField();
                if ($element->$groupKeyField) {
                    $groupId = $element->$groupKeyField;
                }

                if ($element->project_id) {
                    $projectId = $element->project_id;
                }

                if ($parentModel) {
                    $pid = 'pid';
                    if ($parentIdField) {
                        $pid = $parentIdField;
                    }
                    if ($pid && $element->$pid) {
                        $parentIdField = $element->$pid;
                        $dialogParams->setParentIdField($parentIdField);
                        $parentId = $element->$pid;
                    }
                }
            } elseif (!$parentId) {
                $parentId = \Session::getInstance()->get('c4g_brick_parent_id');
            }
        }

        \Session::getInstance()->set("c4g_brick_dialog_id", $id);
        //$parent_id = $parentId;

        //ToDo überarbeiten brickDatabase
        $parentModel = $dialogParams->getParentModel();
        if ($parentId && $parentModel) {
            $parent = $parentModel::findByPk($parentId);
            if ($parent) {
                //implemented for permalinks
                $groupKeyField = $viewParams->getGroupKeyField();
                if ($parent->$groupKeyField) {
                    $groupId = $parent->$groupKeyField;
                    $dialogParams->setGroupId($groupId);
                }

                if ($parent->project_id) {
                    $projectId = $parent->project_id;
                    $dialogParams->setProjectId($projectId);
                }
                $caption = $parent->caption;
                if (!$caption) {
                    $caption = $parent->name;
                }
                if ($parentCaptionFields && is_array($parentCaptionFields)) {
                    $caption = '';
                    foreach($parentCaptionFields as $key=>$value) {
                        if (strlen($value) == 1) {
                            if ($value == ')') {
                                //if there is no bracketed value remove brackets
                                if (substr(trim($caption), -1, 1) == '(') {
                                    $caption = substr(trim($caption), 0, -1);
                                } else {
                                    $caption = trim($caption).$value;
                                }
                            } else {
                                $caption .= $value;
                            }
                        } else {
                            $caption .= $parent->$value . ' ';
                        }
                    }
                } elseif ($parentCaptionCallback = $dialogParams->getParentCaptionCallback()) {
                    $class = $parentCaptionCallback[0];
                    $function = $parentCaptionCallback[1];
                    $arrCaptions = $class::$function(
                        [$parent],
                        $this->brickDatabase->getEntityManager()
                    );
                    $caption = $arrCaptions[$parentId];
                }
                $parent_headline = '<div class="c4g_brick_headtext"> '.$dialogParams->getParentCaption().': <b>'.$caption.'</b></div>';
            }
        }

        $doCopy = false;
        if (((($viewType == C4GBrickViewType::MEMBERFORM) || ($viewType == C4GBrickViewType::MEMBERVIEW) ||
                ($viewType == C4GBrickViewType::GROUPFORM) && (($id == null) || ($id == -1))) ||
            ($viewType == C4GBrickViewType::GROUPFORMCOPY) ||
            ($viewType == C4GBrickViewType::PROJECTFORMCOPY) ||
            ($viewType == C4GBrickViewType::GROUPPARENTVIEW) ||
            ($viewType == C4GBrickViewType::PUBLICFORM) ||
            ($viewType == C4GBrickViewType::PROJECTPARENTFORMCOPY)) || ($viewType == C4GBrickViewType::PUBLICUUIDVIEW) || ($viewType == C4GBrickViewType::PUBLICVIEW)) {
            $groupKeyField = $viewParams->getGroupKeyField();
            switch($viewType) {
                case C4GBrickViewType::GROUPFORM:
                    $elements = $brickDatabase->findby($groupKeyField, $groupId);
                    break;
                case C4GBrickViewType::GROUPFORMCOPY:
                    $elements = $brickDatabase->findby($groupKeyField, $groupId);
                    if ($elements) {
                        $doCopy = true;
                    }
                    break;
                case C4GBrickViewType::PROJECTFORMCOPY:
                    /* $t = $this->tableName;
                     $arrColumns = array("$t.id=$id");
                     $arrValues = array();
                     $arrOptions = array(
                         'order' => "$t.tstamp DESC"
                     );

                     $elements = $model::findBy($arrColumns, $arrValues, $arrOptions);
                     if (!$elements) {
                         $elements = $model::findby('project_id', $this->project_id);
                     }*/
                    $elements = $brickDatabase->findBy('id', $id);
                    if ($elements) {
                        $doCopy = true;
                    }
                    break;
                case C4GBrickViewType::PROJECTPARENTFORMCOPY:
                    $pidField = 'pid';
                    if ($dialogParams->getParentIdField()) {
                        $pidField = $dialogParams->getParentIdField();
                    }
                    $elements = $brickDatabase->findby($pidField, $id);
                    if ($elements) {
                        $doCopy = true;
                    }
                    break;
                case C4GBrickViewType::MEMBERFORM:
                    $memberKeyField = $viewParams->getMemberKeyField();
                    $elements = $brickDatabase->findby($memberKeyField, $memberId);
                    break;
                case C4GBrickViewType::MEMBERVIEW:
                    if($modelListFunction){
                        $function = $modelListFunction;

                        //Todo überarbeiten brickDatabase
                        $database = $brickDatabase->getParams()->getDatabase();
                        $tablename = $brickDatabase->getParams()->getTableName();
                        $modelClass = $brickDatabase->getParams()->getModelClass();
                        $model = $modelClass;
                        $elements = $model::$function($memberId, $tablename, $database, $this->getFieldList(), $this->getListParams());
                        if ($id >= 0) {
                            foreach ($elements as $value) {
                                if ($value->id == $id) {
                                    $element = $value;
                                    $elements = null;
                                    break;
                                }
                            }
                        }
                    }
                    break;
                case C4GBrickViewType::PUBLICUUIDVIEW:
                    if($modelListFunction){
                        $function = $modelListFunction;

                        //Todo überarbeiten brickDatabase
                        $database = $brickDatabase->getParams()->getDatabase();
                        $tablename = $brickDatabase->getParams()->getTableName();
                        $modelClass = $brickDatabase->getParams()->getModelClass();
                        $model = $modelClass;
                        $elements = $model::$function($this->dialogParams->getUuid(), $tablename, $database, $this->getFieldList(), $this->getListParams());
                        if ($id >= 0) {
                            foreach ($elements as $value) {
                                if ($value->id == $id) {
                                    $element = $value;
                                    $elements = null;
                                    break;
                                }
                            }
                        }
                    }
                    break;
                case C4GBrickViewType::PUBLICFORM:
                case C4GBrickViewType::PUBLICVIEW:
                    if($modelListFunction){
                        $function = $modelListFunction;

                        //Todo überarbeiten brickDatabase
                        $modelClass = $brickDatabase->getParams()->getModelClass();
                        $model = $modelClass;
                        $elements = $model::$function($parentId);
                        if ($id >= 0) {
                            foreach ($elements as $value) {
                                if ($value->id == $id) {
                                    $element = $value;
                                    $elements = null;
                                    break;
                                }
                            }
                        }
                    }
                    break;
                case C4GBrickViewType::GROUPPARENTVIEW:
                    $pid_field = 'pid';
                    if ($parentIdField) {
                        $pid_field = $parentIdField;
                    }

                    if ($modelListFunction) {
                        $function = $modelListFunction;
                        $database = $brickDatabase->getParams()->getDatabase();
                        //Todo überarbeiten brickDatabase
                        $modelClass = $brickDatabase->getParams()->getModelClass();
                        $model = $modelClass;
                        $elements = $model::$function($groupId, $pid_field, $parentId, $database, $this->getListParams(),$id);
                        if ($id > 0) {
                            foreach ($elements as $value) {
                                if ($value['id'] == $id) {
                                    $element = $value;
                                    $elements = null;
                                    break;
                                }
                            }
                        }
                    } else {
                        $elements = $brickDatabase->findby($pid_field, $parentId);
                    }
                    $dialogParams->setFrozen(true);
                    break;
            }

            if ( ($elements != null) && ($elements[0] != null) ) {
                $element = $elements[count($elements)-1];

                if (($element) && ($doCopy)) {
                    $element->id = -1; //Neuer Datensatz wird erzeugt.

                    if ($viewType == C4GBrickViewType::PROJECTFORMCOPY) {
                        $element->uuid = null; //Neue uuid wird erzeugt.
                    }

//                    if (($this->viewType == C4GBrickViewType::PROJECTPARENTFORMCOPY) ||
//                        ($this->viewType == C4GBrickViewType::PROJECTPARENTVIEW)){
//                        $parentModel = $this->parentModel;
//                        $parent = $parentModel::findByPk($element->pid);
//                        if ($parent) {
//                            $this->project_id = $parent->project_id;
//                            \Session::getInstance()->set("c4g_brick_project_id", $this->project_id);
//
//                            if ($this->project_id) {
//                                $project = \C4gProjectsModel::findByPk($this->project_id);
//                                if ($project) {
//                                    $this->group_id = $project->group_id;
//                                    \Session::getInstance()->set("c4g_brick_group_id", $this->group_id);
//                                }
//                            }
//                        }
//                    }

                }
            }
        }

        if (C4GBrickView::isGroupBased($viewType) && $modelListFunction) {
            $function = $modelListFunction;
            $database = $brickDatabase->getParams()->getDatabase();
            //Todo überarbeiten brickDatabase
            $modelClass = $brickDatabase->getParams()->getModelClass();
            $model = $modelClass;
            $elements = $model::$function($groupId, $database, $this->getListParams(), $brickDatabase);
            $element = $elements->$id;
        }

        //ToDo Weitere ViewTypes überprüfen
        if ($viewType != C4GBrickViewType::PROJECTPARENTFORMCOPY) {
            $uuid = $this->getElementUuid($element->uuid);
            $dialogParams->setProjectUuid($uuid);
        }

        //ToDo nach Umbau prüfen
        $homeDir = $dialogParams->getHomeDir();
        if (!$homeDir) {
            $dialogParams->setHomeDir($this->getHomeDir());
        }

        //Das Karteninhaltselement wird geladen und der Maske übergeben.
        $content = '';
        if (!$dialogParams->getC4gMap()) {
            $result = $this->withMap($this->getFieldList(), $dialogParams->getContentId());
            if ($result) {
                $content = \Controller::replaceInsertTags('{{insert_content::'.$result.'}}');
            }
        } else {
            $content = $dialogParams->getC4gMap();
        }


        if (C4GBrickView::isWithGroup($viewType)) {

//            $group_id = $this->group_id;
            if ($groupId) {
                $group = \MemberGroupModel::findByPk($groupId);
                if ($group) {
                    $group_headline = '<div class="c4g_brick_headtext"> Aktive Gruppe: <b>'.$group->name.'</b></div>';
                }
            }
        }
        $headlineTag = $dialogParams->getHeadlineTag();

        $headtext = '<'.$headlineTag.'>'.$dialogParams->getHeadline().'</'.$headlineTag.'>';
        if ( ($group_headline) && ($parent_headline)) {
            $headtext = $headtext . $group_headline . $parent_headline;
        } else if ($group_headline){
            $headtext = $headtext.$group_headline;
        } else if (($group_headline) && ($parent_headline)) {
            $headtext = $headtext.$group_headline . $parent_headline;
        } else if ($group_headline) {
            $headtext = $headtext.$group_headline;
        }
        if ($additionalHeadtext) {
            $additionalHeadtext = '<div class="c4g_brick_headtext">' . $additionalHeadtext . '</div>';
            $headtext .= $additionalHeadtext;
        }

        $database = $brickDatabase->getParams()->getDatabase();
        if ((empty($element)) && ($dialogParams->getModelDialogFunction())) {
            $model = $this->module->getModelClass();
            $function = $dialogParams->getModelDialogFunction();
            $element = $model::$function($dialogParams->getId());
        }

        $count = 0;
        foreach ($this->fieldList as $field) {
            if ($field instanceof C4GSubDialogField) {
                $subFields = $field->getFieldList();
                $table = $field->getTable();
                $subDlgValues = array();


                $databaseParams = new C4GBrickDatabaseParams($field->getDatabaseType());
                $databaseParams->setPkField('id');
                $databaseParams->setTableName($table);

                if (class_exists($field->getEntityClass())) {
                    $class      = new \ReflectionClass($field->getEntityClass());
                    $namespace  = $class->getNamespaceName();
                    $dbClass    = str_replace($namespace . '\\', '', $field->getEntityClass());
                    $dbClass    = str_replace('\\', '', $dbClass);
                } else {
                    $class      = new \ReflectionClass(get_called_class());
                    $namespace  = str_replace("contao\\modules", "database", $class->getNamespaceName());
                    $dbClass    = $field->getModelClass();
                }

                $databaseParams->setFindBy($field->getFindBy());
                $databaseParams->setEntityNamespace($namespace);
                $databaseParams->setDatabase($field->getDatabase());

                if ($field->getDatabaseType() == C4GBrickDatabaseType::DCA_MODEL) {
                    $databaseParams->setModelClass($field->getModelClass());
                } else {
                    $databaseParams->setEntityClass($dbClass);
                }
                $field->setBrickDatabase(new C4GBrickDatabase($databaseParams));

                $values = $field->getBrickDatabase()->findBy($field->getForeignKeyField()->getFieldName(), $id);
                $count = 0;
                foreach ($values as $value) {
                    $count += 1;
                    if ($value instanceof \stdClass) {
                        foreach ($value as $key => $val) {
                            $index = $field->getFieldName().'_'.$key.'_'.$count;
                            $element->$index = $val;
                        }
                    }
                }
                $element = $this->loadC4GForeignArrayFieldData($this->fieldList, $element);
            }
        }

        $element = $this->loadC4GForeignArrayFieldData($this->fieldList, $element);

        //Wenn $element an dieser Stelle null ist wird ein neuer Datensatz angelegt (Hinzufügen),
        //ansonsten wird der bestehende Datensatz zur Bearbeitung angeboten
        $result = C4GBrickDialog::showC4GDialog(
            $this->getFieldList(),
            $database,
            $element,
            $content,
            $headtext,
            $dialogParams
        );

        return $result;
    }

    /**
     * saving to have an ID before showing dialog
     * @param $id
     * @return bool
     */
    private function saveAndGetId() {
        $database = $this->getBrickDatabase()->getParams()->getDatabase();
        $tableName = $this->getBrickDatabase()->getParams()->getTableName();

        $set = array();
        $set['tstamp'] = time();
        $objInsertStmt = $database->prepare("INSERT INTO $tableName %s")
            ->set($set)
            ->execute();

        if (!$objInsertStmt->affectedRows)
        {
            return false;
        }

        $insertId = $objInsertStmt->insertId;

        if ($insertId) {
            return $insertId;
        } else {
            return false;
        }
    }

    /**
     * @param $uuid
     * @return string
     */
    private function getElementUuid($uuid) {
        $result =  $uuid;
        if (!$result) {
            $result = C4GBrickCommon::getGUID();
        }
        return $result;
    }

    private function getHomeDir()
    {
        $dialogParams = $this->getDialogParams();
        $memberId     = $dialogParams->getMemberId();
        $groupId      = $dialogParams->getGroupId();
        $projectUuid  = $dialogParams->getProjectUuid();
        $viewType     = $dialogParams->getViewType();

        if (C4GBrickView::isWithMember($viewType)) {
            $homeDir = '/' . C4GBrickConst::PATH_MEMBER_DATA . '/' . $memberId . '/';
        }

        if (C4GBrickView::isWithGroup($viewType)) {
            $homeDir = '/' . C4GBrickConst::PATH_GROUP_DATA . '/' . $groupId . '/';
        }

        if (C4GBrickView::isWithProject($viewType)) {
            $homeDir = '/' . C4GBrickConst::PATH_GROUP_DATA . '/' . $groupId . '/' . $projectUuid . '/';
        }

        return $homeDir;
    }

    public function isReadOnly()
    {
        return true;
    }

    public function loadC4GForeignArrayFieldData($fieldList, $element) {
        foreach ($fieldList as $field) {
            if ($field instanceof C4GForeignArrayField) {
                $index = $field->getFieldName();

                $databaseParams = new C4GBrickDatabaseParams($field->getDatabaseType());
                $databaseParams->setPkField('id');
                $databaseParams->setTableName($field->getForeignTable());

                if (class_exists($field->getEntityClass())) {
                    $class      = new \ReflectionClass($field->getEntityClass());
                    $namespace  = $class->getNamespaceName();
                    $dbClass    = str_replace($namespace . '\\', '', $field->getEntityClass());
                    $dbClass    = str_replace('\\', '', $dbClass);
                } else {
                    $class      = new \ReflectionClass(get_called_class());
                    $namespace  = str_replace("contao\\modules", "database", $class->getNamespaceName());
                    $dbClass    = $field->getModelClass();
                }

                $databaseParams->setFindBy($field->getFindBy());
                $databaseParams->setEntityNamespace($namespace);
                $databaseParams->setDatabase($field->getDatabase());

                if ($field->getDatabaseType() == C4GBrickDatabaseType::DCA_MODEL) {
                    $databaseParams->setModelClass($field->getModelClass());
                } else {
                    $databaseParams->setEntityClass($dbClass);
                }
                $field->setBrickDatabase(new C4GBrickDatabase($databaseParams));

                if ($field->getDatabaseType() == C4GBrickDatabaseType::DOCTRINE) {
                    foreach ($element->$index as $value) {
                        $dbValues = $field->getBrickDatabase()->findBy($field->getForeignKey(), $value);
                        foreach ($dbValues as $dbVal) {
                            if ($dbVal instanceof \stdClass) {
                                foreach ($dbVal as $key => $val) {
                                    $ind = $field->getFieldName().'_'.$key.'_'.$value;
                                    $element->$ind = $val;
                                }
                            }
                        }
                    }
                } else {
                    foreach (unserialize($element->$index) as $value) {
                        $dbValues = $field->getBrickDatabase()->findBy($field->getForeignKey(), $value);
                        foreach ($dbValues as $dbVal) {
                            if ($dbVal instanceof \stdClass) {
                                foreach ($dbVal as $key => $val) {
                                    $ind = $field->getFieldName().'_'.$key.'_'.$value;
                                    $element->$ind = $val;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $element;
    }
}
