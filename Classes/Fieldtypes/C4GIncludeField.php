<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018.
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\CoreBundle\Resources\contao\classes\container\C4GContainer;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;

class C4GIncludeField extends C4GBrickField
{
    protected $includeType = C4GBrickConst::INCLUDE_CONTENT; //see class for all options
    protected $ids = array();

    public function getC4GDialogField($fieldList, C4GContainer $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        $id = "c4g_" . $this->getFieldName();
        $required = $this->generateRequiredString($data, $dialogParams);
        $value = $this->generateInitialValue($data);
        $result = '';

        if ($this->isShowIfEmpty() || !empty($value)) {

            $condition = $this->createConditionData($fieldList, $data);

            $content = '';
            $ids = $this->ids;
            if ($ids && count($ids) > 0) {
                foreach ($ids as $id) {
                    $content .= '<div class="c4g_include_field">';
                    switch ($this->includeType) {
                        case C4GBrickConst::INCLUDE_ARTICLE:
                            $content .= '{{insert_article::'.$id.'}}';
                            break;
                        case C4GBrickConst::INCLUDE_CONTENT:
                            $content .= '{{insert_content::'.$id.'}}';
                            break;
                        case C4GBrickConst::INCLUDE_MODULE:
                            $content .= '{{insert_module::'.$id.'}}';
                            break;
                        case C4GBrickConst::INCLUDE_FORM:
                            $content .= '{{insert_form::'.$id.'}}';
                            break;
                        case C4GBrickConst::INCLUDE_NEWS:
                            $content .= '<a href="{{news_url::'.$id.'}}"><h3>{{news_title::'.$id.'}}</h3></a>';
                            $content .= '<div class="teaser">{{news_teaser::'.$id.'}}</div>';
                            break;
                        case C4GBrickConst::INCLUDE_EVENT:
                            $content .= '<a href="{{event_url::'.$id.'}}"><h3>{{event_title::'.$id.'}}</h3></a>';
                            $content .= '<div class="teaser">{{event_teaser::'.$id.'}}</div>';
                            break;
                    }
                    $content .= '</div>';
                }
            }

            if ($content) {
                $content = '<div id="'.$id.'" class="c4g_include_fields" '.$required.'>'.$content.'</div>';
            }

            $result = $this->addC4GField($condition, $dialogParams, $fieldList, $data, \Controller::replaceInsertTags($content));
        }

        return $result;
    }

    /**
     * @param $dbValues
     * @param $dlgValues
     * @return array|C4GBrickFieldCompare|null
     */
    public function compareWithDB($dbValues, $dlgValues)
    {
        $result = null;
        return $result;
    }

    /**
     * @return mixed
     */
    public function getIncludeType()
    {
        return $this->includeType;
    }

    /**
     * @param $includeType
     * @return $this
     */
    public function setIncludeType($includeType)
    {
        $this->includeType = $includeType;
        return $this;
    }

    /**
     * @return array
     */
    public function getIds()
    {
        return $this->ids;
    }

    /**
     * @param $ids
     * @return $this
     */
    public function setIds($ids)
    {
        $this->ids = $ids;
        return $this;
    }

}