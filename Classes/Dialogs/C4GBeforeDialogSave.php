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


class C4GBeforeDialogSave
{
    private $function = null;
    // this property needs to be a real class reference, not a string with a class name.
    private $class = null;
    // set true if the call should be static
    private $isStaticCall = false;

    /**
     * C4GBeforeDialogSave constructor.
     * @param $class
     * @param $function
     * @param $isStatic
     */
    public function __construct($class, $function, $isStatic = false) {
        $this->class = $class;
        $this->function = $function;
        $this->isStaticCall = $isStatic;
    }

    /**
     * Helper function to call the function. Used for simplicity, so the rest of projects does not need to know
     * what is called inside and how.
     * @param $params
     */
    public function call($params) {
        if ($this->class && $this->function) {
            $class = $this->class;
            $func = $this->function;
            if ($this->isStaticCall) {
                return $class::$func($params);
            } else {
                return $class->$func($params);
            }
        }
        return $params;
    }

    /**
     * @return null
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * @param $function
     * @return $this
     */
    public function setFunction($function)
    {
        $this->function = $function;
        return $this;
    }

    /**
     * @return null
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param $class
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

}