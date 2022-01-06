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
namespace con4gis\ProjectsBundle\Classes\Actions;

use con4gis\ProjectsBundle\Classes\Models\C4gProjectsModel;

class C4GSetProjectIdAction extends C4GBrickDialogAction
{
    public function run()
    {
        $dlgValues = $this->getPutVars();
        $dialogParams = $this->getDialogParams();
        $dialogParams->setProjectId($dlgValues['project_id']);
        $projectId = $dlgValues['project_id'];

        $project = C4gProjectsModel::findByPk($projectId);
        $dialogParams->setProjectUuid($project->uuid);

        $dialogParams->getSession()->setSessionValue('c4g_brick_project_id', $projectId);
        $dialogParams->getSession()->setSessionValue('c4g_brick_project_uuid', $project->uuid);

        $dialogParams->getSession()->setSessionValue('c4g_brick_parent_id', '');
        $dialogParams->setParentId('');

        $this->setPutVars(null);

        $dialogParams->setId(-1);
        $action = new C4GShowListAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());

        return $action->run();
    }

    public function isReadOnly()
    {
        return true;
    }
}
