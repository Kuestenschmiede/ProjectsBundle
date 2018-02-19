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

namespace con4gis\ProjectsBundle\Classes\Lists;
use con4gis\ProjectsBundle\Classes\Buttons\C4GBrickButton;
use con4gis\ProjectsBundle\Classes\Buttons\C4GExportButtons;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Filter\C4GBrickFilterParams;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickView;

/**
 * Class C4GBrickListParams
 * @package c4g\projects
 */
class C4GBrickListParams
{
    private $displayLength = 25; //dataTable displayLength
    private $withModelListFunction = false; //calls modelListFunction
    private $withDetails = true; //show details by tablerow click
    private $filterParams = null; //filterParams to filter big datasets
    private $renderMode = C4GBrickRenderMode::TABLEBASED; //see C4GBrickRenderMode
    private $buttons = array(); //table buttons
    private $withExportButtons = true; //show exportButtons under datatable
    private $exportButtons = null;//C4GExportButtons
    private $printOnlyVisibleColumns = true; //export button prints just visible columns
    private $headline = ''; //set datatable headline
    private $selectRow = -1; //mark row as initial selection
    private $paginate = true; //shows pagination
    private $lengthChange = true; //shows lengthChange
    private $filter = true; //shows filter
    private $info = true; //shows info
    private $withoutListButtons = false; //remove all list button for views
    private $withFunctionCallOnClick = false; // true for a custom function call instead of dialog opening
    private $onClickFunction = '';  // the name of the function in the module which should be called on click
    private $additionalOnClickParams = array(); // parameters for the function
    private $withHoverText = false; // if true, a hover text will be set for each element, the text will be searched in $element['hovertext'] (modellistfunction needed)
    private $onloadScript = ''; // javascript code that should be executed when the list is loaded
    private $removeUnpublishedElements = false; //if true we ignore unpublished elements (!published)
    private $groupCount = 0;
    private $viewFormatFunction = '';   //function in the module model to change the presentation of data
    private $withJQueryUI = true; // disable jQueryUI
    private $captionField = 'caption'; //used for list tooltip
    private $rowCount = 0; //number of datasets (LISTBASED)
    private $redirectTo = '';
    private $Uuid = '';


    /**
     * C4GBrickListParams constructor.
     */
    public function __construct($brickKey, $viewType)
    {
        $this->buttons = $this->getDefaultListButtons($viewType);
        $this->exportButtons = new C4GExportButtons();

        $this->filterParams = new C4GBrickFilterParams($brickKey);
        $this->filterParams->setHeadtext($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['headText']);
        $this->filterParams->setButtontext($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['buttonText']);
    }

    /**
     * @return boolean
     */
    private function getDefaultListButtons($viewType)
    {
        $buttons = array();

        if ($viewType) {
            if (!C4GBrickView::isWithoutEditing($viewType)) {
                $buttons[] = new C4GBrickButton(C4GBrickConst::BUTTON_ADD);
            }

            if (C4GBrickView::isWithGroup($viewType)) {
                $buttons[] = new C4GBrickButton(C4GBrickConst::BUTTON_GROUP);
            }

            if (C4GBrickView::isWithProject($viewType)) {
                $buttons[] = new C4GBrickButton(C4GBrickConst::BUTTON_PROJECT);
            }

            if (C4GBrickView::isWithParent($viewType)) {
                $buttons[] = new C4GBrickButton(C4GBrickConst::BUTTON_PARENT);
            }
        }

        return $buttons;
    }

    public function addButton($type, $caption='', $visible=true, $enabled=true, $action = '') {
        $exists = false;

        if ($caption == '') {
            $caption =  C4GBrickButton::getTypeCaption($type);
        }

        if ($action == '') {
            $action =  C4GBrickButton::getTypeAction($type);
        }

        if ($type && ($type != C4GBrickConst::BUTTON_CLICK)) {
            foreach ($this->buttons as $btn) {
                if ($btn->getType() == $type) {
                    $btn->setCaption($caption);
                    $btn->setVisible($visible);
                    $btn->setEnabled($enabled);
                    $btn->setAction($action);

                    $exists = true;
                    break;
                }
            }
        }

        if (!$exists) {
            $this->buttons[] = new C4GBrickButton($type, $caption, $visible, $enabled, $action);
        }
    }

    public function deleteButton($type) {

        $exists = false;
        foreach($this->buttons as $btn) {
            if ($btn->getType() == $type) {
                $btn->setCaption('');
                $btn->setVisible(false);
                $btn->setEnabled(false);
                $btn->setAction(false);

                $exists = true;
                break;
            }
        }

        if (!$exists) {
            $this->buttons[] = new C4GBrickButton($type, '', false, false, false);
        }
    }

    public function changeButtonText($type, $caption) {
        foreach($this->buttons as $btn) {
            if ($btn->getType() == $type) {
                $btn->setCaption($caption);
                return true;
            }
        }

        return false;
    }

    public function getButton($type) {
        foreach($this->buttons as $button) {
            if ($button->getType() == $type) {
                return $button;
            }
        }

        return null;
    }

    public function checkButtonVisibility($type) {

        foreach($this->buttons as $button) {
            if ($button->getType() == $type) {
                if ($button->isVisible()) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        return false;
    }

    /**
     * @return int
     */
    public function getDisplayLength()
    {
        return $this->displayLength;
    }

    /**
     * @param int $displayLength
     */
    public function setDisplayLength($displayLength)
    {
        $this->displayLength = $displayLength;
    }

    /**
     * @return boolean
     */
    public function isWithModelListFunction()
    {
        return $this->withModelListFunction;
    }

    /**
     * @param boolean $withModelListFunction
     */
    public function setWithModelListFunction($withModelListFunction)
    {
        $this->withModelListFunction = $withModelListFunction;
    }

    /**
     * @return boolean
     */
    public function isWithDetails()
    {
        return $this->withDetails;
    }

    /**
     * @param boolean $withDetails
     */
    public function setWithDetails($withDetails)
    {
        $this->withDetails = $withDetails;
    }

    /**
     * @return null
     */
    public function getFilterParams()
    {
        return $this->filterParams;
    }

    /**
     * @param null $filterParams
     */
    public function setFilterParams(&$filterParams)
    {
        $this->filterParams = $filterParams;
    }

    /**
     * @return string
     */
    public function getRenderMode()
    {
        return $this->renderMode;
    }

    /**
     * @param string $renderMode
     */
    public function setRenderMode($renderMode)
    {
        $this->renderMode = $renderMode;
    }

    /**
     * @return array
     */
    public function getButtons()
    {
        return $this->buttons;
    }

    /**
     * @param array $buttons
     */
    public function setButtons($buttons)
    {
        $this->buttons = $buttons;
    }

    /**
     * @return boolean
     */
    public function isWithExportButtons()
    {
        return $this->withExportButtons;
    }

    /**
     * @param boolean $withExportButtons
     */
    public function setWithExportButtons($withExportButtons)
    {
        $this->withExportButtons = $withExportButtons;
    }

    /**
     * @return string
     */
    public function getHeadline()
    {
        return $this->headline;
    }

    /**
     * @param string $headline
     */
    public function setHeadline($headline)
    {
        $this->headline = $headline;
    }

    /**
     * @return int
     */
    public function getSelectRow()
    {
        return $this->selectRow;
    }

    /**
     * @param int $selectRow
     */
    public function setSelectRow($selectRow)
    {
        $this->selectRow = $selectRow;
    }

    /**
     * @return bool
     */
    public function isPaginate()
    {
        return $this->paginate;
    }

    /**
     * @param bool $paginate
     */
    public function setPaginate($paginate)
    {
        $this->paginate = $paginate;
    }

    /**
     * @return bool
     */
    public function isLengthChange()
    {
        return $this->lengthChange;
    }

    /**
     * @param bool $lengthChange
     */
    public function setLengthChange($lengthChange)
    {
        $this->lengthChange = $lengthChange;
    }

    /**
     * @return bool
     */
    public function isFilter()
    {
        return $this->filter;
    }

    /**
     * @param bool $filter
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    /**
     * @return bool
     */
    public function isInfo()
    {
        return $this->info;
    }

    /**
     * @param bool $info
     */
    public function setInfo($info)
    {
        $this->info = $info;
    }

    /**
     * @return bool
     */
    public function isWithoutListButtons()
    {
        return $this->withoutListButtons;
    }

    /**
     * @param bool $withoutListButtons
     */
    public function setWithoutListButtons($withoutListButtons)
    {
        $this->withoutListButtons = $withoutListButtons;
    }

    /**
     * @return boolean
     */
    public function isWithFunctionCallOnClick()
    {
        return $this->withFunctionCallOnClick;
    }

    /**
     * @param boolean $withFunctionCallOnClick
     */
    public function setWithFunctionCallOnClick($withFunctionCallOnClick)
    {
        $this->withFunctionCallOnClick = $withFunctionCallOnClick;
    }

    /**
     * @return string
     */
    public function getOnClickFunction()
    {
        return $this->onClickFunction;
    }

    /**
     * @param string $onClickFunction
     */
    public function setOnClickFunction($onClickFunction)
    {
        $this->onClickFunction = $onClickFunction;
    }

    /**
     * @return array
     */
    public function getAdditionalOnClickParams()
    {
        return $this->additionalOnClickParams;
    }

    /**
     * @param array $additionalOnClickParams
     */
    public function setAdditionalOnClickParams($additionalOnClickParams)
    {
        $this->additionalOnClickParams = $additionalOnClickParams;
    }

    /**
     * @return bool
     */
    public function isWithHoverText()
    {
        return $this->withHoverText;
    }

    /**
     * @param bool $withHoverText
     */
    public function setWithHoverText($withHoverText)
    {
        $this->withHoverText = $withHoverText;
    }

    /**
     * @return bool
     */
    public function isRemoveUnpublishedElements()
    {
        return $this->removeUnpublishedElements;
    }

    /**
     * @param bool $removeUnpublishedElements
     */
    public function setRemoveUnpublishedElements($removeUnpublishedElements)
    {
        $this->removeUnpublishedElements = $removeUnpublishedElements;
    }

    /**
     * @return int
     */
    public function getGroupCount()
    {
        return $this->groupCount;
    }

    /**
     * @param int $groupCount
     */
    public function setGroupCount($groupCount)
    {
        $this->groupCount = $groupCount;
    }

    /**
     * @return string
     */
    public function getViewFormatFunction()
    {
        return $this->viewFormatFunction;
    }

    /**
     * @param string $viewFormatFunction
     */
    public function setViewFormatFunction($viewFormatFunction)
    {
        $this->viewFormatFunction = $viewFormatFunction;
    }

    /**
     * @return bool
     */
    public function isWithJQueryUI()
    {
        return $this->withJQueryUI;
    }

    /**
     * @param bool $withJQueryUI
     */
    public function setWithJQueryUI($withJQueryUI)
    {
        $this->withJQueryUI = $withJQueryUI;
    }

    /**
     * @return bool
     */
    public function isPrintOnlyVisibleColumns()
    {
        return $this->printOnlyVisibleColumns;
    }

    /**
     * @param bool $printOnlyVisibleColumns
     */
    public function setPrintOnlyVisibleColumns($printOnlyVisibleColumns)
    {
        $this->printOnlyVisibleColumns = $printOnlyVisibleColumns;
    }

    /**
     * @return array
     */
    public function getExportButtons()
    {
        return $this->exportButtons;
    }

    /**
     * @param array $exportButtons
     */
    public function setExportButtons($exportButtons)
    {
        $this->exportButtons = $exportButtons;
    }

    /**
     * @return string
     */
    public function getCaptionField()
    {
        return $this->captionField;
    }

    /**
     * @param string $captionField
     */
    public function setCaptionField($captionField)
    {
        $this->captionField = $captionField;
    }

    /**
     * @return int
     */
    public function getRowCount()
    {
        return $this->rowCount;
    }

    /**
     * @param int $rowCount
     */
    public function setRowCount($rowCount)
    {
        $this->rowCount = $rowCount;
    }

    /**
     * @return string
     */
    public function getRedirectTo()
    {
        return $this->redirectTo;
    }

    /**
     * @param string $redirectTo
     */
    public function setRedirectTo($redirectTo)
    {
        $this->redirectTo = $redirectTo;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->Uuid;
    }

    /**
     * @param string $Uuid
     */
    public function setUuid($Uuid)
    {
        $this->Uuid = $Uuid;
    }

}