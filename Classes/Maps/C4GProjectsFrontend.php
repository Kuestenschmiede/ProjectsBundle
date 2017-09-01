<?php

namespace con4gis\ProjectsBundle\Classes\Maps;
use c4g\Maps\C4gMapsModel;

/**
 * Class C4GProjectsFrontend
 * @package c4g\projects
 */
class C4GProjectsFrontend extends C4GBrickMapFrontendParent
{
    /**
     *
     */
    const STARBOARD_TAB = 'startab';

    /**
     * This function creates appropriate project data, if the passed child is a project
     * @param $level
     * @param $child
     * @return array|void
     */
    public function createProjectData($level, $child)
    {
        if ($child['location_type'] == self::STARBOARD_TAB)
        {
            $arrData = $this->addMapStructureElement(
                $level,
                $child['id'],
                $child['id'],
                self::STARBOARD_TAB,
                $child['name'],
                $child['layername'],
                true,
                $child['hide']);

            $arrChildData = $this->getProjectChildData($child);

            if (sizeof($arrChildData) == 0 && $child['raw']->tDontShowIfEmpty) {
                $return = C4GCustomEditorTabs::addProject($arrData, false, true, $child);
            } else {
                $arrData = $this->addMapStructureChilds($arrData, $arrChildData, false);
                $return = C4GCustomEditorTabs::addProject($arrData, true, true, $child);
            }
            return $return;
        }
        // if nothing can be done, return null
        return null;
    }

    /**
     * @param $parent
     * @return array
     */
    private function getProjectChildData($parent)
    {
//        $childs = $parent['childs'];
        $childs = C4gMapsModel::findBy('pid', $parent['id']);
        $childData = array();
        if ($childs)
        {
            $childs->reset();
            while($childs->next())
            {
                $elem = $childs->current()->row();
                if ($elem['location_type'] == self::STARBOARD_TAB)
                {
                    // do not allow projects as childs of projects
                    //TODO: throw warning that subprojects are not allowed
                    continue;
                }
                $childData[$elem['id']] = $this->addMapStructureElement($elem['pid'],
                    $elem['id'],
                    $elem['id'],
                    $elem['type'],
                    $elem['name'],
                    $elem['layername'],
                    $elem['display'],
                    $elem['hide']);
            }
        }
        return $childData;
    }


}