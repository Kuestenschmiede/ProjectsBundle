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

namespace con4gis\ProjectsBundle\Classes\Framework;

use Contao\Database;
use Contao\User;

/**
 * Used for maintenance tasks like clearing the session on login.
 * Class C4GMaintenance
 * @package con4gis\ProjectsBundle\Classes\Framework
 */
class C4GMaintenance
{
    /**
     * Clears all brick variables from the session.
     * @param User $user
     */
    public function onLogoutClearSessions(User $user)
    {
        $session = \Session::getInstance();
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