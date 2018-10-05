<?php
/**
 * Created by PhpStorm.
 * User: cro
 * Date: 04.10.18
 * Time: 10:53
 */

namespace con4gis\ProjectsBundle\Classes\Framework;


use con4gis\CoreBundle\Resources\contao\classes\ResourceLoader;
use con4gis\ProjectsBundle\Classes\Actions\C4GBrickActionType;
use con4gis\ProjectsBundle\Classes\Actions\C4GRedirectAction;
use con4gis\ProjectsBundle\Classes\Buttons\C4GBrickButton;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Database\C4GBrickDatabaseType;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GTextField;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;

class C4GSearchModule extends C4GBrickModuleParent
{
    protected $withPermissionCheck = false;
    protected $databaseType = C4GBrickDatabaseType::NO_DB;
    protected $viewType = C4GBrickViewType::PUBLICFORM;

    public function initBrickModule($id)
    {
        parent::initBrickModule($id);
        $this->dialogParams->deleteButton(C4GBrickConst::BUTTON_SAVE);
        $this->dialogParams->addButton(
            C4GBrickConst::BUTTON_CLICK,
            'Zur Liste',
            true,
            true,
            C4GBrickActionType::ACTION_BUTTONCLICK.':redirectToList'
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
        $searchField->setTitle("Suchbegriff");   // TODO language
        $searchField->setDescription("Geben Sie den gewünschten Suchbegriff ein."); // TODO language
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