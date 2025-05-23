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
namespace con4gis\ProjectsBundle\Classes\Views;

class C4GBrickViewParams
{
    private $viewType = C4GBrickViewType::GROUPBASED;

    private $memberKeyField = 'member_id';
    private $groupKeyField = 'group_id';
    private $parentKeyField = 'pid';
    private $projectKeyField = 'project_id';
    private $modelListFunction = null; //Lädt die Datensätze der Tabelle über eine spezielle Modelfunktion.
    private $modelDialogFunction = null; //Lädt die Datensätze der Tabelle über eine spezielle Modelfunktion.
    private $loginRedirect = '';
    private $actionNamespaces = [];    //Namespaces for custom actions, full path including the name of the action.

    /**
     * C4GBrickViewParams constructor.
     * @param string $viewType
     */
    public function __construct($viewType)
    {
        $this->viewType = $viewType;
    }

    /**
     * @return string
     */
    public function getViewType()
    {
        return $this->viewType;
    }

    /**
     * @param string $viewType
     */
    public function setViewType($viewType)
    {
        $this->viewType = $viewType;
    }

    /**
     * @return string
     */
    public function getMemberKeyField()
    {
        return $this->memberKeyField;
    }

    /**
     * @param string $memberKeyField
     */
    public function setMemberKeyField($memberKeyField)
    {
        $this->memberKeyField = $memberKeyField;
    }

    /**
     * @return string
     */
    public function getGroupKeyField()
    {
        return $this->groupKeyField;
    }

    /**
     * @param string $groupKeyField
     */
    public function setGroupKeyField($groupKeyField)
    {
        $this->groupKeyField = $groupKeyField;
    }

    /**
     * @return string
     */
    public function getParentKeyField()
    {
        return $this->parentKeyField;
    }

    /**
     * @param string $parentKeyField
     */
    public function setParentKeyField($parentKeyField)
    {
        $this->parentKeyField = $parentKeyField;
    }

    /**
     * @return null
     */
    public function getModelListFunction()
    {
        return $this->modelListFunction;
    }

    /**
     * @param null $modelListFunction
     */
    public function setModelListFunction($modelListFunction)
    {
        $this->modelListFunction = $modelListFunction;
    }

    /**
     * @return null
     */
    public function getModelDialogFunction()
    {
        return $this->modelDialogFunction;
    }

    /**
     * @param null $modelDialogFunction
     */
    public function setModelDialogFunction($modelDialogFunction): void
    {
        $this->modelDialogFunction = $modelDialogFunction;
    }

    /**
     * @return string
     */
    public function getLoginRedirect()
    {
        return $this->loginRedirect;
    }

    /**
     * @param string $loginRedirect
     */
    public function setLoginRedirect($loginRedirect)
    {
        $this->loginRedirect = $loginRedirect;
    }

    /**
     * @return array
     */
    public function getActionNamespaces()
    {
        return $this->actionNamespaces;
    }

    /**
     * @param string $actionNamespace
     */
    public function setActionNamespaces($actionNamespace)
    {
        $this->actionNamespaces = $actionNamespace;
    }

    /**
     * @return string
     */
    public function getProjectKeyField()
    {
        return $this->projectKeyField;
    }

    /**
     * @param string $projectKeyField
     */
    public function setProjectKeyField($projectKeyField)
    {
        $this->projectKeyField = $projectKeyField;
    }
}
