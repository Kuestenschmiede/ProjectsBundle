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
        $brickDatabase     = $this->getBrickDatabase();
        $dialogParams      = $this->getDialogParams();
        $viewParams        = $dialogParams->getViewParams();
        $viewType          = $viewParams->getViewType();
        $id                = $dialogParams->getId();
        $groupId           = $dialogParams->getGroupId();
        $parentId          = $dialogParams->getParentId();
        $parentModel       = $dialogParams->getParentModel();
        $additionalHeadtext = $dialogParams->getAdditionalHeadText();

        \Session::getInstance()->set("c4g_brick_dialog_id", $id);
        
        if ($parentModel !== '' && $parentId !== -1) {
            $parent_headline = $this->createParentCaption($parentModel, $parentId);
        }
        
        $homeDir = $dialogParams->getHomeDir();
        if (!$homeDir) {
            $dialogParams->setHomeDir($this->getHomeDir());
        }

        $map = '';
        if (!$dialogParams->getC4gMap()) {
            $result = $this->withMap($this->getFieldList(), $dialogParams->getContentId());
            if ($result) {
                $map = \Controller::replaceInsertTags('{{insert_content::'.$result.'}}');
            }
        } else {
            $map = $dialogParams->getC4gMap();
        }

        if (C4GBrickView::isWithGroup($viewType)) {
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

        //Wenn $element an dieser Stelle null ist wird ein neuer Datensatz angelegt (Hinzufügen),
        //ansonsten wird der bestehende Datensatz zur Bearbeitung angeboten
        //Todo Refactor this so it is object-oriented
        $this->module->getDialogDataObject()->loadValuesAndAuthenticate();
        $dialogData = $this->module->getDialogDataObject()->getDbValues();
        $result = C4GBrickDialog::showC4GDialog(
            $this->getFieldList(),
            $brickDatabase->getParams()->getDatabase(),
            C4GBrickCommon::arrayToObject($dialogData),
            $map,
            $headtext,
            $dialogParams
        );

        return $result;
    }

    public function isReadOnly()
    {
        return true;
    }

    private function getHomeDir()
    {
        $dialogParams = $this->getDialogParams();
        $memberId     = $dialogParams->getMemberId();
        $groupId      = $dialogParams->getGroupId();
        $projectUuid  = $dialogParams->getProjectUuid();
        $viewType     = $dialogParams->getViewType();

        if (C4GBrickView::isPublicBased($viewType)) {
            $homeDir = '/' . C4GBrickConst::PATH_BRICK_DATA;
        }

        if (C4GBrickView::isPublicParentBased($viewType)) {
            $homeDir = '/' . C4GBrickConst::PATH_BRICK_DATA . '/' . $dialogParams->getParentId() . '/';
        }

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

    /**
     * @param $parentModel
     * @param $parentId
     * @return string
     */
    private function createParentCaption($parentModel, $parentId) {
        $parent_headline = '';
        $parent = $parentModel::findByPk($parentId);
        if ($parent) {
            //implemented for permalinks
            $groupKeyField = $this->dialogParams->viewParams->getGroupKeyField();
            if ($parent->$groupKeyField) {
                $groupId = $parent->$groupKeyField;
                $this->dialogParams->setGroupId($groupId);
            }
            if ($parent->project_id) {
                $projectId = $parent->project_id;
                $this->dialogParams->setProjectId($projectId);
            }
            $caption = $parent->caption;
            if (!$caption) {
                $caption = $parent->name;
            }
            $parentCaptionFields = $this->dialogParams->getParentCaptionFields();
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
            } elseif ($parentCaptionCallback = $this->dialogParams->getParentCaptionCallback()) {
                $class = $parentCaptionCallback[0];
                $function = $parentCaptionCallback[1];
                $arrCaptions = $class::$function(
                    [$parent],
                    $this->brickDatabase->getEntityManager()
                );
                $caption = $arrCaptions[$parentId];
            }
            $parent_headline = '<div class="c4g_brick_headtext"> '.$this->dialogParams->getParentCaption().': <b>'.$caption.'</b></div>';
        }
        return $parent_headline;
    }

}
