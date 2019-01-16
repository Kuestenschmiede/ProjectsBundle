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
namespace con4gis\ProjectsBundle\Classes\Fieldlist;

class C4GBrickLoadOptions
{
    private $model      = ''; //Model Klasse
    private $keyField   = 'id'; //wenn leer werden alle Datensätze geladen, ansonsten wird die Value des Feldes als Schlüßel benutzt
    private $idField    = ''; //id Feld für Select
    private $nameField  = ''; //Beschriftung für Select
    private $pathField  = ''; //Für die Bildgalerie
    private $publishedField = ''; //Boolean Spalte steuert Sichtbarkeit

    /**
     * @return string
     */
    public function getPublishedField()
    {
        return $this->publishedField;
    }

    /**
     * @param $publishedField
     * @return $this
     */
    public function setPublishedField($publishedField)
    {
        $this->publishedField = $publishedField;
        return $this;
    }


    /**
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return string
     */
    public function getKeyField()
    {
        return $this->keyField;
    }

    /**
     * @param $keyField
     * @return $this
     */
    public function setKeyField($keyField)
    {
        $this->keyField = $keyField;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdField()
    {
        return $this->idField;
    }

    /**
     * @param $idField
     * @return $this
     */
    public function setIdField($idField)
    {
        $this->idField = $idField;
        return $this;
    }

    /**
     * @return string
     */
    public function getNameField()
    {
        return $this->nameField;
    }

    /**
     * @param $nameField
     * @return $this
     */
    public function setNameField($nameField)
    {
        $this->nameField = $nameField;
        return $this;
    }

    /**
     * @return string
     */
    public function getPathField()
    {
        return $this->pathField;
    }

    /**
     * @param $pathField
     * @return $this
     */
    public function setPathField($pathField)
    {
        $this->pathField = $pathField;
        return $this;
    }



}