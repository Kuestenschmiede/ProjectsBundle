<?php
/**
 * Created by PhpStorm.
 * User: cro
 * Date: 19.07.17
 * Time: 17:00
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
     * @param null $function
     */
    public function setFunction($function)
    {
        $this->function = $function;
    }

    /**
     * @return null
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param null $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

}