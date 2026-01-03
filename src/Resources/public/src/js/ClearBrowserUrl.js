/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @author con4gis contributors (see "authors.md")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2026, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

"use strict";

var clearBrowserUrl = function() {
  var newHref = window.location.href;
  var pos = newHref.indexOf("?state");
  if (pos > -1) {
    newHref = newHref.substring(0, pos);
    history.pushState({}, null, newHref);
  }
};
ready(function () {
  clearBrowserUrl();
  c4g.projects = c4g.projects || {};
  c4g.projects.clearUrl = true;
});