// "namespace"
this.c4g = this.c4g || {};

(function ($, c4g) {
  'use strict';

  c4g.maps = c4g.maps || {};
  c4g.plugin = c4g.plugin || {};

  c4g.plugin.CustomTab = function (editor, options, plugin, projects, opt_starboardcontrol) {
    var project, category, element, selector, awesomeicon, layerStyleFunction, self;

    self = this;
    this.starboardcontrol = opt_starboardcontrol || null;
    this.editor = editor;
    this.options = $.extend({
      type: 'Point',
      styleIds: [],
      tabConfig: {
        headline: 'Project',
        tipLabel: 'Projects-Plugin'
      }
    }, options);
    this.plugin = plugin;
    this.projects = projects;
    for (var i = 0; i < this.projects.length; i++) {
      project = this.projects[i];
      for (var j = 0; j < project.categories.length; j++) {
        category = project.categories[j];
        for (var k = 0; k < category.elements.length; k++) {
          element = category.elements[k];
          this.options.styleIds.push(element.styleId);
        }
      }
    }

    // add editor layer for this tab
    layerStyleFunction = function (feature, projection) {
      var styleId;

      if (feature && typeof feature.get === 'function') {
        // get the styleId of the current feature
        styleId = feature.get('styleId');
        // and execute the appropriate function
        if (c4g.maps.locationStyles[styleId]) {
          return c4g.maps.locationStyles[styleId].style(feature, projection);
        }
      }
      return false;
    };

    // Add editor layers
    this.tabPointLayer = new ol.layer.Vector({source: new ol.source.Vector(), style: layerStyleFunction});
    // create group so we can extend this later more easily
    this.tabLayerGroup = new ol.layer.Group({
      layers: new ol.Collection([
        this.tabPointLayer
      ]),
      visible: true
    });
    // add the layer group to the map
    this.editor.proxy.options.mapController.map.addLayer(this.tabLayerGroup);

    var saveCallback = function(saveData) {

      var format = new ol.format.GeoJSON();
      saveData[self.options.tabConfig.id] = format.writeFeatures(self.tabPointLayer.getSource().getFeatures());
    };
    c4g.maps.hook.editor_onSave = c4g.maps.hook.editor_onSave || [];
    c4g.maps.hook.editor_onSave.push(saveCallback);

    var loadCallback = function(loadData) {
      if (loadData[self.options.tabConfig.id]) {
        var format = new ol.format.GeoJSON();
        self.tabPointLayer.getSource().addFeatures(format.readFeatures(loadData[self.options.tabConfig.id]));
      }
    };
    c4g.maps.hook.editor_onLoad = c4g.maps.hook.editor_onLoad || [];
    c4g.maps.hook.editor_onLoad.push(loadCallback);


    this.className = 'c4g-editor-view-trigger-projects-plugin-' + this.options.tabConfig.id;
    // use same icon as the corresponding starboard tab
    awesomeicon = 'f13d';
    if (c4g.maps.layers[this.options.tabConfig.id]) {
        awesomeicon = c4g.maps.layers[this.options.tabConfig.id].awesomeicon;
    }
    selector = 'button.' + this.className;
    if (awesomeicon && awesomeicon.length > 0) {
      // catch firefox, because FF does not know "addRule"loadData.tabPointLayer
      if (document.styleSheets[0].addRule && typeof document.styleSheets[0].addRule === 'function') {
        document.styleSheets[0].addRule(selector + ':before', 'content: "\\'+ awesomeicon +'";');
      } else {
        document.styleSheets[0].insertRule(selector + ':before { content: "\\'+ awesomeicon +'";}', 0);
      }
    }
    this.tabControl = new c4g.plugin.TabControl(this);
  };

  $.extend(c4g.plugin.CustomTab.prototype, {
    /**
     * Function which adds an editor tab to the editor.
     * It fetches the data of the tab out of other classes.
     */
    addEditorView: function () {
      var TRIGGER_DRAW,
          drawView,
          editor,
          self,
          key,
          options,
          plugin,
          projectContent,
          addProject,
          selectBox;

      self = this;
      editor = this.editor;
      options = this.options;
      plugin = this.plugin;
      // layerIds are the Ids of the drawn features (random uids)
      this.layerIds = [];
      // drawnFeatures are the feature objects (ol.Feature)
      this.drawnFeatures = {};
      if (this.projects[0]) {
        self.currentProject = null;//this.projects[0];

        // We need a tab id
        if (options.tabConfig.id === undefined) {
          console.log('Error: no ID was given...');
          return false;
        }
        TRIGGER_DRAW = 'EDITOR_VIEW_TRIGGER_DRAW_' + options.type.toUpperCase();


        // first headline in tab
        this.drawContent = document.createElement('div');
        this.drawContent.className = c4g.maps.constant.css['EDITOR_DRAW_CONTENT_PROJECT'];
        var headlineDiv = document.createElement('div');
        headlineDiv.className = 'c4g-project-headline-field';
        var projectHeadline = document.createElement('h4');
        projectHeadline.innerText = options.tabConfig.headline;
        headlineDiv.appendChild(projectHeadline);
        this.drawContent.appendChild(headlineDiv);
        //this.drawContent.innerHTML = '<h4>' + options.tabConfig.headline + '</h4>';
        if (options.tabConfig.withButton) {
          //console.log(this.drawContent);
          //this.drawContent.getElementsByTagName('label')[0].style.width = '60%';
          var newButton = document.createElement('button');
          newButton.className = 'c4g-new-project-button';
          var newButtonLink = document.createElement('a');
          newButtonLink.setAttribute('onclick', options.tabConfig.button.link);
          newButtonLink.innerHTML = options.tabConfig.button.text;
          newButton.appendChild(newButtonLink);
          headlineDiv.append(newButton);
          //console.log(options.tabConfig.button);
        }

        addProject = function() {
          // add project(s) to editor section
          var categoryDiv,
              currentCategory;

          if (self.currentProject) {
              for (key in self.currentProject.categories) {
                  if (self.currentProject.categories.hasOwnProperty(key)) {
                      currentCategory = self.currentProject.categories[key];
                      categoryDiv = self.addCategoryForProject(currentCategory, self.currentProject);
                      self.drawContent.appendChild(categoryDiv);
                      self.addElementsForCategory(currentCategory.elements, currentCategory);
                  }
              }
          }
        };

        drawView = editor.addView({
          name: this.options.tabConfig.headline,
          triggerConfig: {
            tipLabel: options.tabConfig.tipLabel,
            className: this.className,
            withHeadline: false
          },
          sectionElements: [
            {section: editor.contentContainer, element: this.drawContent},
            {section: editor.bottomToolbar, element: editor.viewTriggerBar}
          ],
          initFunction: function () {
            var i,j,k,
                styleId,
                neededStyles,
                element;

            // Show loading animation
            editor.spinner.show();
            neededStyles = [];
            // Make sure that all needed styles are loaded
            if (!c4g.maps.locationStyles) {
              // no styles are loaded, so load all styles
              c4g.maps.locationStyles = {};
              neededStyles = options.styleIds;
            } else {
              // check which styles are missing
              for (i = 0; i < options.styleIds.length; i += 1) {
                styleId = options.styleIds[i];
                if (!c4g.maps.locationStyles[styleId] || !c4g.maps.locationStyles[styleId].style) {
                  neededStyles.push(styleId);
                }
              }

              for (i = 0; i < self.projects.length; i += 1) {
                for (j = 0; j < self.projects[i].categories; j +=1) {
                  for (k = 0; k < self.projects[i].categories[j].elements; k +=1) {
                    element = self.projects[i].categories[j].elements[k];
                    if (!c4g.maps.locationStyles[element.styleId]) {
                        neededStyles.push(element.styleId);
                    }
                  }
                }
              }
            }

            if (neededStyles.length > 0) {
              if (!editor.proxy) {
                console.warn('Could not load locStyles, as the map-proxy was not initiallized.');
              }
              editor.proxy.loadLocationStyles(
                neededStyles,
                {
                  success: function () {
                    self.sortAndAddStyles();
                    selectBox = self.createProjectSelect();
                    self.drawContent.appendChild(selectBox);
                    addProject();
                  },
                  complete: function () {
                    // Hide loading-animation
                    editor.spinner.hide();
                    editor.update();
                  }
                }
              );
            } else {
              self.sortAndAddStyles();
              selectBox = self.createProjectSelect();
              self.drawContent.appendChild(selectBox);
              addProject();
              editor.update();
              editor.spinner.hide();
            }
            return true;
          },
          activateFunction: function () {
            if (!self.starboardcontrol) {
              self.editor.options.mapController.controls.starboard.open();
              self.editor.options.mapController.controls.starboard.close();
            }
            return true;
          },
          deactivateFunction: function () {
            return true;
          }
        });
        return drawView;
        }

    }, //End of addEditorView

    /**
     * Sorts all the styles (by extension configurable sort order) and adds them to the draw view.
     *
     * @return  {[type]}  [description]
     */
    sortAndAddStyles: function () {
      var j,
          features,
          styleIds,
          options,
          plugin,
          tabId;

      options = this.options;
      plugin = this.plugin;
      tabId = options.tabConfig.id;
      features = plugin.featuresPerTab[tabId];
      styleIds = options.styleIds;
      if (!styleIds || !features) {
        return false;
      }
      return true;
    }, // end of "sortAndAddStyles"

    /**
     * Builds a style for each element in a category. Creates interactions when they are clicked and adds
     * a clickable label to the editor section.
     * @param styleId
     * @param element
     * @returns {*}
     */
    addDrawStyle: function (styleId, element, opt_projectId, opt_container) {
      var interactionView,
          source,
          interaction,
          features,
          editorStyle,
          style,
          styleIcon,
          styleImage,
          styleTriggerLabel,
          featureIdCount,
          plugin,
          options,
          name,
          editor,
          self;

      self = this;
      plugin = this.plugin;
      options = this.options;
      editor = this.editor;
      // Style "shortcut"
      if (c4g.maps.locationStyles[styleId]) {
          style = c4g.maps.locationStyles[styleId].style()[0];
          editorStyle = c4g.maps.locationStyles[styleId].editor;
      }
      featureIdCount = 0;
      // Create label for interaction-trigger
      styleTriggerLabel = document.createElement('span');
      opt_projectId = opt_projectId || "";
      // @TODO use css-class for dimensions
      styleTriggerLabel.style.display = 'block';
      styleTriggerLabel.style.minWidth = '30px';
      styleTriggerLabel.style.minHeight = '30px';
      styleTriggerLabel.style.margin = '2px';
      // "style.getImage().getImage()", does not work in every case
      if (style) {
        styleImage = style.getImage() || undefined;
        if ((editorStyle && editorStyle.iconSrc) || (styleImage && styleImage instanceof ol.style.Icon)) {
          styleIcon = document.createElement('img');
          styleIcon.src = editorStyle.iconSrc || styleImage.getSrc();
          styleTriggerLabel.appendChild(styleIcon);
        } else {
          styleTriggerLabel.style.background = style.getFill().getColor();
          styleTriggerLabel.style.border = '1px solid ' + style.getStroke().getColor();
        }
      }

      // Create label for interaction-trigger-counter
      if (!element.elem) {
        element.elem = document.createElement('span');
      }
      styleTriggerLabel.appendChild(element.elem);
      // initialize counter. this call is initially needed for displaying the counters correctly.
      plugin.functions.updateFeatureCounter(styleId, element.count, 'set', element);

      // Create interactionView
      //   "addView" will be used for this, because the functionality
      //   ist mostly equal
      name = element.name;
      interactionView = editor.addView({
        name: 'projects-plugin:draw:' + name,
        triggerConfig: {
          label: styleTriggerLabel,
          tipLabel: name,
          className: c4g.maps.constant.css.EDITOR_DRAW_TRIGGER + ' project-' + opt_projectId,
          target: opt_container || this.drawContent,
          withHeadline: false
        },
        sectionElements: [
          {section: editor.contentContainer, element: this.drawContent},
          {section: editor.bottomToolbar, element: editor.viewTriggerBar}
        ],
        initFunction: function () {
          var interactionStyleImage,
              activeSketch,
              activeTooltip;

          // Only show original icon, when the drawing POIs
          if (style) {
              if (options.type.toLowerCase() === 'point' && style.getImage()) {
                  interactionStyleImage = style.getImage();
              } else {
                  interactionStyleImage = new ol.style.Circle({
                      fill: style.getFill(),
                      stroke: style.getStroke(),
                      radius: 5
                  });
              }
          }

          // Set appropriate source
          source = self.tabPointLayer.getSource();
          //source = self.editor.editPointLayer.getSource();
          source.on('addfeature', function(event){
            if (self.starboardcontrol) {
                self.starboardcontrol.transferLayer(event.feature, event.target);
            }
          });

          features = new ol.Collection();
          interaction = new ol.interaction.Draw({
            features: features,
            source: source,
            type: options.type,
            style: [
              new ol.style.Style({
                stroke: new ol.style.Stroke({
                  color: 'rgba(255,255,255,.5)',
                  width: style.getStroke().getWidth() + 2
                }),
                image: interactionStyleImage
              }),
              new ol.style.Style({
                geometry: style.getGeometry(),
                fill: style.getFill(),
                stroke: style.getStroke()
              })
            ]
          });

          // @TODO doku
          //
          interaction.on('drawstart',
              function (event) {
                activeSketch = event.feature;
                activeSketch.set('styleId', styleId);

              }, editor);

          // @TODO doku
          //
          editor.options.mapController.map.on('pointermove',
              function (event) {

              }, editor);

          // @TODO doku
          //
          interaction.on('drawend',
              function (event) {
                var i,
                    vars,
                    editorVars,
                    name,
                    c4g_tabId,
                    tooltip,
                    uid,
                    layer;

                // name the feature
                featureIdCount += 1;
                name = c4g.maps.locationStyles[styleId].name.replace("&#40;", "(").replace("&#41;", ")");
                //tooltip = self.plugin.elements[self.currentProject.projectId + "-" + element.cid + "-" + element.id].name;
                //activeSketch.set('tooltip', tooltip + ' (' + featureIdCount + ')');
                // add styleId
                activeSketch.set('styleId', styleId);

                // add editor-vars
                vars = editorStyle.vars;
                editorVars = [];
                for (i = 0; i < vars.length; i += 1) {
                  editorVars[i] = {};
                  editorVars[i].key = vars[i].key;
                  editorVars[i].label = vars[i].value;
                  editorVars[i].value = '';
                }
                activeSketch.set('editorVars', editorVars);
                activeSketch.set('eid', element.id);
                activeSketch.set('catId', element.cid);
                activeSketch.set('projectId', self.currentProject.projectId);
                // needed for determining which hide/show to use
                activeSketch.set('onEditLayer', true);
                // Add uid to drawn feature
                uid = c4g.maps.utils.getUniqueId();
                activeSketch.set('uid', uid);
                activeSketch.set('active', true);
                self.layerIds.push(uid);
                self.drawnFeatures[uid] = activeSketch;
                // reset active-element variables
                activeSketch = null;
                if (activeTooltip) {
                  activeTooltip.close();
                  activeTooltip = null;
                }

                if ( self.starboardcontrol && c4g.maps.layers[self.options.tabConfig.id]) {
                  layer = self.starboardcontrol.insertNewInstance(self.currentProject, self.currentProject.categories[element.cid],
                      self.currentProject.categories[element.cid].elements[element.id], self.options.tabConfig.id,
                      self.drawnFeatures[uid]);
                }

                // update feature counter
                plugin.functions.updateFeatureCounter(styleId, 1, '+', element);
                plugin.functions.sendCreatedElementToServer(layer, editor.options.mapController, element);
              }, editor);
          element.interaction = interaction;
          editor.options.mapController.map.addInteraction(interaction);
          return true;

        }, // end of "initFunction()"

        activateFunction: function () {

          // deactivate mapHover
          editor.options.mapController.mapHover.deactivate();

          // Reset feature-list
          features.clear();

          // Enable interaction
          // editor.options.mapController.map.addInteraction(interaction);
          if (element.limit > element.count) {
            interaction.setActive(true);
          } else if (element.limit == -1) {
            interaction.setActive(true);
          } else {
            interaction.setActive(false);
          }
          return true;
        },

        deactivateFunction: function () {

          // reactivate mapHover
          editor.options.mapController.mapHover.activate();

          // finish drawings, if not already done
          // Hint: This is pretty much redundant, as we only have Point drawings.
          // But since lines and polygons can follow later on, we keep it.
          if (options.type.toLowerCase() !== 'point') {
            try {
              interaction.finishDrawing();
            } catch (ignore) {
              // ignore
            }
          }
          // Remove from map
          interaction.setActive(false);
          return true;
        }
      });

      return interactionView;
    }, // End addDrawStyle()

    /**
     * Adds a category to the editor drawContent-section. It is bound to a project and is displayed when the project
     * is selected. A category contains different elements, which can be dragged onto the map.
     * @param category
     * @param project
     * @return Object div containing the category
     */
    addCategoryForProject: function(category, project) {
      var categoryDiv,
          catHeadline,
          elementDiv,
          toggleView,
          headlineDiv;

      categoryDiv = document.createElement('div');
      elementDiv = document.createElement('div');
      headlineDiv = document.createElement('div');
      headlineDiv.className = "c4g-category-headline";
      elementDiv.id = "project-" + project.projectId + "-category-" + category.id;
      toggleView = document.createElement('button');
      toggleView.className = "c4g-toggle-category";
      // default: show units and vehicles, hide other categories
      if (category.visible === undefined) {
        toggleView.className += " c4g-toggle-category-closed";
        elementDiv.style.display = "none";
      } else {
        if (category.visible) {
          toggleView.className += " c4g-toggle-category-open";
        } else {
          toggleView.className += " c4g-toggle-category-closed";
          elementDiv.style.display = "none";
        }
      }

      // click listener for toggling the categories
      $(toggleView).on('click', function(event) {
        if ($(this).hasClass("c4g-toggle-category-open")) {
          $(this).removeClass("c4g-toggle-category-open").addClass("c4g-toggle-category-closed");
          elementDiv.style.display = "none";
          category.visible = false;
        } else {
          $(this).removeClass("c4g-toggle-category-closed").addClass("c4g-toggle-category-open");
          elementDiv.style.display = "block";
          category.visible = true;
        }
      });
      categoryDiv.className = "project-" + project.projectId;
      catHeadline = document.createElement('h4');
      catHeadline.innerHTML = category.name;
      headlineDiv.appendChild(catHeadline);
      headlineDiv.appendChild(toggleView);
      categoryDiv.appendChild(headlineDiv);
      categoryDiv.appendChild(elementDiv);
      this.plugin.categories[category.id] = category;
      return {categoryDiv: categoryDiv, elements: elementDiv};
    },

    /**
     * Adds a selection of elements to a category. For each element, a trigger label is created and drawn under the
     * corresponding category.
     * @param elements
     * @param category
     * @param catContainer  HTML container for the category
     */
    addElementsForCategory: function(elements, category, catContainer) {
      var element,
          missingStyles = [],
          self = this,
          missingElements = [];

      var addElement = function(element) {
        element.drawInteraction = self.addDrawStyle(element.styleId, element, category.projectId, catContainer);
        self.plugin.elements[category.projectId + '-' + category.id + '-' + element.id] = element;
      };

      for (var key in elements) {
        if (elements.hasOwnProperty(key)) {
          element = elements[key];
          if (!c4g.maps.locationStyles[element.styleId]) {
            missingStyles.push(element.styleId);
            missingElements.push(element);
          } else {
            addElement(element);
          }
        }
      }

      if (missingStyles.length > 0) {
        this.editor.proxy.loadLocationStyles(missingStyles,
          {
            success: function() {
              for (var i = 0; i < missingElements.length; i++) {
                addElement(missingElements[i]);
              }
               self.editor.update();
               self.plugin.elementsLoaded = true;
               if (c4g.maps.hook !== undefined && typeof c4g.maps.hook.elements_Loaded === 'object') {
                 c4g.maps.utils.callHookFunctions(c4g.maps.hook.elements_Loaded);
               }
            } // end success
          });
        return false;
      } else {
        self.plugin.elementsLoaded = true;
        if (c4g.maps.hook !== undefined && typeof c4g.maps.hook.elements_Loaded === 'object') {
          c4g.maps.utils.callHookFunctions(c4g.maps.hook.elements_Loaded);
        }
      }
      return true;
    },

    createProjectSelect: function() {
      var selectBox,
          key,
          option,
          scope,
          oldValue,
          rerun = false,
          lastKey = 0;

      scope = this;
      selectBox = document.createElement('select');
      selectBox.id = 'c4g_projects_select';
      option = document.createElement('option');
      option.text = "Wähle ein Projekt ...";
      option.value = "";
      option.disabled = true;
      option.selected = true;
      selectBox.options[0] = option;
      for (key = 0; key < this.projects.length; key++) {
        option = document.createElement('option');
        option.text = this.projects[key].name;
        option.value = this.projects[key].projectId;
        selectBox.options[key+1] = option;
      }
      oldValue = selectBox.value;
      $(selectBox).on('change', function(event) {
        for (var key in scope.projects) {
          if (scope.projects.hasOwnProperty(key)) {
            if (scope.projects[key].projectId == this.value) {
              scope.changeProjectSelection(scope.projects[key], oldValue);
              oldValue = this.value;
              break;
            }
          }
        }
      });
      return selectBox;
    },

    changeProjectSelection: function(newProject, oldProjectId) {
      var categoryDiv,
          currentCategory,
          key,
          arrCategories = [],
          layer,
          tab;

      this.plugin.elementsLoaded = false;
      this.currentProject = newProject;
      this.editor.update();

      // open starboard and active the correct tab
      this.starboardcontrol.starboard.open();
      tab = this.starboardcontrol.starboard.plugins["customTab" + this.options.tabConfig.id];
      tab.activate();
      for (key in tab.layers) {
        if (tab.layers.hasOwnProperty(key)) {
          this.editor.proxy.hideLayer(key);
        }
      }
      // search the project layer and turn it on
      for (key in c4g.maps.layers) {
        if (c4g.maps.layers.hasOwnProperty(key)) {
          layer = c4g.maps.layers[key];
          if (layer.name === newProject.starboardName) {
            this.editor.proxy.showLayer(layer.id);
            break;
          }
        }
      }
      // remove all nodes associated with the old project
      $(this.drawContent.getElementsByClassName("project-" + oldProjectId)).detach();
      // copy categories into array
      for (key in this.currentProject.categories) {
        if (this.currentProject.categories.hasOwnProperty(key)) {
          arrCategories.push(this.currentProject.categories[key]);
        }
      }
      // sort by first character of name
      arrCategories.sort(function(a, b) {
        var x = a.name.toLowerCase();
        var y = b.name.toLowerCase();
        return x < y ? -1 : x > y ? 1 : 0;
      });
      // add categories to editor
      for (key = 0; key < arrCategories.length; key++) {
        currentCategory = arrCategories[key];
        categoryDiv = this.addCategoryForProject(currentCategory, this.currentProject);
        this.drawContent.appendChild(categoryDiv.categoryDiv);
        this.addElementsForCategory(currentCategory.elements, currentCategory, categoryDiv.elements);
      }

    }, // end of changeProjectSelection()

    setStarboardControl: function(starboardCtrl) {
      this.starboardcontrol = starboardCtrl;
    }
  }); // end extend

}(jQuery, this.c4g));
