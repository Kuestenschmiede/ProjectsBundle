<?php
/**
 * Created by PhpStorm.
 * User: rro
 * Date: 08.10.18
 * Time: 15:47
 */

namespace con4gis\ProjectsBundle\Classes\DialogData;


abstract class DialogData
{
    protected $dbValues = array();
    protected $dlgValues = array();
    protected $changes = array();

    public final function authenticateAndLoadVaues() {
        if ($this->authenticate()) {
            $this->loadValues();
        }
    }

    public final function authenticateAndSaveValues() {
        if ($this->authenticate()) {
            $this->saveValues();
        }
    }

    protected abstract function loadValues();

    protected abstract function saveValues();

    public function getDbValues() {
        return $this->dbValues;
    }

    public abstract function setDlgValues($dlgValues);

    public function getDlgValues() {

    }

    public abstract function getChanges();

    public abstract function authenticate();

}