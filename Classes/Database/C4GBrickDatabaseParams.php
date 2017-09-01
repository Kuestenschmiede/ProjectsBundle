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

namespace con4gis\ProjectsBundle\Classes\Database;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

/**
 * Class C4GBrickDatabaseParams
 * @package c4g\projects
 */
class C4GBrickDatabaseParams
{
    private $type = C4GBrickDatabaseType::DCA_MODEL;

    //params for all types
    private $pkField   = 'id';
    private $tableName = '';
    private $database  = null;

    //dca_model type
    private $modelClass = '';
    private $modelListFunction = '';

    //doctrine type
    private $entityClass = '';
    private $entityNamespace = array();
    private $deviceMode = true;
    private $config = null;
    private $entityManager = null;

    /**
     * C4GBrickDatabaseParams constructor.
     * @param $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getPkField()
    {
        return $this->pkField;
    }

    /**
     * @param string $pkField
     */
    public function setPkField($pkField)
    {
        $this->pkField = $pkField;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @return string
     */
    public function getModelClass()
    {
        return $this->modelClass;
    }

    /**
     * @param string $modelClass
     */
    public function setModelClass($modelClass)
    {
        $this->modelClass = $modelClass;
    }

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * @param string $entityClass
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
    }

    /**
     * @return array
     */
    public function getEntityNamespace()
    {
        return $this->entityNamespace;
    }

    /**
     * @param array $entityNamespace
     */
    public function setEntityNamespace($entityNamespace)
    {
        $this->entityNamespace = $entityNamespace;
    }

    /**
     * @return boolean
     */
    public function isDeviceMode()
    {
        return $this->deviceMode;
    }

    /**
     * @param boolean $deviceMode
     */
    public function setDeviceMode($deviceMode)
    {
        $this->deviceMode = $deviceMode;
    }

    /**
     * @return null
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param null $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return null
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param null $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return null
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param null $database
     */
    public function setDatabase($database)
    {
        $this->database = $database;
    }

    /**
     * @return string
     */
    public function getModelListFunction()
    {
        return $this->modelListFunction;
    }

    /**
     * @param string $modelListFunction
     */
    public function setModelListFunction($modelListFunction)
    {
        $this->modelListFunction = $modelListFunction;
    }

}