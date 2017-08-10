<?php
/**
 * Created by PhpStorm.
 * User: cro
 * Date: 10.08.17
 * Time: 10:20
 */

namespace con4gis\ProjectBundle\Classes\Dialogs;

use con4gis\ProjectBundle\Classes\Fieldlist\C4GBrickField;

class C4GDialogChangeHandler
{
    // TODO kill session for module key on dialog close/save
    /**
     * Reads the incoming changes from the changes array and applies them to the correct field from the
     * passed fieldList. The changes are then saved into the session for persisting them through multiple
     * ajax requests.
     * @param $changes
     * @param $fieldList
     * @param $moduleKey
     * @param $save
     * @return array
     */
    public function applyChanges($changes, $fieldList, $moduleKey, $save = true)
    {
        /* @var $field C4GBrickField */
        foreach ($fieldList as $field) {
            if ($changes[$field->getFieldName()]) {
                $fieldChanges = $changes[$field->getFieldName()];
                // there is a change for the current field
                foreach ($fieldChanges as $property => $value) {
                    $setter = 'set' . ucfirst($property);
                    $value = $this->checkVal($value);
                    $field->$setter($value);
                }
            }
        }
        if ($save) {
            $this->saveChangesToSession($changes, $moduleKey);
        }
        return $fieldList;
    }

    /**
     * Stores the change array into the session.
     * @param $changes
     * @param $moduleKey
     */
    private function saveChangesToSession($changes, $moduleKey)
    {
        \Session::getInstance()->set($moduleKey . '_fieldChanges', $changes);
    }

    /**
     * Loads the change array for the given module key from session.
     * @param $moduleKey
     * @return array|mixed
     */
    private function loadChangesFromSession($moduleKey)
    {
        if ($changes = \Session::getInstance()->get($moduleKey . '_fieldChanges')) {
            return $changes;
        } else {
            return array();
        }
    }

    /**
     * Loads the changes from session and applies them. Used when an ajax is fired and the
     * field changes still need to be kept.
     * @param $moduleKey
     * @param $fieldList
     * @return mixed
     */
    public function reapplyChanges($moduleKey, $fieldList)
    {
        $changes = $this->loadChangesFromSession($moduleKey);
        return $this->applyChanges($changes, $fieldList, $moduleKey, false);
    }

    /**
     * Parses the actual value from a string.
     * @param $value
     * @return mixed
     */
    private function checkVal($value)
    {
        if ($value === "true" || $value === "false") {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        } else {
            return $value;
        }
    }

    public function clearSession($moduleKey)
    {
        \Session::getInstance()->remove($moduleKey . '_fieldChanges');
    }
}
