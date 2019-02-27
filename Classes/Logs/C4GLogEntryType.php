<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    6
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Logs;

class C4GLogEntryType {
    const SAVE_DATASET      = 'save';
    const DELETE_DATASET    = 'delete';

    public static function getOptions()
    {
        $optionlist = array(
            array(
                'id'     => C4GLogEntryType::SAVE_DATASET,
                'name'   => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['LOG_ENTRY_TYPE']['save']
            ),

            array(
                'id'     => C4GLogEntryType::DELETE_DATASET,
                'name'   => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['LOG_ENTRY_TYPE']['delete']
            ),
        );

        return $optionlist;
    }
}
