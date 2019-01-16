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
namespace con4gis\ProjectsBundle\Classes\Dialogs;
use con4gis\CoreBundle\Resources\contao\classes\C4GUtils;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use Contao\ModuleModel;


/**
 * Class C4GBrickOverlay
 * @package con4gis
 */
class C4GBrickOverlay
{
    private $id = '';
    private $link = '';
    private $html = null;
    private $type = C4GBrickConst::OVERLAY_DIALOG;

    /**
     * C4GBrickOverlay constructor.
     * @param string $id
     * @param string $link
     */
    public function __construct($type, $id, $link)
    {
        $this->type = $type;
        $this->id = $id;
        $this->link = $link;

        switch ($type) {
            case C4GBrickConst::OVERLAY_DIALOG:
                $this->html = '<a id="'.$id.'" onclick="' .
                    C4GBrickCommon::getPopupWindowString($link) . '"></a>';
                break;
            case C4GBrickConst::OVERLAY_ANIMATION:
                $this->html = '<div id="c4g_brick_overlay_content"></div><a id="'.$id.'" onclick="'.C4GBrickCommon::getPopupElementString('\''.$id.'_animation\'').';document.getElementById(\''.$id.'_animation\').play(); document.getElementById(\''.$id.'_animation\').addEventListener(\'ended\',C4GAnimationHandler,false);
                    function C4GAnimationHandler(e) {jQuery.magnificPopup.close()}" style="display:none"></a>';
                break;
            default:
                break;
        }
    }


    /**
     * @param $class
     * @param $link
     * @return string
     */
//    private function getPopupVideoString($id) {
//        $result = '';
//        if ($id) {
//            $markup = htmlentities('<div class="mfp-iframe-scaler"><div class="mfp-close"></div><iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe></div>');
//
//            $result = 'jQuery(#'.$id.').magnificPopup({
//                        type: iframe,
//                        mainClass: mfp-fade,
//                        removalDelay: 160,
//                        preloader: false,
//                        fixedContentPos: false,
//                        iframe: { markup : '.$markup.', srcAction: iframe_src }
//                        });';
//
//        }
//
//        return $result;
//    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param $link
     * @return $this
     */
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @return null
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }


}