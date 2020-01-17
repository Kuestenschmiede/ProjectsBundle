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
namespace con4gis\ProjectsBundle\Classes\Filter;

use con4gis\CoreBundle\Classes\C4GUtils;
use con4gis\CoreBundle\Classes\Helper\ArrayHelper;

class C4GBrickMatching
{
    public function quickSearch($id, $tableName, $database, $prefix, $fieldList, $dlgValues, $own_query = '')
    {
        $tmpArray = [];
        foreach ($fieldList as $field) {
            //sorting out fields like headline etc
            if ($field->getFieldName() !== null) {
                $tmpArray[$field->getFieldName()] = $field;
            }
        }
        $fieldList = $tmpArray;
        $data = [];
        $importance_fields = [];
        $importance_values = [];
        foreach ($dlgValues as $name => $dlgValue) {
            //filter importance fields
            foreach ($fieldList as $field) {
                if ($field instanceof C4GGeopickerField && C4GUtils::endsWith($name, $prefix)) {
                    $importance_fields[$field->getFieldName()] = $field;
                    if ($importance_values[$field->getFieldName() . $prefix] == '') {
                        $importance_values[$field->getFieldName() . $prefix] = 3;
                    }
                } elseif (C4GUtils::startsWith($field->getFieldName(), $name) && C4GUtils::endsWith($name, $prefix)) {
                    if ($dlgValue > 0) { //vorher > 1
                        $importance_values[$field->getFieldName()] = $dlgValue;
                        //saving importance fields for query
                        $importanceFieldName = substr($field->getFieldName(), 0, -strlen($prefix));
                        if ($fieldList[$importanceFieldName] != null) {
                            $importance_fields[$importanceFieldName] = $fieldList[$importanceFieldName];
                        }
                    }
                }
            }
        }

        if ($tableName && $dlgValues) {
            //getting all available data
            if ($own_query !== '') {
                $own_field_value = $own_query[0];
                $own_field_name = $own_query[1];
                $query = $database->prepare("SELECT * FROM $tableName WHERE published = TRUE AND $own_field_name > $own_field_value")->execute();
            } else {
                $query = $database->prepare("SELECT * FROM $tableName WHERE published = TRUE")->execute();
            }
            $resultSet = [];
            while ($query->next()) {
                $resultSet[] = $query->row();
            }
            foreach ($resultSet as $entry) {
                $entry = ArrayHelper::arrayToObject($entry);
                $accuracy_number = 0;
                foreach ($importance_fields as $importance_field) {
                    if ($importance_field->isSearchField()) {
                        $fieldName = $importance_field->getFieldName();

                        //adding accuracy_numbers if Db and Dlgvalues are the same
                        $result = $importance_field->compareWithDB($entry, $dlgValues);

                        if (empty($result)) {
                            if (isset($entry->$fieldName)) {
                                if ($importance_field instanceof C4GGeopickerField) {
                                    if (isset($dlgValues[$importance_field->getLongitudeField()]) &&
                                      isset($dlgValues[$importance_field->getLatitudeField()]) &&
                                      isset($dlgValues[$importance_field->getRadiusFieldName()])) {
                                        if ($dlgValues[$importance_field->getLatitudeField()] !== '' && $dlgValues[$importance_field->getLongitudeField()] !== '') {
                                            $resultRadius = self::radiusSearchList($dlgValues[$importance_field->getLatitudeField()], $dlgValues[$importance_field->getLongitudeField()], $dlgValues[$importance_field->getRadiusFieldName()], $database, $tableName, $entry->id);
                                        }
                                        //set resultRadius true if coordinates are empty
                                        else {
                                            $resultRadius = true;
                                        }

                                        if ($resultRadius) {
                                            $accuracy_number += $importance_values[$fieldName . $prefix] * $importance_field->getSearchWeightings();
                                        }
                                    }
                                } else {
                                    $accuracy_number += $importance_values[$fieldName . $prefix] * $importance_field->getSearchWeightings();
                                }
                            }
                        } elseif ($importance_values[$fieldName . $prefix] == 3) {
                            //bad dataset because of highest importance
                            $accuracy_number = 0;
                            $entry = null;

                            break;
                        }
                    }
                }
                if (is_object($entry)) {
                    $entry->accuracy_number = $accuracy_number;
                    $entry->search_name = $dlgValues['teaser'];
                    if ($entry->accuracy_number > 0) {
                        $data[$entry->id] = $entry;
                    }
                }
            }
        }
        //sorting data which highest accuracy_number
        usort($data, function ($a, $b) {
            return ($a->accuracy_number) > ($b->accuracy_number);
        });
        $data = array_reverse($data);

        return $data;
    }

    /**
     * @param $latitude
     * @param $longitude
     * @param $radius
     * @param $database
     * @param $table
     * @param null $entryId
     * @return bool
     */
    private static function radiusSearchList($latitude, $longitude, $radius, $database, $table, $entryId = null)
    {
        if (isset($entryId)) {
            $query = $database->prepare("SELECT id,(6371 * acos(cos(radians($latitude)) * cos(radians(loc_geoy)) * cos(radians(loc_geox) - radians($longitude)) + sin(radians($latitude)) * sin(radians(loc_geoy)))) AS distance FROM (SELECT * FROM $table WHERE id=$entryId) AS list HAVING distance <= $radius ORDER BY distance ASC")->execute();
            if ($query->numRows > 0) {
                return true;
            }

            return false;
        }
        $query = $database->prepare("SELECT id,loc_geox,loc_geoy,(6371 * acos(cos(radians($latitude)) * cos(radians(loc_geoy)) * cos(radians(loc_geox) - radians($longitude)) + sin(radians($latitude)) * sin(radians(loc_geoy)))) AS distance FROM $table HAVING distance <= $radius ORDER BY distance ASC")->execute();

        return $query->fetchAllAssoc();
    }
}
