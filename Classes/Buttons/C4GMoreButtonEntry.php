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

    private $title = '';

    private $callMode = 0;

    private $callable = '';

    private $condition = array(); //C4GBrickCondition. Type must be Method Switch.

    private $toolTip = '';

    // for creating a title via callback, [Model, function]
    private $dynamicTitleCallback = array();

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return array
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @param array $condition
     */
    public function setCondition($condition)
    {
        $this->condition = $condition;
    }

    /**
     * @return string
     */
    public function getToolTip()
    {
        return $this->toolTip;
    }

    /**
     * @param string $toolTip
     */
    public function setToolTip($toolTip)
    {
        $this->toolTip = $toolTip;
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
        switch($callmode) {
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
            default:
                break;
        }
    }

    /**
     * @param $params   array An array of params passed to the callback.
     * @return mixed
     */
    public function call($params = array())
    {
        if (count($params) > 0) {
            return call_user_func($this->callable, $params);
        } else {
            return call_user_func($this->callable);
        }
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
        } else {
            if ($entry->getTitle() === $this->getTitle() &&
                $entry->getCallMode() == $this->getCallMode()) {
                return true;
            }
            if ($entry === $this) {
                return true;
            }
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
     * @param array $dynamicTitleCallback
     */
    public function setDynamicTitleCallback($dynamicTitleCallback)
    {
        $this->dynamicTitleCallback = $dynamicTitleCallback;
    }
}