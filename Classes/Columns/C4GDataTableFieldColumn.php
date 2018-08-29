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

namespace con4gis\ProjectsBundle\Classes\Columns;

/**
 * Class C4GDataTableFieldColumn
 * Base Column object for a column in the C4GDataTableField
 */

abstract class C4GDataTableFieldColumn
{
    private $name = '';     //name of the corresponding database field
    private $table = '';    //Database table containing the value of the field
    private $label = '';    //Label of the column in the browser

    public function __construct($name, $table, $label) {
        $this->name = $name;
        $this->table = $table;
        $this->label = $label;
    }

    public abstract function validateFieldValue($value);

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return C4GDataTableFieldColumn
     */
    public function setName(string $name): C4GDataTableFieldColumn
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @param string $table
     * @return C4GDataTableFieldColumn
     */
    public function setTable(string $table): C4GDataTableFieldColumn
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return C4GDataTableFieldColumn
     */
    public function setLabel(string $label): C4GDataTableFieldColumn
    {
        $this->label = $label;
        return $this;
    }



}