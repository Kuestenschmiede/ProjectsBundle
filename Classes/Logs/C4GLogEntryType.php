<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */
namespace con4gis\ProjectBundle\Classes\Logs;

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
