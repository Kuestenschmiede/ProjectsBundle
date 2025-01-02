<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 10
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2025, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

namespace con4gis\ProjectsBundle\Classes\jQuery;

use Contao\LayoutModel;
use con4gis\CoreBundle\Classes\ResourceLoader;
use con4gis\MapsBundle\Classes\ResourceLoader as MapsResourceLoader;
use con4gis\CoreBundle\Classes\C4GVersionProvider;

/**
 * Class C4GJQueryGUI
 */
class C4GJQueryGUI
{
    public static function initializeLibraries(
        $addCore = true,
        $addJQuery = true,
        $addJQueryUI = true,
        $useTree = true,
        $useTable = true,
        $useHistory = true,
        $useTooltip = true,
        $useMaps = false,
        $useScrollpane = false,
        $usePopups = false,
        $loadDateTimePicker = false
    ) {
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
                ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/c4g-vendor-jquery.js', ResourceLoader::JAVASCRIPT, 'c4g_jquery');
                // just until the old plugins are replaced
                // Set JQuery to noConflict mode immediately after load of jQuery
                ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/c4gjQueryNoConflict.js', ResourceLoader::BODY, 'c4g_jquery_noconflict');
            }
        }

        if ($addJQueryUI || $useTree || $useMaps) {
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/jquery-ui.min.js', ResourceLoader::BODY, 'c4g_jquery_ui');
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/jquery-ui-de.js', ResourceLoader::BODY, 'c4g_jquery_ui_i18n');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jquery.legacy.min.js', ResourceLoader::BODY, 'c4g_a');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/dist/js/DialogHandler.js', ResourceLoader::BODY, 'dialog_handler');
        }

        //Defaault?
        ResourceLoader::loadJavaScriptResource('bundles/con4giscore/dist/js/AlertHandler.js', ResourceLoader::BODY, 'alert_handler');

        if ($useTable) {
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/jquery.scrollTo.min.js', ResourceLoader::BODY, 'c4g_jq_scrollTo');

            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/jquery.dataTables.min.js', ResourceLoader::BODY, 'c4g_jq_datatables');
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/dataTables.jqueryui.min.js', ResourceLoader::BODY, 'c4g_jq_datatables_ui');

            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/dataTables.buttons.js', ResourceLoader::BODY, 'c4g_jq_datatables_buttons');
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/buttons.print.js', ResourceLoader::BODY, 'c4g_jq_datatables_buttons_print');
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/buttons.jqueryui.js', ResourceLoader::BODY, 'c4g_jq_datatables_buttons_jquery');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/pdfmake.min.js', ResourceLoader::BODY, 'c4g_jq_datatables_buttons_pdf');
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/buttons.html5.js', ResourceLoader::BODY, 'c4g_jq_datatables_buttons_html5');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/vfs_fonts.js', ResourceLoader::BODY, 'c4g_jq_datatables_buttons_font');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/jszip.min.js', ResourceLoader::BODY, 'c4g_jq_datatables_jszip');
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/dataTables.scroller.min.js', ResourceLoader::BODY, 'c4g_jq_datatables_scroller');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Sorting/date-de.js', ResourceLoader::BODY, 'c4g_jq_datatables_sort_date_de');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Sorting/text-de.js', ResourceLoader::BODY, 'c4g_jq_datatables_sort_text_de');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js', ResourceLoader::BODY, 'c4g_jq_datatables_responsive');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Responsive/js/responsive.jqueryui.min.js', ResourceLoader::BODY, 'c4g_jq_datatables_responsive_ui');

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
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/dynatree/jquery.dynatree.min.js', ResourceLoader::BODY, 'c4g_jq_dynatree');
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/plugins/dynatree/skin/ui.dynatree.css');
        }

        if ($useHistory) {
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jquery.history.js', ResourceLoader::BODY, 'c4g_jq_history');
        }

        if ($useTooltip) {
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jquery.tooltip.pack.js', ResourceLoader::BODY, 'c4g_jq_tooltip_b');
        }

        if ($useScrollpane) {
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jScrollPane/js/jquery.jscrollpane.min.js', ResourceLoader::BODY, 'c4g_jq_scrollpane');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jScrollPane/js/jquery.mousewheel.js', ResourceLoader::BODY, 'c4g_jq_mousewheel');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jScrollPane/js/mwheelIntent.js', ResourceLoader::BODY, 'c4g_mwheelintent');
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/plugins/jScrollPane/css/jquery.jscrollpane.css');
        }

        if ($usePopups) {
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/vendor-magnific-popup.js', ResourceLoader::BODY, 'magnific-popup');
        }

        if ($useMaps && C4GVersionProvider::isInstalled('con4gis/maps')) {
            // TODO: recieve and use profileId
            MapsResourceLoader::loadResources();
            MapsResourceLoader::loadTheme();

            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/clipboard.min.js', ResourceLoader::BODY, 'clipboard');
        }

        if ($addCore) {
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/c4gGui.js?v=' . time(), ResourceLoader::BODY, 'c4g_jquery_gui');
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

        if ($loadDateTimePicker || ($useMaps && C4GVersionProvider::isInstalled('con4gis/maps'))) {
            ResourceLoader::loadJavaScriptResource(
                'bundles/con4giscore/vendor/jQuery/plugins' .
                '/jquery-simple-datetimepicker/1.13.0/jquery.simple-dtpicker.js',
                ResourceLoader::BODY,
                'datetimepicker'
            );
        }
    }

    //just projects
    public static function initializeBrickLibraries(
        $addCore = true,
        $addJQuery = true,
        $addJQueryUI = true,
        $useTree = true,
        $useTable = true,
        $useHistory = true,
        $useTooltip = true,
        $useMaps = false,
        $useScrollpane = false,
        $usePopups = false,
        $loadDateTimePicker = false
    ) {
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
                ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/c4g-vendor-jquery.js', ResourceLoader::JAVASCRIPT, 'c4g_jquery');
                // just until the old plugins are replaced
                // Set JQuery to noConflict mode immediately after load of jQuery
                ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/c4gjQueryNoConflict.js', ResourceLoader::BODY, 'c4g_jquery_noconflict');
            }
        }

        if ($addJQueryUI || $useTree || $useMaps) {
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/jquery-ui.min.js', ResourceLoader::BODY, 'c4g_jquery_ui');
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/jquery-ui-de.js', ResourceLoader::BODY, 'c4g_jquery_ui_i18n');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jquery.legacy.min.js', ResourceLoader::BODY, 'c4g_a');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/dist/js/DialogHandler.js', ResourceLoader::BODY, 'dialog_handler');
            //ResourceLoader::loadJavaScriptResource('bundles/con4giscore/dist/js/AlertHandler.js', ResourceLoader::BODY, 'alert_handler');
        }

        ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/c4g-vendor-alerthandler.js', ResourceLoader::HEAD, 'alert_handler');

        if ($useTable) {
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/jquery.scrollTo.min.js', ResourceLoader::BODY, 'c4g_jq_scrollTo');

            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/jquery.dataTables.min.js', ResourceLoader::BODY, 'c4g_jq_datatables');
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/dataTables.jqueryui.min.js', ResourceLoader::BODY, 'c4g_jq_datatables_ui');

            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/dataTables.buttons.js', ResourceLoader::BODY, 'c4g_jq_datatables_buttons');
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/buttons.print.js', ResourceLoader::BODY, 'c4g_jq_datatables_buttons_print');
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/buttons.jqueryui.js', ResourceLoader::BODY, 'c4g_jq_datatables_buttons_jquery');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/pdfmake.min.js', ResourceLoader::BODY, 'c4g_jq_datatables_buttons_pdf');
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/buttons.html5.js', ResourceLoader::BODY, 'c4g_jq_datatables_buttons_html5');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/vfs_fonts.js', ResourceLoader::BODY, 'c4g_jq_datatables_buttons_font');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/jszip.min.js', ResourceLoader::BODY, 'c4g_jq_datatables_jszip');
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/dataTables.scroller.min.js', ResourceLoader::BODY, 'c4g_jq_datatables_scroller');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Sorting/date-de.js', ResourceLoader::BODY, 'c4g_jq_datatables_sort_date_de');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Sorting/text-de.js', ResourceLoader::BODY, 'c4g_jq_datatables_sort_text_de');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js', ResourceLoader::BODY, 'c4g_jq_datatables_responsive');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Responsive/js/responsive.jqueryui.min.js', ResourceLoader::BODY, 'c4g_jq_datatables_responsive_ui');

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
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/dynatree/jquery.dynatree.min.js', ResourceLoader::BODY, 'c4g_jq_dynatree');
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/plugins/dynatree/skin/ui.dynatree.css');
        }

        if ($useHistory) {
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jquery.history.js', ResourceLoader::BODY, 'c4g_jq_history');
        }

        if ($useTooltip) {
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jquery.tooltip.pack.js', ResourceLoader::BODY, 'c4g_jq_tooltip_b');
        }

        if ($useScrollpane) {
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jScrollPane/js/jquery.jscrollpane.min.js', ResourceLoader::BODY, 'c4g_jq_scrollpane');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jScrollPane/js/jquery.mousewheel.js', ResourceLoader::BODY, 'c4g_jq_mousewheel');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jScrollPane/js/mwheelIntent.js', ResourceLoader::BODY, 'c4g_mwheelintent');
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/plugins/jScrollPane/css/jquery.jscrollpane.css');
        }

        if ($usePopups) {
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/vendor-magnific-popup.js', ResourceLoader::BODY, 'magnific-popup');
        }

        if ($useMaps && C4GVersionProvider::isInstalled('con4gis/maps')) {
            // TODO: recieve and use profileId
            MapsResourceLoader::loadResources();
            MapsResourceLoader::loadTheme();

            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/clipboard.min.js', ResourceLoader::BODY, 'clipboard');
        }

        if ($addCore) {
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/c4gGuiBrick.js?v=' . time(), ResourceLoader::BODY, 'c4g_jquery_gui');
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

        if ($loadDateTimePicker || ($useMaps && C4GVersionProvider::isInstalled('con4gis/maps'))) {
            ResourceLoader::loadJavaScriptResource(
                'bundles/con4giscore/vendor/jQuery/plugins' .
                '/jquery-simple-datetimepicker/1.13.0/jquery.simple-dtpicker.js',
                ResourceLoader::BODY,
                'datetimepicker'
            );
        }
    }

}
