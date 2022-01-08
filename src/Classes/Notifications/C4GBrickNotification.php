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
namespace con4gis\ProjectsBundle\Classes\Notifications;

use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GDateField;
use Contao\MemberModel;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GMultiCheckboxField;

class C4GBrickNotification
{
    public static function getArrayTokens($dlgValues, $fieldList, $button_email = false, $object = null)
    {
        $field_array = [];
        $tokensValues = [];
        $permalink_name = '';
        // filter the notifications fields
        foreach ($fieldList as $field) {
            if ($field->isNotificationField()) {
                if ($field->getFieldName() == 'permalink') {
                    $permalink_name = $field->getPermaLinkName();
                }

                $field_array[] = $field;
            }
        }

        // adding values to the notifications fields
        foreach ($dlgValues as $name => $dlgValue) {
            foreach ($field_array as $field) {
                $additionalId = $field->getAdditionalID();
                $fieldName = $field->getFieldName();
                if (!empty($additionalId)) {
                    $fieldName = $fieldName . '_' . $additionalId;
                }

                if (($fieldName == $name) || ((strpos($name, $fieldName) !== false) && $field instanceof C4GMultiCheckboxField)) {
                    if (!$field instanceof C4GDateField) {
                        if (!$field instanceof C4GMultiCheckboxField) {
                            $dlgValue = $field->translateFieldValue($dlgValue);
                        } else {
                            if ($dlgValue && ($dlgValue != 'false')) {
                                $pos = strpos($name, '|');
                                if ($pos !== false) {
                                    $dlgValue = substr($name, $pos + 1);
                                    $dlgValue = $field->translateFieldValue($dlgValue);
                                }
                            }
                        }
                    }
                    if ($field instanceof C4GMultiCheckboxField && (strpos($name, '|') !== false)) {
                        if (!empty($tokensValues[$field->getFieldName()]) && ($dlgValue && ($dlgValue != 'false'))) {
                            $multiCheckboxString = ', ' . $dlgValue;
                        } else {
                            $multiCheckboxString = $dlgValue && $dlgValue != 'false' ? $dlgValue : '';
                        }
                        $tokensValues[$field->getFieldName()] .= $multiCheckboxString;
                    } elseif (!$field instanceof C4GMultiCheckboxField) {
                        $tokensValues[$field->getFieldName()] = $dlgValue;
                    }
                }
            }
        }

        foreach ($field_array as $field) {
            $fieldName = $field->getFieldName();

            //ToDo check field class type instead
            if ($fieldName == 'permalink') {
                $tokensValues[$fieldName] = $field->getInitialValue();
            } else {
                //use dbset instead of dlgValues?
                if ($object) {
                    $dbvalue = $object->$fieldName;
                    if ($dbvalue && $dbvalue != 'false') {
                        $tokensValues[$fieldName] = $dbvalue;
                    }
                }
            }
        }

        //ToDo prüfen ob flüssiger als flüssig
        if ($button_email) {
            $tokensValues = $dlgValues;
        }

        if ($dlgValues['c4g_member_id']) {
            $tokensValues['c4g_member_id'] = $dlgValues['c4g_member_id'];
        }

        $arrTokens = C4GBrickNotification::getMemberDetails($tokensValues, $permalink_name);

        if ($arrTokens) {
            $raw_data = '';
            foreach ($arrTokens as $key => $value) {
                $raw_data = $raw_data ? $raw_data . ', ' . $key . '=>' . $value : $key . '=>' . $value;
            }

            $arrTokens['raw_data'] = $raw_data;
        }

        return $arrTokens;
    }

    public static function getMemberDetails($tokensValues, $permalink_name = '')
    {
        if ($tokensValues) {
            foreach ($tokensValues as $name => $tokenValue) {
                if ($name == 'c4g_member_id') {
                    $member = MemberModel::findByPk($tokenValue);
                    if ($member) {
                        $tokensValues['firstname'] = $member->firstname;
                        $tokensValues['lastname'] = $member->lastname;
                    }

                    //Sonderlocke
                    if ($tokensValues['email']) {
                        $tokensValues['user_email'] = $tokensValues['email'];
                    } elseif ($member) {
                        $tokensValues['user_email'] = $member->email;
                    }
                }

                if ($name == 'permalink' && $permalink_name !== '') {
                    if ($tokensValues[$permalink_name]) {
                        $tokensValues['permalink'] = $tokenValue . $tokensValues[$permalink_name];
                    }
                }
            }
        }

        return $tokensValues;
    }
}
