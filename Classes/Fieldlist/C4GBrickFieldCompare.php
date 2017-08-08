<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\ProjectBundle\Classes\Fieldlist;


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
     * @param mixed $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }

    /**
     * @return mixed
     */
    public function getDbValue()
    {
        return $this->dbValue;
    }

    /**
     * @param mixed $dbValue
     */
    public function setDbValue($dbValue)
    {
        $this->dbValue = $dbValue;
    }

    /**
     * @return mixed
     */
    public function getDlgValue()
    {
        return $this->dlgValue;
    }

    /**
     * @param mixed $dlgValue
     */
    public function setDlgValue($dlgValue)
    {
        $this->dlgValue = $dlgValue;
    }


}