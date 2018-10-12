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

namespace con4gis\ProjectsBundle\Classes\DialogData;


use con4gis\CoreBundle\Resources\contao\classes\container\C4GContainer;
use con4gis\CoreBundle\Resources\contao\classes\container\C4GContainerContainer;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewParams;

abstract class C4GDialogData
{
    /**
     * A C4GContainer object. The keys correspond to the database columns.
     * The values represent the respective database values.
     * @var C4GContainer
     */
    protected $dbValues;
    /**
     * A C4GContainer object. The keys correspond to the dialog field names and match the keys in the
     *  $dbValues property.
     * The values represent the input. The type must match that of the corresponding
     *  values in the $dbValues property.
     * @var C4GContainer
     */
    protected $dialogValues;
    /**
     * A C4GContainer object containing the differences between the $dbValues and $dialogValues.
     * @var C4GContainer
     */
    protected $differences;
    protected $dialogParams;
    protected $viewParams;
    protected $id;
    protected $authenticated = false;

    /**
     * DialogData constructor.
     * Do NOT change the method signature!
     * @param C4GBrickDialogParams $dialogParams
     * @param C4GBrickViewParams $viewParams
     * @param Int $id
     */
    public function __construct(C4GBrickDialogParams $dialogParams, C4GBrickViewParams $viewParams, int $id) {
        $this->dialogParams = $dialogParams;
        $this->viewParams = $viewParams;
        $this->id = $id;
        $this->dbValues = new C4GContainer();
        $this->dialogValues = new C4GContainer();
        $this->differences = new C4GContainerContainer();
    }

    public final function loadValuesAndAuthenticate() {
        $this->loadValues();
        if ($this->authenticated !== true && $this->authenticate() !== true) {
            $this->dbValues = array();
            $this->authenticationFailed('load');
        }
    }

    public final function authenticateAndSaveValues() {
        if ($this->authenticated !== true && $this->authenticate() !== true) {
            $this->authenticationFailed('save');
        } else {
            $affectedRow = $this->saveValues();
            if ($affectedRow > 0) {
                $this->dbValues = $this->dialogValues;
                $this->dbValues->addElement($affectedRow, 'id');
                $this->id = $affectedRow;
            }
        }
    }

    private function authenticationFailed($action) {
        throw new \Exception(
            "Failed to authenticate when attempting to $action the data associated with instance of class "
            .static::class);
    }

    /**
     * Load the values from the database into the object's dbValues property.
     */
    protected abstract function loadValues();

    /**
     * Save the values from the object's dialogValues property to the database.
     * You might want to get the changes first and only save those.
     * Return the id of the updated or inserted row, -1 in case of an error.
     * @return int
     */
    protected abstract function saveValues();

    public function getDbValues() {
        return $this->dbValues;
    }

    public function getDbValueByIndex(string $index) {
        return $this->dbValues->getByKey($index);
    }

    /**
     * Load the values from the given dialogValues into the object's dialogValues property.
     * @param $dialogValues
     * @return C4GDialogData
     */
    public abstract function setDialogValues($dialogValues);

    public function getDialogValues() {
        return $this->dialogValues;
    }

    public function getDialogValueByIndex(string $index) {
        return $this->dialogValues->getByKey($index);
    }

    /**
     * Compare the dbValues and dialogValues properties and place the differences in the differences property.
     */
    protected abstract function generateDifferences();

    public function getDifferences() {
        $this->generateDifferences();
        return $this->differences;
    }

    /**
     * Determine if the current user should have access to the data associated with this object based on
     * memberId, groupId, etc. Set the $authenticated property to true or false and also return the result.
     * For load operations and save operations with existing data sets (updates),
     *  $this->loadValues has already been executed and $this->dbValues is set unless
     *  the requested data does not exist or an error occurred.
     *  This means the data can be used to authenticate the user.
     * For save operations with new data sets (inserts),
     *  no data can be loaded and the user needs to be authenticated differently,
     *  e.g. by login status.
     * You can differentiate between the two cases simply by checking if $this->id is greater than 0.
     * @return bool
     */
    public abstract function authenticate();

}