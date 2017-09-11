// "namespace"
this.c4g = this.c4g || {};
this.c4g.maps = this.c4g.maps || {};
this.c4g.maps.plugins = this.c4g.maps.plugins || {};

(function ($, c4g) {
  'use strict';

  var plugin,
      apiBaseUrl,
      editorTabApiUrl,
      refreshOnDelete,
      sendChangeToServer,
      spinner;

  // check hook object
  c4g.maps.hook = c4g.maps.hook || {};
  plugin = {};
  plugin.tabData = {};
  plugin.customTabs = {};
  plugin.editorViews = {};
  plugin.featuresPerTab = {};
  plugin.starboardTabs = {};
  plugin.layersFromDBPerTab = {};
  plugin.projects = [];
  plugin.categories = {};
  plugin.elements = {};
  plugin.elementsLoaded = false;
  plugin.loadedCounters = {};
  apiBaseUrl = 'src/con4gis/CoreBundle/Resources/contao/api/index.php';
  editorTabApiUrl = apiBaseUrl + '/editorTabService';

  /**
   * Start of method definitions
   */
  plugin.functions = {
    updateFeatureCounter: function (styleId, value, mode, element) {
      var newCounter;

      if (element) {
        switch (mode) {
          case '+':
          case 'inc':
          case 'increment':
            newCounter = element.count + value;
            break;
          case '-':
          case 'dec':
          case 'decrement':
            newCounter = element.count - value;
              if (newCounter < 0) {
                newCounter = 0;
              }
            break;
          case 'set':
            newCounter = value;
            break;
          default:
            // do nothing
            break;
        }

        if (element.limit < 0 || (newCounter <= element.limit)) {
          element.count = newCounter;
          element.elem = element.elem || document.createElement('span');
          element.elem.innerHTML = (element.limit < 0) ? '' : element.limit - element.count;
          element.elem.title = (element.limit < 0) ? '' : element.count + '/' + element.limit;
          element.elem.title = element.name + ' ' + element.elem.title;
          if (element.limit >= 0 && element.interaction && element.interaction.getActive() && (element.count >= element.limit)) {
            element.interaction.setActive(false);
          }
          plugin.functions.updateStyle(styleId, element);
          // maybe delete later, save is called elsewhere
          //plugin.editor.save();
          return true;
        } else if (element.interaction && element.interaction.getActive()) {
          element.interaction.setActive(false);
        }
      }

      return false;
    }, // end of "updateFeatureCounter()"
    /**
     * This function checks if the limit for a given feature is reached, and changes its appearance to grey, if true
     * @param styleId
     * @param feature
     */
    updateStyle: function(styleId, feature) {
      var style,
          parentElem;

      parentElem = feature.elem.parentElement;
      if (parentElem === null) {
        return false;
      }
      if (c4g.maps.locationStyles[styleId]) {
          style = c4g.maps.locationStyles[styleId].style()[0];
          if (style && !style.getImage() instanceof ol.style.Icon) {
              if (feature.limit > 0 && (feature.count == feature.limit)) {
                  // Limit has reached, set style of feature to grey
                  parentElem.style.background = 'grey';
                  parentElem.style.border = '1px solid grey';
              } else {
                  // There are features left to draw, so set normal style
                  parentElem.style.background = style.getFill().getColor();
                  parentElem.style.border = '1px solid ' + style.getStroke().getColor();
              }
          }
      }

    }, // end of "updateStyle()"

    getVectorLayer: function (source, style) {
      var fnStyle;

      // make sure that the style is a function
      if (typeof style === 'function') {
        fnStyle = style;
      } else if (style !== undefined) {
        fnStyle = function () {
          return style;
        };
      }

      return new ol.layer.Vector({
        source: source,
        style: fnStyle
      });
    }, // end of "getVectorLayer()"

    sendCreatedElementToServer: function(layer, mapController, element) {
      var copiedLayer = {};
      var self = this;
      // copy so we don't delete the vectorLayer property in the real layer
      for (var key in layer) {
        if (layer.hasOwnProperty(key) && key !== "vectorLayer") {
          copiedLayer[key] = layer[key];
        }
      }
      copiedLayer.element = element.id;
      copiedLayer.category = element.cid;
      mapController.spinner.show();
      $.ajax({
        url: editorTabApiUrl,
        method: "POST",
        dataType: 'json',
        data: {layer: copiedLayer}
      }).always(function(data) {
        mapController.spinner.hide();
      }).done(function(data) {
        // update layer with result from server
        layer = self.updateLayer(data, layer);
        // needed because the reference is not correct
        if (layer) {
            c4g.maps.layers[layer.id] = layer;
        }
      });
    }, // end of "sendCreatedElementToServer()"

    updateLayer: function(layerUpdate, layer) {
      var i,
          j,
          updatedLayer,
          updates,
          setPopup = false,
          label;

      for (i = 0; i < layerUpdate.length; i++) {
        if (layerUpdate[i].layer && layerUpdate[i].updatedProperties) {
          updatedLayer = layerUpdate[i].layer;
          updates = layerUpdate[i].updatedProperties;
          for (j = 0; j < updates.length; j++) {
            // check the type of the layer update
            // this switch statement has to be extended if more than the current options should be updated
            switch(updates[j]) {
              case 'popup':
                layer.content[0].data.properties.popup = updatedLayer.content[0].data.properties.popup;
                setPopup = true;
                break;
              case 'type':
                layer.type = updatedLayer.type;
                break;
              case 'name':
                layer.name = updatedLayer.name;
                break;
              case 'tooltip':
                  layer.vectorLayer.getLayers().forEach(function(element, index, array) {
                    element.getSource().forEachFeature(function(feature) {
                      feature.set('tooltip', updatedLayer.tooltip);
                    });
                    element.set('tooltip', updatedLayer.tooltip);
                  });
                //layer.tooltip = updatedLayer.tooltip;
                break;
              case 'settings':
                layer.content[0].settings = updatedLayer.content[0].settings;
                break;
              case 'label':
                label = updatedLayer.content[0].data.properties.label;
                layer.content[0].data.properties.label = label;
                layer.vectorLayer.getLayers().forEach(function(element, index, array) {
                  element.getSource().forEachFeature(function(feature) {
                    var oldStyleFunction = feature.getStyleFunction();
                    var oldStyle = oldStyleFunction()[0];
                    var textStyle = new ol.style.Text({
                      font: '13px sans-serif',
                      offsetX: 0,
                      offsetY: 0,
                      scale: 1,
                      rotateWithView: false,
                      rotation: 0,
                      text: label,
                      textAlign: 'center',
                      textBaseline: 'middle',
                      fill: new ol.style.Fill({
                        color: c4g.maps.utils.getRgbaFromHexAndOpacity('#ee0016', {
                          unit: '%',
                          value: 100
                        })
                      })
                    });
                    oldStyle.setText(textStyle);
                    feature.setStyle(oldStyle);
                  });
                });
                break;
              default:
                // do nothing
            }
          }
        }
      }
      if (setPopup) {
        c4g.maps.hook.proxy_fillPopup = c4g.maps.hook.proxy_fillPopup || [];
        c4g.maps.hook.proxy_fillPopup.push(function(object) {
          var objLayer,
              currentLayer;

          // find layer in c4g.maps.layers for the clicked feature
          for (var key in c4g.maps.layers) {
            if (c4g.maps.layers.hasOwnProperty(key)) {
              currentLayer = c4g.maps.layers[key];
              if (currentLayer.vectorLayer) {
                currentLayer.vectorLayer.getLayers().forEach(function(element, index, array) {
                  if (element === object.layer) {
                    objLayer = currentLayer;
                  }
                });
              }
            }
          }
          // if layer exists, set the popup
          if (objLayer) {
            if (objLayer.id == layer.id) {
              var popup = {};
              popup.content = layer.content[0].data.properties.popup;
              popup.async = false;
              popup.routing_link = "1";
              object.popup = popup;
              object.feature.set('popup', popup);
            }
          }
        });
      }
      return layer;
    }
  };

  /**
   * This function is called everytime a feature was deleted. It handles the feature counter updates.
   * @param objParam
   */
  refreshOnDelete = function (objParam) {
    var styleId,
        uid,
        eid,
        catId,
        projectId;

    if (objParam.action === 'deleted' && typeof objParam.feature.get === 'function') {
      styleId = objParam.feature.get('styleId');
      projectId = objParam.feature.get('projectId');
      uid = objParam.feature.get('uid');
      eid = objParam.feature.get('eid');
      catId = objParam.feature.get('catId');
      if (plugin.elements[projectId + '-' + catId + '-' + eid]) {
        plugin.functions.updateFeatureCounter(styleId, 1, '-', plugin.elements[projectId + '-' + catId + '-' + eid]);
      }
    }
  }; // end of "refreshOnDelete()"

  c4g.maps.hook.editor_featureChanged = c4g.maps.hook.editor_featureChanged || [];
  c4g.maps.hook.editor_featureChanged.push(refreshOnDelete);

  /**
   * Calls the C4GEditorTabApi and loads the custom editor configuration. Highly influenced by various extensions.
   */
  var loadEditorData = function(params) {
    var proxy = params.proxy;
    var layer, layersSend = [];
    var currentApiUrl;
    currentApiUrl = editorTabApiUrl;
    plugin.editor = proxy.options.mapController.controls.editor;
    for (var key in c4g.maps.layers) {
      if (c4g.maps.layers.hasOwnProperty(key)) {
        layer = c4g.maps.layers[key];
        currentApiUrl = editorTabApiUrl;
        if (layer.type === "startab") {
          currentApiUrl += '/';
          currentApiUrl += layer.id;
          $.get(
              currentApiUrl,
            //layersSend,
            function(editorTabs) {
              var tabskey,
                  projectskey,
                  data,
                  editorTab,
                  project,
                  projects = [],
                  currentTab,
                  tabId;

              // iterate all editortabs
              for (tabskey in editorTabs) {
                if (editorTabs.hasOwnProperty(tabskey)) {
                  // for each editortab, collect projects
                  editorTab = editorTabs[tabskey];
                  if (!editorTab) {
                    continue;
                  }
                  tabId = editorTab['tabId'];
                  for (projectskey in editorTab['projects']) {
                    if (editorTab['projects'].hasOwnProperty(projectskey)) {
                      project = editorTab['projects'][projectskey];
                      plugin.projects.push(project);
                      projects.push(project);
                    }
                  }

                  // and create a customTab object
                  currentTab = new c4g.plugin.CustomTab(proxy.options.mapController.controls.editor,
                      {tabConfig:
                        {
                          id: tabId,
                          headline: editorTab.name,
                          withButton: editorTab.withNewButton,
                          button: editorTab.newButton
                        }
                      },
                      plugin, projects);
                  // add the editorview
                  currentTab.addEditorView();

                  if (proxy.options.mapController.controls.starboard.initialized) {
                    // if starboard is already loaded, call the hook functions
                    var starboardtab,
                        sbPlugin,
                        starboard;

                    starboard = proxy.options.mapController.controls.starboard;
                    for (var key in starboard.plugins) {
                      if (starboard.plugins.hasOwnProperty(key)) {
                        sbPlugin = starboard.plugins[key];
                        if (sbPlugin.tabId && sbPlugin.tabId == tabId) {
                          starboardtab = sbPlugin;
                          currentTab.setStarboardControl(new c4g.plugin.Starboardcontrol(starboard, starboardtab, plugin));
                        }
                      }
                    }
                  } else {
                    // add starboard controller creation to starboard hooks
                    c4g.maps.hook.starboard_loadPlugins = c4g.maps.hook.starboard_loadPlugins || [];
                    c4g.maps.hook.starboard_loadPlugins.push(function sbControlAdd(starboard) {
                      var starboardtab,
                          sbPlugin;

                      for (var key in starboard.plugins) {
                        if (starboard.plugins.hasOwnProperty(key)) {
                          sbPlugin = starboard.plugins[key];
                          if (sbPlugin.tabId && plugin.customTabs[sbPlugin.tabId]) {
                            starboardtab = sbPlugin;
                            plugin.customTabs[sbPlugin.tabId].setStarboardControl(new c4g.plugin.Starboardcontrol(starboard, starboardtab, plugin));
                          }
                        }
                      }
                      c4g.maps.hook.starboard_loadPlugins.splice(c4g.maps.hook.starboard_loadPlugins.indexOf(sbControlAdd), 1);
                    });
                  }
                  // and add the editor tab to the plugin object
                  plugin.customTabs[tabId] = currentTab;
                }
              }
            }
          );
        }

      }
    }

  };
  // add loader function to onLayerLoad callbacks
  c4g.maps.hook.proxy_layer_loaded = c4g.maps.hook.proxy_layer_loaded || [];
  c4g.maps.hook.proxy_layer_loaded.push(loadEditorData);

  ///**
  // * Stores the element counter states into the editor save slot.
  // */
  //var saveCallback = function(saveData) {
  //  var elements;
  //
  //  saveData.elementCounters = {};
  //  if (plugin.elementsLoaded) {
  //    //saveData.elements = plugin.elements;
  //    elements = plugin.elements;
  //    for (var key in elements) {
  //      if (elements.hasOwnProperty(key)) {
  //        saveData.elementCounters[key] = elements[key].count;
  //      }
  //    }
  //  }
  //};
  //c4g.maps.hook.editor_onSave = c4g.maps.hook.editor_onSave || [];
  //c4g.maps.hook.editor_onSave.push(saveCallback);

  ///**
  // * Loads the element counter states when the editor is dynamically loaded. Needed for keeping the correct
  // * counters after page reload, where the editor features are kept too.
  // */
  //var loadCallback = function(loadData) {
  //  c4g.maps.hook.elements_Loaded = c4g.maps.hook.elements_Loaded || [];
  //  c4g.maps.hook.elements_Loaded.push(function() {
  //    var curElem;
  //
  //    for (var key in loadData.elementCounters) {
  //      if (loadData.elementCounters.hasOwnProperty(key)) {
  //        curElem = plugin.elements[key];
  //        if (curElem && !plugin.loadedCounters[key]) {
  //          plugin.functions.updateFeatureCounter(curElem.styleId, loadData.elementCounters[key], 'set', curElem);
  //          plugin.loadedCounters[key] = true;
  //        }
  //      }
  //    }
  //  });
  //};
  //c4g.maps.hook.editor_onLoad = c4g.maps.hook.editor_onLoad || [];
  //c4g.maps.hook.editor_onLoad.push(loadCallback);

  c4g.maps.plugins.projects = plugin;

}(jQuery, this.c4g));
