<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2022, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Maps;

use con4gis\CoreBundle\Classes\Helper\ArrayHelper;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use con4gis\CoreBundle\Classes\C4GHTMLFactory;
use Contao\Controller;

class C4GBrickMapFrontendParent
{
    public function getClosePopupString()
    {
        return '$.magnificPopup.close()';
    }

    /**
     * @param $siteId
     * @param $state
     * @param $buttonText
     * @param $isEditButton
     * @param $member_id
     * @param $brick
     * @return string
     * @deprecated
     */
    public function addPopupButton($siteId, $state, $buttonText, $isEditButton, $member_id, $project_id, $brick, $group_id = null, $parent_id = null)
    {
        return $this->createPopupButton($siteId, $state, $buttonText, $isEditButton, $member_id, $project_id, $brick, $group_id, $parent_id);
    }

    /**
     * @param $siteId
     * @param $state
     * @param $buttonText
     * @param $isEditButton
     * @param $member_id
     * @param $brick
     * @return string
     */
    public function createPopupButton($siteId, $state, $buttonText, $isEditButton, $member_id, $project_id, $brick, $group_id = null, $parent_id = null)
    {
        $result = '';

        //testweise weitere Werte mit übergeben.
        if ($group_id) {
            $state = $state . ':' . $group_id;
            if ($project_id) {
                $state = $state . ':' . $project_id;
                if ($parent_id) {
                    $state = $state . ':' . $parent_id;
                }
            }
        }

        $link = '{{link_url::' . $siteId . '}}?state=' . $state;

        if ((!$isEditButton) || (C4GBrickCommon::hasMemberRightsForBrick($member_id, $project_id, $brick))) {
            $result = '<div class = "c4g_brick_popup_button"><a onclick="' .
                C4GBrickCommon::getPopupWindowString($link) . '"> ' . $buttonText . ' </a></div>';
        }

        return $result;
    }

    /**
     * @param $siteId
     * @param $state
     * @param $buttonText
     * @param $isEditButton
     * @param $member_id
     * @param $project_id
     * @param $brick
     * @param null $group_id
     * @param null $parent_id
     * @param null $newTab
     * @return string
     * @deprecated
     */
    public function addRedirectButton($siteId, $state, $buttonText, $isEditButton, $member_id, $project_id, $brick, $group_id = null, $parent_id = null, $newTab = null)
    {
        return $this->createRedirectButton($siteId, $state, $buttonText, $isEditButton, $member_id, $project_id, $brick, $group_id, $parent_id, $newTab);
    }
    public function createRedirectButton($siteId, $state, $buttonText, $isEditButton, $member_id, $project_id, $brick, $group_id = null, $parent_id = null, $newTab = null)
    {
        $result = '';

        if ($siteId) {
            //testweise weitere Werte mit übergeben.
            if ($group_id) {
                $state = $state . ':' . $group_id;
                if ($project_id) {
                    $state = $state . ':' . $project_id;
                    if ($parent_id) {
                        $state = $state . ':' . $parent_id;
                    }
                }
            }

            $link = '{{link_url::' . $siteId . '}}?state=' . $state;

            if ((!$isEditButton) || (C4GBrickCommon::hasMemberRightsForBrick($member_id, $project_id, $brick))) {
                if ($newTab) {
                    $result = '<div class = "c4g_brick_popup_button"><a href="' .
                        $link . '" target="_blank"> ' . $buttonText . '</a></div>';
                } else {
                    $result = '<div class = "c4g_brick_popup_button"><a href="' .
                        $link . '"> ' . $buttonText . ' </a></div>';
                }
            }
        }

        return $result;
    }

    /**
     * @param $name
     * @param $type
     * @return string
     * @deprecated
     */
    public function addPopupHeader($name, $type)
    {
        return $this->createPopupHeader($name, $type);
    }

    /**
     * @param $name
     * @param $type
     * @return string
     */
    public function createPopupHeader($name, $type)
    {
        $result =
            '<div class=c4g_popup_header_featurename>' . $name . '</div>' .
            '<div class=c4g_popup_header_featuretype>' . $type . '</div>' .
            C4GHTMLFactory::lineBreak();

        return $result;
    }

    /**
     * @param $value
     * @return string
     */
    public function backslashNToBr($value)
    {
        $pos = strpos($value, "\n");
        if ($pos !== false) {
            return C4GHTMLFactory::lineBreak() . nl2br($value) . C4GHTMLFactory::lineBreak() . C4GHTMLFactory::lineBreak();
        }

        return $value;
    }

    /**
     * @param $key
     * @return string
     * @deprecated
     */
    public function addPopupKeyElement($key)
    {
        return $this->createPopupKeyElement($key);
    }

    /**
     * @param $key
     * @return string
     */
    public function createPopupKeyElement($key)
    {
        $result = '';
        if ($key) {
            $result = '<input type="hidden" id="c4g_brick_popup_id" value="' . $key . '">';
        }

        return $result;
    }

    /**
     * @param $title
     * @param $value
     * @param bool $space_before
     * @return string
     * @deprecated
     */
    public function addPopupListElement($title, $value, $space_before = false)
    {
        return $this->createPopupListElement($title, $value, $space_before);
    }

    /**
     * @param $title
     * @param $value
     * @param bool $space_before
     * @return string
     */
    public function createPopupListElement($title, $value, $space_before = false)
    {
        $result = '';
        $before = '';
        if (($value) && ($title) && (strtolower($value) != 'unknown')) {
            if ($space_before) {
                $before = '<p>';
            }

            $result = $before . '<li>' . $title . ': ' . C4GBrickMapFrontendParent::backslashNToBr($value) . '</li>';
        }

        return $result;
    }

    /**
     * @param $title
     * @param $description
     * @return string
     * @deprecated
     */
    public function addPopupDescriptionElement($title, $description, $last_member_id = 0, $maxLength = 254)
    {
        return $this->createPopupDescriptionElement($title, $description, $last_member_id, $maxLength);
    }

    /**
     * @param $title
     * @param $description
     * @return string
     */
    public function createPopupDescriptionElement($title, $description, $last_member_id = 0, $maxLength = 254)
    {
        $result = '';
        if (($title) && ($description)) {
            $description = C4GBrickCommon::cutText($description, $maxLength);
            $result = $title . ':' . C4GHTMLFactory::lineBreak() . nl2br($description) . C4GHTMLFactory::lineBreak();
        }

        if ($last_member_id && $last_member_id > 0) {
            $result .= C4GHTMLFactory::lineBreak() . 'Letzter Bearbeiter: ' . C4GBrickCommon::getNameForMember($last_member_id) .
                C4GHTMLFactory::lineBreak();
        }

        return $result;
    }

    /**
     * @param $pid
     * @param $id
     * @param $key
     * @param $type
     * @param $name
     * @param $layername
     * @param $display
     * @param $hide
     * @param null $content
     * @param bool $withUrl
     * @return array
     * @deprecated
     */
    protected function addMapStructureElement($pid, $id, $key, $type, $name, $layername, $display, $hide, $content_async = null, $content = null, $withUrl = false)
    {
        return $this->createMapStructureElement($pid, $id, $key, $type, $name, $layername, $display, $hide, $content_async, $content, $withUrl);
    }
    /**
     * @param $pid
     * @param $id
     * @param $key
     * @param $type
     * @param $name
     * @param $layername
     * @param $display
     * @param $hide
     * @param null $content
     * @param bool $withUrl
     * @return array
     */
    protected function createMapStructureElement($pid, $id, $key, $type, $name, $layername, $display, $hide, $content_async = null, $content = null, $withUrl = false)
    {
        //ToDo only refresh we do not use the url
        $arrData = [];
        $arrData['pid'] = $pid;
        $arrData['id'] = $id;
        $arrData['key'] = $key;
        $arrData['async_content'] = $content_async;

        if ($content != null) {
            $content[0]['id'] = $id + 1;
            $content[0]['data']['position']['positionId'] = $id + 1;
            if ($withUrl) {
                $arrData['type'] = 'ajax';
                if ($type) {
                    $arrData['origType'] = $type;
                }
            } else {
                if ($type) {
                    $arrData['type'] = $type;
                } else {
                    $arrData['type'] = 'none';
                }
            }
        } else {
            $arrData['type'] = $type;
        }
        $arrData['name'] = C4GBrickCommon::cutText($name, 44);
        $arrData['layername'] = C4GBrickCommon::cutText($layername ?: $name, 44);
        $arrData['activeForBaselayers'] = 'all';
        $arrData['display'] = ($display && ($content != null));
        $arrData['hide'] = $hide;

        if ($content != null) {
            $arrData['content'] = $content;
        }

        return $arrData;
    }

    /**
     * @param $elementId
     * @param $parentId
     * @param $parentPid
     * @param $parentIdent
     * @param $type
     * @param $name
     * @param $layername
     * @param $display
     * @param $hide
     * @param null $content
     * @param bool $withUrl
     * @return array
     * @deprecated
     */
    public function addMapStructureElementWithIdCalc(
            $elementId,
            $parentId,
            $parentPid,
            $parentIdent,
            $type,
            $name,
            $layername,
            $display,
            $hide,
            $content = null,
            $withUrl = false)
    {
        return $this->createMapStructureElementWithIdCalc(
            $elementId,
            $parentId,
            $parentPid,
            $parentIdent,
            $type,
            $name,
            $layername,
            $display,
            $hide,
            $content,
            $withUrl);
    }

    /**
     * @param $elementId
     * @param $parentId
     * @param $parentPid
     * @param $parentIdent
     * @param $type
     * @param $name
     * @param $layername
     * @param $display
     * @param $hide
     * @param null $content
     * @param bool $withUrl
     * @param array $dataLayer
     * @return array
     */
    public function createMapStructureElementWithIdCalc(
            $elementId,
            $parentId,
            $parentPid,
            $parentIdent,
            $type,
            $name,
            $layername,
            $display,
            $hide,
            $content = null,
            $withUrl = false,
            $dataLayer = [])
    {
        //ToDo only refresh we do not use the url
        $arrData = [];

        $calcId = C4GBrickCommon::calcLayerID($elementId, $parentId, $parentIdent + 1);

        if ($parentPid == 0) {
            $arrData['pid'] = $parentId;//ToDo test with child['id']
        } else {
            $arrData['pid'] = C4GBrickCommon::calcLayerID($parentId, $parentPid, $parentIdent);
        }

        $arrData['id'] = $calcId;
        $arrData['key'] = $calcId;

        if ($content != null) {
            $content[0]['id'] = $calcId + 1;
            $content[0]['data']['position']['positionId'] = $calcId + 1;
            if ($withUrl) {
                $arrData['type'] = 'ajax';
                if ($type) {
                    $arrData['origType'] = $type;
                }
            } else {
                if ($type) {
                    $arrData['type'] = $type;
                } else {
                    $arrData['type'] = 'none';
                }
            }
        } else {
            $arrData['type'] = $type;
        }

        $arrData['name'] = C4GBrickCommon::cutText($name, 44);
        $arrData['layername'] = C4GBrickCommon::cutText($layername ?: $name, 44);
        $arrData['activeForBaselayers'] = $dataLayer['activeForBaselayers'];
        $arrData['noFilter'] = $dataLayer['noFilter'];
        $arrData['noRealFilter'] = $dataLayer['noRealFilter'];
        $arrData['display'] = ($display && ($content != null));
        $arrData['hide'] = $hide;

        if ($content != null) {
            $arrData['content'] = $content;
        }

        return $arrData;
    }

    /**
     * @param $locationStyle
     * @param $geoJson
     * @param $popupInfo
     * @param string $label
     * @param string $graphicTitle
     * @param null $cluster
     * @param null $url
     * @param int $interval
     * @return array
     * @deprecated
     */
    public function addMapStructureContentFromGeoJson($locationStyle, $geoJson, $popupInfo, $label = '', $graphicTitle = '', $cluster = null, $url = null, $interval = 60000, $properties = [])
    {
        return $this->createMapStructureContentFromGeoJson(
            $locationStyle, $geoJson, $popupInfo, $label, $graphicTitle, $cluster, $url, $interval, $properties
        );
    }

    /**
     * @param $locationStyle
     * @param $geoJson
     * @param $popupInfo
     * @param string $label
     * @param string $graphicTitle
     * @param null $cluster
     * @param null $url
     * @param int $interval
     * @return array
     */
    public function createMapStructureContentFromGeoJson($locationStyle, $geoJson, $popupInfo, $label = '', $graphicTitle = '', $cluster = null, $url = null, $interval = 60000, $properties = [])
    {
        $stringClass = $GLOBALS['con4gis']['stringClass'];
        $popupInfo = $stringClass::toHtml5($popupInfo);
        $popupInfo = Controller::replaceInsertTags($popupInfo, false);
        $popupInfo = str_replace(['{{request_token}}', '[{]', '[}]'], [REQUEST_TOKEN, '{{', '}}'], $popupInfo);
        $popupInfo = Controller::replaceDynamicScriptTags($popupInfo);
        $objComments = new \Comments();
        $popupInfo = $objComments->parseBbCode($popupInfo);

        $fillcolor = '';
        $fontcolor = '';
        $popup = '';
        $zoom = '';

        if ($cluster) {
            $fillcolor = $cluster['cluster_fillcolor'];
            $fontcolor = $cluster['cluster_fontcolor'];
            $popup = $cluster['cluster_popup'];
            $zoom = $cluster['cluster_zoom'];
        }

        if (($url) && ($interval != null) && ($interval > 0)) {
            if ($cluster && $cluster['cluster_locations']) {
                $settings = [
                    'loadAsync' => true,
                    'refresh' => true,
                    'interval' => $interval,
                    'crossOrigin' => false,
                    'boundingBox' => false,
                    'cluster' => $cluster['cluster_distance'],
                ];
            } else {
                $settings = [
                    'loadAsync' => true,
                    'refresh' => true,
                    'interval' => $interval,
                    'crossOrigin' => false,
                    'boundingBox' => false,
                ];
            }
            $objGeoJson = json_decode($geoJson, true);
            $objGeoJson['properties'] = array_merge($objGeoJson['properties'], $properties);
            $content = [
                'id' => 0,
                'type' => 'urlData',
                'format' => 'GeoJSON',
                //'origType' => 'single',
                'locationStyle' => $locationStyle,
                'cluster_fillcolor' => $fillcolor,
                'cluster_fontcolor' => $fontcolor,
                'cluster_popup' => $popup,
                'cluster_zoom' => $zoom,
                'properties' => $properties,
                'data' => json_decode($geoJson, true),
                'settings' => $settings,
            ];
        } else {
            if ($cluster['cluster_locations']) {
                $settings = [
                    'loadAsync' => false,
                    'refresh' => false,
                    'crossOrigine' => false,
                    'boundingBox' => false,
                    'cluster' => $cluster['cluster_distance'],
                ];
            } else {
                $settings = [
                    'loadAsync' => false,
                    'refresh' => false,
                    'crossOrigine' => false,
                    'boundingBox' => false,
                ];
            }

            $content = [
                'id' => 0,
                'type' => 'GeoJSON',
                'format' => 'GeoJSON',
                //'origType' => 'single',
                'locationStyle' => $locationStyle,
                'cluster_fillcolor' => $fillcolor,
                'cluster_fontcolor' => $fontcolor,
                'cluster_popup' => $popup,
                'cluster_zoom' => $zoom,
                'data' => json_decode($geoJson, true),
                'settings' => $settings,
            ];
        }

        return [$content];
    }

    /**
     * @param $id
     * @param $locationStyle
     * @param $loc_geox
     * @param $loc_geoy
     * @param $popupInfo
     * @param string $label
     * @param string $graphicTitle
     * @return array
     * @deprecated
     */
    public function addMapStructureContent($locationStyle, $loc_geox, $loc_geoy, $popupInfo, $label = '', $graphicTitle = '', $cluster = null, $url = null, $interval = 60000, $properties = [])
    {
        return $this->createMapStructureContent(
            $locationStyle,
            $loc_geox,
            $loc_geoy,
            $popupInfo,
            $label,
            $graphicTitle,
            $cluster,
            $url,
            $interval,
            $properties
        );
    }

    /**
     * @param $id
     * @param $locationStyle
     * @param $loc_geox
     * @param $loc_geoy
     * @param $popupInfo
     * @param string $label
     * @param string $graphicTitle
     * @return array
     */
    public function createMapStructureContent($locationStyle, $loc_geox, $loc_geoy, $popupInfo, $label = '', $graphicTitle = '', $cluster = null, $url = null, $interval = 60000, $properties)
    {
        $stringClass = $GLOBALS['con4gis']['stringClass'];
        $popupInfo = $stringClass::toHtml5($popupInfo);
        $popupInfo = Controller::replaceInsertTags($popupInfo, false);
        $popupInfo = str_replace(['{{request_token}}', '[{]', '[}]'], [REQUEST_TOKEN, '{{', '}}'], $popupInfo);
        $popupInfo = Controller::replaceDynamicScriptTags($popupInfo);
        $objComments = new \Comments();
        $popupInfo = $objComments->parseBbCode($popupInfo);

        $fillcolor = '';
        $fontcolor = '';
        $popup = '';
        $zoom = '';

        if ($cluster) {
            $fillcolor = $cluster['cluster_fillcolor'];
            $fontcolor = $cluster['cluster_fontcolor'];
            $popup = $cluster['cluster_popup'];
            $zoom = $cluster['cluster_zoom'];
        }

        if (($url) && ($interval != null) && ($interval > 0)) {
            if ($cluster && $cluster['cluster_locations']) {
                $settings = [
                    'loadAsync' => true,
                    'refresh' => true,
                    'interval' => $interval,
                    'crossOrigin' => false,
                    'boundingBox' => false,
                    'cluster' => $cluster['cluster_distance'],
                ];
            } else {
                $settings = [
                    'loadAsync' => true,
                    'refresh' => true,
                    'interval' => $interval,
                    'crossOrigin' => false,
                    'boundingBox' => false,
                ];
            }
            $objProperties = array_merge([
                'projection' => 'EPSG:4326',
                'positionId' => 0,
                'popup' => [
                    'content' => $popupInfo,
                    'routing_link' => '1',
                    'async' => false,
                ],
                'label' => $label,
                'graphicTitle' => $graphicTitle,
            ], $properties);

            $content = [
                'id' => 0,
                'type' => 'urlData',
                'format' => 'GeoJSON',
                //'origType' => 'single',
                'locationStyle' => $locationStyle,
                'cluster_fillcolor' => $fillcolor,
                'cluster_fontcolor' => $fontcolor,
                'cluster_popup' => $popup,
                'cluster_zoom' => $zoom,
                'properties' => $properties,
                'data' => [
                    'url' => $url,
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [
                            (float) $loc_geox,
                            (float) $loc_geoy,
                        ],
                    ],
                    'properties' => $objProperties,
                ],
                'settings' => $settings,
            ];
        } else {
            if ($cluster['cluster_locations']) {
                $settings = [
                    'loadAsync' => false,
                    'refresh' => false,
                    'crossOrigine' => false,
                    'boundingBox' => false,
                    'cluster' => $cluster['cluster_distance'],
                ];
            } else {
                $settings = [
                    'loadAsync' => false,
                    'refresh' => false,
                    'crossOrigine' => false,
                    'boundingBox' => false,
                ];
            }

            $objProperties = array_merge([
                'projection' => 'EPSG:4326',
                'popup' => [
                    'content' => $popupInfo,
                    'routing_link' => '1',
                    'async' => false,
                ],
                'label' => $label,
                'graphicTitle' => $graphicTitle,

            ], $properties);
            $content = [
                'id' => 0,
                'type' => 'GeoJSON',
                'format' => 'GeoJSON',
                //'origType' => 'single',
                'locationStyle' => $locationStyle,
                'cluster_fillcolor' => $fillcolor,
                'cluster_fontcolor' => $fontcolor,
                'cluster_popup' => $popup,
                'cluster_zoom' => $zoom,
                'properties' => $properties,
                'data' => [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [
                            (float) $loc_geox,
                            (float) $loc_geoy,
                        ],
                    ],
                    'properties' => $objProperties,
                ],
                'settings' => $settings,
            ];
        }

        return [$content];
    }

    /**
     * @param $arrData
     * @param $arrChildData
     * @param bool $sort
     * @return mixed
     * @deprecated
     */
    public function addMapStructureChilds($arrData, $arrChildData, $sort = true)
    {
        return $this->createMapStructureChilds($arrData, $arrChildData, $sort);
    }

    /**
     * @param $arrData
     * @param $arrChildData
     * @param bool $sort
     * @return mixed
     */
    public function createMapStructureChilds($arrData, $arrChildData, $sort = true)
    {
        if ($arrChildData) {
            if (!$sort) {
                $arrSortedData = $arrChildData;
            } elseif ($arrChildData['layername'] != $arrChildData['name']) {
                $arrSortedData = ArrayHelper::array_sort($arrChildData, 'layername', SORT_ASC, true);
            } else {
                $arrSortedData = ArrayHelper::array_sort($arrChildData, 'name', SORT_ASC, true);
            }

            foreach ($arrSortedData as $key => $arrSortedDataValue) {
                $arrSortedData[$key]['pid'] = $arrData['id'];
                if ($arrSortedData[$key]['content'] != null) {
                    $arrSortedData[$key]['content'][0]['id'] = $arrSortedDataValue['id'] + 1;
                    $arrSortedData[$key]['content'][0]['data']['position']['positionId'] = $arrSortedDataValue['id'] + 1;
                }
            }

            $size = is_array($arrSortedData) ? sizeof($arrSortedData) : 0;
            $arrData['hasChilds'] = true;
            $arrData['display'] = ($size > 0);
            $arrData['childsCount'] = $size;
            $arrData['childs'] = $arrSortedData;
        } else {
            $arrData['display'] = false;
        }

        return $arrData;
    }

    /**
     * @param $arrData
     * @param $childData
     * @param bool $sort
     * @return mixed
     * @deprecated
     */
    public function addMapStructureChild($arrData, $childData, $sort = true)
    {
        return $this->createMapStructureChild($arrData, $childData, $sort);
    }

    /**
     * @param $arrData
     * @param $childData
     * @param bool $sort
     * @return mixed
     */
    public function createMapStructureChild($arrData, $childData, $sort = true)
    {
        if ($arrData && $childData) {
            $arrData['childs'][] = $childData;

            if (!$sort) {
                $arrSortedData = $arrData['childs'];
            } elseif ($childData['layername'] != $childData['name']) {
                $arrSortedData = ArrayHelper::array_sort($arrData['childs'], 'layername', SORT_ASC, true);
            } else {
                $arrSortedData = ArrayHelper::array_sort($arrData['childs'], 'name', SORT_ASC, true);
            }

            foreach ($arrSortedData as $key => $arrSortedDataValue) {
                $arrSortedData[$key]['pid'] = $arrData['id'];
                if ($arrSortedData[$key]['content'] != null) {
                    $arrSortedData[$key]['content'][0]['id'] = $arrSortedDataValue['id'] + 1;
                    $arrSortedData[$key]['content'][0]['data']['position']['positionId'] = $arrSortedDataValue['id'] + 1;
                }
            }

            $size = is_array($arrSortedData) ? sizeof($arrSortedData) : 0;
            $arrData['hasChilds'] = true;
            $arrData['display'] = ($size > 0);
            $arrData['childsCount'] = $size;
            $arrData['childs'] = $arrSortedData;
        } else {
            $arrData['display'] = false;
        }

        return $arrData;
    }

    /**
     * @param $level
     * @param $child
     * @return array|void
     * @deprecated
     */
    public function addLocations($level, $child)
    {
        return $this->createLocations($level, $child);
    }

    /**
     * @param $level
     * @param $child
     * @return array|void
     */
    public function createLocations($level, $child)
    {
        //Wird in der erbenen Klasse überschrieben.
    }

    /**
     * If $value is true-y, return $entry, else return ''.
     * @param $value
     * @param $entry
     * @return string
     */
    public function checkDefinedForEntry($value, $entry)
    {
        return $value ? $entry : '';
    }
}
