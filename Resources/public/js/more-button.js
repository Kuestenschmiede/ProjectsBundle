"use strict";

function toggleContainer(button) {
  event.stopPropagation();
  var containerId = button.id + '_container';

  var container = document.getElementById(containerId);
  if (jQuery(container).hasClass('c4g_more_button_mode_table')) {
    animateTableMode(button, container);
  } else if (jQuery(container).hasClass('c4g_more_button_mode_tiles')) {
    animateTileMode(button, container);
  } else if (jQuery(container).hasClass('c4g_more_button_mode_entry_tiles')) {
    // if (container.style.display === 'none') {
    //   container.style.display = 'inline-block';
    // } else {
    //   container.style.display = 'none';
    // }
  } else {
    if (container.style.display === 'none') {
      container.style.display = 'block';
    } else {
      container.style.display = 'none';
    }
  }

  var containers = document.getElementsByClassName('c4g_more_button_container');
  jQuery.each(containers, function(key, value) {
    if (value.id !== containerId) {
      value.style.display = 'none';
    }
  });
}

/**
 * Handles the rendering of the button if the rendermode is table.
 * @param button
 * @param container
 */
function animateTableMode(button, container) {
  var x,y;
  jQuery(container.parentElement).css({position: 'relative'});
  if (container.style.display === 'none') {
    container.style.display = 'block';
    x = jQuery(button).offset().left - button.offsetWidth * (container.offsetWidth / button.offsetWidth);
    y = jQuery(button).offset().top;
    jQuery(container).offset({ top: y, left: x });
  } else {
    container.style.display = 'none';
  }
}

/**
 * Handles the rendering of the button if the rendermode is tiles.
 * @param button
 * @param container
 */
function animateTileMode(button, container) {
  var x,y;

  if (container.style.display === 'none') {
    container.style.display = 'block';
    container.style.position = 'relative';
    // x = jQuery(button).offset().left - button.offsetWidth * (container.offsetWidth / button.offsetWidth);
    x = jQuery(button).offset().left - button.offsetWidth * (2.75);
    var tileWidth = container.parentElement.offsetWidth;
    var marginLeft = (tileWidth - container.offsetWidth) / 2;
    x = jQuery(container.parentElement).offset().left + marginLeft;
    y = jQuery(button).offset().top + button.offsetHeight;
    jQUery(container).offset({ top: y, left: x });
  } else {
    container.style.display = 'none';
  }
}

function executeSelection(span, event) {
  if (typeof(event) !== 'undefined') {
    event.stopPropagation();
  }
  var gui = c4g.projects.C4GGui;
  var url = gui.options.ajaxUrl + '/' + gui.options.moduleId + '/' + span.getAttribute('href');
  jQuery.ajax({
    url: url
  }).done(function (data) {
    gui.fnHandleAjaxResponse(data, gui.options.moduleId);
  });
}