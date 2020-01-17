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
namespace con4gis\ProjectsBundle\Classes\Common;

use con4gis\CoreBundle\Classes\C4GVersionProvider;
use con4gis\CoreBundle\Resources\contao\models\C4gLogModel;
use con4gis\MapsBundle\Resources\contao\models\C4gMapProfilesModel;
use con4gis\MapsBundle\Resources\contao\models\C4gMapsModel;
use con4gis\MapsBundle\Resources\contao\modules\api\ReverseSearchApi;
use con4gis\ProjectsBundle\Classes\Models\C4gProjectsModel;
use Symfony\Component\Config\Definition\Exception\Exception;
use con4gis\GroupsBundle\Resources\contao\models\MemberModel;
use con4gis\GroupsBundle\Resources\contao\models\MemberGroupModel;

class C4GBrickCommon
{
    /**
     * @param $file
     * @return string
     */
    public static function getFileName($file)
    {
        return basename($file);
    }

    /**
     * @param $file
     * @return string
     */
    public static function getFileExtension($file)
    {
        $pos = strrpos($file, '.');

        return substr($file, $pos + 1);
    }

    /**
     * @param $uuid
     * @return \Model|null
     */
    public static function loadFile($uuid)
    {
        $objFile = null;
        if (\Validator::isUuid($uuid)) {
            $objFile = \FilesModel::findByUuid($uuid);
        }

        return $objFile;
    }

    /**
     * @param $uuid
     */
    public static function deleteFileByUUID($uuid)
    {
        if (\Validator::isUuid($uuid)) {
            $objFile = \FilesModel::findByUuid($uuid);
            $file = $objFile->path;

            if ($objFile) {
                $objFile->delete();
            }

            try {
                if ($file) {
                    if (file_exists($test = $_SERVER['DOCUMENT_ROOT'] . '' . $file)) {
                        unlink($test2 = $_SERVER['DOCUMENT_ROOT'] . '' . $file);
                    }
                }
            } catch (Exception $e) {
                C4gLogModel::addLogEntry('projects', $e->getMessage());
            }
        }
    }

    /**
     * @param $path
     * @return bool
     */
    public static function mkdir($path)
    {
        $result = false;
        if ($path) {
            try {
                $objFiles = \Files::getInstance();
                $result = $objFiles->mkdir($path);
            } catch (Exception $e) {
                C4gLogModel::addLogEntry('projects', $e->getMessage());
            }
        }

        return $result;
    }

    /**
     * @param $file
     */
    public static function deleteFile($file)
    {
        if ($file) {
            $file = TL_ROOT . '/' . $file;

            try {
                if (file_exists($file)) {
                    unlink($file);
                }
            } catch (Exception $e) {
                C4gLogModel::addLogEntry('maps', $e->getMessage());
            }
        }
    }

    /**
     * @param $string
     * @return null|string
     */
    public static function convertBackslash($string)
    {
        if ($string) {
            $newString = '';
            $rounds = strlen($string);

            for ($i = 0; $i < $rounds; $i++) {
                if ($string[$i] == '\\') {
                    $newString .= '/';
                } else {
                    $newString .= $string[$i];
                }
            }

            return $newString;
        }

        return null;
    }

    /**
     * @param $fieldName
     * @param $original_filename
     * @param $new_filename
     * @param $generated_filename
     * @return int
     * @throws \Exception
     */
    public static function saveFile($fieldName, $original_filename, $new_filename, $generated_filename, $unique = false)
    {
        $path = pathinfo($new_filename, PATHINFO_DIRNAME);
        $filename = pathinfo($new_filename, PATHINFO_FILENAME);
        //$originalFilename = pathinfo($original_filename, PATHINFO_FILENAME);
        $extension = pathinfo($new_filename, PATHINFO_EXTENSION);
        $pos = strpos($path, 'files');
        $contaoPath = substr($path, $pos);

        if ($unique && file_exists($original_filename)) {
            self::deleteFile($original_filename);
        }

        if ($generated_filename != $new_filename) {
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $generated_filename)) {
                if (!rename($_SERVER['DOCUMENT_ROOT'] . $generated_filename, $_SERVER['DOCUMENT_ROOT'] . $new_filename)) {
                    //ToDo Fehlerbehandlung
                }
            }
        }

        $objNew = \Dbafs::addResource($contaoPath . '/' . $filename . '.' . $extension);

        return $objNew->uuid;
    }

    /** TYPEN */
    public static function strToInt($str)
    {
        // sicherstellen, dass ein String uebergeben wurde und Leerzeichen am Anfang/Ende entfernen
        $str = trim(strval($str));

        // Pruefe, ob der String einen gueltigen Aufbau hat:
        // Erst Vorzeichen (optional), dann Ziffern 0-9, dann optional e oder E mit folgenden Ziffern 0-9 (bei Exponentenschreibweise)
        if (!preg_match('/^(\+|\-)?[0-9]+((e|E)[0-9]+)?$/', $str)) {
            if ($str == '') {
                return 0;
            }

            return $str;
            ;
        }

        // String bei e/E teilen (falls Exponentenschreibweise)
        $arr = preg_split('/[eE]/', $str);

        // Teil vor e/E in Integer umwandeln
        $pre = intval($arr[0]);

        // Teil nach e/E (falls vorhanden) in Integer umwandeln
        $post = (isset($arr[1]) ? intval($arr[1]) : null);

        if ($post === null) {
            // keine Exponentenschreibweise, nur Teil vor e/E wird benoetigt
            return $pre;
        }
        // Exponentenschreibweise, entsprechend (Teil vor e/E) * (10 hoch (Teil nach e/E)) rechnen
        return $pre * pow(10, $post);
    }

    public static function strToFloat($str)
    {
        $pos = strrpos($str = strtr(trim(strval($str)), ',', '.'), '.');

        return ($pos === false ? floatval($str) : floatval(str_replace('.', '', substr($str, 0, $pos)) . substr($str, $pos)));
    }

    /**
     * @param $string
     * @return mixed
     */
    public static function strToBool($string)
    {
        return filter_var($string, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param $memberId
     * @param $brickKey
     * @return array
     */
    public static function getGroupListForBrick($memberId, $brickKey)
    {
        $resultList = [];

        if (C4GVersionProvider::isInstalled('con4gis/groups')) {
            $groups = MemberGroupModel::getGroupListForMember($memberId);
            foreach ($groups as $group) {
                $rights = MemberGroupModel::getMemberRightsInGroup($group->id, $memberId);
                if ($rights) {
                    foreach ($rights as $right) {
                        if ($right == $brickKey) {
                            $resultList[] = $group;

                            break;
                        }
                    }
                };
            }
        }

        return $resultList;
    }

    /**
     * @return \ContentModel|\ContentModel[]|\Model\Collection|null
     */
    public function getContentId()
    {
        $maps = \ContentModel::findBy('type', 'c4g_maps');
        $resultList = [];
        foreach ($maps as $map) {
            $map->name = C4gMapsModel::findByPk($map->c4g_map_id)->name;
            if ($map->name) {
                $resultList[$map->id] = $map->name;
            }
        }

        return $resultList;
    }

    /**
     * @param $memberId
     * @param $brickKey
     * @return array
     */
    public static function hasMemberRightsForBrick($memberId, $projectId, $brickKey)
    {
        $result = false;

        if ($projectId) {
            $project = C4gProjectsModel::findByPk($projectId);
            if ($project) {
                $groupId = $project->group_id;
                $rights = MemberGroupModel::getMemberRightsInGroup($groupId, $memberId);
                if ($rights) {
                    foreach ($rights as $right) {
                        if ($right == $brickKey) {
                            $result = true;

                            break;
                        }
                    }
                };
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public static function getGUID()
    {
        mt_srand((double) microtime() * 10000);
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = chr(123)// "{"
            . substr($charid, 0, 8) . $hyphen
            . substr($charid, 8, 4) . $hyphen
            . substr($charid, 12, 4) . $hyphen
            . substr($charid, 16, 4) . $hyphen
            . substr($charid, 20, 12)
            . chr(125);// "}"
        return $uuid;
    }

    public static function getUUID()
    {
        $s = uniqid('', false);
        //return base_convert($s, 16, 36);

        $hex = substr($s, 0, 13);
        $dec = $s[13] . substr($s, 15); // skip the dot
        $result = base_convert($hex, 16, 36) . base_convert($dec, 10, 36);

        return $result;
    }

    public static function getRandomID()
    {
        return rand(999999, 99999999);
    }

    public static function calcLayerID($id, $key, $identifier)
    {
        if (($id != 0) && ($key != 0) && ($identifier != 0)) {
            return str_pad($key, 5, 0, STR_PAD_RIGHT) . str_pad($id, 5, 0, STR_PAD_LEFT) . str_pad($identifier, 4, 0, STR_PAD_LEFT);
        }

        return C4GBrickCommon::getRandomID();
    }

    public static function getLayerIDParam($layerID, $param = 'id')
    {
        if ($param == 'id') {
            $result = (int) substr($layerID, 5, 5);
        } elseif ($param == 'key') {
            $result = (int) substr($layerID, 0, 5);
        } elseif ($param == 'identifier') {
            $result = (int) substr($layerID, 10, 4);
        }

        return $result;
    }

    public static function translateSelectOption($id, $options)
    {
        if ($options) {
            foreach ($options as $option) {
                if ($option['id'] == $id) {
                    return $option['name'];
                }
            }
        }

        return $id;
    }

    public static function cutText($string, $lenght)
    {
        if (strlen($string) > $lenght) {
            $string = substr($string, 0, $lenght) . '...';
            $last = strrchr($string, ' ');
            $string = str_replace($last, ' ...', $string);
        }

        return html_entity_decode($string);
    }

    //ToDo zur Auswertung des Displaynames in das Gruppenmodul bringen
    public static function getNameForMember($memberId)
    {
        if (!is_numeric($memberId)) {
            return;
        }
        $member = MemberModel::findByPk($memberId);

        return $member->firstname . ' ' . $member->lastname;
    }

    public function setSessionValues($group_id, $project_id, $parent_id)
    {
        if ($group_id) {
            \Session::getInstance()->set('c4g_brick_group_id', $group_id);
        }

        if ($project_id) {
            \Session::getInstance()->set('c4g_brick_project_id', $project_id);
        }

        if ($parent_id) {
            \Session::getInstance()->set('c4g_brick_parent_id', $parent_id);
        }
    }

    /**
     * @param $loc_geox         x-coordinate
     * @param $loc_geoy         y-coordinate
     * @param $intProfileId     integer Profile-Id of map
     * @return mixed
     */
    public static function convert_coordinates_to_address($lat, $lon, $intProfileId = 0, $database = null)
    {
        if ($database) {
            if (C4GVersionProvider::isInstalled('con4gis/tracking')) {
                $address = \con4gis\TrackingPortalBundle\Resources\contao\models\C4gTrackingPortalPositionsModel::lookupCache($database, $lat, $lon, false);
                if ($address != 'not cached') {
                    return $address;
                }
            }
        }
        $xml = null;
        $return = $GLOBALS['TL_LANG']['fe_c4g_projects']['address_not_found'];
        $arrParams = [
            'format' => 'xml',
            'lat' => $lat,
            'lon' => $lon,
            'addressdetails' => 1,
        ];

        try {
            $nominatimApi = new ReverseSearchApi();
            if ($nominatimApi) {
                $xmlOutput = $nominatimApi->getReverseNominatimResponse($intProfileId, $arrParams);
                if ($xmlOutput) {
                    $xml = simplexml_load_string($xmlOutput);
                    if ($xml) {
                        $address = $xml->addressparts[0];
                        if ($address) {
                            $anonymous = false;
                            $objMapsProfile = C4gMapProfilesModel::findBy('id', $intProfileId);
                            if ($objMapsProfile) {
                                if ($objMapsProfile->geopicker_anonymous) {
                                    $anonymous = true;
                                }
                            }

                            if (!$anonymous) {
                                $housenumber = $address->house_number; //Hausnummer
                            }
                            $pedestrian = $address->pedestrian; //Fussweg
                            $road = $address->road; //Straße
                            $suburb = $address->suburb; //Ortsteil
                            $town = $address->town; //Ort
                            $county = $address->county; //Landkreis
                            $state = $address->state; //Bundesland
                            $postcode = $address->postcode; //Postleitzahl
                            $country = $address->country; //Land
                            $country_code = $address->country_code; //Länderschlüssel

                            $return = $road;
                            if (!$return) {
                                $return = $pedestrian;
                            }

                            if ($return && $housenumber) {
                                $return .= ' ' . $housenumber;
                            }

                            if (($postcode || $town) && ($road || $pedestrian)) {
                                $return .= ',';
                            }

                            if ($postcode) {
                                $return .= ' ' . $postcode;
                            }

                            if ($town) {
                                $return .= ' ' . $town;
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            C4gLogModel::addLogEntry('maps', $e->getMessage());
        }

        return trim($return);
    }

    /**
     * @return null
     */
    public static function getProfileId()
    {
        $find_profile = C4gMapProfilesModel::findBy('geosearch_engine', 1);
        $profile_id = $find_profile->id;
        if ($profile_id) {
            $return = $profile_id;
        } else {
            $return = null;
        }

        return $return;
    }

    /**
     * @param $number
     * @return null|string
     */
    public static function NumberToTelLink($number)
    {
        if ($number) {
            $TelLink = '<a href="tel:' . $number . '">' . $number . '</a>';

            return $TelLink;
        }

        return null;
    }

    /**
     * @return string
     */
    public function getWindowString()
    {
        return "window.open(this.href,'window_title','height=540,width=768," .
        "chrome=yes, alwaysRaised=yes, dependent=yes, dialog=yes, centerscreen=yes, navigation=no,
                    status=no, horizontal=no, site=no, tab=no, bookmarks=no,
                    titlebar=no, menubar=no, location=no, personalbar=no,
                    toolbar=no, resizable=yes, scrollbars=yes').focus(); return false";
    }

    /**
     * @param $link
     * @return string
     */
    public static function getPopupWindowString($link)
    {
        $result = '';
        if ($link) {
            $result = 'jQuery.magnificPopup.open({ items: { src: ' .
                '\'' . $link . '\' }, type: \'iframe\' }, 0)';
        }

        return $result;
    }

    /**
     * @param $link
     * @return string
     */
    public function getPopupElementString($id)
    {
        $result = '';
        if ($id) {
            $result = 'jQuery.magnificPopup.open({ items: { src: document.getElementById(' . $id . '), }, type: \'inline\'}, 0)';
        }

        return $result;
    }

    /**
     * @param $array
     * @param $seed
     * @return array
     *
     * for reproducable random array
     */
    public function seedShuffle($array, $seed)
    {
        $tmp = [];
        for ($rest = $count = count($array); $count > 0; $count--) {
            $seed %= $count;
            $t = array_splice($array, $seed, 1);
            $tmp[] = $t[0];
            $seed = $seed * $seed + $rest;
        }

        return $tmp;
    }

    /**
     * Converts a given string to a timestamp. Returns the result of strtotime, if it's not false. Converts the string
     * into a parseable format otherwise. CAUTION: Only works for dates >= 01.01.2000.
     * @param $string
     */
    public static function getTimestampFromDate($string)
    {
        $time = strtotime($string);
        if (!$time) {
            $arrDate = explode('-', $string);
            if (count($arrDate) == 2) {
                $date = explode('.', $arrDate[0]);
                // this disables the view of years before 2000
                $date[2] = '20' . $date[2];
                $arrDate[0] = implode('.', $date);
                $timeString = $arrDate[0] . trim($arrDate[1]) . ':00';
                $time = strtotime($timeString);
            }
        }

        return $time;
    }
}
