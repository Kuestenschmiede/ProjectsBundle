<?php

namespace con4gis\ProjectsBundle\Resources\contao\modules\api;

class C4GEditorTabApi
{
    public function createTabs($layerIds) {
        // here we store the starboard tabs (parent elements) for later iteration
        // insert "createTest" into hook array for testing purpose
        // array for all needed editor tabs
        $editorTabs = array();
        if (isset($GLOBALS['TL_HOOKS']['c4gGetProjectData']) && is_array($GLOBALS['TL_HOOKS']['c4gGetProjectData'])) {
            // call each defined function and receive project data from them
            // all but one entry in $editorConfig will be null
            foreach($GLOBALS['TL_HOOKS']['c4gGetProjectData'] as $callback) {
                $class = new $callback[0]();
                $editor = $class->{$callback[1]}($layerIds);
                if ($editor != null) {
                    $editorTabs[] = $editor;
                }
            }
        }
        return $editorTabs;
    }

    /**
     * Calls all functions who are registered to the specific element creation hook.
     * The functions need to return some sort of confirmation.
     * @param $postVars
     * @return array
     */
    public function onElementCreation($postVars) {
        $layerUpdate = array();
        if (isset($GLOBALS['TL_HOOKS']['elementCreated']) && is_array($GLOBALS['TL_HOOKS']['elementCreated'])) {
            // call each defined function and receive project data from them
            // all but one entry in $editorConfig will be null
            foreach($GLOBALS['TL_HOOKS']['elementCreated'] as $callback) {
                $class = new $callback[0]();
                $layerUpdate[] = $class->{$callback[1]}($postVars);
            }
        }
        return $layerUpdate;
    }

    /**
     * Calls all functions who are registered to the specific element modification hook.
     * The functions need to return some sort of confirmation.
     * @param $layer
     */
    public function onElementModification($layer) {
        return null;
    }

    /**
     * Calls all functions who are registered to the specific element deletion hook.
     * The functions need to return some sort of confirmation.
     * @param $layer
     */
    public function onElementDeletion($layer) {
        return null;
    }
}