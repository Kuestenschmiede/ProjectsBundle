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


use con4gis\CoreBundle\Resources\contao\classes\C4GUtils;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewParams;

/**
 * DialogData extension for standard modules.
 */
final class C4GStandardDialogDataContao extends C4GDialogData
{
    protected $db;
    protected $indexes;
    protected $table;
    protected $authenticateBy;

    const AUTHENTICATE_SKIP = 0;
    const AUTHENTICATE_BY_MEMBER_ID = 10;
    const AUTHENTICATE_BY_GROUP_ID = 20;
    const AUTHENTICATE_BY_PROJECT_ID = 30;

    public function __construct(C4GBrickDialogParams $dialogParams,
                                C4GBrickViewParams $viewParams,
                                int $id,
                                \Contao\Database $db,
                                array $indexes,
                                string $table,
                                int $authenticateBy)
    {
        parent::__construct($dialogParams, $viewParams, $id);
        $this->db = $db;
        $this->indexes = $indexes;
        $this->table = $table;
        $this->authenticateBy = $authenticateBy;
    }

    /**
     * Load the values from the database into the object's dbValues property.
     */
    protected function loadValues()
    {
        if (!$this->dbValues || $this->id > 0) {
            $indexes = implode(',', $this->indexes);
            $table = $this->table;
            $stmt = $this->db->prepare("SELECT $indexes FROM $table WHERE id = ?");
            $result = $stmt->execute($this->id);
            $result = $result->fetchAssoc();
            $dbValues = array();
            foreach ($result as $key => $value) {
                $dbValues[$key] = $value;
            }
            $this->dbValues = $dbValues;
        }
    }

    /**
     * Save the values from the object's dlgValues property to the database.
     * You might want to get the changes first and only save those.
     * Return whether the operation was successful or not.
     * @return bool
     */
    protected function saveValues()
    {
        if ($this->dialogValues) {
            $dialogValues = $this->dialogValues;
            if ($this->id > 0) {
                $setString = '';
                foreach ($this->indexes as $index) {
                    if ($dialogValues[$index] !== '') {
                        if (strlen($setString) > 0) {
                            $setString .= ',';
                        }
                        $setString .= $index . '=' . $dialogValues[$index];
                    }
                }
                $table = $this->table;
                $stmt = $this->db->prepare("UPDATE $table SET $setString WHERE id = ?");
                $result = $stmt->execute($this->id);
                return $result->affectedRows ? true : false;
            } else {
                $indexes = implode(',', $this->indexes);
                $table = $this->table;
                $columnsString = '';
                $valuesString = '';
                foreach ($this->indexes as $index) {
                    if ($dialogValues[$index] !== '') {
                        if (strlen($valuesString) > 0) {
                            $valuesString .= ',';
                        }
                        $valuesString .= $dialogValues[$index];
                        if (strlen($columnsString) > 0) {
                            $columnsString .= ',';
                        }
                        $columnsString .= $index;
                    }
                }
                if ($columnsString !== '' && $valuesString !== '') {
                    $stmt = $this->db->prepare("INSERT INTO $table ($columnsString) VALUES ($valuesString)");
                    $result = $stmt->execute();
                    return $result->affectedRows ? true : false;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * Load the values from the given dialogValues into the object's dialogValues property.
     * @param $dialogValues
     * @return C4GDialogData
     */
    public function setDialogValues($dialogValues)
    {
        $this->dialogValues = array();
        if (is_array($dialogValues)) {
            foreach ($this->indexes as $index) {
                if (isset($dialogValues[$index])) {
                    $this->dialogValues[$index] = $dialogValues[$index] ? $dialogValues[$index] : '';
                }
            }
        }
        return $this;
    }

    /**
     * Compare the dbValues and dlgValues properties and place the differences in the differences property.
     */
    protected function generateDifferences()
    {
        if ($this->dbValues && $this->dialogValues) {
            $dbValues = $this->dbValues;
            $dialogValues = $this->dialogValues;
            $differences = array();
            foreach ($this->indexes as $index) {
                if ($dbValues !== $dialogValues) {
                    $differences[$index] = array($dbValues[$index], $dialogValues[$index]);
                }
            }
            $this->differences = $differences;
        }
        $this->differences = array();
    }

    /**
     * Determine if the current user should have access to the data associated with this object based on
     * memberId, groupId, etc.
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
    public function authenticate()
    {
        $dialogParams = $this->dialogParams;
        $viewParams = $this->viewParams;
        if ($this->id > 0) {
            $dbValues = $this->dbValues;
            switch ($this->authenticateBy) {
                case self::AUTHENTICATE_SKIP;
                    return true;
                    break;
                case self::AUTHENTICATE_BY_MEMBER_ID;
                    if ($dialogParams->getMemberId() === $dbValues[$viewParams->getMemberKeyField()]) {
                        return true;
                    } else {
                        return false;
                    }
                    break;
                case self::AUTHENTICATE_BY_GROUP_ID;
                    if ($dialogParams->getGroupId() === $dbValues[$viewParams->getGroupKeyField()]) {
                        return true;
                    } else {
                        return false;
                    }
                    break;
                case self::AUTHENTICATE_BY_PROJECT_ID;
                    if ($dialogParams->getProjectId() === $dbValues[$viewParams->getProjectKeyField()]) {
                        return true;
                    } else {
                        return false;
                    }
                    break;
                default:
                    return false;
                    break;
            }
        } else {
            switch ($this->authenticateBy) {
                case self::AUTHENTICATE_SKIP;
                    return true;
                    break;
                default:
                    return C4GUtils::checkFrontendUserLogin();
                    break;
            }
        }
    }

}