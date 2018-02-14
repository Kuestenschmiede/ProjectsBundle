"use strict";

var $ = jQuery;

function showOptions(button, fieldName) {
  var currentChild,
    childElement,
    childLink,
    container,
    list,
    children,
    i;

  event.stopPropagation();
  container = document.createElement('div');
  list = document.createElement('ul');
  list.style.listStyleType = 'none';
  children = $(button).children();
  for (i = 0; i < children.length; i++) {
    currentChild = children[i];
    childElement = document.createElement('li');
    childLink = document.createElement('a');
    childLink.innerHTML = currentChild.innerHTML;
    childLink.setAttribute('data-index', currentChild.getAttribute('data-index'));
    $(childLink).on('click', function(event) {
      event.stopPropagation();
      if (button.parentNode.getAttribute('data-action')) {
        // case for tiles
        selectOption(this, fieldName, button.parentNode.getAttribute('data-action'));
      } else {
        selectOption(this, fieldName);
      }
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
function selectOption(childLink, fieldName, dataAction) {
  var id;
  var jqGui = c4g.projects.C4GGui;
  var url = jqGui.options.ajaxUrl + '/' + jqGui.options.moduleId;
  if (dataAction) {
    // tiles
    id = dataAction.slice(-1);
  } else if (childLink) {
    // table
    var data = jqGui.dataTableApi.row($(childLink).parents('tr')).data();
    // assuming the content of the first column is always like click:id
    if (data) {
      id = data[0].split(':')[1];
    } else {
      var enclosingDiv = childLink.parentNode.parentNode.parentNode.parentNode;
      var arrId = enclosingDiv.id.split('_');
      id = arrId[arrId.length - 1];
    }
  }
  if (id) {
    url += '/morebutton_' + fieldName + ':' + id + ':' + childLink.getAttribute('data-index');
    $.ajax({
      url: url
    }).done(function (data) {
      jqGui.fnHandleAjaxResponse(data, jqGui.internalId ? jqGui.internalId : jqGui.options.id);
    });
  }
}

