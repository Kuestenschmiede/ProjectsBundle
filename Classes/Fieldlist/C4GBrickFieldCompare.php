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
namespace con4gis\ProjectsBundle\Classes\Fieldlist;


class C4GBrickFieldCompare
{
    private $field;
    private $dbValue;
    private $dlgValue;

    /**
     * @param $field
     * @param $dbValue
     * @param $dlgValue
     */
    public function __construct($field, $dbValue, $dlgValue)
    {
        $this->field = $field;

        \System::loadLanguageFile('fe_c4g_dialog');

        if ($dbValue == ''/* && $dlgValue != null && $dlgValue != '' && $dlgValue != '-1'*/) {
            $dbValue = $GLOBALS['TL_LANG']['FE_C4G_DIALOG_COMPARE']['newEntry'];
        }
        $this->dbValue = $dbValue;

        if ($dlgValue == ''/* && $dbValue != ''*/) {
            $dlgValue = $GLOBALS['TL_LANG']['FE_C4G_DIALOG_COMPARE']['deletedEntry'];
        }
        $this->dlgValue = $dlgValue;
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param $field
     * @return $this
     */
    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDbValue()
    {
        return $this->dbValue;
    }

    /**
     * @param $dbValue
     * @return $this
     */
    public function setDbValue($dbValue)
    {
        $this->dbValue = $dbValue;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDlgValue()
    {
        return $this->dlgValue;
    }

    /**
     * @param $dlgValue
     * @return $this
     */
    public function setDlgValue($dlgValue)
    {
        $this->dlgValue = $dlgValue;
        return $this;
    }


}