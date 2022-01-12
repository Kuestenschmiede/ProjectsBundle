<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Dialogs;

use con4gis\ProjectsBundle\Classes\Actions\C4GBrickActionType;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GSelectField;
use con4gis\ProjectsBundle\Classes\Models\C4gProjectsModel;
use con4gis\ProjectsBundle\Classes\Actions\C4GShowRedirectDialogAction;

class C4GBrickSelectProjectDialog extends C4GBrickDialog
{
    /**
     * @param $memberId
     * @param $group_id
     * @return array
     */
    public function show()
    {
        $dialogParams = $this->getDialogParams();

        $memberId = $dialogParams->getMemberId();
        $groupId = $dialogParams->getGroupId();
        $projectId = $dialogParams->getProjectId();
        $projectKey = $dialogParams->getProjectKey();

        $confirmAction = C4GBrickActionType::ACTION_CONFIRMPROJECTSELECT;
        $confirmButtonText = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_PROJECT_DIALOG_CONFIRM_BUTTON'];

        $cancelAction = C4GBrickActionType::ACTION_CANCELPROJECTSELECT;
        $cancelButtonText = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_PROJECT_DIALOG_CANCEL_BUTTON'];

        $projectlist = [];
        $projects = C4gProjectsModel::getProjectListForBrick($memberId, $groupId, $projectKey);

        if (!$projects) {
            //$dialogParams->setRedirectDialogMessage($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_PROJECT_NO_PROJECT']);
            $redirects = $dialogParams->getRedirects();
            if ($redirects) {
                foreach ($redirects as $redirect) {
                    $redirect->setActive($redirect->getType() == C4GBrickConst::REDIRECT_PROJECT);
                }

                $action = new C4GShowRedirectDialogAction(
                    $dialogParams,
                    $this->listParams,
                    $this->fieldList,
                    $this->putVars,
                    $this->brickDatabase
                );

                return $action->run();
            }

            return ['usermessage' => &$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_PROJECT_NO_PROJECT']];
        }

        foreach ($projects as $project) {
            if (!$project->is_frozen) {
                $projectlist[] = [
                    'id' => $project->id,
                    'name' => $project->caption, ];
            }
        }

        $projectField = new C4GSelectField();
        $projectField->setFieldName('project_id');
        $projectField->setTitle($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_PROJECT']);
        $projectField->setSortColumn(false);
        $projectField->setTableColumn(false);
        $projectField->setSize(1);
        $projectField->setOptions($projectlist);
        $projectField->setChosen(true);

        if ($projectId && ($projectId > -1)) {
            $projectField->setInitialValue($projectId);
        }

        return C4GBrickDialog::showC4GSelectDialog($dialogParams,$projectField,
            $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_PROJECT_DIALOG_CONFIRM_QUESTION'],
            $confirmAction, $confirmButtonText, $cancelAction, $cancelButtonText);
    }
}
