<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2022, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Framework;

use Contao\Database;
use Contao\System;
use Contao\User;

/**
 * Used for maintenance tasks like clearing the session on login.
 * Class C4GMaintenance
 * @package con4gis\ProjectsBundle\Classes\Framework
 */
class C4GMaintenance
{
    //ToDo with C4gBrickSession (Symfony Session)

    /**
     * Clears all brick variables from the session.
     * @param User $user
     */
    public function onLogoutClearSessions(User $user)
    {
        //ToDo Test
        $session = System::getContainer()->get('session');
        $session->remove('c4g_brick_project_id');
        $session->remove('c4g_brick_group_id');
        $session->remove('c4g_brick_member_id');
        $session->remove('c4g_brick_parent_id');
        $session->remove('c4g_brick_project_uuid');
        $db = Database::getInstance();
        if ($user instanceof \FrontendUser) {
            $userId = $user->id;
            $db->execute("UPDATE tl_member SET session = DEFAULT WHERE id = $userId");
        }
    }

    /**
     * Clears all brick variables from the session.
     * @param User $user
     */
    public function onLoginClearSessions(User $user)
    {
        //ToDo Test
        $session = System::getContainer()->get('session');
        $session->remove('c4g_brick_project_id');
        $session->remove('c4g_brick_group_id');
        $session->remove('c4g_brick_member_id');
        $session->remove('c4g_brick_parent_id');
        $session->remove('c4g_brick_project_uuid');
        $db = Database::getInstance();
        if ($user instanceof \FrontendUser) {
            $userId = $user->id;
            $db->execute("UPDATE tl_member SET session = DEFAULT WHERE id = $userId");
        }
    }
}
