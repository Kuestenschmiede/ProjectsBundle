<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    7
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Database;

/**
 * Class C4GBrickDatabaseType
 * @package c4g\projects
 */
class C4GBrickDatabaseType
{
    const NO_DB = 'no_db';
    const DCA_MODEL = 'dca_model'; //dieser Typ geht von einer Standard Contao DCA und der zugehörigen Model-Klasse aus.
    const DOCTRINE = 'doctrine'; //dieser Typ erwartet eine doctrine entity Klasse
}
