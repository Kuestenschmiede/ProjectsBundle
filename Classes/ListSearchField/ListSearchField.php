<?php
/**
 * Created by PhpStorm.
 * User: rro
 * Date: 31.05.18
 * Time: 09:44
 */

namespace con4gis\ProjectsBundle\Classes\ListSearchField;


class ListSearchField
{
    /** Not a BrickField! */

    private $pattern = '';      //As soon as the field input matches this RegEx, the Ajax Request will be sent
    private $searchField = '';  //The database field to try and match the field input against

    public function getHTML()
    {

    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param $pattern
     * @return $this
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * @return string
     */
    public function getSearchField()
    {
        return $this->searchField;
    }

    /**
     * @param $searchField
     * @return $this
     */
    public function setSearchField($searchField)
    {
        $this->searchField = $searchField;
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
     */
    public function setId($id)
    {
        $this->id = $id;
    }




}