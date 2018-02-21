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

namespace con4gis\ProjectsBundle\Classes\Database;

use \Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\GenerateValue;

/**
 * Class C4GBrickEntity
 * @package c4g\projects
 */
abstract class C4GBrickEntity
{
    /**
     * BaseEntiy constructor.
     * @param array $data
     */
    public function __construct($data = array())
    {
        $this->setData($data);
    }


    /**
     * Setzt die Daten eines Arrays als Eigenschaften der Klasse.
     * @param $data
     */
    public function setData($data)
    {
        if (is_array($data) && count($data)) {
            foreach ($data as $column => $value) {
                if (property_exists($this, $column)) {
                    $column = 'set' . ucfirst($column);
                    $this->$column($value);
                }
            }
        }
    }
}