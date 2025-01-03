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
namespace con4gis\ProjectsBundle\Controller;

use con4gis\ProjectsBundle\Classes\Actions\C4GBrickActionType;
use con4gis\ProjectsBundle\Classes\Actions\C4GRedirectAction;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Database\C4GBrickDatabaseType;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GTextField;
use con4gis\ProjectsBundle\Classes\Framework\C4GBaseController;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;

class C4GSearchModuleController extends C4GBaseController
{
    protected $withPermissionCheck = false;
    protected $databaseType = C4GBrickDatabaseType::NO_DB;
    protected $viewType = C4GBrickViewType::PUBLICFORM;
    protected $languageFile = 'fe_c4g_search_module';
    protected $loadTriggerSearchFromOtherModuleResources = false;
    protected $jQueryUseScrollPane = false;
    protected $jQueryUseTable = false;
    protected $loadHistoryPushResources = false;
    protected $loadDefaultResources = true;
    protected $loadTrixEditorResources = false;
    protected $loadDateTimePickerResources = false;
    protected $loadChosenResources = false;
    protected $loadClearBrowserUrlResources = false;
    protected $loadConditionalFieldDisplayResources = false;
    protected $loadMoreButtonResources = false;
    protected $loadFontAwesomeResources = false;
    protected $loadFileUploadResources = false;
    protected $loadMultiColumnResources = false;
    protected $loadMiniSearchResources = false;
    protected $loadSignaturePadResources = false;
    protected $jQueryAddCore = true;
    protected $jQueryAddJquery = true;
    protected $jQueryAddJqueryUI = false;
    protected $jQueryUseTree = false;
    protected $jQueryUseHistory = false;
    protected $jQueryUseTooltip = false;
    protected $jQueryUseMaps = false;
    protected $jQueryUseGoogleMaps = false;
    protected $jQueryUseMapsEditor = false;
    protected $jQueryUseWswgEditor = false;
    protected $jQueryUsePopups = false;

    public function initBrickModule($id)
    {
        parent::initBrickModule($id);
        $this->dialogParams->deleteButton(C4GBrickConst::BUTTON_SAVE);
        $this->dialogParams->deleteButton(C4GBrickConst::BUTTON_SAVE_AND_REDIRECT);

        $this->dialogParams->setWithoutGuiHeader(true);

        if (strval($this->searchButtonCaption) === '') {
            $this->dialogParams->addButton(
                C4GBrickConst::BUTTON_CLICK,
                $this->searchButtonCaption,
                true,
                true,
                C4GBrickActionType::ACTION_BUTTONCLICK . ':redirectToList',
                '',
                true,
                null,
                null,
                'c4g_nodisplay'
            );
        } else {
            $this->dialogParams->addButton(
                C4GBrickConst::BUTTON_CLICK,
                $this->searchButtonCaption,
                true,
                true,
                C4GBrickActionType::ACTION_BUTTONCLICK . ':redirectToList',
                '',
                true
            );
        }
    }

    public function addFields() : array
    {
        $this->fieldList = $this->getFieldList();
        return $this->fieldList;
    }

    public function getFieldList()
    {
        $fieldlist = [];
        $searchField = new C4GTextField();
        $searchField->setFieldName('searchValue');
        $searchField->setFormField(true);
        if ($this->hideSearchFieldCaption !== '1') {
            $searchField->setTitle($this->searchFieldCaption);
        }
        $searchField->setAriaLabel($this->searchFieldCaption);
        $searchField->setPlaceholder(strval($this->searchFieldPlaceholder));
        $fieldlist[] = $searchField;

        return $fieldlist;
    }

    public function redirectToList($values, $putVars)
    {
        $page = $this->listModule;
        $this->session->setSessionValue('c4g_list_searchValue', $putVars['searchValue']);
        $this->dialogParams->setRedirectSite($page);
        $action = new C4GRedirectAction(
            $this->dialogParams,
            $this->listParams,
            $this->fieldList,
            $putVars,
            $this->brickDatabase
        );
        $action->setRedirectWithSaving(false);

        return $action->run();
    }
}
