"use strict";

var $ = jQuery;
function toggleContainer(button) {
  event.stopPropagation();
  console.log(button);
  var containerId = button.id + '_container';
  var container = document.getElementById(containerId);
  if (container.style.visibility === 'hidden') {
    container.style.visibility = 'visible';
  } else {
    container.style.visibility = 'hidden';
  }
}

function executeSelection(span) {
  event.stopPropagation();
  console.log(span);
  var gui = c4g.projects.C4GGui;
  var url = gui.options.ajaxUrl + '/' + gui.options.moduleId + '/' + span.getAttribute('href');
  $.ajax({
    url: url
  }).done(function (data) {
    gui.fnHandleAjaxResponse(data, gui.internalId ? gui.internalId : gui.options.id);
  });
}

