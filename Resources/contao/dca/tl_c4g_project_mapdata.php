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

$GLOBALS['TL_DCA']['tl_c4g_project_mapdata'] = array
(
    //config
    'config' => array
    (
        'dataContainer'     => 'Table',
        'ptable'            => 'tl_c4g_projects',
        'enableVersioning'  => true,
        'databaseAssisted'  => true,
        'onsubmit_callback'           => array(
            array('\con4gis\CoreBundle\Resources\contao\classes\C4GAutomator', 'purgeApiCache')
        ),
        'sql'               => array
        (
            'keys' => array
            (
                'id' => 'primary',
                'pid' => 'index',
            )
        )
    ),


    //List
/*    'list' => array
    (
        'sorting' => array
        (
            'mode'              => 2,
            'fields'            => array('pid'),
            'panelLayout'       => 'filter;sort,search,limit',
            #'headerFields'      => array('group_Id', 'number', 'caption'),
        ),

        'label' => array
        (
            'fields'            => array('pid'),
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
                'label'         => $GLOBALS['TL_LANG']['tl_c4g_project_mapdata']['edit'],
                'href'          => 'act=edit',
                'icon'          => 'edit.gif',
            ),
            'copy' => array
            (
                'label'         => $GLOBALS['TL_LANG']['tl_c4g_project_mapdata']['copy'],
                'href'          => 'act=copy',
                'icon'          => 'copy.gif',
            ),
            'delete' => array
            (
                'label'         => $GLOBALS['TL_LANG']['tl_c4g_project_mapdata']['delete'],
                'href'          => 'act=delete',
                'icon'          => 'delete.gif',
                'attributes'    => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false;Backend.getScrollOffset()"',
            ),
            'show' => array
            (
                'label'         => $GLOBALS['TL_LANG']['tl_c4g_project_mapdata']['show'],
                'href'          => 'act=show',
                'icon'          => 'show.gif',
            ),
        )
    ),

    //Palettes
    'palettes' => array
    (
        'default'   =>  '{custom_legend},pid;'
    ),*/

    //Fields
    'fields' => array
    (
        'id' => array
        (
            'sql'               => "int(10) unsigned NOT NULL auto_increment"
        ),

        'tstamp' => array
        (
            'sql'               => "int(10) unsigned NOT NULL default '0'"
        ),

        'uuid' => array
        (
            'label'                   => array('uuid','uuid'),
            'exclude'                 => true,
            'inputType'               => 'text',
            'search'                  => true,
            'eval'                    => array('rgxp'=>'alias', 'doNotCopy'=>true, 'maxlength'=>128),
            'save_callback'           => array(array('tl_c4g_project_mapdata','generateUuid')),
            'sql' => "varchar(128) COLLATE utf8_bin NOT NULL default ''"
        ),

        'pid' => array
        (
            //'label'             => $GLOBALS['TL_LANG']['tl_mapcil_components']['pid'],
            'exclude'           => true,
            'sorting'           => true,
            'search'            => true,
            'flag'              => 1,
            'inputType'         => 'select',
            'eval'              => array('mandatory' => true),
            'foreignKey'        => 'tl_c4g_projects.caption',
            'sql'               => "int(10) unsigned NOT NULL default '0'",
            'relation'          => array('type'=>'belongsTo', 'load'=>'lazy')
        ),

        'geojson' => array
        (
            'sql'                => "blob NULL"
        ),

        'permalink' => array
        (
            'sql'                => "blob NULL"
        ),

        'last_member_id' => array
        (
            //'label'             => $GLOBALS['TL_LANG']['tl_c4g_projects']['last_member_id'],
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


/* Class tl_c4g_projects
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @package   con4gis_rescuemap
 * @author    Matthias Eilers
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
*/
class tl_c4g_project_mapdata extends Backend
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
