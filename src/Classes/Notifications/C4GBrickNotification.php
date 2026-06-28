<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @author con4gis contributors (see "authors.md")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2026, by Küstenschmiede GmbH Software & Design
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

        // Pre-fill tokens with all available dialog values to ensure no manually added tokens are lost
        if (is_array($dlgValues)) {
            foreach ($dlgValues as $key => $val) {
                if ($val !== '' && $val !== null && !is_array($val)) {
                    $tokensValues[$key] = $val;
                }
            }
        }

        // filter the notifications fields
        foreach ($fieldList as $field) {
            $fieldName = $field->getFieldName();
            if ($field->isNotificationField()) {
                if ($fieldName == 'permalink') {
                    $permalink_name = $field->getPermaLinkName();
                }

                $field_array[] = $field;
            }
            
            // ALWAYS check if the value is in dlgValues and needs translation.
            // This ensures data like calculated prices from putVars are kept in tokensValues,
            // but also translated if a field definition exists.
            $additionalId = $field->getAdditionalID();
            $lookupNames = [$fieldName];
            if (!empty($additionalId)) {
                $lookupNames[] = $fieldName . '_' . $additionalId;
            }
            
            foreach ($lookupNames as $name) {
                if (isset($dlgValues[$name]) && $dlgValues[$name] !== '' && $dlgValues[$name] !== null) {
                    $translated = $field->translateFieldValue($dlgValues[$name]);
                    // If the current value in tokensValues is empty, unformatted, or if we have a better translated value
                    if (!isset($tokensValues[$fieldName]) || $tokensValues[$fieldName] === '' || $tokensValues[$fieldName] === '0,00 €' || $tokensValues[$fieldName] === ' ' || $tokensValues[$fieldName] === null) {
                        $tokensValues[$fieldName] = $translated;
                    } elseif (is_string($translated) && strpos($translated, ' €') !== false && (!is_string($tokensValues[$fieldName]) || strpos($tokensValues[$fieldName], ' €') === false)) {
                        // Prefer the translated value if it's a formatted price and current one isn't
                        $tokensValues[$fieldName] = $translated;
                    }
                }
            }
        }

        // adding values to the notifications fields
        foreach ($dlgValues as $name => $dlgValue) {
            foreach ($field_array as $field) {
                $additionalId = $field->getAdditionalID();
                $fieldName = $field->getFieldName();
                $suffixedName = $fieldName . ($additionalId ? ('_' . $additionalId) : '');

                if (($suffixedName == $name) || (($fieldName == $name) && $field instanceof C4GMultiCheckboxField) || ((strpos($name, $fieldName) !== false) && $field instanceof C4GMultiCheckboxField)) {
                    if (!$field instanceof C4GDateField) {
                        if (!$field instanceof C4GMultiCheckboxField) {
                            $translatedValue = $field->translateFieldValue($dlgValue);
                            // Avoid overwriting already translated/formatted values (like prices from putVars)
                            // if the new value is numeric/unformatted and the existing one is formatted.
                            if (isset($tokensValues[$field->getFieldName()]) && is_string($tokensValues[$field->getFieldName()]) && strpos($tokensValues[$field->getFieldName()], ' €') !== false && (!is_string($translatedValue) || strpos($translatedValue, ' €') === false)) {
                                // Keep the formatted value
                            } else {
                                $dlgValue = $translatedValue;
                            }
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
                        
                        if (!isset($tokensValues[$field->getFieldName()])) {
                            $tokensValues[$field->getFieldName()] = '';
                        }
                        $tokensValues[$field->getFieldName()] .= $multiCheckboxString;
                    } elseif (!$field instanceof C4GMultiCheckboxField) {
                        $translatedValue = $field->translateFieldValue($dlgValue);
                        // Priority for formatted currency strings
                        if (isset($tokensValues[$field->getFieldName()]) && is_string($tokensValues[$field->getFieldName()]) && strpos($tokensValues[$field->getFieldName()], ' €') !== false && (!is_string($translatedValue) || strpos($translatedValue, ' €') === false)) {
                            // Keep the formatted value
                        } else {
                            $tokensValues[$field->getFieldName()] = $translatedValue;
                        }
                        // For fields with suffixes, also provide a token with the full name
                        if ($fieldName != $name) {
                            $tokensValues[$name] = $tokensValues[$field->getFieldName()];
                        }
                    }
                }
            }
        }

        // Add admin_email and ensure it's replaced in all tokens
        $adminEmail = $dlgValues['admin_email'] ?? (\Contao\Config::get('adminEmail') ?: ($GLOBALS['TL_CONFIG']['adminEmail'] ?? 'info@kuestenschmiede.de'));
        if (!$adminEmail || $adminEmail === '##admin_email##') {
            $adminEmail = 'info@kuestenschmiede.de';
        }
        $tokensValues['admin_email'] = $adminEmail;

        foreach ($tokensValues as $key => $val) {
            if (is_string($val)) {
                $tokensValues[$key] = str_replace(['##admin_email##', '%23%23admin_email%23%23', '##admin_email_url##'], $adminEmail, $val);
                if (strpos($tokensValues[$key], '##admin_email##') !== false) {
                    $tokensValues[$key] = str_replace('##admin_email##', $adminEmail, $tokensValues[$key]);
                }
            }
        }

        // Add dlgValues as fallbacks if not already set, but don't overwrite
        // already formatted/translated values.
        foreach ($dlgValues as $key => $val) {
            if ($val !== '' && $val !== null && !is_array($val) && (!isset($tokensValues[$key]) || $tokensValues[$key] === '' || $tokensValues[$key] === ' ' || $tokensValues[$key] === '0,00 €' || $tokensValues[$key] === '0.00 €' || $tokensValues[$key] === '0' || $tokensValues[$key] === 0)) {
                $tokensValues[$key] = $val;
            }
        }
        
        // Ensure price tokens from dlgValues are not lost
        $priceKeys = ['priceSum', 'priceSumNet', 'priceSumTax', 'priceOptionSum', 'priceOptionSumNet', 'priceOptionSumTax', 'priceDiscount', 'priceNet', 'priceTax', 'price'];
        foreach ($priceKeys as $pk) {
            if (isset($dlgValues[$pk]) && $dlgValues[$pk] !== '0,00 €' && $dlgValues[$pk] !== '0' && $dlgValues[$pk] !== 0) {
                $tokensValues[$pk] = $dlgValues[$pk];
            }
        }

        // Framework might overwrite tokens here with DB values if $object is provided.
        // For reservations, we often want to keep the fresh calculated tokens.
        foreach ($field_array as $field) {
            $fieldName = $field->getFieldName();

            //ToDo check field class type instead
            if ($fieldName == 'permalink') {
                $tokensValues[$fieldName] = $field->getInitialValue();
            } else {
                //use dbset instead of dlgValues?
                if ($object) {
                    $dbvalue = $object->$fieldName;
                    // Fix: Only use DB value if token is empty, otherwise we lose calculated values.
                    // Special case: if it is "0,00 €" it might be a default value we want to override with real DB data if available.
                    if ($dbvalue && $dbvalue != 'false' && $dbvalue !== null && $dbvalue !== '') {
                        $translatedDb = $field->translateFieldValue($dbvalue);
                        if (empty($tokensValues[$fieldName]) || $tokensValues[$fieldName] === '0,00 €' || $tokensValues[$fieldName] === '0 €' || $tokensValues[$fieldName] === ' ' || $tokensValues[$fieldName] === null) {
                            $tokensValues[$fieldName] = $translatedDb;
                        } elseif (is_string($translatedDb) && strpos($translatedDb, ' €') !== false && (!is_string($tokensValues[$fieldName]) || strpos($tokensValues[$fieldName], ' €') === false)) {
                            // If DB has a formatted price and current token doesn't, prefer DB
                            $tokensValues[$fieldName] = $translatedDb;
                        }
                    }
                }
            }
        }

        //ToDo prüfen ob flüssiger als flüssig
        if ($button_email) {
            $tokensValues = $dlgValues;
        }

        if (isset($dlgValues['c4g_member_id']) && $dlgValues['c4g_member_id']) {
            $tokensValues['c4g_member_id'] = $dlgValues['c4g_member_id'];
        }

        $arrTokens = C4GBrickNotification::getMemberDetails($tokensValues, $permalink_name);
        
        // Ensure all dlgValues are also in the final arrTokens if not already there,
        // but avoid overwriting translated values.
        foreach ($dlgValues as $key => $val) {
            if ($val !== '' && $val !== null && !is_array($val) && !isset($arrTokens[$key])) {
                $arrTokens[$key] = $val;
            }
        }

        // FINAL PROTECTION for price tokens - ensure they are in the final array
        $priceKeys = ['priceSum', 'priceSumNet', 'priceSumTax', 'priceOptionSum', 'priceOptionSumNet', 'priceOptionSumTax', 'priceDiscount', 'priceNet', 'priceTax', 'price'];
        foreach ($priceKeys as $pk) {
            if (isset($dlgValues[$pk]) && $dlgValues[$pk] !== '0,00 €' && $dlgValues[$pk] !== '0' && $dlgValues[$pk] !== 0) {
                $arrTokens[$pk] = $dlgValues[$pk];
            }
        }

        // Ensure critical fallback for admin_email even if it wasn't in any array yet
        if (!isset($arrTokens['admin_email']) || !$arrTokens['admin_email'] || $arrTokens['admin_email'] === '##admin_email##') {
            $arrTokens['admin_email'] = $adminEmail;
        }

        if ($arrTokens) {
            $raw_data = '';
            foreach ($arrTokens as $key => $value) {
                if (is_string($value)) {
                    $arrTokens[$key] = str_replace(['##admin_email##', '%23%23admin_email%23%23', '##admin_email_url##'], $adminEmail, $value);
                }
                if (is_array($value)) {
                    $value = json_encode($value);
                }
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
                    if (isset($tokensValues['email']) && $tokensValues['email']) {
                        $tokensValues['user_email'] = $tokensValues['email'];
                    } elseif ($member) {
                        $tokensValues['user_email'] = $member->email;
                    }
                }

                if ($name == 'permalink' && $permalink_name !== '') {
                    if (isset($tokensValues[$permalink_name]) && $tokensValues[$permalink_name]) {
                        $tokensValues['permalink'] = $tokenValue . $tokensValues[$permalink_name];
                    }
                }
            }
        }

        return $tokensValues;
    }
}
