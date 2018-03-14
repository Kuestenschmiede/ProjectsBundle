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

namespace con4gis\ProjectsBundle\Classes\Conditions;


class C4GBrickCondition
{
    private $fieldName  = '';   //Abhängig von diesem Feld
    private $type       = C4GBrickConditionType::BOOLSWITCH;
    private $value      = '';
    private $model      = '';
    private $function   = '';

    public function __construct($conditionType, $fieldName, $fieldValue = -1)
    {
        $this->fieldName = $fieldName;
        $this->type      = $conditionType;
        $this->value     = $fieldValue;
    }

    /**
     * Checks the given value against the condition and returns the result.
     * @param string $givenValue
     * @return bool
     */
    public function checkAgainstCondition($Value)
    {
        if ($this->model && $this->function) {
            $model = $this->model;
            $function = $this->function;
            return $model::$function($Value);
        }
        if ($Value == $this->value) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @param string $fieldName
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }


    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param string $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @return string
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * @param string $function
     */
    public function setFunction($function)
    {
        $this->function = $function;
    }


}