"use strict";

var clearBrowserUrl = function() {
  var newHref = window.location.href;
  var pos = newHref.indexOf("?state");
  if (pos > -1) {
    newHref = newHref.substring(0, pos);
    history.pushState({}, null, newHref);
  }
};
jQuery(document).ready(function () {
  clearBrowserUrl();
});