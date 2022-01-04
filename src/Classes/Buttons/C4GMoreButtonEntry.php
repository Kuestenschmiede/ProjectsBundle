<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Buttons;

use con4gis\ProjectsBundle\Classes\Structures\C4GAbstractListEntry;

class C4GMoreButtonEntry extends C4GAbstractListEntry
{
    // $callable = array('MyClass', 'myFunction') -> static call!
    const CALLMODE_STATIC = 1;
    // $callable = 'myFunction' -> for anonymous functions
    const CALLMODE_FUNCTION = 2;
    // $callable = array($object, 'myFunction') -> $obj->myFunction()
    const CALLMODE_OBJECT = 3;
    // $callable = 'myFunction'; -> Javascript function
    // In this callmode, the click event will be received by the element below the entry.
    // It can therefore not be used in tables set to be with details.
    const CALLMODE_JS = 4;

    private $title = '';

    private $callMode = 0;

    private $callable = '';

    private $condition = []; //C4GBrickCondition. Type must be Method Switch.

    private $toolTip = '';

    // for creating a title via callback, [Model, function]
    private $dynamicTitleCallback = [];

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return array
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @param $condition
     * @return $this
     */
    public function setCondition($condition)
    {
        $this->condition = $condition;

        return $this;
    }

    /**
     * @return string
     */
    public function getToolTip()
    {
        return $this->toolTip;
    }

    /**
     * @param $toolTip
     * @return $this
     */
    public function setToolTip($toolTip)
    {
        $this->toolTip = $toolTip;

        return $this;
    }

    /**
     * @return int
     */
    public function getCallMode()
    {
        return $this->callMode;
    }

    public function setCallable($callmode, $callable)
    {
        switch ($callmode) {
            case self::CALLMODE_STATIC:
                if (is_array($callable) && count($callable) == 2) {
                    $this->callMode = $callmode;
                    $this->callable = $callable;
                }

                break;
            case self::CALLMODE_FUNCTION:
                if (is_string($callable)) {
                    $this->callMode = $callmode;
                    $this->callable = $callable;
                }

                break;
            case self::CALLMODE_OBJECT:
                if (is_array($callable) && count($callable) == 2 && is_object($callable[0])) {
                    $this->callMode = $callmode;
                    $this->callable = $callable;
                }

                break;
            case self::CALLMODE_JS:
                if (is_string($callable)) {
                    $this->callMode = $callmode;
                    $this->callable = $callable;
                }

                break;
            default:
                break;
        }

        return $this;
    }

    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * @param $params   array An array of params passed to the callback.
     * @return mixed
     */
    public function call($params = [])
    {
        if (count($params) > 0) {
            return call_user_func($this->callable, $params);
        }

        return call_user_func($this->callable);
    }

    /**
     * Compares two C4GMoreButtonEntry objects.
     * @param C4GAbstractListEntry $entry
     * @return bool
     */
    public function equals(C4GAbstractListEntry $entry)
    {
        if (!$entry instanceof C4GMoreButtonEntry) {
            return false;
        }
        if ($entry->getTitle() === $this->getTitle() &&
                $entry->getCallMode() == $this->getCallMode()) {
            return true;
        }
        if ($entry === $this) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getDynamicTitleCallback()
    {
        return $this->dynamicTitleCallback;
    }

    /**
     * @param $dynamicTitleCallback
     * @return $this
     */
    public function setDynamicTitleCallback($dynamicTitleCallback)
    {
        $this->dynamicTitleCallback = $dynamicTitleCallback;

        return $this;
    }
}
