
// "namespace"
this.c4g.maps = this.c4g.maps || {};
this.c4g.plugin = this.c4g.plugin || {};

(function ($, c4g) {

  /**
   * Constructor for a starboardcontroller.
   * One Starboardcontroller exists for each starboardtab, except the normal layer- and baselayerswitcher.
   * Each Starboardcontroller corresponds to an editortab, and is called when an element from a project is
   * drawn onto the map. Used for making a new entry to the correct level of the tree, deleting and renaming entries.
   * @param starboard
   * @param starboardtab
   * @param plugin
   * @constructor
   */
  c4g.plugin.Starboardcontrol = function(starboard, starboardtab, plugin) {
    this.starboard = starboard;
    this.starboardtab = starboardtab;
    this.plugin = plugin;
    this.featureLayerMap = {};
    this.requestForShow = {};

    var self = this;

    //this.plugin.editor.preOpenFunction = function() {
    //  self.overrideSwitchLayers();
    //};
    //this.plugin.editor.preCloseFunction = function() {
    //  self.changeSwitchingBack();
    //};
  };

  // add methods
  $.extend(c4g.plugin.Starboardcontrol.prototype, {

    /**
     * Finds the parent element where the newly created instance of the element must be placed.
     * @param project
     * @param category
     * @param element
     * @param tabId
     * @return the HTML parent
     */
    findElement: function(project, category, element, tabId) {
      var layers = this.starboardtab.layers;
      var layer,
          exProject,
          exCategory,
          exElement,
          mapLayers,
          id,
          childLayer,
          i,
          categoryFound = false,
          projectFound = false,
          projectName;

      projectName = project.starboardName;
      mapLayers = c4g.maps.layers;
      for (id in mapLayers) {
        if (mapLayers.hasOwnProperty(id)) {
          layer = mapLayers[id];
          if (layer.id === tabId) {
            // layer is the correct tab
            childLayer = layer.childs[0];
            // childLayer is the project type (e.g. "Planungsprojekte")
            break;
          }
        }
      }
      if (childLayer && childLayer.childs) {
        for (i = 0; i < childLayer.childs.length; i++) {
          if (childLayer.childs[i].name === projectName) {
            // project found
            exProject = childLayer.childs[i];
            projectFound = true;
            break;
          }
        }
        if (exProject && exProject.childs) {
          for (i = 0; i < exProject.childs.length; i++) {
            if (exProject.childs[i]) {
              if (exProject.childs[i].name === category.name) {
                // correct category in project found
                exCategory = exProject.childs[i];
                categoryFound = true;
                break;
              }
            }
          }
        }
        if (exProject && exCategory) {
          for (i = 0; i < exCategory.childs.length; i++) {
            if (exCategory.childs[i].name === element.name) {
              // correct category in project found
              exElement = exCategory.childs[i];
              break;
            }
          }
        }
        if (!projectFound) {
          // project does not exist and has to be created
          exProject = this.createProject(project, childLayer);
        }
        if (!categoryFound) {
          // category does not exist and has to be created
          // this is for the case that there is not the normal project->category->element structure, like
          // the stations in the rescuemap
          exCategory = this.createCategory(exProject, category);
          return exCategory;
        }
        if (exElement) {
          return exElement;
        }
        if (exCategory) {
          return exCategory;
        }
      } else if (childLayer) {
        return this.createProject(project, childLayer);
      }
    },

    /**
     * This function creates a project and adds the entry to the starboard, if the entry is not existing.
     * @param project
     * @param childLayer
     */
    createProject: function(project, childLayer) {
      var newProject = {};

      newProject.name = project.name;
      newProject.id = c4g.maps.utils.getUniqueId();
      newProject.key = newProject.id;
      newProject.pid = childLayer.id;
      newProject.childs = [];
      newProject.childsCount = 0;
      newProject.display = true;
      newProject.editable = true;
      newProject.hide = "";
      newProject.isInactive = false;
      newProject.layername = project.name;
      newProject.renderSpecial = true;
      newProject.tabId = childLayer.tabId;
      newProject.content = null;

      console.log("project created");
      c4g.maps.layers[newProject.id] = newProject;
      childLayer.childs = childLayer.childs || [];
      childLayer.childs.push(newProject);
      childLayer.childsCount++;
      // show the starboard tab
      //this.starboard.plugins["customTab" + newProject.tabId].activate();
      this.plugin.editor.proxy.layerIds.push(newProject.id);
      //if (childLayer.hide_when_in_tab) {
      //  this.addCategoryToTree(newProject, childLayer.pid);
      //} else {
      //  this.addCategoryToTree(newProject);
      //}
      return newProject;
    },

    createCategory: function(projectLayer, category) {
      var newCategory = {};

      newCategory.name = category.name;
      newCategory.id = c4g.maps.utils.getUniqueId();
      newCategory.key = newCategory.id;
      newCategory.pid = projectLayer.id;
      newCategory.childs = [];
      newCategory.childsCount = 0;
      newCategory.display = true;
      newCategory.editable = true;
      newCategory.hide = "";
      newCategory.isInactive = false;
      newCategory.layername = category.name;
      newCategory.renderSpecial = true;
      newCategory.tabId = projectLayer.tabId;
      newCategory.content = null;

      c4g.maps.layers[newCategory.id] = newCategory;
      projectLayer.childs = projectLayer.childs || [];
      projectLayer.childs.push(newCategory);
      projectLayer.childsCount++;
      this.plugin.editor.proxy.layerIds.push(newCategory.id);
      //this.addCategoryToTree(newCategory);
      return newCategory;
    },

    /**
     * Inserts the new instance of an element into the layertree of the starboardtab.
     * @param project
     * @param category
     * @param element
     * @param tabId
     * @param feature
     */
    insertNewInstance: function(project, category, element, tabId, feature) {
      var layer,
          newId,
          newName,
          listItem,
          entry,
          item,
          uid,
          $entry,
          handleEntryClick,
          self,
          parent,
          vectorLayer,
          layerGroup,
          childWrapper;

      self = this;
      //if (!this.starboard.options.mapController.activeStarboard) {
      //  this.starboard.open();
      //}
      //this.starboard.plugins["customTab" + tabId].activate();
      // create layer
      parent = this.findElement(project, category, element, tabId);
      newId = c4g.maps.utils.getUniqueId();
      newName = parent.childsCount + 1;
      layer = {
        childs: [],
        childsCount: 0,
        display: true,
        editable: true,
        hasChilds: false,
        hide: parent.hide,
        id: newId,
        isInactive: parent.isInactive,
        key: newId,
        layername: newName,
        name: newName,
        pid: parent.id,
        tabId: tabId,
        type: "single",
        renderSpecial: true,
        projectId: project.projectId
      };
      layer.content = this.createContent(feature, layer);
      // add vector layer
      feature.setStyle(c4g.maps.locationStyles[feature.get('styleId')].style);
      feature.set('tooltip', newName);
      vectorLayer = this.plugin.functions.getVectorLayer(new ol.source.Vector({features: [feature]}), feature.getStyle());
      layerGroup = new ol.layer.Group({layers: [vectorLayer]});
      layer.vectorLayer = layerGroup;
      // add new layer to existing data structures
      c4g.maps.layers[newId] = layer;
      if (parent.childs) {
          parent.childs.push(layer);
      }
      parent.childsCount++;
      parent.hasChilds = true;
      this.plugin.editor.proxy.layerIds.push(newId);
      // create on/off switcher
      //handleEntryClick = function(event) {
      //  var itemUid;
      //
      //  event.preventDefault();
      //  // "this" is the event sending entry
      //  itemUid = $(this).data('uid');
      //  if (self.plugin.editor.proxy.activeLayerIds[itemUid]) {
      //    // hide layer
      //    $(this).removeClass(c4g.maps.constant.css.ACTIVE);
      //    $(this).addClass(c4g.maps.constant.css.INACTIVE);
      //    self.starboardtab.hideLayer(itemUid);
      //  } else {
      //    // show layer
      //    $(this).removeClass(c4g.maps.constant.css.INACTIVE);
      //    $(this).addClass(c4g.maps.constant.css.ACTIVE);
      //    self.starboardtab.showLayer(itemUid);
      //  }
      //};
      //
      //// add element to layertree
      //uid = newId;
      //item = {};
      //this.starboardtab.layers[uid] = item;
      //listItem = document.createElement('li');
      //entry = document.createElement('a');
      //entry.setAttribute('href', '#');
      //entry.appendChild(document.createTextNode(layer.name));
      //listItem.appendChild(entry);
      //this.starboardtab.layers[layer.pid].entryWrappers.push(listItem);
      //this.starboardtab.layers[layer.pid].childWrappers[0].appendChild(listItem);
      //// add switch button
      //$entry = $(entry);
      //item.$entries = item.$entries || [];
      //item.$entries.push($entry);
      //$entry.data('uid', uid);
      //$entry.click(handleEntryClick);
      //// add css class
      //$entry.addClass(c4g.maps.constant.css.ACTIVE);
      //this.starboard.update();
      this.featureLayerMap[feature] = layer;
      if (this.requestForShow[feature]) {
        this.plugin.editor.proxy.showLayer(this.featureLayerMap[feature].id);
      }

      return layer;
    },

    addCategoryToTree: function(category, opt_pid) {
      var self = this,
          pWrapper,
          entry,
          $entry,
          listItem,
          item,
          childWrapper;


      var handleEntryClick = function(event) {
        var itemUid;

        event.preventDefault();
        // "this" is the event sending entry
        itemUid = $(this).data('uid');
        if (self.plugin.editor.proxy.activeLayerIds[itemUid]) {
          // hide layer
          $(this).removeClass(c4g.maps.constant.css.ACTIVE);
          $(this).addClass(c4g.maps.constant.css.INACTIVE);
          self.starboardtab.hideLayer(itemUid);
        } else {
          // show layer
          $(this).removeClass(c4g.maps.constant.css.INACTIVE);
          $(this).addClass(c4g.maps.constant.css.ACTIVE);
          self.starboardtab.showLayer(itemUid);
        }
      }; // end of handleEntryClick

      item = {};
      this.starboardtab.layers[category.id] = item;
      listItem = document.createElement('li');
      item.entryWrappers = item.entryWrappers || [];
      item.entryWrappers.push(listItem);
      entry = document.createElement('a');
      entry.setAttribute('href', '#');
      entry.appendChild(document.createTextNode(category.name));
      listItem.appendChild(entry);
      $entry = $(entry);
      item.$entries = item.$entries || [];
      item.$entries.push($entry);
      $entry.data('uid', category.id);
      $entry.click(handleEntryClick);

      // create toggle button to show childs
      var toggle = document.createElement('span');
      $(listItem).addClass(c4g.maps.constant.css.CLOSE);
      $(toggle).addClass(c4g.maps.constant.css.ICON);
      $(toggle).click(function () {
        if ($(this).parent().hasClass(c4g.maps.constant.css.CLOSE)) {
          $(this).parent().removeClass(c4g.maps.constant.css.CLOSE).addClass(c4g.maps.constant.css.OPEN);
        } else {
          $(this).parent().removeClass(c4g.maps.constant.css.OPEN).addClass(c4g.maps.constant.css.CLOSE);
        }
      });
      $(toggle).insertBefore($entry);
      childWrapper = document.createElement('ul');
      item.childWrappers = item.childWrappers || [];
      item.childWrappers.push(childWrapper);
      listItem.appendChild(childWrapper);

      if (opt_pid) {
        if (!this.starboardtab.layers[opt_pid]) {
          this.starboardtab.treeControl.children[0].appendChild(listItem);
          return;
        } else {
          pWrapper = this.starboardtab.layers[opt_pid].childWrappers;
        }
      } else {
        pWrapper = this.starboardtab.layers[category.pid].childWrappers;
      }
      pWrapper[pWrapper.length - 1].appendChild(listItem);
      this.starboard.update();
    },

    transferLayer: function(feature, editorSource) {
      try {
        editorSource.removeFeature(feature);
      } catch (error) {

      }
      if (!this.featureLayerMap[feature]) {
        this.requestForShow[feature] = true;
      } else {
        this.plugin.editor.proxy.showLayer(this.featureLayerMap[feature].id);
      }

    },

    /**
     * Creates an object for the content variable of a layer.
     * @param feature
     * @param layer
     */
    createContent: function(feature, layer) {
      var arrContent,
          objContent,
          data,
          properties,
          geometry,
          settings;

      arrContent = [];
      objContent = {};
      objContent.format = "GeoJSON";
      objContent.locationStyle = feature.get("styleId");
      objContent.id = c4g.maps.utils.getUniqueId();

      data = {};
      data.type = "Feature";
      properties = {};
      properties.graphicTitle = layer.name;
      properties.label = "";
      properties.projection = "EPSG:4326";
      properties.popup = {};
      properties.popup.content = "empty";
      properties.popup.async = false;
      data.properties = properties;
      geometry = {};
      geometry.type = "Point";
      geometry.coordinates = ol.proj.toLonLat(feature.getGeometry().getCoordinates());
      data.geometry = geometry;
      objContent.data = data;
      settings = {};
      settings.boundingBox = false;
      settings.crossOrigine = false;
      settings.loadAsync = false;
      settings.refresh = false;
      objContent.settings = settings;

      feature.set('popup', properties.popup);

      arrContent.push(objContent);
      return arrContent;
    },

    /**
     * Small getter function for convenience.
     * @returns {*}
     */
    getItems: function() {
      return this.starboardtab.layers;
    },

    switchLayer: function(feature, itemUid, tabId, entry) {
      try {
        this.plugin.customTabs[tabId].tabPointLayer.getSource().removeFeature(feature);
      } catch (error) {
        // feature is not on editor source
      }

      if (feature.get('active')) {
        // hide layer
        $(entry).removeClass(c4g.maps.constant.css.ACTIVE);
        $(entry).addClass(c4g.maps.constant.css.INACTIVE);
        //if (feature.get('onEditLayer')) {
        //  this.plugin.customTabs[tabId].tabControl.hideLayer(itemUid, feature);
        //} else {
          this.starboardtab.hideLayer(itemUid);
        //}
        feature.set('active', false);
      } else {
        // show layer
        $(entry).removeClass(c4g.maps.constant.css.INACTIVE);
        $(entry).addClass(c4g.maps.constant.css.ACTIVE);
        //if (feature.get('onEditLayer')) {
        //  this.plugin.customTabs[tabId].tabControl.showLayer(itemUid, feature);
        //} else {
          this.starboardtab.showLayer(itemUid);
        //}
        feature.set('active', true);
      }
    },

    /**
     * Overrides the hideLayer and showLayer functions of the starboard custom tab, so that they work on the
     * editor layer.
     * @param tabId
     */
    overrideSwitchLayers: function(tabId) {
      var self = this;
      this.starboardtab.oldHideLayer = this.starboardtab.hideLayer;
      this.starboardtab.oldShowLayer = this.starboardtab.showLayer;

      // override hideLayer
      //this.starboardtab.hideLayer = function(layerId) {
      //  if (typeof layerId.charAt === "function" && layerId.charAt(0) === "_") {
      //    return false;
      //  } else {
      //    console.log("layer with id " + layerId + " hidden");
      //    self.starboardtab.oldHideLayer(layerId);
      //  }
      //};

      // override showLayer
      //this.starboardtab.showLayer = function(layerId) {
      //  if (typeof layerId.charAt === "function" && layerId.charAt(0) === "_") {
      //    return false;
      //  } else {
      //    console.log("layer with id " + layerId + " shown");
      //    self.starboardtab.oldShowLayer(layerId);
      //  }
      //};
    },

    changeSwitchingBack: function(tabId) {
      this.starboardtab.showLayer = c4g.maps.control.starboardplugin.Customtab.prototype.showLayer;
      this.starboardtab.hideLayer = c4g.maps.control.starboardplugin.Customtab.prototype.hideLayer;
    },

    /**
     * Deletes an entry in the layertree of this starboardtab.
     * @param project
     * @param category
     * @param element
     */
    deleteInstance: function(project, category, element) {
      //  var fnSearchForEntry,
      //      self,
      //      entry;
      //
      //  self = this;
      //
      //  fnSearchForEntry = function (uid) {
      //    var node,
      //        child,
      //        parent;
      //
      //    if (self.layers[uid]) {
      //      node = self.layers[uid];
      //      if (node.$entries[0].data('uid') === uid) {
      //        child = node.$entries[0];
      //        parent = child[0].parentNode;
      //        return parent;
      //      }
      //    }
      //  }; // end of "fnSearchForEntry()"
      //
      //  // get the layertree element and remove it
      //  entry = fnSearchForEntry(featureUid);
      //  // remove the "li" tag, which contains the entry
      //  if (entry) {
      //    entry.parentNode.removeChild(entry);
      //    delete this.layers[featureUid];
      //    this.starboard.update();
      //  }
    },

    /**
     * Renames an entry in the layertree of this starboardtab.
     * @param project
     * @param category
     * @param element
     */
    renameInstance: function(project, category, element) {
      //  var uId,
      //      fnSearchForEntry,
      //      entry,
      //      self,
      //      newEntry;
      //
      //  self = this;
      //  uId = feature.get('uid');
      //  options = options || {};
      //  options = $.extend({
      //    parseAsList: true
      //  }, options);
      //
      //  fnSearchForEntry = function (uid) {
      //    var node,
      //        child;
      //
      //    if (self.layers[uid]) {
      //      node = self.layers[uid];
      //      if (node.$entries[0].data('uid') === uid) {
      //        child = node.$entries[0];
      //        return child[0];
      //      }
      //    }
      //  }; // end of "fnSearchForEntry()"
      //
      //  entry = fnSearchForEntry(uId);
      //  entry.innerText = feature.get('tooltip');
      //  //this.deleteFeature(uid);
      //  //this.addFeatureToTree(feature, options);
      //  this.starboard.update();
    }
  });

}(jQuery, this.c4g));