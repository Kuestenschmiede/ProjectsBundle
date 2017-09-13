<?php

use con4gis\CoreBundle\Resources\contao\classes\HttpResultHelper;

class C4GStarboardTabApi
{
    public function generate($arrInput)
    {
        // only allow GET requests
        if (strtoupper($_SERVER['REQUEST_METHOD']) != 'GET')
        {
            HttpResultHelper::MethodNotAllowed();
        }
        if (strtoupper($_SERVER['REQUEST_METHOD']) == 'GET')
        {
            echo json_encode($this->fetchStarboardData());
        }
    }

    private function handleStarboardTabs()
    {
        $starboardTabs = array();
        $layers = \con4gis\MapsBundle\Resources\contao\models\C4gMapsModel::findBy('location_type', \con4gis\ProjectsBundle\Classes\Maps\C4GProjectsFrontend::STARBOARD_TAB);
        if ($layers)
        {
            $layers->reset();
            while($layers->next())
            {
                $layer = $layers->current()->row();
                if ($layer['layername'] == '')
                {
                    $layer['layername'] = $layer['name'];
                }
                $starboardTabs[] = \con4gis\ProjectsBundle\Classes\Maps\C4GCustomEditorTabs::addEditorTab($layer['layername'], $layer['layername'], '', $layer['id']);
                $tabLayers = C4gMapsModel::findBy('pid', $layer['id']);
            }
        }
        return $starboardTabs;
    }

    private function fetchStarboardData()
    {
        $starboardTabs = $this->handleStarboardTabs();
        $starboardData = array();
        if (isset($GLOBALS['TL_HOOKS']['addProjectData']) && is_array($GLOBALS['TL_HOOKS']['addProjectData']))
        {
            foreach($GLOBALS['TL_HOOKS']['addProjectData'] as $callback)
            {
                $class = new $callback[0]();
                $starboardData[] = $class->$callback[1]($starboardTabs);
            }
        }
        return $starboardTabs;
    }
}