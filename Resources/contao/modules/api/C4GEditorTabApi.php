<?php

namespace con4gis\ProjectsBundle\Resources\contao\modules\api;

class C4GEditorTabApi
{

    const ALLOWED_REQUEST_METHODS = array('POST', 'GET', 'PUT', 'DELETE');
    /**
     * Determines the method of the request and executes the desired action.
     * @param $arrInput
     */
    public function generate(array $arrInput)
    {
        // check request method
        if (!in_array(strtoupper($_SERVER['REQUEST_METHOD']), self::ALLOWED_REQUEST_METHODS))
        {
            \HttpResultHelper::MethodNotAllowed();
        }
        switch(strtoupper($_SERVER['REQUEST_METHOD'])) {
            case 'GET':
                // get is the initial request of tab configuration
                echo json_encode($this->createTabs($arrInput[0]));
                break;
            case 'POST':
                // a new element has been drawn onto the map
                // requires a response to complete the creation process
                echo json_encode($this->onElementCreation($_POST));
//                echo json_encode($_POST);
                break;
            case 'PUT':
                // an existing element has been modified
                // requires a response to complete the creation process
                echo json_encode($this->onElementModification($arrInput[0]));
                break;
            case 'DELETE':
                // an existing element has been deleted
                // requires a response to complete the creation process
                echo json_encode($this->onElementDeletion($arrInput[0]));
                break;
            default:
                \HttpResultHelper::MethodNotAllowed();
                break;
        }
    }

    private function createTabs($layerIds) {
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

    public function createTest($layerId) {
        $editor = array();
        $project = array();

        // die indizes im array der kategorien und elemente müssen gleich ihrer id sein!
        $project['name'] = "Testprojekt 2";
        $project['categories'] = array();
        $project['projectId'] = 48;
        $elements = array();
        $elements[1] = array('name' => 'Lila Punkt', 'styleId' => 2, 'limit' => 4, 'count' => 0, 'id' => 1, 'cid' => 1);
        $elements[2] = array('name' => 'Test 1', 'styleId' => 6,'limit' => -1, 'id' => 2, 'cid' => 1);
        $project['categories'][1] = array('name' => 'Testszenario', 'elements' => $elements, 'id' => 1, 'projectId' => 48);
        $editor['projects'][] = $project;

        $project2['name'] = "Testtest";
        $project2['categories'] = array();
        $project2['projectId'] = 4234;
        $elements = array();
        $elements[4] = array('name' => 'Quadrat', 'styleId' => 15, 'limit' => 12, 'count' => 0, 'id' => 4, 'cid' => 2);
        $elements[2] = array('name' => 'Stern', 'styleId' => 22, 'limit' => 9, 'count' => 0, 'id' => 2, 'cid' => 2);
        $project2['categories'][2] = array('name' => 'Testkategorie', 'elements' => $elements, 'id' => 2, 'projectId' => 4234);
        $editor['projects'][] = $project2;
        $editor['name'] = 'Planungsprojekte';
        $editor['tabId'] = $layerId;

        return $editor;
    }

    /**
     * Calls all functions who are registered to the specific element creation hook.
     * The functions need to return some sort of confirmation.
     * @param $postVars
     * @return array
     */
    private function onElementCreation($postVars) {
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
    private function onElementModification($layer) {
        return null;
    }

    /**
     * Calls all functions who are registered to the specific element deletion hook.
     * The functions need to return some sort of confirmation.
     * @param $layer
     */
    private function onElementDeletion($layer) {
        return null;
    }
}