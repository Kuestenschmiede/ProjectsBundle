<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\ProjectsBundle\Classes\Dialogs;
use c4g\C4GUtils;
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
    private $type = \c4g\projects\C4GBrickConst::OVERLAY_DIALOG;

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
            case \c4g\projects\C4GBrickConst::OVERLAY_DIALOG:
                $this->html = '<a id="'.$id.'" onclick="' .
                    \c4g\projects\C4GBrickCommon::getPopupWindowString($link) . '"></a>';
                break;
            case \c4g\projects\C4GBrickConst::OVERLAY_ANIMATION:
                $this->html = '<div id="c4g_brick_overlay_content"></div><a id="'.$id.'" onclick="'.\c4g\projects\C4GBrickCommon::getPopupElementString('\''.$id.'_animation\'').';document.getElementById(\''.$id.'_animation\').play(); document.getElementById(\''.$id.'_animation\').addEventListener(\'ended\',C4GAnimationHandler,false);
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
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink($link)
    {
        $this->link = $link;
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
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }


}