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

namespace con4gis\ProjectsBundle\Classes\Framework;




/**
 * Class C4GListModuleParent
 * @package c4g\projects
 */
/*abstract*/ class C4GListModuleParent extends \Module
{
    protected $headline = '';           //String to be displayed above the list
    protected $headerGallery = null;    //C4GGalleryField to be displayed above the list, above the headline if it exists
    protected $javascript = '';         //javascript that handles the list and fields in the frontend
    protected $id = '';                 //numeric identifier that tells the ajax response which list sent the request
    protected $tableName = '';

    /*public abstract function getFields();*/
    /**
     *  Example:
     *  $fieldList = array();
     * $fieldLIst[]
     */

    /*public abstract function getListSearchFields();*/

    /*public abstract function getDefaultList();*/

    /**
     * Parse the template
     *
     * @return string
     */
    public function generate()
    {
        $this->Template = new \FrontendTemplate($this->strTemplate);

        $this->Template->id = $this->id;
        $this->Template->headerGallery = null;
        $this->Template->headline = $this->headline;
//        $this->Template->defaultList = $this->getDefaultList();

        $GLOBALS['TL_JAVASCRIPT'][] = $this->javascript;

        return $this->Template->parse();
    }

    /**
     * Unused.
     */
    protected function compile() {}

    /**
     * @return string
     */
    public function getHeadline()
    {
        return $this->headline;
    }

    /**
     * @param string $headline
     * @return C4GListModuleParent
     */
    public function setHeadline($headline)
    {
        $this->headline = $headline;
        return $this;
    }

    /**
     * @return null
     */
    public function getHeaderGallery()
    {
        return $this->headerGallery;
    }

    /**
     * @param null $headerGallery
     * @return C4GListModuleParent
     */
    public function setHeaderGallery($headerGallery)
    {
        $this->headerGallery = $headerGallery;
        return $this;
    }

    /**
     * @return string
     */
    public function getJavascript()
    {
        return $this->javascript;
    }

    /**
     * @param string $javascript
     * @return C4GListModuleParent
     */
    public function setJavascript($javascript)
    {
        $this->javascript = $javascript;
        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return C4GListModuleParent
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    

}
