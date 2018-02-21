<?php

/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */
namespace con4gis\ProjectsBundle\Classes\Views;

class C4GBrickViewParams {

    private $viewType = C4GBrickViewType::GROUPBASED;

    private $memberKeyField = 'member_id';
    private $groupKeyField  = 'group_id';
    private $parentKeyField = 'pid';
    private $modelListFunction = null; //Lädt die Datensätze der Tabelle über eine spezielle Modelfunktion.
    private $loginRedirect  = '';

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
}
