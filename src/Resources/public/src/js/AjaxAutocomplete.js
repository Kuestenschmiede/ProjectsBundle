/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

/**
 * Created by cro on 26.06.17.
 */
/**
 * Class AjaxAutocomplete
 * Takes an url and a field as parameters. Whenever a change event is fired upon the input field, an ajax request is made
 * to the passed url and expects an array of options for the field to suggest as autocompletion. The options then are
 * displayed below the input field.
 * @constructor
 */
function AjaxAutocomplete(ajaxUrl, fieldToComplete) {
  var scope = this;
  if (!ajaxUrl || !fieldToComplete) {
    console.warn("Not enough parameters passed. Aborting...");
    return;
  }
  this.ajaxUrl = ajaxUrl;
  this.fieldToComplete = fieldToComplete;

  this.loadData = function() {
    jQuery.get(ajaxUrl + "/" + this.fieldToComplete.id + "/" + this.fieldToComplete.value).done(function(data) {
      var jsonData = JSON.parse(data);
      scope.addOptions(jsonData.data);
    });
  };

  this.addOptions = function(options) {
    var optionCollection = [];
    for (var key in options) {
      if (options.hasOwnProperty(key)) {
        var option = document.createElement("option");
        option.text = options[key].name;
        optionCollection.push(option);
      }
    }
    if (optionCollection.length > 0) {
      var selectbox = document.createElement('select');
      for (var i = 0; i < optionCollection.length; i++) {
        selectbox.add(optionCollection[i]);
      }
      // TODO schöner/eleganter umsetzen
      selectbox.onchange = function(event) {
        if (this.value) {
          console.log(scope.fieldToComplete);
          scope.fieldToComplete.value = this.value;
        }
      };
      jQuery('#' + scope.fieldToComplete.id).parent().append(selectbox);

      // we got options, now we have to display them
      // also, we need to make them clickable/choosable
    }
  };

  this.fieldToComplete.onchange = function(event) {
    scope.loadData();
  };
}

