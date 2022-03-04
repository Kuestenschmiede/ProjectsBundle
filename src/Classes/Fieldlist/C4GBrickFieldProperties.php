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
namespace con4gis\ProjectsBundle\Classes\Fieldlist;

class C4GBrickFieldProperties
{
    private $data = null;
    private $dialogParams = null;
    private $frozen = false;

    /**
     * C4GBrickFieldProperties constructor.
     * @param null $fieldList
     * @param null $field
     * @param null $data
     * @param null $dialogParams
     * @param bool $is_frozen
     */
    public function __construct($data, $dialogParams, $is_frozen)
    {
        $this->data = $data;
        $this->dialogParams = $dialogParams;
        $this->frozen = $is_frozen;
    }

    /**
     * @return null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return null
     */
    public function getDialogParams()
    {
        return $this->dialogParams;
    }

    /**
     * @param null $dialogParams
     */
    public function setDialogParams($dialogParams)
    {
        $this->dialogParams = $dialogParams;
    }

    /**
     * @return boolean
     */
    public function isFrozen()
    {
        return $this->frozen;
    }

    /**
     * @param $frozen
     * @return $this
     */
    public function setFrozen($frozen = true)
    {
        $this->frozen = $frozen;

        return $this;
    }
}
