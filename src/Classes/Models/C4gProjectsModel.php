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
namespace con4gis\ProjectsBundle\Classes\Models;

use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;

class C4gProjectsModel extends \Model
{
    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_c4g_projects';

    public static function getProjectListForBrick($memberId, $groupId, $brick_key)
    {
        $t = static::$strTable;

        $arrColumns = ["$t.group_id=? AND $t.brick_key=?"];
        $arrValues = [$groupId, $brick_key];
        $arrOptions = [
            'order' => "$t.caption ASC",
        ];

        $projects = static::findBy($arrColumns, $arrValues, $arrOptions);

        $result = [];
        if ($projects) {
            foreach ($projects as $project) {
                if (C4GBrickCommon::hasMemberRightsForBrick($memberId, $project->id, $brick_key)) {
                    $result[] = $project;
                }
            }
        }

        return $result;
    }

    public static function checkProjectId($project_id, $brick_key, &$session)
    {
        if ($project_id && $brick_key) {
            $project = static::findByPk($project_id);
            if ($project && ($project->brick_key == $brick_key)) {
                $session->setSessionValue('c4g_brick_project_uuid', $project->uuid);

                return true;
            }
        }

        return false;
    }

    public static function checkProjectGroup($project_id, $groupId)
    {
        if ($project_id && $groupId) {
            $project = static::findByPk($project_id);
            if ($project && ($project->group_id == $groupId)) {
                return true;
            }
        }

        return false;
    }
}
