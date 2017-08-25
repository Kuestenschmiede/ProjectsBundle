<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\ProjectBundle\Classes\Dialogs;

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
     * @param int $column
     */
    public function setCol($col)
    {
        $this->col = $col;
    }

    /**
     * @return string
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param string $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return null
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param null $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }

    /**
     * @return int
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * @param int $row
     */
    public function setRow($row)
    {
        $this->row = $row;
    }

    /**
     * @return string
     */
    public function getHorizontal()
    {
        return $this->horizontal;
    }

    /**
     * @param string $horizontal
     */
    public function setHorizontal($horizontal)
    {
        $this->horizontal = $horizontal;
    }

    /**
     * @return string
     */
    public function getVertical()
    {
        return $this->vertical;
    }

    /**
     * @param string $vertical
     */
    public function setVertical($vertical)
    {
        $this->vertical = $vertical;
    }

    /**
     * @return int
     */
    public function getRowspan()
    {
        return $this->rowspan;
    }

    /**
     * @param int $rowspan
     */
    public function setRowspan($rowspan)
    {
        $this->rowspan = $rowspan;
    }

    /**
     * @return int
     */
    public function getColspan()
    {
        return $this->colspan;
    }

    /**
     * @param int $colspan
     */
    public function setColspan($colspan)
    {
        $this->colspan = $colspan;
    }


}