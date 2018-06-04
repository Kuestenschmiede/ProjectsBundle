<?php
/**
 * Created by PhpStorm.
 * User: rro
 * Date: 30.05.18
 * Time: 16:58
 */

namespace con4gis\ProjectsBundle\Classes\ListItem;


class C4GListItem
{
    private $fields = array();

    public function createHTML($href, $caption)
    {
        $return = "<li class='c4g_list_item'><a href='$this->href'></a>";

    }

    /**
     * @param $fieldName
     * @return $this
     */
    public function addField($fieldName) {
        $this->fields[] = $fieldName;
        return $this;
    }

    /**
     * @return array
     */
    public function getFields() {
        return $this->fields;
    }

    /**
     * @return string
     */
    public function getLinkTo()
    {
        return $this->linkTo;
    }

    /**
     * @param string $linkTo
     */
    public function setLinkTo($linkTo)
    {
        $this->linkTo = $linkTo;
    }


}