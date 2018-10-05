this.c4g.projects = this.c4g.projects || [];
this.c4g.projects.hook = this.c4g.projects.hook || [];

(function ($, c4g) {
  c4g.projects.hook = c4g.projects.hook || [];
  c4g.projects.hook.responseHandled = c4g.projects.hook.responseHandled || [];
  c4g.projects.hook.responseHandled.push(function(objParam) {
    const value = objParam.searchValue;
    // TODO diesen selektor überprüfen; greift der immer auf das element zu ?
    const inputField = $("#c4gGuiDataTable\\3a list\\3a -1_filter > label > input[type=\"search\"]");
    inputField.val(value);
    inputField.trigger("input");
  });

})(jQuery, this.c4g);