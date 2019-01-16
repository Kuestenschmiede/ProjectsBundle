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

/**
 * Class C4GBrickDatabaseType
 * @package c4g\projects
 */
class C4GBrickDatabaseType {
    const NO_DB          = 'no_db';
    const DCA_MODEL      = 'dca_model'; //dieser Typ geht von einer Standard Contao DCA und der zugehörigen Model-Klasse aus.
    const DOCTRINE       = 'doctrine'; //dieser Typ erwartet eine doctrine entity Klasse
}
