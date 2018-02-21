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
namespace con4gis\ProjectsBundle\Classes\Conditions;

class C4GBrickConditionType {
    const BOOLSWITCH     = 'bool'; //die condition erwartet einen anderen Feldnamen (boolfeld, checkbox) und deaktiviert das Feld
    const VALUESWITCH    = 'value'; //die Condition blendet anhand der condition Felder ein.
    const METHODSWITCH   = 'method'; //die Condition prüft den Wert anhand einer Methode
}
