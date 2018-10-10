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

namespace con4gis\ProjectsBundle\Classes\ListData;


use con4gis\ProjectsBundle\Classes\Lists\C4GBrickListParams;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewParams;

abstract class C4GListData
{
    protected $listElements = array();
    protected $listParams;
    protected $viewParams;

    public function __construct(C4GBrickListParams $listParams, C4GBrickViewParams $viewParams) {
        $this->listParams = $listParams;
        $this->viewParams = $viewParams;
    }

    /**
     * Load the values from the database into the object's $listElements property.
     */
    public abstract function loadListElements();

    public function getListElements() {
        return $this->listElements;
    }
}