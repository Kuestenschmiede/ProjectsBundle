<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */
namespace c4g\projects;


class C4gProjectsModel extends \Model
{

    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_c4g_projects';

    public static function getProjectListForBrick($memberId, $groupId, $brick_key) {
        $t = static::$strTable;

        $arrColumns = array("$t.group_id=? AND $t.brick_key=?");
        $arrValues = array($groupId, $brick_key);
        $arrOptions = array(
            'order' => "$t.caption ASC"
        );

        $projects = static::findBy($arrColumns, $arrValues, $arrOptions);

        $result = array();
        if ($projects) {
            foreach($projects as $project) {
                if (\c4g\projects\C4GBrickCommon::hasMemberRightsForBrick($memberId, $project->id, $brick_key)) {
                    $result[] = $project;
                }
            }
        }

        return $result;
    }

    public static function checkProjectId($project_id, $brick_key) {
        if ($project_id  && $brick_key) {
            $project = static::findByPk($project_id);
            if ($project && ($project->brick_key == $brick_key)) {
                \Session::getInstance()->set("c4g_brick_project_uuid", $project->uuid);
                return true;
            }
        }

        return false;
    }

    public static function checkProjectGroup($project_id, $groupId) {
        if ($project_id  && $groupId) {
            $project = static::findByPk($project_id);
            if ($project && ($project->group_id == $groupId)) {
                return true;
            }
        }

        return false;
    }
}
