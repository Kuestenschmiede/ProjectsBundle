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

class C4GBrickGrid
{
    private $elements = [];
    private $columns = 0; //just css grid

    /**
     * C4GBrickGrid
     */
    public function __construct($elements, $columns = 0)
    {
        $this->elements = $elements;
        $this->columns = $columns;
    }

    /**
     * @return array
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * @param $elements
     * @return $this
     */
    public function setElements($elements)
    {
        $this->elements = $elements;

        return $this;
    }

    /**
     * @return int|mixed
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param int|mixed $columns
     */
    public function setColumns($columns): void
    {
        $this->columns = $columns;
    }
}
