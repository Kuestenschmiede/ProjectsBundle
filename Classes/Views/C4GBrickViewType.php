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
namespace con4gis\ProjectsBundle\Classes\Views;

class C4GBrickViewType {
    const GROUPBASED        = 'group'; //Gruppenbasiert - Unsere Daten
    const MEMBERBASED       = 'member'; //Mitgliederbasiert - Meine Daten
    const PUBLICBASED       = 'public'; //Öffentliche Ansicht ohne Bearbeitungsmöglichkeit
    const PUBLICUUIDBASED   = 'publicuuid'; //Speicherung in Datenbank ohne Mitgliedschaft per UUID
    const PROJECTBASED      = 'projectbased'; //Ein Projekt muss geladen sein

    const PROJECTPARENTBASED = 'projectparentbased'; //Abhängig von einer pid (Parenttabelle bzw. brick)
    const GROUPPARENTBASED  = 'groupparentbased'; // wie groupparentview nur mit bearbeitungs-/hinzufügen-möglichkeit

    const GROUPVIEW         = 'groupview'; //Gruppenansicht ohne Bearbeitungsmöglichkeit
    const MEMBERVIEW        = 'memberview'; //Mitgliederansicht ohne Bearbeitungsmöglichkeit
    const PUBLICVIEW        = 'publicview'; //Noch nicht benutzt

    const PROJECTPARENTVIEW = 'projectparentview'; //Ohne Bearbeitungsmöglichkeit
    const GROUPPARENTVIEW   = 'groupparentview'; //wir parentbased aber ohne Project

    const GROUPFORM         = 'groupform'; //direkt ins Formular (1 Datensatz)
    const MEMBERFORM        = 'memberform'; //direkt ins Formular (1 Datensatz)
    const PROJECTFORM       = 'projectform'; //direkt ins Formular (1 Datensatz)
    const PUBLICFORM        = 'publicform'; // direkt ins Formular (1 Datensatz)

    const PROJECTPARENTFORM = 'projectparentform'; //direkt ins Formular (1 Datensatz)

    const GROUPFORMCOPY     = 'groupformcopy'; //Öffnet immer neuen Datensatz als Kopie vom Letzten
    const PROJECTFORMCOPY   = 'projectformcopy'; //Öffnet immer neuen Datensatz als Kopie vom Letzten

    const PROJECTPARENTFORMCOPY  = 'projectparentformcopy'; //Öffnet immer neuen Datensatz als Kopie vom Letzten

    const GROUPPROJECT      = 'project'; //wie GROUPBASED mit Projekterweiterungen
    const MEMBERBOOKING     = 'memberbooking'; //Merkmale eines Shops für con4gis-booking
}
