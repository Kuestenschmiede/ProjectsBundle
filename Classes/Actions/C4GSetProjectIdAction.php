<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\ProjectBundle\Classes\Actions;

class C4GSetProjectIdAction extends C4GBrickDialogAction
{
    public function run()
    {
        $dlgValues = $this->getPutVars();
        $dialogParams = $this->getDialogParams();
        $dialogParams->setProjectId($dlgValues['project_id']);
        $projectId = $dlgValues['project_id'];

        $project = \c4g\projects\C4gProjectsModel::findByPk($projectId);
        $dialogParams->setProjectUuid($project->uuid);

        \Session::getInstance()->set("c4g_brick_project_id", $projectId);
        \Session::getInstance()->set("c4g_brick_project_uuid", $project->uuid);

        \Session::getInstance()->set("c4g_brick_parent_id", '');
        $dialogParams->setParentId('');

        $this->setPutVars(null);

        $dialogParams->setId(-1);
        $action = new C4GShowListAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());
        return $action->run();
    }
}
