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



use con4gis\CoreBundle\Resources\contao\classes\C4GUtils;

/**
 * Class C4GTablePermission
 * @package con4gis\ProjectsBundle\Classes\Permission
 * @deprecated
 */
class C4GTablePermission
{

    private $table;
    private $ids;
    private $level = 1;
    private $action = '';

    /**
     * C4GTablePermission constructor.
     * @param $table
     * @param $ids array/int
     * @deprecated
     */

    public function __construct($table, $ids)
    {
        $this->table = $table;
        $this->ids = $ids;
    }

    /**
     * Write this permission to the session.
     * @deprecated
     */

    public function set()
    {
        $table = $this->table;
        $ids = $this->ids;
        if (is_array($ids)) {
            $session = \Session::getInstance();
            foreach ($ids as $id) {
                $permission = 'C4GTablePermission:' . $table . ':' . $id;
                $session->set($permission, $this->level);
            }
        } else {
            $permission = 'C4GTablePermission:' . $table . ':' . $ids;
            \Session::getInstance()->set($permission, $this->level);
        }

    }

    /**
     * Check if this permission is written to the session
     * @throws \Exception Will be thrown if the permission does not exist in the session.
     * @deprecated
     */

    public function check()
    {
        $table = $this->table;
        $ids = $this->ids;
        if (is_array($ids)) {
            $session =  \Session::getInstance();
            foreach ($ids as $id) {
                if ($id == -1) {
                    continue;
                }
                $permission = 'C4GTablePermission:' . $table . ':' . $id;
                $value = $session->get($permission);
                if (!($value >= $this->level)) {
                    if ($this->level == 1) {
                        $access = 'read from';
                    } else {
                        $access = 'write to';
                    }
                    if ($this->action != '') {
                        $this->action = " Action $this->action was attempted.";
                    }
                    throw new \Exception("C4GTablePermission denied - User does not have permission to $access data set with ID $id in table $table.$this->action Did the session expire?");
                }
            }

        } else {
            if ($ids == -1) {
                return;
            }
            $permission = 'C4GTablePermission:' . $table . ':' . $ids;
            $value = \Session::getInstance()->get($permission);
            if (!($value >= $this->level)) {
                if ($this->level == 1) {
                    $access = 'read from';
                } else {
                    $access = 'write to';
                }
                if ($this->action != '') {
                    $this->action = " Action $this->action was attempted.";
                }
                throw new \Exception("C4GTablePermission denied - User does not have permission to $access data set with ID $ids in table $table.$this->action Did the session expire?");
            }
        }
    }

    /**
     * Clear this permission from the session.
     * @deprecated
     */

    public function clear()
    {
        $table = $this->table;
        $ids = $this->ids;
        if (is_array($ids)) {
            $session = \Session::getInstance();
            foreach ($ids as $id) {
                $permission = 'C4GTablePermission:' . $table . ':' . $id;
                $session->remove($permission);
            }
        } else {
            $permission = 'C4GTablePermission:' . $table . ':' . $ids;
            \Session::getInstance()->remove($permission);
        }
    }

    /**
     * Clears all permissions from the session.
     * @throws \Exception
     * @deprecated
     */

    public static function clearAll()
    {
        $sessionData = \Session::getInstance()->getData();

        foreach ($sessionData as $key => $value)
        {
            if (C4GUtils::startswith($key, 'C4GTablePermission')) {
                unset($sessionData[$key]);
            }
        }

        \Session::getInstance()->setData($sessionData);
    }

    /**
     * @param int $level
     * @deprecated
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @param string $action
     * @deprecated
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

}