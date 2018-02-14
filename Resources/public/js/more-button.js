"use strict";

var $ = jQuery;

function showOptions(button) {
  var currentChild,
    childElement,
    childLink,
    container,
    list,
    children,
    i;
  console.log(button);
  event.stopPropagation();
  container = document.createElement('div');
  list = document.createElement('ul');
  list.style.listStyleType = 'none';
  children = $(button).children();
  console.log(children);
  for (i = 0; i < children.length; i++) {
    currentChild = children[i];
    childElement = document.createElement('li');
    childLink = document.createElement('a');
    childLink.innerHTML = currentChild.innerHTML;
    childLink.setAttribute('data-index', currentChild.getAttribute('data-index'));
    $(childLink).on('click', function(event) {
      event.stopPropagation();
      selectOption(this, button.parentNode.getAttribute('data-action'));
    });
    childElement.appendChild(childLink);
    list.appendChild(childElement);
  }
  container.appendChild(list);
  container.style.backgroundColor = 'lightgrey';
  container.style.zIndex = '500';
  // add options menu
  button.parentNode.appendChild(container);
  button.onclick = function(event) {
    event.stopPropagation();
    button.parentNode.removeChild(container);
    this.onclick = function(event) {
      event.stopPropagation();
      showOptions(this);
    };
  };
}

/**
 * Executes an ajax call
 */
function selectOption(childLink, dataAction) {
  var jqGui = c4g.projects.C4GGui;
  var url = jqGui.options.ajaxUrl + '/' + jqGui.options.moduleId;
  url += '/morebutton:' + dataAction.slice(-1) + ':' + childLink.getAttribute('data-index');
  console.log(url);
  $.ajax({
    url: url
  }).done(function(data) {
    jqGui.fnHandleAjaxResponse(data, jqGui.internalId ? jqGui.internalId : jqGui.options.id);
  });

}

