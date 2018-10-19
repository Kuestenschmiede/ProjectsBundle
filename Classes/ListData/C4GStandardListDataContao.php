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
    protected $as = array();

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
        $table = $this->table;
        if (empty($this->as)) {
            $columns = implode(',', $this->columns);
        } else {
            $columns = '';
            foreach($this->columns as $column) {
                if ($columns !== '') {
                    $columns .= ',';
                }
                if (isset($this->as[$column])) {
                    $columns .= "$column as ".$this->as[$column];
                } else {
                    $columns .= $column;
                }
            }
        }
        $columns .= ',id';
        $queryString = "SELECT $columns FROM $table";

        $where = $this->where;
        if ($where !== '') {
            $queryString .= ' WHERE '.$where;
        }
        $stmt = $this->db->prepare($queryString);
        $result = call_user_func_array(array($stmt, 'execute'),$this->parameters);
        $result = $result->fetchAllAssoc();
        $this->listElements->addContainersFromArray($result);
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

    /**
     * Helper function to add a where clause, including the parameter, corresponding to the currently logged in member.
     */
    public function whereMemberIsCurrentUser() {
        $this->where($this->viewParams->getMemberKeyField() . ' = ?');
        $this->setParameters(array(\Contao\FrontendUser::getInstance()->id));
    }

    /**
     * Define an alias under which the column data will be available.
     * @param string $column
     * @param string $as
     */
    public function as(string $column, string $as) {
        $this->as[$column] = $as;
    }
}