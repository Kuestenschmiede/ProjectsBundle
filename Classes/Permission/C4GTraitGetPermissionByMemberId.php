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

namespace con4gis\ProjectsBundle\Classes\Permission;

/**
 * Trait C4GTraitGetPermissionByMemberId
 * @package con4gis\ProjectsBundle\Classes\Permission
 * Standard case to get permissions for member based modules. Simply use this trait in the module class.
 */

trait C4GTraitGetPermissionByMemberId
{
    public function getC4GTablePermission()
    {
        $elements = $this->brickDatabase->findBy('member_id', $this->getDialogParams()->getMemberId());
        if ($elements == null)
        {
            return null;
        }
        $array = array();
        foreach ($elements as $element) {
            $e = $element->row();
            $array[] = $e['id'];
        }
        if (sizeof($array) > 0) {
            $result = new C4GTablePermission($this->tableName, $array);
            return $result;
        } else {
            return null;
        }
    }
}