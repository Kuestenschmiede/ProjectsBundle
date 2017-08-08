<?php

class C4GStarboardTabApi
{
    public function generate($arrInput)
    {
        // only allow GET requests
        if (strtoupper($_SERVER['REQUEST_METHOD']) != 'GET')
        {
            \c4g\HttpResultHelper::MethodNotAllowed();
        }
        if (strtoupper($_SERVER['REQUEST_METHOD']) == 'GET')
        {
            echo json_encode($this->fetchStarboardData());
        }
    }

    private function handleStarboardTabs()
    {
        $starboardTabs = array();
        $layers = \c4g\Maps\C4gMapsModel::findBy('location_type', \c4g\projects\C4GProjectsFrontend::STARBOARD_TAB);
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
                $starboardTabs[] = \c4g\projects\C4GCustomEditorTabs::addEditorTab($layer['layername'], $layer['layername'], '', $layer['id']);
                $tabLayers = \c4g\Maps\C4gMapsModel::findBy('pid', $layer['id']);
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