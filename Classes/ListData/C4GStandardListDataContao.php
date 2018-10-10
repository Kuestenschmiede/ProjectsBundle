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

namespace con4gis\ProjectsBundle\Classes\ListData;


use con4gis\ProjectsBundle\Classes\Lists\C4GBrickListParams;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewParams;

final class C4GStandardListDataContao extends C4GListData
{
    protected $db;
    protected $columns;
    protected $table;
    protected $where = '';
    protected $parameters = array();

    public function __construct(C4GBrickListParams $listParams,
                                C4GBrickViewParams $viewParams,
                                \Contao\Database $db,
                                array $columns,
                                string $table)
    {
        parent::__construct($listParams, $viewParams);
        $this->db = $db;
        $this->columns = $columns;
        $this->table = $table;
    }

    /**
     * Load the values from the database into the object's $listElements property.
     */
    public function loadListElements()
    {
        $columns = implode(',', $this->columns);
        $table = $this->table;
        $where = $this->where;
        $queryString = "SELECT $columns FROM $table";
        if ($where !== '') {
            $queryString .= ' WHERE '.$where;
        }
        $stmt = $this->db->prepare($queryString);
        $result = call_user_func_array(array($stmt, 'execute'),$this->parameters);
        $this->listElements = $result->fetchAllAssoc();
    }

    /**
     * Specify a where clause for your query. You can use any standard mySQL clause, including the ? placeholder
     *  for prepared statements.
     * @param string $clause
     */
    public function where(string $clause) {
        $this->where = ' '.$clause;
    }

    /**
     * Specify the parameters for the execute() method called on the statement object.
     * @param array $parameters
     */
    public function setParameters(array $parameters) {
        $this->parameters = $parameters;
    }

    /**
     * Alternative to setParameters. Can be used to set parameters one by one.
     * @param string $parameter
     */
    public function addParameter(string $parameter) {
        $this->parameters[] = $parameter;
    }
}