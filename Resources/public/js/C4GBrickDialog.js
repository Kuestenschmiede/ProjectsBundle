/*
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

/**
 *
 * @param id
 * @param command
 * @param object
 * @constructor
 */
function C4GTimePicker(id, command, object) {
    var d = new Date();
    var m = String(d.getMinutes());
    var h = String(d.getHours());
    var hf = document.getElementById(id);
    var i = object.previousSibling;

    if (m.length == 1) {
        m = "0" + m;
    }
    if (h.length == 1) {
        h = "0" + h;
    }
    if (command == "gettime") {
        hf.value = h+":"+m;
        i.value = h+":"+m;
    }
}

/**
 *
 * @param id
 * @param type
 * @param min
 * @param max
 * @param format
 * @constructor
 */
function C4GDatePicker(id,
                       type,
                       minDate,
                       maxDate,
                       format,
                       lang,
                       weekdays,
                       exclude)
{

    if(type == "date")
    {
        var dMin = '';
        if (minDate) {
            dMin = new Date(minDate * 1000);
        }

        var dMax = '';
        if (maxDate) {
            dMax = new Date(maxDate * 1000);
        }

        jQuery("#"+id).datepicker({
            beforeShowDay: function(date){
                if (weekdays) {
                    var wd = new Array();
                    wd = weekdays.split(",");
                    for(var i = 0; i < wd.length; i++) {
                        var iDay = wd[i];
                        if (date.getDay() == iDay) {
                            return [false,""];
                        }
                    }
                }
                if (exclude) {
                    var ed = new Array();
                    ed = exclude.split(",");
                    for(var i = 0; i < ed.length; i++) {
                        //var iDate = new Date(ed[i] * 1000);
                        if ((Date.parse(date)/1000) == ed[i]) {
                            return [false,""];
                        }
                    }
                }
                return [true,""];
            },
            //yearRange: dMin.getFullYear()+":"+dMax.getFullYear(),
            minDate: dMin,
            maxDate: dMax
        });
        jQuery(function($){
            var regional = $.datepicker.regional[lang];
            if (!regional) {
                // fallback
              regional = $.datepicker.regional[lang.substr(0, 2)];
            }
            regional.dateFormat = format;
            $.datepicker.setDefaults(regional);
        });
    }
}

/**
 *
 * @param id
 * @constructor
 */
function C4GDateTimePicker(id)
{
    var dateTimePickerOptions = {
        "dateFormat":"DD.MM.YY - hh:mm",
        "locale":"de",
        "closeOnSelected": "true",

        "todayButton":false,
        //"futureOnly":true,
        //"minuteInterval": 15,
        //"allowWdays": [1, 2, 3, 4, 5],
        //"minTime":"09:15",
        //"maxTime":"12:45"
    };
    jQuery("#"+id).appendDtpicker(dateTimePickerOptions);
}

/**
 *
 * @param filter
 * @constructor
 */
function C4GFilterButtonTiles(filter)
{
    var tiles = document.getElementsByClassName("c4g_tile_button");
    var value = filter.value;

    //ToDO implementation
}

/**
 *
 * @param search
 * @constructor
 */
function C4GSearchTiles(search)
{
    var tiles = document.getElementsByClassName("c4g_tile_button");
        text;
        value = search.value.toLowerCase();
        founded;

    if(value)
    {
        for (aTimer = 0; aTimer < tiles.length; aTimer+=1)
        {
            founded = false;

            fields = tiles[aTimer].children[0].children;

            for (fTimer = 0; fTimer < fields.length; fTimer+=1)
            {
                if(fields[fTimer].innerHTML)
                {
                    text = fields[fTimer].innerHTML.toLowerCase();

                    searchedStatus = text.search(value);
                    if(searchedStatus == "-1") {
                      //ToDo implementation
                    }
                    else
                    {
                        founded = true;
                    }
                }
            }

            if(founded == true)
            {
                tiles[aTimer].style.display = "";
            }
            else
            {
                tiles[aTimer].style.display = "none";
            }
        }
    }
    else
    {
        for(cTimer = 0; cTimer < tiles.length; cTimer +=1)
        {
            tiles[cTimer].style.display = "";
        }
    }
}

/**
 *
 * @param button
 */
function tileSort(button) // '<button type="button" onclick="tileSort(this)">ASC</button>'
{
    var wrapper = document.getElementsByClassName("c4g_brick_tiles")[0];

    if(wrapper.style.flexFlow == "row wrap")
    {
        wrapper.style.flexFlow = "row-reverse wrap-reverse";
        button.textContent = button.dataset["langDesc"];

    }
    else
    {
        wrapper.style.flexFlow = "row wrap";
        button.textContent = button.dataset["langAsc"];
    }
}

/**
 *
 * @param object
 * @returns {boolean}
 */
function createNewPopupWindow(object)
{
    //$.magnificPopup.open({ items: { src: object.dataset.linkHref }, type: 'iframe' });
    $.magnificPopup.open({ items: { src: object.dataset.linkHref }, type:  "iframe" }, 0);

    return false;
}

/**
 *
 */
function closePopupWindow()
{
    $.magnificPopup.close();
}

/**
 *
 * @param checkbox
 * @param element
 */
function handleBoolSwitch(checkbox, element, reverse) {
    if (checkbox && element) {
        var checkboxId = checkbox.id;
        var elementId = element.id;

        var checked = document.getElementById(checkboxId).checked;

        if (reverse == '1') {
            document.getElementById(elementId).disabled = checked;
        } else {
            document.getElementById(elementId).disabled = !checked;
        }
    }

}

/**
 *
 * @param fileList
 * @param path
 * @param targetField
 */
function handleC4GBrickFile(fileList, path, targetField, mimeTypes) {
    if (fileList) {
        if (document.getElementById("c4g_uploadURL").value !== fileList[0]) {
            var img = document.createElement("img");
            img.file = fileList[0];
            img.name = "img_" + 0;
            img.classList.add("obj");
            var reader = new FileReader();
            reader.onload = (function (aImg) {
                return function (e) {
                    aImg.src = e.target.result;
                };
            })(img);
            reader.readAsDataURL(fileList[0]);

            C4GBrickFileUpload(fileList[0], path, targetField, mimeTypes);
        }
    }
}

/**
 *
 * @param targetField
 */
function deleteC4GBrickFile(targetField) {
    document.getElementById("c4g_deleteURL").value =  document.getElementById("c4g_uploadURL").value;
    document.getElementById("c4g_uploadURL").value = "";
    document.getElementById("c4g_uploadLink").innerHTML = "";
    if (document.getElementById("c4g_deleteButton")) {
        document.getElementById("c4g_deleteButton").style = "display:none";
    }

    if (targetField) {
        if (document.getElementsByClassName("c4g_"+targetField+"_src")[0]) {
            document.getElementsByClassName("c4g_"+targetField+"_src")[0].getElementsByTagName("img")[0].src = xhr.responseText;
        }
    }
}

/**
 *
 * @param file
 * @param path
 * @param targetField
 * @constructor
 */
function C4GBrickFileUpload( file, path, targetField, mimeTypes )
{
    var xhr = new XMLHttpRequest();

    var fd = new FormData();
    fd.append("File", file);
    fd.append("Path", path);
    fd.append("MimeTypes", mimeTypes);
    fd.append("REQUEST_TOKEN", c4g_rq);

    xhr.onreadystatechange = function(){
        if (xhr.readyState==4 && xhr.status==200){
            document.getElementById("c4g_uploadURL").value = xhr.responseText;
            document.getElementById("c4g_uploadLink").innerHTML = "<a href='" + xhr.responseText + "' target='_blank'>" + file.name.replace("C:\\fakepath\\", "") + "</a>";
            if (document.getElementById("c4g_deleteButton")) {
                document.getElementById("c4g_deleteButton").style = "display:inline";
            }
            document.getElementById("c4g_deleteURL").value = "";

            //document.getElementById('c4g_fileField').value = '';
            if (targetField) {
                if (document.getElementsByClassName("c4g_"+targetField+"_src")[0]) {
                    document.getElementsByClassName("c4g_"+targetField+"_src")[0].getElementsByTagName("img")[0].src = xhr.responseText;
                }
            }
        }
    }

    xhr.open("POST", "system/modules/con4gis_projects/assets/php/c4gUpload.php", true);
    xhr.overrideMimeType("text/plain; charset=x-user-defined-binary");

    xhr.send(fd);
}

/**
 *
 * @param object
 * @constructor
 */
function C4GCallOnChange(object) {

    var fields = document.getElementsByClassName("c4g_brick_dialog")[0].children;
    fields = C4GSortConditionFields(fields);

    for(var i = 0; i < fields.length; i++)
    {
        var field = fields[i];

        C4GCheckCondition(field);
    }

    var accordion_fields = document.getElementsByClassName("c4gGuiCollapsible_target");
    if (accordion_fields) {
        for(var i = 0; i < accordion_fields.length; i++) {
            var accordion_field = accordion_fields[i];

            var fields = accordion_field.children;

            fields = C4GSortConditionFields(fields);

            for(j = 0; j < fields.length; j++)
            {
                var field = fields[j];

                C4GCheckCondition(field);
            }
        }
    }

    var tab_content = document.getElementsByClassName("c4gGuiTabContent");
    if (tab_content) {
        for(var i = 0; i < tab_content.length; i++) {
            var content_field = tab_content[i];

            var fields = content_field.children;

            fields = C4GSortConditionFields(fields);

            for(j = 0; j < fields.length; j++)
            {
                var field = fields[j];

                C4GCheckCondition(field);
            }
            checkC4GTab();
        }
    }

}

/**
 *
 * @param object
 * @constructor
 */
function C4GCallOnChangeMethodswitchFunction(object) {

    var fields = document.getElementsByClassName("c4g_brick_dialog")[0].children;
    fields = C4GSortConditionMethodswitchFields(fields);

    for(var i = 0; i < fields.length; i++)
    {
        var field = fields[i];

        C4GCheckMethodswitchCondition(field);
    }

    var accordion_fields = document.getElementsByClassName("c4gGuiCollapsible_target");
    if (accordion_fields) {
        for(var i = 0; i < accordion_fields.length; i++) {
            var accordion_field = accordion_fields[i];

            var fields = accordion_field.children;

            fields = C4GSortConditionMethodswitchFields(fields);

            for(j = 0; j < fields.length; j++)
            {
                var field = fields[j];

                C4GCheckMethodswitchCondition(field);
            }
        }
    }

    var tab_content = document.getElementsByClassName("c4gGuiTabContent");
    if (tab_content) {
        for(var i = 0; i < tab_content.length; i++) {
            var content_field = tab_content[i];

            var fields = content_field.children;

            fields = C4GSortConditionMethodswitchFields(fields);

            for(j = 0; j < fields.length; j++)
            {
                var field = fields[j];

                C4GCheckMethodswitchCondition(field);
            }
            checkC4GTab();
        }
    }
}

/**
 *
 * @param field
 * @constructor
 */
function C4GCheckCondition(field)
{
    var fieldNames = field.dataset.conditionName.split("~");
    var fieldValues = field.dataset.conditionValue.split("~");


    var currentName;
    var currentValue;

    for(f = 0; f < fieldNames.length; f++)
    {
        currentName = "c4g_" + fieldNames[f];
        currentValue = fieldValues[f];

        checkValue = document.getElementById(currentName).value;

        if (checkValue) {
            if (checkValue != currentValue) {
                for (o = 0; o < field.children.length; o++) {
                    try {
                        jQuery(field.children[o]).removeClass("formdata");
                        if (jQuery(field.children[o]).hasClass('chzn-select')) {
                            jQuery(field.children[o]).removeClass("chzn-select");
                            jQuery(field.children[o]).addClass("chzn-select-disabled");
                            jQuery(field.children[o]).style = "display:none";
                            jQuery(field.children[o]).trigger('chosen:updated');
                        }
                        jQuery(field.children[o]).hide();
                        jQuery(field.children[o]).removeAttr("selected");
                    } catch (err) {
                        //ToDo
                    }
                }
            }
        }
    }

    for(f = 0; f < fieldNames.length; f++)
    {
        currentName = "c4g_" + fieldNames[f];
        currentValue = fieldValues[f];

        checkValue = document.getElementById(currentName).value;

        if (checkValue) {
            if (checkValue == currentValue) {
                for (o = 0; o < field.children.length; o++) {
                    try {
                        jQuery(field.children[o]).show();
                        jQuery(field.children[o]).addClass("formdata");

                        if (jQuery(field.children[o]).hasClass("c4g_display_none")) {
                            if (jQuery(field.children[o]).hasClass('chzn-select')) {
                                jQuery(field.children[o]).removeClass("chzn-select");
                                jQuery(field.children[o]).addClass("chzn-select-disabled");
                                // jQuery(field.children[o]).style = "display:none";
                                // jQuery(field.children[o]).trigger('chosen:updated');
                            }
                            jQuery(field.children[o]).hide();
                        } else {
                            if (jQuery(field.children[o]).hasClass('chzn-select-disabled')) {
                                jQuery(field.children[o]).removeClass("chzn-select-disabled");
                                jQuery(field.children[o]).addClass("chzn-select");
                                // jQuery(field.children[o]).style = "display:none";
                                // jQuery(field.children[o]).trigger('chosen:updated');
                                jQuery(field.children[o]).hide();
                            }
                        }

                    } catch (err) {
                        //ToDo
                    }
                }
            }
        }

    }

    //$chosenContainer = document.getElementsByClassName("chosen-container");
    // if ($chosenContainer) {
    //
    //     if (chosen) {
    //         jQuery(".chzn-select").chosen();
    //     }
    //
    //     for(i = 0; i < $chosenContainer.length; i++)
    //     {
    //         if ($chosenContainer[i].style.width != "0px") {
    //             jQuery($chosenContainer[i]).show();
    //
    //         } else {
    //             jQuery($chosenContainer[i]).hide();
    //         }
    //     }
    //
    //
    // }
    //jQuery(".chzn-select").chosen("destroy").chosen();
}

/**
 *
 * @param field
 * @constructor
 */
function C4GCheckMethodswitchCondition(field)
{
    var fieldNames = field.dataset.conditionName.split("~");
    //var fieldValues = field.dataset.conditionValue.split("~");
    var fieldFunction = field.dataset.conditionFunction.split("~");

    var currentName;
    var currentFunction;

    for(f = 0; f < fieldNames.length; f++)
    {
        currentName = "c4g_" + fieldNames[f];
        currentFunction = window[fieldFunction[f]];

        checkValue = document.getElementById(currentName).value;

        if (checkValue) {
            if (!currentFunction(checkValue)) {
                for (o = 0; o < field.children.length; o++) {
                    try {
                        jQuery(field.children[o]).removeClass("formdata");
                        if (jQuery(field.children[o]).hasClass('chzn-select')) {
                            jQuery(field.children[o]).removeClass("chzn-select");
                            jQuery(field.children[o]).addClass("chzn-select-disabled");
                            // jQuery(field.children[o]).style = "display:none";
                            // jQuery(field.children[o]).trigger('chosen:updated');
                        }
                        jQuery(field.children[o]).removeAttr("selected");
                        jQuery(field.children[o]).removeAttr("required");
                        jQuery(field.children[o]).hide();
                    } catch (err) {
                        //ToDo
                    }

                    var hasChilds = jQuery(field.children[o]).children;
                    if (hasChilds) {
                        for (p = 0; p < jQuery(field.children[o].children).length; p++) {
                            try {
                                jQuery(field.children[o].children[p]).removeClass("formdata");
                                if (jQuery(field.children[o].children[p]).hasClass('chzn-select')) {
                                    jQuery(field.children[o].children[p]).removeClass("chzn-select");
                                    jQuery(field.children[o].children[p]).addClass("chzn-select-disabled");
                                    // jQuery(field.children[o].children[p]).style = "display:none";
                                    // jQuery(field.children[o].children[p]).trigger('chosen:updated');
                                }
                                jQuery(field.children[o].children[p]).removeAttr("selected");
                                jQuery(field.children[o].children[p]).removeAttr("required");
                                jQuery(field.children[o].children[p]).hide();
                            } catch (err) {
                                //ToDo
                            }
                        }
                    }
                }
            }
        }
    }

    for(f = 0; f < fieldNames.length; f++)
    {
        currentName = "c4g_" + fieldNames[f];
        currentFunction = window[fieldFunction[f]];

        checkValue = document.getElementById(currentName).value;

        if (checkValue) {
            if (currentFunction(checkValue)) {
                for (o = 0; o < field.children.length; o++) {
                    try {
                        jQuery(field.children[o]).show();
                        jQuery(field.children[o]).addClass("formdata");

                        if (jQuery(field.children[o]).hasClass("c4g_display_none")) {
                            if (jQuery(field.children[o]).hasClass('chzn-select')) {
                                jQuery(field.children[o]).removeClass("chzn-select");
                                jQuery(field.children[o]).addClass("chzn-select-disabled");
                                // jQuery(field.children[o]).style = "display:none";
                                // jQuery(field.children[o]).trigger('chosen:updated');
                            }
                            jQuery(field.children[o]).removeAttr("selected");
                            jQuery(field.children[o]).removeAttr("required");
                            jQuery(field.children[o]).hide();
                        } else {
                            if (jQuery(field.children[o]).hasClass('chzn-select-disabled')) {
                                jQuery(field.children[o]).removeClass("chzn-select-disabled");
                                jQuery(field.children[o]).addClass("chzn-select");
                                // jQuery(field.children[o]).style = "display:none";
                                // jQuery(field.children[o]).trigger('chosen:updated');
                                jQuery(field.children[o]).hide();
                            }
                        }
                    } catch (err) {
                        //ToDo
                    }

                    var hasChilds = jQuery(field.children[o]).children;
                    if (hasChilds) {
                        for (p = 0; p < jQuery(field.children[o].children).length; p++) {
                            try {
                                jQuery(field.children[o].children[p]).show();
                                jQuery(field.children[o].children[p]).addClass("formdata");

                                if (jQuery(field.children[o].children[p]).hasClass("c4g_display_none")) {
                                    if (jQuery(field.children[o].children[p]).hasClass('chzn-select')) {
                                        jQuery(field.children[o].children[p]).removeClass("chzn-select");
                                        jQuery(field.children[o].children[p]).addClass("chzn-select-disabled");
                                        // jQuery(field.children[o].children[p]).style = "display:none";
                                        // jQuery(field.children[o].children[p]).trigger('chosen:updated');
                                    }
                                    jQuery(field.children[o].children[p]).removeAttr("selected");
                                    jQuery(field.children[o].children[p]).removeAttr("required");
                                    jQuery(field.children[o].children[p]).hide();
                                } else {
                                    if (jQuery(field.children[o].children[p]).hasClass('chzn-select-disabled')) {
                                        jQuery(field.children[o].children[p]).removeClass("chzn-select-disabled");
                                        jQuery(field.children[o].children[p]).addClass("chzn-select");
                                        // jQuery(field.children[o].children[p]).style = "display:none";
                                        // jQuery(field.children[o].children[p]).trigger('chosen:updated');
                                        jQuery(field.children[o].children[p]).hide();
                                    }
                                }
                            } catch (err) {
                                //ToDo
                            }
                        }
                    }
                }
            }
        }

    }

    // $chosenContainer = document.getElementsByClassName("chosen-container");
    // if ($chosenContainer) {
    //
    //     if (chosen) {
    //         jQuery(".chzn-select").chosen();
    //     }
    //
    //     for(i = 0; i < $chosenContainer.length; i++)
    //     {
    //         if ($chosenContainer[i].style.width != "0px") {
    //             jQuery($chosenContainer[i]).show();
    //         } else {
    //             jQuery($chosenContainer[i]).hide();
    //         }
    //     }
    //
    // }
    //(".chzn-select").chosen("destroy").chosen();
}

/**
 *
 * @param fields
 * @returns {Array}
 * @constructor
 */
function C4GSortConditionFields(fields)
{
    var goodFields = new Array();
    var timer = 0;

    if (fields) {
        for(i = 0; i < fields.length; i++)
        {
            if (fields[i].dataset.conditionName && fields[i].dataset.conditionValue && fields[i].dataset.conditionType && (fields[i].dataset.conditionType == "value")) {
                goodFields[timer] = fields[i];
                timer++;
            }
        }
    }
    return goodFields;
}

/**
 *
 * @param fields
 * @returns {Array}
 * @constructor
 */
function C4GSortConditionMethodswitchFields(fields)
{
    var goodFields = new Array();
    var timer = 0;

    if (fields) {
        for(i = 0; i < fields.length; i++)
        {
            if (fields[i].dataset.conditionName && fields[i].dataset.conditionFunction && fields[i].dataset.conditionType && (fields[i].dataset.conditionType == "method")) {
                goodFields[timer] = fields[i];
                timer++;
            }
        }
    }
    return goodFields;
}

/**
 *
 * @param profile_id
 * @constructor
 */
function C4GGeopickerAddress(profile_id)
{
    if (profile_id) {

        var lat = document.getElementById("c4g_brick_geopicker_geoy").value;
        var lon = document.getElementById("c4g_brick_geopicker_geox").value;

        var xhr = new XMLHttpRequest();

        var fd = new FormData;
        fd.append("Lat", lat);
        fd.append("Lon", lon);
        fd.append("Profile", profile_id);
        fd.append("REQUEST_TOKEN", c4g_rq);

        xhr.onreadystatechange = function(){
            if (xhr.readyState==4 && xhr.status==200){
                if (xhr.responseText != "") {
                    document.getElementById("c4g_brick_geopicker_address").value = xhr.responseText;
                } else {
                    document.getElementById("c4g_brick_geopicker_address").value = "Adresse nicht ermittelbar."; //ToDo Language
                }
            }
        }

        xhr.open("POST", "system/modules/con4gis_projects/assets/php/c4gGetAddress.php", true);
        xhr.overrideMimeType("text/plain; charset=utf-8");

        xhr.send(fd);
    }
}

function stopwatch(id, seconds, overlay_id, link) {

    var element = document.getElementById(id);

    var secondPassed = function secondPassed() {
        var minutes = Math.round((seconds - 30)/60);
        var remainingSeconds = seconds % 60;
        if (remainingSeconds < 10) {
            remainingSeconds = "0" + remainingSeconds;
        }
        element.innerHTML = minutes + ":" + remainingSeconds;
        if (seconds == 0) {
            clearInterval(countdownTimer);
            if ( (overlay_id != "") && (link != "")) {
                document.getElementById("c4g_brick_overlay_content").innerHTML = "<video id='"+overlay_id+"_animation' autoplay><source src='"+link+"' type='video/mp4'></video>";
                jQuery("#"+overlay_id).click();
            }

            jQuery("#"+id+"_action").click();
        } else {
            seconds--;
        }
    }

    var countdownTimer = setInterval(secondPassed, 1000);
}

function changeNumberFormat(id,number)
{
    var withoutpoints;
    var cutted;
    var count;
    var start;
    var completed="";
    var i;
    withoutpoints = number.replace(/\./, "");
    cutted = withoutpoints.split(",");
    count = Math.floor(cutted[0].length/3);
    start = cutted[0].length-count*3;
    completed = cutted[0].substr(0, start);
    for(i=1; i<=count; i++)
    {
        if(!(i==1 && start==0))
            completed += ".";
        anfang = start+(i-1)*3;
        completed += cutted[0].substr(anfang, 3);
    }
    if(cutted.length>1) {
        completed += ","+cutted[1];
    }
    document.getElementById(id).value = completed;
}

function C4GPopupHandler(e) {
    jQuery.magnificPopup.close()
};

function showAnimation(id, callFunction) {
    var brick_api = apiBaseUrl+"/c4g_brick_ajax",
        animation_id,
        animation_source,
        animation_type = "video/mp4",
        animation_function,
        animation_param1,
        animation_param2,
        animation_param3;

    jQuery.ajax({
        dataType: "json",
        url: brick_api + "/"+id+"/" + "buttonclick:" + callFunction + "?id=0",
        success: function (data) {
            animation_id = data["animation_name"] + "_animation";
            animation_source = data["animation_source"];
            animation_function = data["animation_function"];
            animation_param1 = data["animation_param1"];
            animation_param2 = data["animation_param2"];
            animation_param3 = data["animation_param3"];
            jQuery.magnificPopup.open({
                items: { src: "<video id="+animation_id+" autoplay><source src="+animation_source+" type="+animation_type+"></video>" },
                type: "inline"}, 0);
            document.getElementById(animation_id).addEventListener("ended",C4GPopupHandler,false);

            if (animation_function) {
              var fn = window[animation_function];
              if (animation_param1 && animation_param2 && animation_param3) {
                fn(animation_param1, animation_param2, animation_param3);
              } else if (animation_param1 && animation_param2) {
                fn(animation_param1, animation_param2);
              }else if (animation_param1) {
                fn(animation_param1);
              } else {
                fn();
              }
            }
        }

    });
}


function clickC4GTab(tab_id){
    jQuery(document.getElementsByClassName('c4gGuiTabLink')).removeClass("ui-state-active");
    // jQuery(document.getElementsByClassName('c4gGuiTabLink')).removeClass("ui-state-focus");
    jQuery(document.getElementsByClassName('c4gGuiTabLink')).addClass("ui-state-default");
    jQuery(document.getElementsByClassName('c4gGuiTabContent')).removeClass('current');
    jQuery(document.getElementsByClassName(tab_id)).removeClass("ui-state-default");
    jQuery(document.getElementsByClassName(tab_id)).addClass("ui-state-active");
    jQuery(document.getElementsByClassName(tab_id+"_content")).addClass("current");
}

function clickNextTab() {
    switchTab('+');
}

function clickPreviousTab() {
  switchTab('-');
}

function switchTab(mode) {
  var button = document.getElementsByClassName('ui-state-active')[0];
  var tabId = button.getAttribute('data-tab');
  var number = parseInt(tabId.substring(tabId.length - 1, tabId.length), 10);
  if (mode === '+') {
    number++;
  } else {
    number--;
  }
  if ((number < 0) || (number >= document.getElementsByClassName('c4gGuiTabLink').length)) {
    return false;
  }
  var newTabId = tabId.substr(0, tabId.length - 2);
  newTabId += '_' + number;
  clickC4GTab(newTabId);
}

function checkC4GTab() {
    var hide, hideElements = new Array(), showElements = new Array();
    var classname;
    var isVisible;
    var tabElements = jQuery(document.getElementsByClassName('c4gGuiTabLink'));
    var childElement;
    if (tabElements) {
        for(i=0; i<=tabElements.length; i++)
        {
            hide = true;
            classname = "c4g_tab_"+i+"_content";
            var tabContent = jQuery(document.getElementsByClassName(classname));
            if (tabContent && tabContent[0] && tabContent[0].children) {
                isVisible = 0;
                for(j=0; j<=tabContent[0].children.length; j++)
                {
                     childElement = tabContent[0].children[j];
                     if (childElement && jQuery(childElement).css("display") !== "none") {
                        isVisible++;

                        for(k=0; k<=childElement.children.length; k++) {
                            childOfChildElement = jQuery(childElement.children[k]);
                            if (jQuery(childOfChildElement).hasClass("formdata") || jQuery(childOfChildElement).attr("for")) {
                                if (childOfChildElement && (jQuery(childOfChildElement).css("display") !== "none") &&
                                    !jQuery(childOfChildElement).hasClass("c4g_condition")) {
                                    isVisible++;
                                }
                            }
                        }
                    }
                }

                //ToDo minValue optimization
                if (isVisible > 7) {
                    hide = false;
                }
            }

            if (hide) {
                hideElements[i] = jQuery(tabElements[i]);
            } else {
                showElements[i] = jQuery(tabElements[i]);
            }

        }

        // TODO hier liegt das Problem mit den verschwindenen Tabs, JS debugging!
        for(i=0; i<=hideElements.length; i++)
        {
            jQuery(hideElements[i]).hide();
        }
        for(i=0; i<=showElements.length; i++)
        {
            jQuery(showElements[i]).show();
        }

    }

    $chosenContainer = document.getElementsByClassName("chosen-container");
    if ($chosenContainer) {
        for(i = 0; i < $chosenContainer.length; i++)
        {
            if ($chosenContainer[i].style.width != "0px") {
                jQuery($chosenContainer[i]).show();
            } else {
                jQuery($chosenContainer[i]).hide();
            }
        }
    }
}

function replaceC4GDialog(dialogId) {
  // check if there exists a dialog div with id and without id
  // in that case, throw away the div without dialogId
  if (dialogId !== -1) {
    var oldDialog = document.getElementById('c4gGuiDialogbrickdialog');
    var newDialog = document.getElementById('c4gGuiDialogbrickdialog' + dialogId);
    if (oldDialog && newDialog) {
      oldDialog.parentNode.removeChild(oldDialog);
    }
  }
}
