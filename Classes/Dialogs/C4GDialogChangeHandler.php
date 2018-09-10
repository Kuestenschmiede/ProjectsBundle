<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\ProjectsBundle\Classes\Dialogs;

use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;

class C4GDialogChangeHandler
{
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
        if ($save && $changes) {
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
        // load changes from session
        $oldChanges = \Session::getInstance()->get($moduleKey . '_fieldChanges');
        // merge with new ones
        if ($oldChanges) {
            foreach ($changes as $key => $arrValue) {
                // $key is the fieldname
                // $arrValue is $key = property, $value display value
                if ($oldChanges[$key]) {
                    unset($oldChanges[$key]);
                }
            }
            $changes = array_merge($changes, $oldChanges);
        }
        // store new changes
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
