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

namespace con4gis\ProjectsBundle\Classes\Module;


interface C4GBrickModuleSaveNotificationsInterface
{
    /**
     * Create and send the notification(s).
     * @param $diff array The differences between database and dialog values (might be an empty array).
     * @return bool
     */
    public function sendSaveNotifications($diff);
}