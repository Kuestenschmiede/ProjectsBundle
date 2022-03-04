/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2022, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

/**
 * Eden
 * @version   php 7
 * @package   eden
 * @author    eden authors (see "authors.txt")
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */
function ConditionalFieldDisplay(moduleId) {

  // The field(s) to switch
  this.fields = [];

  // The id of the server module
  this.moduleId = moduleId;

  // The property which determines the display state of the fields
  this.condition = false;

  // This is the value for the condition, at which the fields will be displayed normally
  this.valueForDisplay = 0;

  this.isDisplayed = true;

  var scope = this;

  this.create = function(fields, condition, valueForDisplay, isMandatory) {
    this.fields = fields;
    if (!condition) {
      return;
    }
    // condition should be an input field with some kind of value
    this.condition = condition;
    this.valueForDisplay = valueForDisplay;
    this.condition.callbacks = this.condition.callbacks || [];
    var callback = function() {
      var value = scope.condition.value;
      // checkbox workaround
      if (scope.condition.type === "checkbox") {
        value = scope.condition.checked ? 1 : 0;
        // these are number values (1|0) when we have a checkbox
        scope.isDisplayed = value == scope.valueForDisplay;
      } else {
        scope.isDisplayed = value === scope.valueForDisplay;
      }
      return setFieldDisplay(scope.fields, scope.isDisplayed ? "" : "none", isMandatory);
    };
    this.condition.callbacks.push(callback);
    this.condition.onchange = function(event) {
      var changes = {};
      for (var key in this.callbacks) {
        if (this.callbacks.hasOwnProperty(key)) {
          var value = this.value;
          if (value) {
            jQuery.extend(changes, this.callbacks[key]());
          }
        }
      }
      sendChanges(changes);
    };
  };
  var setFieldDisplay = function(fieldlist, value, isMandatory) {
    var field,
        change = {},
        changes = {};
    for (var i = 0; i < fieldlist.length; i++) {
      field = fieldlist[i];
      field.style.display = value;
      if (value === "none") {
        if (field.childNodes && isMandatory) {
          for (var key in field.childNodes) {
            if (field.childNodes.hasOwnProperty(key)) {
              if (field.childNodes[key].tagName === 'INPUT') {
                field.childNodes[key].removeAttribute('required');
                change['display'] = false;
                changes[field.childNodes[key].id] = change;
              }
            }
          }
        }
      } else {
        if (field.childNodes && isMandatory) {
          for (var keey in field.childNodes) {
            if (field.childNodes.hasOwnProperty(keey)) {
              if (field.childNodes[keey].tagName === 'INPUT') {
                field.childNodes[keey].setAttribute('required', true);
                change['display'] = true;
                changes[field.childNodes[keey].id] = change;
              }
            }
          }
        }
      }
    }
    return changes;
  };

  var sendChanges = function(changes) {
    var ajaxUrl = 'con4gis/brick_ajax_api/';
    ajaxUrl = ajaxUrl + scope.moduleId + '/C4GChangeFieldAction';
    jQuery.post(ajaxUrl, changes, function(data) {
    });
  }
}
var initDisplayConditions = function(moduleId) {
  var field, condition, displayValue;
  var fields = document.querySelectorAll('[data-condition-field]');
  var conditions = [];
  for (var key in fields) {
    if (fields) {
      if (fields.hasOwnProperty(key)) {
        field = fields[key];
        if (!field.hasAttribute('data-condition-handled')) {
          condition = field.getAttribute('data-condition-field');
          displayValue = field.getAttribute('data-condition-value');
          var conditional = new ConditionalFieldDisplay(moduleId);
          conditional.create([field], document.getElementById(condition), displayValue, true);
          for (var i = 0; i <= conditions.length; i++) {
            if (conditions[i] && conditions[i] === condition) {
              // field already added to array
              break;
            } else if (i === conditions.length) {
              conditions.push(condition);
              break;
            }
          }
          field.setAttribute('data-condition-handled', true);
        }
      }
    }
  }
  for (i = 0; i < conditions.length; i++) {
    document.getElementById(conditions[i]).onchange();
  }
};
