<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 10
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2025, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Dialogs;

use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Session\C4gBrickSession;

class C4GDialogChangeHandler
{
    private $session = null;

    public function __construct(C4gBrickSession &$session)
    {
        $this->session = $session;
    }

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
            if (key_exists($field->getFieldName(), $changes) && $changes[$field->getFieldName()]) {
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
        $oldChanges = $this->session->getSessionValue($moduleKey . '_fieldChanges');
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
        $this->session->setSessionValue($moduleKey . '_fieldChanges', $changes);
    }

    /**
     * Loads the change array for the given module key from session.
     * @param $moduleKey
     * @return array|mixed
     */
    private function loadChangesFromSession($moduleKey)
    {
        if ($changes = $this->session->getSessionValue($moduleKey . '_fieldChanges')) {
            return $changes;
        }

        return [];
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
        if ($value === 'true' || $value === 'false') {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        return $value;
    }

    public function clearSession($moduleKey)
    {
        $this->session->remove($moduleKey . '_fieldChanges');
    }
}
