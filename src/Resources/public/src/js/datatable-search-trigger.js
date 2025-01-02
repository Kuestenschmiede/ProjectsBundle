/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 10
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2025, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

this.c4g = this.c4g || {};
this.c4g.projects = this.c4g.projects || [];
this.c4g.projects.hook = this.c4g.projects.hook || [];

(function (jQuery, c4g) {
  c4g.projects.hook = c4g.projects.hook || [];
  c4g.projects.hook.responseHandled = c4g.projects.hook.responseHandled || [];
  c4g.projects.hook.responseHandled.push(function(objParam) {
    const value = objParam.searchValue;
    // TODO diesen selektor überprüfen; greift der immer auf das element zu ?
    let inputField = jQuery("#c4gGuiDataTable\\3a list\\3a -1_filter > label > input[type=\"search\"]");
    inputField.val(value);
    inputField.trigger("input");
    inputField = jQuery("#c4g_list_search");
    inputField.val(value);
    inputField.trigger("input");
  });

})(jQuery, this.c4g);