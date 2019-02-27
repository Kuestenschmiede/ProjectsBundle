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
namespace con4gis\ProjectsBundle\Classes\Modules;


use con4gis\CoreBundle\Resources\contao\classes\ResourceLoader;
use con4gis\ProjectsBundle\Classes\Actions\C4GBrickActionType;
use con4gis\ProjectsBundle\Classes\Actions\C4GRedirectAction;
use con4gis\ProjectsBundle\Classes\Buttons\C4GBrickButton;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Database\C4GBrickDatabaseType;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GTextField;
use con4gis\ProjectsBundle\Classes\Framework\C4GBrickModuleParent;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;

class C4GSearchModule extends C4GBrickModuleParent
{
    protected $withPermissionCheck = false;
    protected $databaseType = C4GBrickDatabaseType::NO_DB;
    protected $viewType = C4GBrickViewType::PUBLICFORM;
    protected $languageFile = 'fe_c4g_search_module';
    protected $loadTriggerSearchFromOtherModuleResources = true;
    protected $jQueryUseScrollPane = false;
    protected $jQueryUseTable = false;
    protected $loadClearBrowserUrlResources = true;

    public function initBrickModule($id)
    {
        parent::initBrickModule($id);
        $this->dialogParams->deleteButton(C4GBrickConst::BUTTON_SAVE);
        $this->dialogParams->setWithoutGuiHeader(true);

        $this->dialogParams->addButton(
            C4GBrickConst::BUTTON_CLICK,
            $this->searchButtonCaption,
            true,
            true,
            C4GBrickActionType::ACTION_BUTTONCLICK.':redirectToList',
            '',
            true
        );
    }


    public function addFields()
    {
        $this->fieldList = $this->getFieldList();
    }

    public function getFieldList()
    {
        $fieldlist = [];
        $searchField = new C4GTextField();
        $searchField->setFieldName("searchValue");
        $searchField->setFormField(true);
        $searchField->setTitle($this->searchFieldCaption);
        $fieldlist[] = $searchField;

        return $fieldlist;
    }

    public function redirectToList($values, $putVars)
    {
        $page = $this->listModule;
        \Session::getInstance()->set("c4g_list_searchValue", $putVars['searchValue']);
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