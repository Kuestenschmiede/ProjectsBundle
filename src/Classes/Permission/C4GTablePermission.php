<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Permission;

use con4gis\CoreBundle\Classes\C4GUtils;
use con4gis\ProjectsBundle\Classes\Session\C4gBrickSession;
use Symfony\Component\HttpFoundation\Session\Session;

class C4GTablePermission
{
    private $table;
    private $ids;
    private $level = 1;
    private $action = '';
    private $session = null;

    /**
     * C4GTablePermission constructor.
     * @param $table
     * @param $ids array/int
     */
    public function __construct($table, $ids, C4gBrickSession &$session)
    {
        $this->table = $table;
        $this->ids = $ids;
        $this->session = $session;
    }

    /**
     * Write this permission to the session.
     */
    public function set()
    {
        $table = $this->table;
        $ids = $this->ids;
        if (is_array($ids)) {
            foreach ($ids as $id) {
                $permission = 'C4GTablePermission:' . $table . ':' . $id;
                $this->session->setSessionValue($permission, $this->level);
            }
        } else {
            $permission = 'C4GTablePermission:' . $table . ':' . $ids;
            $this->session->setSessionValue($permission, $this->level);
        }
    }

    /**
     * Check if this permission is written to the session
     * @throws \Exception Will be thrown if the permission does not exist in the session.
     */
    public function check()
    {
        $table = $this->table;
        $ids = $this->ids;
        if (is_array($ids)) {
            foreach ($ids as $id) {
                if ($id == -1) {
                    continue;
                }
                $permission = 'C4GTablePermission:' . $table . ':' . $id;
                $value = $this->session->getSessionValue($permission);
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
            $value = $this->session->getSessionValue($permission);
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
     */
    public function clear()
    {
        $table = $this->table;
        $ids = $this->ids;
        if (is_array($ids)) {
            foreach ($ids as $id) {
                $permission = 'C4GTablePermission:' . $table . ':' . $id;
                $this->session->remove($permission);
            }
        } else {
            $permission = 'C4GTablePermission:' . $table . ':' . $ids;
            $this->session->remove($permission);
        }
    }

    /**
     * Clears all permissions from the session.
     * @throws \Exception
     */
//ToDo Wird diese Funktion überhaupt genutzt?
//    public static function clearAll()
//    {
//        //ToDo with symfony session (C4gBrickSession)
//
//        $sessionData = \Session::getInstance()->getData();
//
//        foreach ($sessionData as $key => $value) {
//            if (C4GUtils::startswith($key, 'C4GTablePermission')) {
//                unset($sessionData[$key]);
//            }
//        }
//
//        \Session::getInstance()->setData($sessionData);
//    }

    /**
     * @param int $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }
}
