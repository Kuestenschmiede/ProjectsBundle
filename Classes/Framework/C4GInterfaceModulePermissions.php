<?php
/**
 * Created by PhpStorm.
 * User: rro
 * Date: 27.03.18
 * Time: 11:44
 */

namespace con4gis\ProjectsBundle\Classes\Framework;


use con4gis\ProjectsBundle\Classes\Permission\C4GTablePermission;

interface C4GInterfaceModulePermissions
{
    /**
     * Get the permissions given by this module. The return value must be an instance of C4GTablePermission or an array of instances of C4GTablePermission.
     * @return C4GTablePermission
     */
    public function getC4GTablePermission();

    /**
     * Get the table this module needs permission for. Most of the time, this should return $this->tableName.
     * If for whatever reason the module needs access to a different table, put it in here as a string.
     * @return string
     */
    public function getC4GTablePermissionTable();
}