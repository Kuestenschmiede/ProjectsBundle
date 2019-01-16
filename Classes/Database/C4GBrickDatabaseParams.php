<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2019
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

    //database qualifying
    private $findBy = array();

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
     * @param $pkField
     * @return $this
     */
    public function setPkField($pkField)
    {
        $this->pkField = $pkField;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param $tableName
     * @return $this
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * @return string
     */
    public function getModelClass()
    {
        return $this->modelClass;
    }

    /**
     * @param $modelClass
     * @return $this
     */
    public function setModelClass($modelClass)
    {
        $this->modelClass = $modelClass;
        return $this;
    }

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * @param $entityClass
     * @return $this
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    /**
     * @return array
     */
    public function getEntityNamespace()
    {
        return $this->entityNamespace;
    }

    /**
     * @param $entityNamespace
     * @return $this
     */
    public function setEntityNamespace($entityNamespace)
    {
        $this->entityNamespace = $entityNamespace;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isDeviceMode()
    {
        return $this->deviceMode;
    }

    /**
     * @param bool $deviceMode
     * @return $this
     */
    public function setDeviceMode($deviceMode = true)
    {
        $this->deviceMode = $deviceMode;
        return $this;
    }

    /**
     * @return null
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param $config
     * @return $this
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return null
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param $entityManager
     * @return $this
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * @return null
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param $database
     * @return $this
     */
    public function setDatabase($database)
    {
        $this->database = $database;
        return $this;
    }

    /**
     * @return string
     */
    public function getModelListFunction()
    {
        return $this->modelListFunction;
    }

    /**
     * @param $modelListFunction
     * @return $this
     */
    public function setModelListFunction($modelListFunction)
    {
        $this->modelListFunction = $modelListFunction;
        return $this;
    }

    /**
     * @return string
     */
    public function getFindBy()
    {
        return $this->findBy;
    }

    /**
     * @param $findBy
     * @return $this
     */
    public function setFindBy($findBy)
    {
        $this->findBy = $findBy;
        return $this;
    }

}