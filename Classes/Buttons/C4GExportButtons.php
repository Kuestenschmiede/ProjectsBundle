<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2019
 * @link      https://www.kuestenschmiede.de
 */
namespace con4gis\ProjectsBundle\Classes\Buttons;

/**
 * Class C4GExportButtons
 * @package con4gis\ProjectsBundle\Classes\Buttons
 */
class C4GExportButtons
{
    private $withPrintButton = true;
    private $withPdfButton   = true;
    private $withCsvButton   = true;
    private $withExcelButton = false;
    private $withCopyButton  = false;
    private $orientation     = 'portrait'; //or landscape
    private $pageSize        = 'A4'; // or Legal
    private $exportOptions   = array('columns' => ':visible'); /*or array(0,1,2,5) / array(array('name'=>'columnName')) -> https://datatables.net/reference/type/column-selector*/

    public function getButtonArr() {
        $result = array();
        if ($this->withPrintButton) {
            $result[] = array('extend' => 'print', 'orientation' => $this->orientation, 'pageSite' => $this->pageSize, 'exportOptions' => $this->exportOptions);
        }
        if ($this->withPdfButton) {
            $result[] = array('extend' => 'pdf', 'orientation' => $this->orientation, 'pageSite' => $this->pageSize, 'exportOptions' => $this->exportOptions);
        }
        if ($this->withCsvButton) {
            $result[] = array('extend' => 'csv', 'orientation' => $this->orientation, 'pageSite' => $this->pageSize, 'exportOptions' => $this->exportOptions);
        }
        if ($this->withExcelButton) {
            $result[] = array('extend' => 'excel', 'orientation' => $this->orientation, 'pageSite' => $this->pageSize, 'exportOptions' => $this->exportOptions);
        }
        if ($this->withCopyButton) {
            $result[] = array('extend' => 'copy', 'orientation' => $this->orientation, 'pageSite' => $this->pageSize, 'exportOptions' => $this->exportOptions);
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function isWithPrintButton()
    {
        return $this->withPrintButton;
    }

    /**
     * @param $withPrintButton
     * @return $this
     */
    public function setWithPrintButton($withPrintButton = true)
    {
        $this->withPrintButton = $withPrintButton;
        return $this;
    }

    /**
     * @return bool
     */
    public function isWithPdfButton()
    {
        return $this->withPdfButton;
    }

    /**
     * @param bool $withPdfButton
     * @return $this#
     */
    public function setWithPdfButton($withPdfButton = true)
    {
        $this->withPdfButton = $withPdfButton;
        return $this;
    }

    /**
     * @return bool
     */
    public function isWithCsvButton()
    {
        return $this->withCsvButton;
    }

    /**
     * @param bool $withCsvButton
     * @return $this
     */
    public function setWithCsvButton($withCsvButton = true)
    {
        $this->withCsvButton = $withCsvButton;
        return $this;
    }

    /**
     * @return bool
     */
    public function isWithExcelButton()
    {
        return $this->withExcelButton;
    }

    /**
     * @param bool $withExcelButton
     * @return $this
     */
    public function setWithExcelButton($withExcelButton = true)
    {
        $this->withExcelButton = $withExcelButton;
        return $this;
    }

    /**
     * @return bool
     */
    public function isWithCopyButton()
    {
        return $this->withCopyButton;
    }

    /**
     * @param bool $withCopyButton
     * @return $this
     */
    public function setWithCopyButton($withCopyButton = true)
    {
        $this->withCopyButton = $withCopyButton;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrientation()
    {
        return $this->orientation;
    }

    /**
     * @param $orientation
     * @return $this
     */
    public function setOrientation($orientation)
    {
        $this->orientation = $orientation;
        return $this;
    }

    /**
     * @return string
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * @param $pageSize
     * @return $this
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
        return $this;
    }

    /**
     * @return array
     */
    public function getExportOptions()
    {
        return $this->exportOptions;
    }

    /**
     * @param $exportOptions
     * @return $this
     */
    public function setExportOptions($exportOptions)
    {
        $this->exportOptions = $exportOptions;
        return $this;
    }

}