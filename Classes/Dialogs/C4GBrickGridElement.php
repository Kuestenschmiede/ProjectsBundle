<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    6
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Dialogs;

use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;

class C4GBrickGridElement
{
    private $col  = 0;
    private $colspan = 0;
    private $row     = 0;
    private $rowspan = 0;
    private $width   = '100%';
    private $horizontal = 'left';
    private $vertical = 'top';
    private $field   = null;

    /**
     * C4GBrickGridElement constructor.
     */
    public function __construct(C4GBrickField $field, $col, $row, $horizontal = 'left', $vertical = 'top', $width = 'auto', $colspan = 0, $rowspan = 0)
    {
        $this->field = $field;
        $this->col = $col;
        $this->colspan = $colspan;
        $this->row = $row;
        $this->rowspan = $rowspan;
        $this->horizontal = $horizontal;
        $this->vertical = $vertical;
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getCol()
    {
        return $this->col;
    }

    /**
     * @param $col
     * @return $this
     */
    public function setCol($col)
    {
        $this->col = $col;
        return $this;
    }

    /**
     * @return string
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param $width
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return null
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param $field
     * @return $this
     */
    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @return int
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * @param $row
     * @return $this
     */
    public function setRow($row)
    {
        $this->row = $row;
        return $this;
    }

    /**
     * @return string
     */
    public function getHorizontal()
    {
        return $this->horizontal;
    }

    /**
     * @param $horizontal
     * @return $this
     */
    public function setHorizontal($horizontal)
    {
        $this->horizontal = $horizontal;
        return $this;
    }

    /**
     * @return string
     */
    public function getVertical()
    {
        return $this->vertical;
    }

    /**
     * @param $vertical
     * @return $this
     */
    public function setVertical($vertical)
    {
        $this->vertical = $vertical;
        return $this;
    }

    /**
     * @return int
     */
    public function getRowspan()
    {
        return $this->rowspan;
    }

    /**
     * @param $rowspan
     * @return $this
     */
    public function setRowspan($rowspan)
    {
        $this->rowspan = $rowspan;
        return $this;
    }

    /**
     * @return int
     */
    public function getColspan()
    {
        return $this->colspan;
    }

    /**
     * @param $colspan
     * @return $this
     */
    public function setColspan($colspan)
    {
        $this->colspan = $colspan;
        return $this;
    }


}