<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Views;

class C4GBrickViewType
{
    const GROUPBASED = 'group'; //Gruppenbasiert - Unsere Daten
    const MEMBERBASED = 'member'; //Mitgliederbasiert - Meine Daten

    /** ADMINBASED View and Editing for administrators. C4GPermissions grant access to the entire table.  */
    const ADMINBASED = 'admin';

    const PUBLICBASED = 'public'; //Öffentliche Ansicht ohne Bearbeitungsmöglichkeit
    const PUBLICUUIDBASED = 'publicuuid'; //Speicherung in Datenbank ohne Mitgliedschaft per UUID
    const PROJECTBASED = 'projectbased'; //Ein Projekt muss geladen sein

    const PROJECTPARENTBASED = 'projectparentbased'; //Abhängig von einer pid (Parenttabelle bzw. brick)
    const GROUPPARENTBASED = 'groupparentbased'; // wie groupparentview nur mit bearbeitungs-/hinzufügen-möglichkeit
    const PUBLICPARENTBASED = 'publicparentbased'; // Öffentliche Ansicht mit Parentauswahl vor Aufbau der Liste mit Bearbetungsmöglichkeit
    const PUBLICPARENTVIEW = 'publicparentview'; // Öffentliche Ansicht mit Parentauswahl vor Aufbau der Liste ohne Bearbetungsmöglichkeit

    const GROUPVIEW = 'groupview'; //Gruppenansicht ohne Bearbeitungsmöglichkeit
    const MEMBERVIEW = 'memberview'; //Mitgliederansicht ohne Bearbeitungsmöglichkeit
    const PUBLICUUIDVIEW = 'publicuuidview'; //Ansicht für PUBLICUUIDBASED ohne Bearbeitungsmöglichkeit
    const PUBLICVIEW = 'publicview'; //Noch nicht benutzt

    const PROJECTPARENTVIEW = 'projectparentview'; //Ohne Bearbeitungsmöglichkeit
    const GROUPPARENTVIEW = 'groupparentview'; //wir parentbased aber ohne Project

    const GROUPFORM = 'groupform'; //direkt ins Formular (1 Datensatz)
    const MEMBERFORM = 'memberform'; //direkt ins Formular (1 Datensatz)
    const PROJECTFORM = 'projectform'; //direkt ins Formular (1 Datensatz)
    const PUBLICFORM = 'publicform'; // direkt ins Formular (1 Datensatz)

    const PROJECTPARENTFORM = 'projectparentform'; //direkt ins Formular (1 Datensatz)

    const GROUPFORMCOPY = 'groupformcopy'; //Öffnet immer neuen Datensatz als Kopie vom Letzten
    const PROJECTFORMCOPY = 'projectformcopy'; //Öffnet immer neuen Datensatz als Kopie vom Letzten

    const PROJECTPARENTFORMCOPY = 'projectparentformcopy'; //Öffnet immer neuen Datensatz als Kopie vom Letzten

    const GROUPPROJECT = 'project'; //wie GROUPBASED mit Projekterweiterungen
    const MEMBERBOOKING = 'memberbooking'; //Merkmale eines Shops für con4gis-booking
}
