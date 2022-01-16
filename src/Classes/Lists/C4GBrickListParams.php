<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2022, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Lists;

use con4gis\ProjectsBundle\Classes\Buttons\C4GBrickButton;
use con4gis\ProjectsBundle\Classes\Buttons\C4GExportButtons;
use con4gis\ProjectsBundle\Classes\Buttons\C4GFilterButtonInterface;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Filter\C4GBrickFilterParams;
use con4gis\ProjectsBundle\Classes\Filter\C4GListFilter;
use con4gis\ProjectsBundle\Classes\Session\C4gBrickSession;
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
    private $filterParams = null; //filterParams to filter big datasets *DEPRECATED*
    private $filterObject = null; //Filter object to filter the data.
    private $renderMode = C4GBrickRenderMode::TABLEBASED; //see C4GBrickRenderMode
    private $buttons = []; //table buttons
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
    private $additionalOnClickParams = []; // parameters for the function
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
    private $showToolTips = true; //press false to not show tooltips for the table rows
    private $customListViewFunction = [];  //array(Object instance, String function); To build a custom list view. The function takes the following parameters: $fieldList, $database, $tableElements, $content, $listParams
    private $forceShowListAction = false; //Press true to force a ShowListAction to be used to display the module instead of a ShowDialogAction if id > 0
    private $customHeadline = false; // if true, only the headline from the list data will be displayed
    private $showFullTextSearchInHeadline = false; // redundant in table view
    private $filterButtons = [];
    private $showItemType = false;
    private $miniSearchNotice = '';
    private $params = [];
    private $stripFieldList = true;
    private $scrollX = false;
    private $autoWidth = false;
    private $responsive = true;
    private $redirectListPage = 0;
    private $session = null;

    /**
     * C4GBrickListParams constructor.
     */
    public function __construct($brickKey, $viewType, C4gBrickSession $session)
    {
        $this->buttons = $this->getDefaultListButtons($viewType);
        $this->exportButtons = new C4GExportButtons();

        $this->filterParams = new C4GBrickFilterParams($brickKey);
        $this->filterParams->setHeadtext($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['headText']);
        $this->filterParams->setButtontext($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['buttonText']);

        $this->session = $session;
    }

    /**
     * @return boolean
     */
    private function getDefaultListButtons($viewType)
    {
        $buttons = [];

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

    public function addButton($type, $caption = '', $visible = true, $enabled = true, $action = '')
    {
        $exists = false;

        if ($type && ($type != C4GBrickConst::BUTTON_CLICK)) {
            foreach ($this->buttons as $btn) {
                if ($btn->getType() === $type) {
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

    public function deleteButton($type)
    {
        $exists = false;
        foreach ($this->buttons as $btn) {
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

    public function changeButtonText($type, $caption)
    {
        foreach ($this->buttons as $btn) {
            if ($btn->getType() == $type) {
                $btn->setCaption($caption);

                return true;
            }
        }

        return false;
    }

    public function getButton($type)
    {
        foreach ($this->buttons as $button) {
            if ($button->getType() == $type) {
                return $button;
            }
        }

        return null;
    }

    public function checkButtonVisibility($type)
    {
        foreach ($this->buttons as $button) {
            if ($button->getType() == $type) {
                if ($button->isVisible()) {
                    return true;
                }

                return false;
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
     * @param $displayLength
     * @return $this
     */
    public function setDisplayLength($displayLength)
    {
        $this->displayLength = $displayLength;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isWithModelListFunction()
    {
        return $this->withModelListFunction;
    }

    /**
     * @param bool $withModelListFunction
     * @return $this
     */
    public function setWithModelListFunction($withModelListFunction = true)
    {
        $this->withModelListFunction = $withModelListFunction;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isWithDetails()
    {
        return $this->withDetails;
    }

    /**
     * @param bool $withDetails
     * @return $this
     */
    public function setWithDetails($withDetails = true)
    {
        $this->withDetails = $withDetails;

        return $this;
    }

    /**
     * @return null
     */
    public function getFilterParams()
    {
        return $this->filterParams;
    }

    /**
     * @param $filterParams
     * @return $this
     */
    public function setFilterParams(&$filterParams)
    {
        $this->filterParams = $filterParams;

        return $this;
    }

    /**
     * @return string
     */
    public function getRenderMode()
    {
        return $this->renderMode;
    }

    /**
     * @param $renderMode
     * @return $this
     */
    public function setRenderMode($renderMode)
    {
        $this->renderMode = $renderMode;

        return $this;
    }

    /**
     * @return array
     */
    public function getButtons()
    {
        return $this->buttons;
    }

    /**
     * @param $buttons
     * @return $this
     */
    public function setButtons($buttons)
    {
        $this->buttons = $buttons;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isWithExportButtons()
    {
        return $this->withExportButtons;
    }

    /**
     * @param bool $withExportButtons
     * @return $this
     */
    public function setWithExportButtons($withExportButtons = true)
    {
        $this->withExportButtons = $withExportButtons;

        return $this;
    }

    /**
     * @return string
     */
    public function getHeadline()
    {
        return $this->headline;
    }

    /**
     * @param $headline
     * @return $this
     */
    public function setHeadline($headline)
    {
        $this->headline = $headline;

        return $this;
    }

    /**
     * @return int
     */
    public function getSelectRow()
    {
        return $this->selectRow;
    }

    /**
     * @param $selectRow
     * @return $this
     */
    public function setSelectRow($selectRow)
    {
        $this->selectRow = $selectRow;

        return $this;
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
     * @return $this
     */
    public function setPaginate($paginate = true)
    {
        $this->paginate = $paginate;

        return $this;
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
     * @return $this
     */
    public function setLengthChange($lengthChange = true)
    {
        $this->lengthChange = $lengthChange;

        return $this;
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
     * @return $this
     */
    public function setFilter($filter = true)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * @return bool
     */
    public function isInfo()
    {
        return $this->info;
    }

    /**
     * @param $info
     * @return $this
     */
    public function setInfo($info = true)
    {
        $this->info = $info;

        return $this;
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
     * @return $this
     */
    public function setWithoutListButtons($withoutListButtons = true)
    {
        $this->withoutListButtons = $withoutListButtons;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isWithFunctionCallOnClick()
    {
        return $this->withFunctionCallOnClick;
    }

    /**
     * @param bool $withFunctionCallOnClick
     * @return $this
     */
    public function setWithFunctionCallOnClick($withFunctionCallOnClick = true)
    {
        $this->withFunctionCallOnClick = $withFunctionCallOnClick;

        return $this;
    }

    /**
     * @return string
     */
    public function getOnClickFunction()
    {
        return $this->onClickFunction;
    }

    /**
     * @param $onClickFunction
     * @return $this
     */
    public function setOnClickFunction($onClickFunction)
    {
        $this->onClickFunction = $onClickFunction;

        return $this;
    }

    /**
     * @return array
     */
    public function getAdditionalOnClickParams()
    {
        return $this->additionalOnClickParams;
    }

    /**
     * @param $additionalOnClickParams
     * @return $this
     */
    public function setAdditionalOnClickParams($additionalOnClickParams)
    {
        $this->additionalOnClickParams = $additionalOnClickParams;

        return $this;
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
     * @return $this
     */
    public function setWithHoverText($withHoverText = true)
    {
        $this->withHoverText = $withHoverText;

        return $this;
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
     * @return $this
     */
    public function setRemoveUnpublishedElements($removeUnpublishedElements = true)
    {
        $this->removeUnpublishedElements = $removeUnpublishedElements;

        return $this;
    }

    /**
     * @return int
     */
    public function getGroupCount()
    {
        return $this->groupCount;
    }

    /**
     * @param $groupCount
     * @return $this
     */
    public function setGroupCount($groupCount)
    {
        $this->groupCount = $groupCount;

        return $this;
    }

    /**
     * @return string
     */
    public function getViewFormatFunction()
    {
        return $this->viewFormatFunction;
    }

    /**
     * @param $viewFormatFunction
     * @return $this
     */
    public function setViewFormatFunction($viewFormatFunction)
    {
        $this->viewFormatFunction = $viewFormatFunction;

        return $this;
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
     * @return $this
     */
    public function setWithJQueryUI($withJQueryUI = true)
    {
        $this->withJQueryUI = $withJQueryUI;

        return $this;
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
     * @return $this
     */
    public function setPrintOnlyVisibleColumns($printOnlyVisibleColumns = true)
    {
        $this->printOnlyVisibleColumns = $printOnlyVisibleColumns;

        return $this;
    }

    /**
     * @return array
     */
    public function getExportButtons()
    {
        return $this->exportButtons;
    }

    /**
     * @param $exportButtons
     * @return $this
     */
    public function setExportButtons($exportButtons)
    {
        $this->exportButtons = $exportButtons;

        return $this;
    }

    /**
     * @return string
     */
    public function getCaptionField()
    {
        return $this->captionField;
    }

    /**
     * @param $captionField
     * @return $this
     */
    public function setCaptionField($captionField)
    {
        $this->captionField = $captionField;

        return $this;
    }

    /**
     * @return int
     */
    public function getRowCount()
    {
        return $this->rowCount;
    }

    /**
     * @param $rowCount
     * @return $this
     */
    public function setRowCount($rowCount)
    {
        $this->rowCount = $rowCount;

        return $this;
    }

    /**
     * @return string
     */
    public function getRedirectTo()
    {
        return $this->redirectTo;
    }

    /**
     * @param $redirectTo
     * @return $this
     */
    public function setRedirectTo($redirectTo)
    {
        $this->redirectTo = $redirectTo;

        return $this;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->Uuid;
    }

    /**
     * @param $Uuid
     * @return $this
     */
    public function setUuid($Uuid)
    {
        $this->Uuid = $Uuid;

        return $this;
    }

    /**
     * @return bool
     */
    public function isShowToolTips()
    {
        return $this->showToolTips;
    }

    /**
     * @param bool $showToolTips
     * @return $this
     */
    public function setShowToolTips($showToolTips = true)
    {
        $this->showToolTips = $showToolTips;

        return $this;
    }

    /**
     * @return array
     */
    public function getCustomListViewFunction()
    {
        return $this->customListViewFunction;
    }

    /**
     * @param array $customListViewFunction
     * @return C4GBrickListParams
     */
    public function setCustomListViewFunction($customListViewFunction)
    {
        $this->customListViewFunction = $customListViewFunction;

        return $this;
    }

    /**
     * @return bool
     */
    public function isForceShowListAction()
    {
        return $this->forceShowListAction;
    }

    /**
     * @param $forceShowListAction
     * @return $this
     */
    public function setForceShowListAction($forceShowListAction = true)
    {
        $this->forceShowListAction = $forceShowListAction;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCustomHeadline()
    {
        return $this->customHeadline;
    }

    /**
     * @param bool $customHeadline
     */
    public function setCustomHeadline($customHeadline)
    {
        $this->customHeadline = $customHeadline;
    }

    /**
     * @return C4GListFilter|null
     */
    public function getFilterObject(): ?C4GListFilter
    {
        return $this->filterObject;
    }

    /**
     * @param C4GListFilter $filterObject
     * @return $this
     */
    public function setFilterObject(C4GListFilter $filterObject)
    {
        $this->filterObject = $filterObject;

        return $this;
    }

    /**
     *
     */
    public function removeFilterObject()
    {
        $this->filterObject = null;
    }

    /**
     * @return bool
     */
    public function isShowFullTextSearchInHeadline(): bool
    {
        return $this->showFullTextSearchInHeadline;
    }

    /**
     * @param bool $showFullTextSearchInHeadline
     * @return C4GBrickListParams
     */
    public function setShowFullTextSearchInHeadline(bool $showFullTextSearchInHeadline = true): C4GBrickListParams
    {
        $this->showFullTextSearchInHeadline = $showFullTextSearchInHeadline;

        return $this;
    }

    /**
     * @return array
     */
    public function getFilterButtons() : array
    {
        return $this->filterButtons;
    }

    /**
     * @param C4GFilterButtonInterface $filterButton
     * @return $this
     */
    public function addFilterButton(C4GFilterButtonInterface $filterButton)
    {
        $this->filterButtons[] = $filterButton;

        return $this;
    }

    /**
     * @return bool
     */
    public function isShowItemType(): bool
    {
        return $this->showItemType;
    }

    /**
     * @param bool $showItemType
     * @return C4GBrickListParams
     */
    public function setShowItemType(bool $showItemType = true): C4GBrickListParams
    {
        $this->showItemType = $showItemType;

        return $this;
    }

    /**
     * @return string
     */
    public function getMiniSearchNotice(): string
    {
        return $this->miniSearchNotice;
    }

    /**
     * @param string $miniSearchNotice
     * @return C4GBrickListParams
     */
    public function setMiniSearchNotice(string $miniSearchNotice): C4GBrickListParams
    {
        $this->miniSearchNotice = $miniSearchNotice;

        return $this;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     * @return C4GBrickListParams
     */
    public function setParams(array $params): C4GBrickListParams
    {
        $this->params = $params;

        return $this;
    }

    /**
     * @return bool
     */
    public function isStripFieldList(): bool
    {
        return $this->stripFieldList;
    }

    /**
     * @param bool $stripFieldList
     * @return C4GBrickListParams
     */
    public function setStripFieldList(bool $stripFieldList): C4GBrickListParams
    {
        $this->stripFieldList = $stripFieldList;

        return $this;
    }

    /**
     * @return bool
     */
    public function isScrollX(): bool
    {
        return $this->scrollX;
    }

    /**
     * @param bool $scrollX
     */
    public function setScrollX(bool $scrollX): void
    {
        $this->scrollX = $scrollX;
    }

    /**
     * @return bool
     */
    public function isAutoWidth(): bool
    {
        return $this->autoWidth;
    }

    /**
     * @param bool $autoWidth
     */
    public function setAutoWidth(bool $autoWidth): void
    {
        $this->autoWidth = $autoWidth;
    }

    /**
     * @return bool
     */
    public function isResponsive(): bool
    {
        return $this->responsive;
    }

    /**
     * @param bool $responsive
     */
    public function setResponsive(bool $responsive): void
    {
        $this->responsive = $responsive;
    }

    /**
     * @return int
     */
    public function getRedirectListPage(): int
    {
        return $this->redirectListPage;
    }

    /**
     * @param int $redirectListPage
     */
    public function setRedirectListPage(int $redirectListPage): void
    {
        $this->redirectListPage = $redirectListPage;
    }

    /**
     * @return C4gBrickSession
     */
    public function getSession(): C4gBrickSession
    {
        return $this->session;
    }

    /**
     * @param C4gBrickSession $session
     */
    public function setSession(C4gBrickSession $session): void
    {
        $this->session = $session;
    }
}
