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


/**
 * Table tl_module
 */

$GLOBALS['TL_DCA']['tl_c4g_projects'] = array
(
    //config
    'config' => array
    (
        'dataContainer'     => 'Table',
        'enableVersioning'  => true,
        'onsubmit_callback'           => array(
            array('\con4gis\CoreBundle\Resources\contao\classes\C4GAutomator', 'purgeApiCache')
        ),
        'databaseAssisted'  => true,
//        'sql'               => array
//        (
//            'keys' => array
//            (
//                'id' => 'primary',
//                'uuid' => 'unique',
//            )
//        )
    ),


    //List
    'list' => array
    (
        'sorting' => array
        (
            'mode'              => 2,
            'fields'            => array('group_Id', 'caption ASC'),
            'panelLayout'       => 'filter;sort,search,limit',
            #'headerFields'      => array('group_Id', 'number', 'caption'),
        ),

        'label' => array
        (
            'fields'            => array('caption'),
            'format'            => '<span style="color:#023770">%s</span>',
        ),

        'global_operations' => array
        (
            'all' => array
            (
                'label'         => $GLOBALS['TL_LANG']['MSC']['all'],
                'href'          => 'act=select',
                'class'         => 'header_edit_all',
                'attributes'    => 'onclick="Backend.getScrollOffSet()" accesskey="e"'
            )
        ),

        'operations' => array
        (
            'edit' => array
            (
                'label'         => $GLOBALS['TL_LANG']['tl_c4g_projects']['edit'],
                'href'          => 'act=edit',
                'icon'          => 'edit.gif',
            ),
            'copy' => array
            (
                'label'         => $GLOBALS['TL_LANG']['tl_c4g_projects']['copy'],
                'href'          => 'act=copy',
                'icon'          => 'copy.gif',
            ),
            'delete' => array
            (
                'label'         => $GLOBALS['TL_LANG']['tl_c4g_projects']['delete'],
                'href'          => 'act=delete',
                'icon'          => 'delete.gif',
                'attributes'    => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false;Backend.getScrollOffset()"',
            ),
            'show' => array
            (
                'label'         => $GLOBALS['TL_LANG']['tl_c4g_projects']['show'],
                'href'          => 'act=show',
                'icon'          => 'show.gif',
            ),
        )
    ),

    //Palettes
    'palettes' => array
    (
        'default'   =>  '{custom_legend},caption,description;'.
                        '{group_legend},group_id;'
    ),

    //Fields
    'fields' => array
    (
        'id' => array
        (
//            'sql'               => "int(10) unsigned NOT NULL auto_increment"
        ),

        'tstamp' => array
        (
//            'sql'               => "int(10) unsigned NOT NULL default '0'"
        ),

        'uuid' => array
        (
            'save_callback'     => array(array('tl_c4g_projects','generateUuid')),
//            'sql'               => "varchar(128) NOT NULL default ''"
        ),

        'brick_key' => array
        (
            'label'             => $GLOBALS['TL_LANG']['tl_c4g_projects']['brick_key'],
            'sorting'           => true,
            'flag'              => 1,
            'search'            => true,
            'inputType'         => 'text',
            'eval'              => array('mandatory' => true, 'tl_class' => 'w50', 'maxlength' => 255),
//            'sql'               => "varchar(255) NOT NULL"
        ),

        'group_id' => array
            (
            'label'             => $GLOBALS['TL_LANG']['tl_c4g_projects']['group_id'],
            'exclude'           => true,
            'sorting'           => true,
            'search'            => true,
            'flag'              => 1,
            'inputType'         => 'select',
            'foreignKey'        => 'tl_member_group.name',
            'eval'              => array('mandatory' => true),
//            'sql'               => "int(10) unsigned NOT NULL",
            'relation'          => array('type' => 'hasOne', 'load' => 'lazy')
        ),

        'caption' => array
        (
            'label'             => $GLOBALS['TL_LANG']['tl_c4g_projects']['caption'],
            'sorting'           => true,
            'flag'              => 1,
            'search'            => true,
            'inputType'         => 'text',
            'eval'              => array('mandatory' => true, 'maxlength' => 255),
//            'sql'               => "varchar(255) NOT NULL"
        ),

        'description' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_projects']['description'],
            //'exclude'                 => true,
            'inputType'               => 'textarea',
            'search'                  => true,
            'eval'                    => array('tl_class'=>'long','style'=>'height:60px', 'decodeEntities'=>true),
//            'sql'                     => "text NULL"
        ),

        'last_member_id' => array
        (
            'label'             => $GLOBALS['TL_LANG']['tl_c4g_projects']['last_member_id'],
            'flag'              => 1,
            'sorting'           => true,
            'search'            => true,
            'inputType'         => 'select',
            'eval'              => array('mandatory' => true, 'tl_class' => 'w50'),
            'exclude'           => true,
            'foreignKey'        => 'tl_member.firstname',
            'relation'          => array('type' => 'hasOne', 'load' => 'lazy'),
//            'sql'               => "int(10) unsigned NOT NULL default '0'"
        ),

        'is_frozen' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_projects']['is_frozen'],
            //'sorting'                 => true,
            //'flag'                    => 1,
            //'search'                  => true,
            'inputType'               => 'checkbox',
            'eval'                    => array('tl_class'=>'w50'),
//            'sql'                     => "char(1) NOT NULL default '0'"
        ),
)

);


/* Class tl_c4g_projects
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @package   con4gis_rescuemap
 * @author    Matthias Eilers
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
*/
class tl_c4g_projects extends Backend
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

    public function generateUuid($varValue, DataContainer $dc)
    {
        if ($varValue == '') {
            return \con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon::getGUID();
        }
        else {
            return $varValue;
        }
    }
}
