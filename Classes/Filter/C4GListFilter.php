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
namespace con4gis\ProjectsBundle\Classes\Filter;


abstract class C4GListFilter
{
    protected $headText = '';

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
     * Return the text to be displayed above the table.
     *  Make sure to return different values for an active and an inactive filter, if appropriate.
     *  The return value may be an empty string.
     * @return mixed
     */
    public abstract function getFilterHeadline(): string;

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