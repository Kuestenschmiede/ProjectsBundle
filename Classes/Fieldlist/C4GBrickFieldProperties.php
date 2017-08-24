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

namespace con4gis\ProjectBundle\Classes\Fieldlist;


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
     * @param null $data
     */
    public function setData($data)
    {
        $this->data = $data;
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
     * @param boolean $frozen
     */
    public function setFrozen($frozen)
    {
        $this->frozen = $frozen;
    }


}