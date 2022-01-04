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
/**
 * Table tl_module
 */

$GLOBALS['TL_DCA']['tl_c4g_projects_logbook'] = array
(
    //config
    'config' => array
    (
        'closed'            => true, //lässt den Hinzufügen-Button verschwinden.
        'dataContainer'     => 'Table',
        'enableVersioning'  => true,
        'sql'               => array
        (
            'keys' => array
            (
                'id' => 'primary'
            )
        )
    ),

    //List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 2,
            'fields'                  => array('tstamp ASC'),
            'panelLayout'             => 'filter;sort,search,limit',
            'headerFields'            => array('tstamp'),
             'disableGrouping'        => false,
            'flag'                    => 7,
        ),
        'label' => array
        (
            'fields'            => array('tstamp','brick_key','entry_text','entry_type','entry_id','view_type','group_id','member_id'),
            //'format'            => '<b>%s [%s]: %s (%s) </b> id: %s, Ansicht: %s, Gruppe: %s, Mitglied: %s',
            'showColumns'       => true,
            //'label_callback'    => array('tl_c4g_projects_logbook', 'getDate')
        ),
/*
        'global_operations' => array
        (
            'all' => array
            (
                'label'         => $GLOBALS['TL_LANG']['MSC']['all'],
                'href'          => 'act=select',
                'class'         => 'header_edit_all',
                'attributes'    => 'onclick="Backend.getScrollOffSet()" accesskey="e"'
            )
        ),*/

        'operations' => array
        (
            'show' => array
            (
                'label'         => $GLOBALS['TL_LANG']['tl_c4g_projects_logbook']['show'],
                'href'          => 'act=show',
                'icon'          => 'show.svg',
            ),
        )
    ),

    //Palettes
    'palettes' => array
    (
        'default'   =>  '{custom_legend}, entry_id, entry_type, entry_text;'.
                        '{source_legend}, brick_key, view_type, group_id, member_id;'
    ),

    //Fields
    'fields' => array
    (
        'id' => array
        (
            'sql'               => "int(10) unsigned NOT NULL auto_increment"
        ),

        'tstamp' => array
        (
            'label'             => &$GLOBALS['TL_LANG']['tl_c4g_projects_logbook']['tstamp'],
            'sorting'           => true,
            'flag'              => 6,
            'eval'              => array('rgxp'=>'datim'),
            'sql'               => "int(10) unsigned NOT NULL default '0'",
        ),

        'importId' => array
        (
            'sql'               => "int(10) unsigned NOT NULL default '0'",
            'eval'              => array('doNotCopy' => true)
        ),

        'entry_id' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_projects_logbook']['entry_id'],
            'sorting'                 => true,
            'flag'                    => 1,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true, 'rgxp'=>'digit'),
            'sql'                     => "int(10) unsigned NOT NULL"
        ),

        'entry_type' => array
        (
            'label'             => $GLOBALS['TL_LANG']['tl_c4g_projects_logbook']['entry_type'],
            'sorting'           => true,
            'flag'              => 1,
            'search'            => true,
            'inputType'         => 'text',
            'eval'              => array('mandatory' => true, 'tl_class' => 'w50', 'maxlength' => 255),
            'sql'               => "varchar(255) NOT NULL"
        ),

        'entry_text' => array
        (
            'label'             => &$GLOBALS['TL_LANG']['tl_c4g_projects_logbook']['entry_text'],
            'sorting'           => true,
            'flag'              => 1,
            'search'            => true,
            'inputType'         => 'text',
            'eval'              => array('mandatory' => true, 'tl_class' => 'w50', 'maxlength' => 255),
            'sql'               => "varchar(255) NOT NULL"
        ),

        'brick_key' => array
        (
            'label'             => $GLOBALS['TL_LANG']['tl_c4g_projects_logbook']['brick_key'],
            'sorting'           => true,
            'flag'              => 1,
            'search'            => true,
            'inputType'         => 'text',
            'eval'              => array('mandatory' => true, 'tl_class' => 'w50', 'maxlength' => 255),
            'sql'               => "varchar(255) NOT NULL"
        ),

        'view_type' => array
        (
            'label'             => $GLOBALS['TL_LANG']['tl_c4g_projects_logbook']['view_type'],
            'sorting'           => true,
            'flag'              => 1,
            'search'            => true,
            'inputType'         => 'text',
            'eval'              => array('mandatory' => true, 'tl_class' => 'w50', 'maxlength' => 255),
            'sql'               => "varchar(255) NOT NULL"
        ),

        'view_type' => array
        (
            'label'             => $GLOBALS['TL_LANG']['tl_c4g_projects_logbook']['view_type'],
            'sorting'           => true,
            'flag'              => 1,
            'search'            => true,
            'inputType'         => 'text',
            'eval'              => array('mandatory' => true, 'tl_class' => 'w50', 'maxlength' => 255),
            'sql'               => "varchar(255) NOT NULL"
        ),

        'group_id' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_projects_logbook']['group_id'],
            'exclude'                 => true,
            'inputType'               => 'select',
            'foreignKey'              => 'tl_member_group.name',
            'eval'                    => array('mandatory'=>true, 'multiple'=>false),
            'sql'                     => "int(10) unsigned NOT NULL",
            'relation'                => array('type'=>'hasOne', 'load'=>'eager')
        ),

        'member_id' => array
        (
            'label'             => $GLOBALS['TL_LANG']['tl_c4g_projects_logbook']['member_id'],
            'flag'              => 1,
            'sorting'           => true,
            'search'            => true,
            'inputType'         => 'select',
            'eval'              => array('mandatory' => true, 'tl_class' => 'w50'),
            'exclude'           => true,
            'foreignKey'        => 'tl_member.firstname',
            'relation'          => array('type' => 'hasOne', 'load' => 'lazy'),
            'sql'               => "int(10) unsigned NOT NULL default '0'"
        ),

)

);


/* Class tl_c4g_projects_logbook
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @package   con4gis_projects
 * @author    Matthias Eilers
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
*/
class tl_c4g_projects_logbook extends Backend
{
    /**
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }


    public function updateDCA (DataContainer $dc)
    {

    }

    /**
     * @param array
     * @return string
     */
    public function getDate($arrRow)
    {
        $date = date($GLOBALS['TL_CONFIG']['dateFormat'],$arrRow['tstamp']);
        return $arrRow;
    }
}
