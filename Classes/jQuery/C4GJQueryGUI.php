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

namespace con4gis\ProjectsBundle\Classes\jQuery;

use Contao\LayoutModel;
use con4gis\CoreBundle\Classes\ResourceLoader;
use con4gis\MapsBundle\Classes\ResourceLoader as MapsResourceLoader;
use con4gis\CoreBundle\Classes\C4GVersionProvider;

if (!defined('TL_ROOT')) {
    die('You can not access this file directly!');
}

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

/**
 * Class C4GJQueryGUI
 */
class C4GJQueryGUI
{
    public static function initializeTree($addCore = false, $addJQuery = true, $addJQueryUI = true)
    {
        C4GJQueryGUI::initializeLibraries($addCore, $addJQuery, $addJQueryUI, true, false, false, false, false, false, false, false, true, false);
    }

    public static function initializeLibraries($addCore = true, $addJQuery = true, $addJQueryUI = true, $useTree = true, $useTable = true, $useHistory = true, $useTooltip = true,
                                                    $useMaps = false, $useGoogleMaps = false, $useMapsEditor = false, $useWswgEditor = false, $useScrollpane = false, $usePopups = false)
    {
        if ($addJQuery) {
            global $objPage;

            //workaround hasJQuery param with contao >= 4.5
            if ($objPage->layout) {
                $objLayout = LayoutModel::findByPk($objPage->layout);
                $objPage->hasJQuery = $objLayout->addJQuery;
            }

            if ($objPage->hasJQuery) {
                // jQuery is already loaded by Contao, don't load again!
            } else {
                // Include JQuery JS
                ResourceLoader::loadJavaScriptResource('assets/jquery/js/jquery.min.js', $location = ResourceLoader::BODY, $key = 'c4g_jquery');
                // just until the old plugins are replaced
                // Set JQuery to noConflict mode immediately after load of jQuery
                ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/c4gjQueryNoConflict.js', $location = ResourceLoader::BODY, $key = 'c4g_jquery_noconflict');
            }
        }

        if ($addJQueryUI || $useTree || $useMaps) {
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/jquery-ui-1.12.1.custom/jquery-ui.min.js', $location = ResourceLoader::BODY, $key = 'c4g_jquery_ui');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/jquery-ui-i18n.min.js', $location = ResourceLoader::BODY, $key = 'c4g_jquery_ui_i18n');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jquery.legacy.min.js', $location = ResourceLoader::BODY, $key = 'c4g_a');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/dist/js/DialogHandler.js', $location = ResourceLoader::BODY, $key = 'dialog_handler');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/dist/js/AlertHandler.js', $location = ResourceLoader::BODY, $key = 'alert_handler');
        }

        if ($useTable) {
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jquery.scrollTo/jquery.scrollTo.min.js', $location = ResourceLoader::BODY, $key = 'c4g_jq_scrollTo');

            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/media/js/jquery.dataTables.min.js', $location = ResourceLoader::BODY, $key = 'c4g_jq_datatables');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/media/js/dataTables.jqueryui.min.js', $location = ResourceLoader::BODY, $key = 'c4g_jq_datatables_ui');

            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/dataTables.buttons.min.js', $location = ResourceLoader::BODY, $key = 'c4g_jq_datatables_buttons');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/buttons.print.min.js', $location = ResourceLoader::BODY, $key = 'c4g_jq_datatables_buttons_print');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/buttons.jqueryui.min.js', $location = ResourceLoader::BODY, $key = 'c4g_jq_datatables_buttons_jquery');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/pdfmake.min.js', $location = ResourceLoader::BODY, $key = 'c4g_jq_datatables_buttons_pdf');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/buttons.html5.min.js', $location = ResourceLoader::BODY, $key = 'c4g_jq_datatables_buttons_html5');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/vfs_fonts.js', $location = ResourceLoader::BODY, $key = 'c4g_jq_datatables_buttons_font');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/jszip.min.js', $location = ResourceLoader::BODY, $key = 'c4g_jq_datatables_jszip');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min.js', $location = ResourceLoader::BODY, $key = 'c4g_jq_datatables_scroller');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Sorting/date-de.js', $location = ResourceLoader::BODY, $key = 'c4g_jq_datatables_sort_date_de');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Sorting/text-de.js', $location = ResourceLoader::BODY, $key = 'c4g_jq_datatables_sort_text_de');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js', $location = ResourceLoader::BODY, $key = 'c4g_jq_datatables_responsive');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Responsive/js/responsive.jqueryui.min.js', $location = ResourceLoader::BODY, $key = 'c4g_jq_datatables_responsive_ui');

            // Include DataTables CSS
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/plugins/datatables/media/css/jquery.dataTables_themeroller.css');
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/plugins/datatables/media/css/dataTables.jqueryui.min.css');

            // Include DataTables Extensions CSS
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/css/buttons.dataTables.min.css');
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/css/buttons.jqueryui.min.css');
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Scroller/css/scroller.dataTables.min.css');
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Scroller/css/scroller.jqueryui.min.css');
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Responsive/css/responsive.dataTables.min.css');
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Responsive/css/responsive.jqueryui.min.css');
        }

        if ($useTree || $useMaps) {
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/dynatree/jquery.dynatree.min.js', $location = ResourceLoader::BODY, $key = 'c4g_jq_dynatree');
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/plugins/dynatree/skin/ui.dynatree.css');
        }

        if ($useHistory) {
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jquery.history.js', $location = ResourceLoader::BODY, $key = 'c4g_jq_history');
        }

        if ($useTooltip) {
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jquery.tooltip.pack.js', $location = ResourceLoader::BODY, $key = 'c4g_jq_tooltip_b');
        }

        if ($useScrollpane) {
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jScrollPane/js/jquery.jscrollpane.min.js', $location = ResourceLoader::BODY, $key = 'c4g_jq_scrollpane');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jScrollPane/js/jquery.mousewheel.js', $location = ResourceLoader::BODY, $key = 'c4g_jq_mousewheel');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jScrollPane/js/mwheelIntent.js', $location = ResourceLoader::BODY, $key = 'c4g_mwheelintent');
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/plugins/jScrollPane/css/jquery.jscrollpane.css');
        }

        if ($usePopups) {
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/c4g-vendor-magnificpopup.js', $location = ResourceLoader::BODY, $key = 'magnific-popup');
        }

        if ($useMaps && C4GVersionProvider::isInstalled('con4gis/maps')) {
            // TODO: recieve and use profileId
            MapsResourceLoader::loadResources();
            MapsResourceLoader::loadTheme();

            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/clipboard.min.js', $location = ResourceLoader::BODY, $key = 'clipboard');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jquery-simple-datetimepicker/1.13.0/jquery.simple-dtpicker.js', $location = ResourceLoader::BODY, $key = 'datetimepicker');
        }

        if ($addCore) {
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/c4gGui.js?v=' . time(), $location = ResourceLoader::BODY, $key = 'c4g_jquery_gui');
            ResourceLoader::loadCssResourceDeferred('bundles/con4gisprojects/dist/css/c4gGui.min.css');
            ResourceLoader::loadCssResourceDeferred('bundles/con4gisprojects/dist/css/c4gLoader.min.css');
        }

        if ($addJQueryUI || $useTree || $useMaps) {
            // Add the JQuery UI CSS to the bottom of the $GLOBALS['TL_CSS'] array to prevent overriding from other plugins
            $GLOBALS['TL_CSS']['c4g_jquery_ui_core'] = 'bundles/con4giscore/vendor/jQuery/jquery-ui-1.12.1.custom/jquery-ui.min.css';
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/jquery-ui-1.12.1.custom/jquery-ui.min.css');
            // Set the JQuery UI theme to be used
            if (empty($GLOBALS['TL_CSS']['c4g_jquery_ui'])) {
                ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/jquery-ui-1.12.1.custom/jquery-ui.theme.min.css');
            }
        }
    }
}
