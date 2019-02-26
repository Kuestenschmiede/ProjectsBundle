<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    6
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Actions;

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialog;
use con4gis\ProjectsBundle\Classes\Models\C4gProjectsModel;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;

class C4GCloseDialogAction extends C4GBrickDialogAction
{
    private $ignoreChanges = false;

    public function run()
    {
        $dialogParams = $this->getDialogParams();
        $viewType     = $dialogParams->getViewType();
        $id = $dialogParams->getId();

        $dlgValues = $this->getPutVars();
        $dlgValues['id'] = $id;

        $brickDatabase = $this->getBrickDatabase();
        if ($brickDatabase && ($id > 0)) {
            $dbValues = $brickDatabase->findByPk($id);
        }

        $project_id = $dialogParams->getProjectId();
        $is_frozen = $dialogParams->isFrozen();
        if ($viewType == C4GBrickViewType::GROUPPROJECT) {
            if ($dbValues) {
                $is_frozen = $dbValues->is_frozen;
            }
        } else if ($project_id) {
            // TODO projects model fixen
//            $project = C4gProjectsModel::findByPk($project_id);
//            if ($project) {
//                $is_frozen = $project->is_frozen;
//            }
        }

        if ($viewType == C4GBrickViewType::GROUPPROJECT) {
            if ($dbValues) {
                $is_frozen = $dbValues->is_frozen;
            }
        }

        $fieldList = $this->makeRegularFieldList($this->getFieldList());

        //$changes = array();
        //if ($dbValues) {
        $changes = C4GBrickDialog::compareWithDB($fieldList, $dlgValues, $dbValues, $viewType, $is_frozen);
        //}

        if (count($changes) > 0 && $this->ignoreChanges == false) {
            $action = new C4GShowMessageChangesDialogAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());
            $action->setChanges($changes);
            return $action->run();
        } else {
            $dialogParams->setId(-1);
            $action = new C4GShowListAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());
            return $action->run();
        }

        return $return;
    }

    /**
     * @param bool $ignoreChanges
     * @return $this
     */
    public function setIgnoreChanges($ignoreChanges = true)
    {
        $this->ignoreChanges = $ignoreChanges;
        return $this;
    }

    public function isReadOnly()
    {
        return true;
    }
}
