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
                    if (!isset($tokensValues[$fieldName]) || in_array($tokensValues[$fieldName], ['', ' ', '0,00 €', '0.00 €', '0', 0, '##' . $fieldName . '##', '%', ' %', '0 %', '0,00 %'], true) || (is_string($tokensValues[$fieldName]) && strpos($tokensValues[$fieldName], '##') === 0)) {
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
            if ($val !== '' && $val !== null && !is_array($val) && (!isset($tokensValues[$key]) || in_array($tokensValues[$key], ['', ' ', '0,00 €', '0.00 €', '0', 0, '##' . $key . '##'], true) || (is_string($tokensValues[$key]) && strpos($tokensValues[$key], '##') === 0))) {
                $tokensValues[$key] = $val;
            }
        }
        
        // Ensure price tokens from dlgValues are not lost
        $priceKeys = ['priceSum', 'priceSumNet', 'priceSumTax', 'priceOptionSum', 'priceOptionSumNet', 'priceOptionSumTax', 'priceDiscount', 'priceNet', 'priceTax', 'price', 'discountPercent'];
        foreach ($priceKeys as $pk) {
            if (isset($dlgValues[$pk]) && !in_array($dlgValues[$pk], ['0,00 €', '0.00 €', '0', 0, '', ' ', '##' . $pk . '##', '%', ' %', '0 %', '0,00 %', ' '], true)) {
                $tokensValues[$pk] = $dlgValues[$pk];
            }
        }
        
        // Log tokens after dlgValues merge
        if (isset($tokensValues['priceDiscount']) || isset($tokensValues['discountPercent'])) {
            \con4gis\CoreBundle\Resources\contao\models\C4gLogModel::addLogEntry('reservation', "Tokens after dlgValues merge: Discount: " . ($tokensValues['priceDiscount'] ?? 'MISSING') . ", Percent: " . ($tokensValues['discountPercent'] ?? 'MISSING'));
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
        $priceKeys = ['priceSum', 'priceSumNet', 'priceSumTax', 'priceOptionSum', 'priceOptionSumNet', 'priceOptionSumTax', 'priceDiscount', 'priceNet', 'priceTax', 'price', 'discountPercent'];
        foreach ($priceKeys as $pk) {
            $val = $dlgValues[$pk] ?? $tokensValues[$pk] ?? '';
            
            // Check if we have a valid non-empty value
            $isZero = in_array($val, ['0,00 €', '0.00 €', '0', 0, '', ' ', '##' . $pk . '##', '%', ' %', '0 %', '0,00 %', '0,00', '0.00', ' '], true);
            
            if (!$isZero) {
                $arrTokens[$pk] = $val;
            }
            
            // Special handling for discountPercent to ensure it always has a value if set
            if ($pk === 'discountPercent' && (empty($arrTokens[$pk]) || $arrTokens[$pk] === '0 %' || $arrTokens[$pk] === ' ' || $arrTokens[$pk] === '0,00 %' || $arrTokens[$pk] === '0,00')) {
                $dp = $dlgValues['discountPercent'] ?? $tokensValues['discountPercent'] ?? '';
                if ($dp !== '' && $dp !== '0 %' && $dp !== '0' && $dp !== '##discountPercent##' && $dp !== ' ' && $dp !== '0,00 %' && $dp !== '0,00' && $dp !== '0.00' && $dp !== '0.00 %') {
                    $arrTokens['discountPercent'] = is_numeric($dp) ? ($dp . ' %') : $dp;
                }
            }
        }
        
        // Ensure priceDiscount is set if we have a discountPercent and a total
        if ((!isset($arrTokens['priceDiscount']) || $arrTokens['priceDiscount'] === '0,00 €' || $arrTokens['priceDiscount'] === ' ') && isset($arrTokens['discountPercent']) && isset($arrTokens['priceSum'])) {
             $dpVal = floatval(str_replace([' ', '%', ','], ['', '', '.'], $arrTokens['discountPercent']));
             $psRaw = str_replace([' ', '€'], ['', ''], $arrTokens['priceSum']);
             $psVal = floatval(str_replace(',', '.', str_replace('.', '', $psRaw)));
             if ($dpVal > 0 && $psVal > 0) {
                 // The priceSum might already have the discount deducted.
                 // If priceSum is 10.00 and discount is 5%, then priceSum = priceBefore * (1 - 0.05)
                 // priceBefore = priceSum / 0.95 = 10.526...
                 // discountAmount = 10.526 - 10.00 = 0.526 -> 0,53 €
                 $discountAmount = ($psVal / (100 - $dpVal)) * $dpVal;
                 $arrTokens['priceDiscount'] = \con4gis\ReservationBundle\Classes\Helper\C4gReservationHandler::formatPrice($discountAmount);
                 \con4gis\CoreBundle\Resources\contao\models\C4gLogModel::addLogEntry('reservation', "Calculated priceDiscount: $discountAmount from Total: $psVal, Percent: $dpVal");
             } elseif ($dpVal > 0 && ($psVal == 0 || empty($arrTokens['priceSum']))) {
                 // If priceSum is not yet set or zero, we can't calculate amount, but we should at least keep the percent
                 \con4gis\CoreBundle\Resources\contao\models\C4gLogModel::addLogEntry('reservation', "PriceSum is zero, can't calculate discount amount. Percent: $dpVal");
             }
        }
        
        // Ensure priceNet and priceTax are set if priceSum is set but they are missing
        if (isset($arrTokens['priceSum']) && $arrTokens['priceSum'] !== '0,00 €' && $arrTokens['priceSum'] !== ' ') {
             if (!isset($arrTokens['priceSumTax']) || $arrTokens['priceSumTax'] === '0,00 €' || $arrTokens['priceSumTax'] === ' ') {
                  // Fallback calculation if missing
                  $psRaw = str_replace([' ', '€'], ['', ''], $arrTokens['priceSum']);
                  $psVal = floatval(str_replace(',', '.', str_replace('.', '', $psRaw)));
                  if ($psVal > 0) {
                       $net = $psVal / 1.19;
                       $tax = $psVal - $net;
                       $arrTokens['priceSumNet'] = \con4gis\ReservationBundle\Classes\Utils\C4gReservationHandler::formatPrice($net);
                       $arrTokens['priceSumTax'] = \con4gis\ReservationBundle\Classes\Utils\C4gReservationHandler::formatPrice($tax);
                       \con4gis\CoreBundle\Resources\contao\models\C4gLogModel::addLogEntry('reservation', "Notification Fallback VAT Calculation: Sum: $psVal, Net: $net, Tax: $tax");
                  }
             }
             // Ensure priceNet and priceTax also match priceSumNet and priceSumTax
             if (!isset($arrTokens['priceNet']) || $arrTokens['priceNet'] === '0,00 €' || $arrTokens['priceNet'] === ' ' || $arrTokens['priceNet'] === $arrTokens['priceSum']) {
                  $arrTokens['priceNet'] = $arrTokens['priceSumNet'] ?? '0,00 €';
             }
             if (!isset($arrTokens['priceTax']) || $arrTokens['priceTax'] === '0,00 €' || $arrTokens['priceTax'] === ' ') {
                  $arrTokens['priceTax'] = $arrTokens['priceSumTax'] ?? '0,00 €';
             }
        }
        
        // Ensure priceNet and priceTax are set if price is set but they are missing
        if (isset($arrTokens['price']) && (!isset($arrTokens['priceNet']) || $arrTokens['priceNet'] === '0,00 €' || $arrTokens['priceNet'] === ' ')) {
             $arrTokens['priceNet'] = $arrTokens['price'];
        }
        
        // Ensure priceSum is set if not present but we have components
        if (!isset($arrTokens['priceSum']) || $arrTokens['priceSum'] === '0,00 €') {
            if (isset($arrTokens['price'])) {
                $arrTokens['priceSum'] = $arrTokens['price'];
            }
        }
        
        // Log final tokens for debugging
        if (isset($arrTokens['priceDiscount']) || isset($arrTokens['discountPercent']) || isset($arrTokens['priceSum'])) {
            $msg = "Notification Tokens: Discount: " . ($arrTokens['priceDiscount'] ?? 'MISSING') . ", Percent: " . ($arrTokens['discountPercent'] ?? 'MISSING') . ", Total: " . ($arrTokens['priceSum'] ?? 'MISSING');
            if (isset($dlgValues['discountPercent'])) {
                $msg .= " | dlgPercent: " . $dlgValues['discountPercent'];
            }
            if (isset($tokensValues['discountPercent'])) {
                $msg .= " | tokensPercent: " . $tokensValues['discountPercent'];
            }
            if (isset($dlgValues['discountCode'])) {
                $msg .= " | dlgCode: " . $dlgValues['discountCode'];
            }
            $msg .= " | Keys: " . implode(', ', array_keys($arrTokens));
            $msg .= " | dlgValuesKeys: " . implode(', ', array_keys($dlgValues));
            \con4gis\CoreBundle\Resources\contao\models\C4gLogModel::addLogEntry('reservation', $msg);
        }

        // Ensure critical fallback for admin_email even if it wasn't in any array yet
        if (!isset($arrTokens['admin_email']) || !$arrTokens['admin_email'] || $arrTokens['admin_email'] === '##admin_email##') {
            $arrTokens['admin_email'] = $adminEmail;
        }

        // Final protection: if we have a price sum but no tax, and we didn't calculate it above
        if (isset($arrTokens['priceSum']) && (!isset($arrTokens['priceSumTax']) || $arrTokens['priceSumTax'] === '0,00 €' || $arrTokens['priceSumTax'] === ' ')) {
             $psRaw = str_replace([' ', '€'], ['', ''], $arrTokens['priceSum']);
             $psVal = floatval(str_replace(',', '.', str_replace('.', '', $psRaw)));
             if ($psVal > 0) {
                  $tax = $psVal - ($psVal / 1.19);
                  $arrTokens['priceSumTax'] = \con4gis\ReservationBundle\Classes\Utils\C4gReservationHandler::formatPrice($tax);
                  if (!isset($arrTokens['priceTax']) || $arrTokens['priceTax'] === '0,00 €') {
                       $arrTokens['priceTax'] = $arrTokens['priceSumTax'];
                  }
             }
        }
        
        // Final cleanup for unreplaced tokens
        foreach ($arrTokens as $key => $value) {
            if (is_string($value) && strpos($value, '##' . $key . '##') !== false) {
                // If it's a critical price/discount token, prefer a zero-value instead of empty space
                if (in_array($key, ['priceSum', 'priceSumNet', 'priceSumTax', 'priceOptionSum', 'priceOptionSumNet', 'priceOptionSumTax', 'priceDiscount', 'priceNet', 'priceTax', 'price'])) {
                    $arrTokens[$key] = '0,00 €';
                } elseif ($key === 'discountPercent') {
                    $arrTokens[$key] = '0 %';
                } elseif ($key === 'admin_email' && (!$value || $value === '##admin_email##' || strpos($value, '##admin_email##') !== false || strpos($value, '%23%23admin_email%23%23') !== false)) {
                     $arrTokens[$key] = 'info@kuestenschmiede.de';
                } else {
                    $arrTokens[$key] = ' ';
                }
            }
        }
        
        // Ensure admin_email is never a placeholder in the final collection
        if (isset($arrTokens['admin_email']) && (strpos($arrTokens['admin_email'], '##') !== false || strpos($arrTokens['admin_email'], '%23%23') !== false)) {
             $arrTokens['admin_email'] = 'info@kuestenschmiede.de';
        }

        if ($arrTokens) {
            $raw_data = '';
            $adminEmailForReplacement = $arrTokens['admin_email'] ?? 'info@kuestenschmiede.de';
            foreach ($arrTokens as $key => $value) {
                if (is_string($value)) {
                    $arrTokens[$key] = str_replace(['##admin_email##', '%23%23admin_email%23%23', '##admin_email_url##'], $adminEmailForReplacement, $value);
                }
                $valForRaw = $arrTokens[$key];
                if (is_array($valForRaw)) {
                    $valForRaw = json_encode($valForRaw);
                }
                if (is_string($valForRaw)) {
                     $valForRaw = str_replace(['##admin_email##', '%23%23admin_email%23%23', '##admin_email_url##'], $adminEmailForReplacement, $valForRaw);
                }
                $raw_data = $raw_data ? $raw_data . ', ' . $key . '=>' . $valForRaw : $key . '=>' . $valForRaw;
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
