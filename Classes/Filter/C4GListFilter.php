<?php
/**
 * Created by PhpStorm.
 * User: rro
 * Date: 21.09.18
 * Time: 09:30
 */

namespace con4gis\ProjectsBundle\Classes\Filter;


abstract class C4GListFilter
{
    private $headText = '';

    /**
     * C4GListFilter constructor.
     * @param $brickKey
     */
    public final function __construct($brickKey) {
        $this->getFilterCookies($brickKey);
    }

    /**
     * @param $dlgValues
     * @param $brickKey
     */
    public abstract function setFilter($dlgValues, $brickKey);

    /**
     * Filter out undesired elements and return the desired ones.
     * @param $elements
     * @param $dialogParams
     * @return mixed
     */
    public abstract function filter($elements, $dialogParams);

    /**
     * Call listParams->addButton() to dynamically add the filter button to the list.
     * @param $listParams
     */
    public abstract function addButton($listParams);

    /**
     * Save the current filter settings in a cookie.
     * @param $brickKey
     */
    protected abstract function setFilterCookies($brickKey);

    /**
     * Load the settings stored in the cookie into the object.
     * @param $brickKey
     */
    public abstract function getFilterCookies($brickKey);

    /**
     * @return string
     */
    public function getHeadText(): string
    {
        return $this->headText;
    }

    /**
     * @param string $headText
     * @return C4GListFilter
     */
    public function setHeadText(string $headText): C4GListFilter
    {
        $this->headText = $headText;
        return $this;
    }


}