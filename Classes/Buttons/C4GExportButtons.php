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
     * @param bool $withPrintButton
     */
    public function setWithPrintButton($withPrintButton)
    {
        $this->withPrintButton = $withPrintButton;
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
     */
    public function setWithPdfButton($withPdfButton)
    {
        $this->withPdfButton = $withPdfButton;
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
     */
    public function setWithCsvButton($withCsvButton)
    {
        $this->withCsvButton = $withCsvButton;
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
     */
    public function setWithExcelButton($withExcelButton)
    {
        $this->withExcelButton = $withExcelButton;
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
     */
    public function setWithCopyButton($withCopyButton)
    {
        $this->withCopyButton = $withCopyButton;
    }

    /**
     * @return string
     */
    public function getOrientation()
    {
        return $this->orientation;
    }

    /**
     * @param string $orientation
     */
    public function setOrientation($orientation)
    {
        $this->orientation = $orientation;
    }

    /**
     * @return string
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * @param string $pageSize
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
    }

    /**
     * @return array
     */
    public function getExportOptions()
    {
        return $this->exportOptions;
    }

    /**
     * @param array $exportOptions
     */
    public function setExportOptions($exportOptions)
    {
        $this->exportOptions = $exportOptions;
    }

}