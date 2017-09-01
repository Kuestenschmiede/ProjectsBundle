/**
 * Created by cro on 17.02.2017.
 */
// "namespace"
this.c4g = this.c4g || {};

(function ($, c4g) {
  'use strict';

  c4g.maps = c4g.maps || {};
  c4g.plugin = c4g.plugin || {};

  c4g.plugin.TabControl = function(customTab) {
    this.customTab = customTab;
    // map proxy shortcut
    this.proxy = this.customTab.editor.proxy;
    // we store the layers of all elements contained in the project
    this.projectLayers = [];
  };

  $.extend(c4g.plugin.TabControl.prototype, {

    /**
     * Shows a former invisble layer in the tabLayerGroup
     * @param layerId
     * @param feature The feature of the layer
     */
    showLayer: function(layerId, feature) {
      var layer, addLayer, tabFeatures;

      if (this.proxy.checkLayerIsActiveForZoom(layerId)) {
        layer = c4g.maps.layers[layerId];
        if (layer && layer.vectorLayer) {
          addLayer = true;
          // loop over tabFeatures so prevent adding the same layer multiple times
          tabFeatures = this.customTab.tabPointLayer.getSource().getFeatures();
          tabFeatures.forEach(function (element, index, array) {
            if (element === feature) {
              addLayer = false;
            }
          });
          if (addLayer) {
            this.customTab.tabPointLayer.getSource().addFeature(feature);
            feature.set('active', true);
            this.proxy.activeLayerIds[layerId] = "visible";
          }
          //c4g.maps.utils.callHookFunctions(this.proxy.hook_layer_visibility, layerId);
          return true;
        } else {
          return false;
        }
      } else {
        return false;
      }
    },

    /**
     * Hides a former visible layer in the tabLayerGroup
     * @param layerId
     * @param feature The feature of the layer
     */
    hideLayer: function(layerId, feature) {
      if (this.proxy.activeLayerIds[layerId] === "visible") {
        this.proxy.options.mapController.map.removeLayer(c4g.maps.layers[layerId]);
        console.log(feature);
        this.customTab.tabPointLayer.getSource().removeFeature(feature);
        feature.set('active', false);
        delete this.proxy.activeLayerIds[layerId];
        //c4g.maps.utils.callHookFunctions(this.proxy.hook_layer_visibility, layerId);
      }
    },

    /**
     * Transfers the layers, which are part of the current project of the editortab, from the map source to the
     * tabLayerGroup, so they can be edited.
     */
    makeVisibileForEditing: function() {
      var currentProject,
          projectLayers,
          uid,
          layerFeatures,
          currentItem,
          layers,
          fnAddProjectChilds,
          i,
          starboardItems,
          self = this;

      // layers shortcut
      layers = c4g.maps.layers;
      currentProject = this.customTab.currentProject;

      fnAddProjectChilds = function(array, parent) {
        array.push(parent);
        if (parent.hasChilds) {
          for (var i = 0; i < parent.childsCount; i++) {
            fnAddProjectChilds(array, parent.childs[i]);
          }
        }
      };
      // find all layers for the current project
      if (layers[currentProject.projectId]) {
        projectLayers = [];
        // layers[uid] is the layer representation of the current project
        fnAddProjectChilds(projectLayers, layers[currentProject.projectId]);
      }
      // remove them from the map
      for (i = 0; i < projectLayers.length; i++) {
        if (projectLayers[i].vectorLayer) {
          this.proxy.options.mapController.map.removeLayer(projectLayers[i].vectorLayer);
          this.projectLayers.push(projectLayers[i]);
        }
      }
      // collect all features from the layers
      var features = new ol.Collection();
      var newLayers = new ol.Collection();
      for (i = 0; i < this.projectLayers.length; i++) {
        if (this.projectLayers[i].vectorLayer) {
          this.projectLayers[i].vectorLayer.getLayers().forEach(function(element, index, array) {
            element.getSource().forEachFeature(function(feature) {
              feature.set("onEditLayer", true);
              features.push(feature);
            });
            newLayers.push(element);
          });
        }
      }
      var oldCollection = this.customTab.tabLayerGroup;

      // add the features to the tabPointLayer
      features.forEach(function(element, index, array) {
        try {
          if (element.get('active')) {
            this.customTab.tabPointLayer.getSource().addFeature(element);
          }
        } catch (assertionerror) {
          console.log(assertionerror.message);
          console.warn("An error has occurred while adding the features to the editor source...");
        }

      }, this);
    },

    /**
     * Reverses the makeVisibileForEditing function. So it transfers the layers from the tabLayerGroup to the map
     * source, so the normal proxy switching mechanics are able to work.
     */
    makeInvisibleForEditing: function() {
      var currentProject,
          projectLayers,
          uid,
          layerFeatures,
          currentItem,
          layers,
          fnAddProjectChilds,
          i,
          starboardItems,
          self = this;

      // layers shortcut
      layers = c4g.maps.layers;
      currentProject = this.customTab.currentProject;

      fnAddProjectChilds = function(array, parent) {
        array.push(parent);
        if (parent.hasChilds) {
          for (var i = 0; i < parent.childsCount; i++) {
            fnAddProjectChilds(array, parent.childs[i]);
          }
        }
      };
      // find all layers for the current project
      if (layers[currentProject.projectId]) {
        projectLayers = [];
        // layers[uid] is the layer representation of the current project
        fnAddProjectChilds(projectLayers, layers[currentProject.projectId]);
      }
      // remove them from the map
      for (i = 0; i < projectLayers.length; i++) {
        if (projectLayers[i].vectorLayer) {
          this.proxy.options.mapController.map.addLayer(projectLayers[i].vectorLayer);
        }
      }
      // collect all features from the layers
      var features = new ol.Collection();
      var newLayers = new ol.Collection();
      for (i = 0; i < projectLayers.length; i++) {
        if (projectLayers[i].vectorLayer) {
          projectLayers[i].vectorLayer.getLayers().forEach(function(element, index, array) {
            element.getSource().forEachFeature(function(feature) {
              feature.set("onEditLayer", false);
              features.push(feature);
            });
            newLayers.push(element);
          });
        }
      }
      var oldCollection = this.customTab.tabLayerGroup;
      features = this.customTab.tabPointLayer.getSource().getFeatures();
      // add the features to the tabPointLayer
      features.forEach(function(element, index, array) {
        try {
          this.customTab.tabPointLayer.getSource().removeFeature(element);
        } catch (assertionerror) {
          console.warn("An error has occurred while removing the features to the editor source...");
        }
      }, this);
    }

  });

}(jQuery, this.c4g));