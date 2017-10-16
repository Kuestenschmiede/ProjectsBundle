<?php
/**
 * Created by PhpStorm.
 * User: mei
 * Date: 08.03.17
 * Time: 08:30
 */

namespace con4gis\ProjectsBundle\Classes\Dialogs;


use con4gis\ProjectsBundle\Classes\Actions\C4GBrickActionType;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GSelectField;
use con4gis\ProjectsBundle\Classes\Models\C4gProjectsModel;

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

        $memberId   = $dialogParams->getMemberId();
        $groupId    = $dialogParams->getGroupId();
        $projectId  = $dialogParams->getProjectId();
        $projectKey = $dialogParams->getProjectKey();

        $confirmAction = C4GBrickActionType::ACTION_CONFIRMPROJECTSELECT;
        $confirmButtonText = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_PROJECT_DIALOG_CONFIRM_BUTTON'];

        $cancelAction = C4GBrickActionType::ACTION_CANCELPROJECTSELECT;
        $cancelButtonText = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_PROJECT_DIALOG_CANCEL_BUTTON'];

        $projectlist = array();
        $projects = C4gProjectsModel::getProjectListForBrick($memberId, $groupId, $projectKey);

        if (!$projects) {
            return array(
                'usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_PROJECT_NO_PROJECT']
            );
        }

        foreach($projects as $project) {
            if (!$project->is_frozen) {
                $projectlist[] = array(
                    'id'     => $project->id,
                    'name'   => $project->caption);
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

        if ( $projectId && ($projectId > -1)) {
            $projectField->setInitialValue($projectId);
        }

        return C4GBrickDialog::showC4GSelectDialog($dialogParams,$projectField,
            $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_PROJECT_DIALOG_CONFIRM_QUESTION'],
            $confirmAction, $confirmButtonText, $cancelAction, $cancelButtonText);
    }

}