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

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getCallMode(): int
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

    public function call()
    {
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
}