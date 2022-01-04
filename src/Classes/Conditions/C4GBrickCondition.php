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
namespace con4gis\ProjectsBundle\Classes\Conditions;

class C4GBrickCondition
{
    private $fieldName = '';   //Abhängig von diesem Feld
    private $type = C4GBrickConditionType::BOOLSWITCH;
    private $value = '';
    private $model = '';
    private $function = '';

    public function __construct($conditionType, $fieldName, $fieldValue = -1)
    {
        $this->fieldName = $fieldName;
        $this->type = $conditionType;
        $this->value = $fieldValue;
    }

    /**
     * Checks the given value against the condition and returns the result.
     * @param string $Value
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
        }

        return false;
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @param $fieldName
     * @return $this
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return string
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
}
