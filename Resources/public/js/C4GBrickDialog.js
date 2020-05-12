/*
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
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
        jQuery(function(jQuery){
            if (lang) {
                regional = jQuery.datepicker.regional[lang.substr(0, 2)];
            } else {
                regional = ['en'];
            }
            jQuery.datepicker.setDefaults(regional);
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
    //jQuery.magnificPopup.open({ items: { src: object.dataset.linkHref }, type: 'iframe' });
    jQuery.magnificPopup.open({ items: { src: object.dataset.linkHref }, type:  "iframe" }, 0);

    return false;
}

/**
 *
 */
function closePopupWindow()
{
    jQuery.magnificPopup.close();
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
 * @param fileList
 * @param path
 * @param uploadURL
 * @param deleteURL
 * @param fieldName
 * @param targetField
 * @param mimeTypes
 */
function handleC4GBrickFile(fileList, path, uploadURL, deleteURL, fieldName, targetField, mimeTypes) {
    //useful for canvas saving
    if (document.getElementById("c4g_file") && document.getElementById("c4g_file").getAttribute("dataURL")) {
        fileList = [];
        var blobBin = atob(document.getElementById("c4g_file").getAttribute("dataURL").split(',')[1]);
        var array = [];
        var i = 0;
        for(var i = 0; i < blobBin.length; i++) {
          array.push(blobBin.charCodeAt(i));
        }
        var file = new Blob([new Uint8Array(array)], {type: 'image/png'});
        file.name = fieldName+'.png';
        fileList[0] = file;
    }
    if (fileList && fileList[0]) {
        if (document.getElementById(uploadURL+fieldName).value !== fileList[0]) {
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

            C4GBrickFileUpload(fileList[0], path, uploadURL, deleteURL, fieldName, targetField, mimeTypes);
        }
    }
}

/**
 * @param button
 */
function deleteC4GBrickFile(button) {
    var inputFields = button.parentNode.getElementsByTagName('input');
    var upload = inputFields.item(0);
    var del = inputFields.item(1);
    /*var file = inputFields.item(2);*/
    var link = button.parentNode.getElementsByClassName('c4g_uploadLink').item(0);
    del.value = upload.value;
    upload.value = "";
    link.innerHTML = "";
    button.style.display = "none";

    /*if (targetField) {
        if (document.getElementsByClassName("c4g_"+targetField+"_src")[0]) {
            document.getElementsByClassName("c4g_"+targetField+"_src")[0].getElementsByTagName("img")[0].src = xhr.responseText;
        }
    }*/
}

/**
 * @param button
 */
function deleteC4GBrickImage(button) {
    button.parentNode.removeChild(button.parentNode.firstChild);
}

/**
 *
 * @param file
 * @param path
 * @param uploadURL
 * @param deleteURL
 * @param fieldName
 * @param targetField
 * @param mimeTypes
 */
function C4GBrickFileUpload( file, path, uploadURL, deleteURL, fieldName, targetField, mimeTypes )
{
    var xhr = new XMLHttpRequest();

    var fd = new FormData();
    fd.append("File", file);
    fd.append("Path", path);
    fd.append("MimeTypes", mimeTypes);
    fd.append("REQUEST_TOKEN", c4g_rq);
    fd.append("name", file.name);

    xhr.onreadystatechange = function(){
        if (xhr.readyState===4 && xhr.status===200) {
            var filename = JSON.parse(xhr.responseText)[0];
            var field = document.getElementById(uploadURL+fieldName);
            field.value = filename;
            document.getElementById("c4g_uploadLink_"+fieldName).innerHTML = "<a href='" + filename + "' target='_blank'>" + file.name.replace("C:\\fakepath\\", "") + "</a>";
            if (document.getElementById("c4g_deleteButton_"+fieldName)) {
                document.getElementById("c4g_deleteButton_"+fieldName).style = "display:inline";
            }
            if (deleteURL !== '') {
                document.getElementById(deleteURL+fieldName).value = "";
            }
            if (targetField) {
                if (document.getElementsByClassName("c4g_"+targetField+"_src")[0]) {
                    document.getElementsByClassName("c4g_"+targetField+"_src")[0].getElementsByTagName("img")[0].src = filename;
                }
            }
        }
    };

    xhr.open("POST", "con4gis/upload_file", true);
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
/*
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
    var fieldType = field.dataset.conditionType.split("~");


    var currentName;
    var currentValue;
    var currentType;

    for(f = 0; f < fieldNames.length; f++)
    {
        currentName = "c4g_" + fieldNames[f];
        currentValue = fieldValues[f];
        currentType = fieldType[f];

        if (currentType == "method") {
            continue;
        }

        checkValue = document.getElementById(currentName) ? document.getElementById(currentName).value : false;

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
                try {
                    jQuery(field).removeClass("formdata");
                    if (jQuery(field).hasClass('chzn-select')) {
                        jQuery(field).removeClass("chzn-select");
                        jQuery(field).addClass("chzn-select-disabled");
                        jQuery(field).style = "display:none";
                        jQuery(field).trigger('chosen:updated');
                    }
                    jQuery(field.hide());
                    jQuery(field.removeAttr("selected"));
                } catch (err) {
                    //ToDo
                }
            }
        }
    }

    for(f = 0; f < fieldNames.length; f++)
    {
        currentName = "c4g_" + fieldNames[f];
        currentValue = fieldValues[f];
        currentType = fieldType[f];

        if (currentType == "method") {
            continue;
        }

        checkValue = document.getElementById(currentName) ? document.getElementById(currentName).value : false;

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
                            }
                            jQuery(field.children[o]).hide();
                        } else {
                            if (jQuery(field.children[o]).hasClass('chzn-select-disabled')) {
                                jQuery(field.children[o]).removeClass("chzn-select-disabled");
                                jQuery(field.children[o]).addClass("chzn-select");
                                jQuery(field.children[o]).hide();
                            }
                        }

                    } catch (err) {
                        //ToDo
                    }
                }
                try {
                    jQuery(field).show();
                    jQuery(field).addClass("formdata");

                    if (jQuery(field).hasClass("c4g_display_none")) {
                        if (jQuery(field).hasClass('chzn-select')) {
                            jQuery(field).removeClass("chzn-select");
                            jQuery(field).addClass("chzn-select-disabled");
                        }
                        jQuery(field).hide();
                    } else {
                        if (jQuery(field).hasClass('chzn-select-disabled')) {
                            jQuery(field).removeClass("chzn-select-disabled");
                            jQuery(field).addClass("chzn-select");
                            jQuery(field).hide();
                        }
                    }

                } catch (err) {
                    //ToDo
                }
            }
        }
    }
}

/**
 *
 * @param field
 * @constructor
 */
function C4GCheckMethodswitchCondition(field)
{
    var fieldNames = field.dataset.conditionName.split("~");
    var fieldFunction = field.dataset.conditionFunction.split("~");
    var fieldType = field.dataset.conditionType.split("~");

    var currentName;
    var currentFunction;
    var currentType;

    for(f = 0; f < fieldNames.length; f++)
    {
        currentName = "c4g_" + fieldNames[f];
        currentFunction = window[fieldFunction[f]];
        currentType = fieldType[f];

        if (currentType == "value") {
            continue;
        }

        if (!currentFunction) {
            continue;
        }

        checkValue = document.getElementById(currentName) ? document.getElementById(currentName).value : false;

        if (checkValue) {
            if (!currentFunction(checkValue)) {
                for (o = 0; o < field.children.length; o++) {
                    try {
                        jQuery(field.children[o]).removeClass("formdata");
                        if (jQuery(field.children[o]).hasClass('chzn-select')) {
                            jQuery(field.children[o]).removeClass("chzn-select");
                            jQuery(field.children[o]).addClass("chzn-select-disabled");
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
        currentType = fieldType[f];

        if (currentType == "value") {
            continue;
        }
        if (!currentFunction) {
            continue;
        }

        checkValue = document.getElementById(currentName) ? document.getElementById(currentName).value : false;

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
                            }
                            jQuery(field.children[o]).removeAttr("selected");
                            jQuery(field.children[o]).removeAttr("required");
                            jQuery(field.children[o]).hide();
                        } else {
                            if (jQuery(field.children[o]).hasClass('chzn-select-disabled')) {
                                jQuery(field.children[o]).removeClass("chzn-select-disabled");
                                jQuery(field.children[o]).addClass("chzn-select");
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
                                    }
                                    jQuery(field.children[o].children[p]).removeAttr("selected");
                                    jQuery(field.children[o].children[p]).removeAttr("required");
                                    jQuery(field.children[o].children[p]).hide();
                                } else {
                                    if (jQuery(field.children[o].children[p]).hasClass('chzn-select-disabled')) {
                                        jQuery(field.children[o].children[p]).removeClass("chzn-select-disabled");
                                        jQuery(field.children[o].children[p]).addClass("chzn-select");
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
            if (fields[i].dataset.conditionName && fields[i].dataset.conditionValue && fields[i].dataset.conditionType && (fields[i].dataset.conditionType.split("~").includes("value"))) {
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
            if (fields[i].dataset.conditionName && fields[i].dataset.conditionFunction && fields[i].dataset.conditionType && (fields[i].dataset.conditionType.split("~").includes("method"))) {
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

        var latElem = document.getElementById("c4g_brick_geopicker_geoy");
        var lonElem = document.getElementById("c4g_brick_geopicker_geox");

        if (latElem && lonElem) {
            var lat = document.getElementById("c4g_brick_geopicker_geoy").value;
            var lon = document.getElementById("c4g_brick_geopicker_geox").value;

            var xhr = new XMLHttpRequest();

            var fd = new FormData;
            fd.append("Lat", lat);
            fd.append("Lon", lon);
            fd.append("Profile", profile_id);
            fd.append("REQUEST_TOKEN", c4g_rq);

            xhr.onreadystatechange = function() {
                if (xhr.readyState==4 && xhr.status==200){
                    if (xhr.responseText != "") {
                        document.getElementById("c4g_brick_geopicker_address").value = JSON.parse(xhr.responseText);
                    } else {
                        document.getElementById("c4g_brick_geopicker_address").value = "Adresse nicht ermittelbar."; //ToDo Language
                    }
                }
            };
            var url = "/con4gis/get_address/" + profile_id + '/' + lat + '/' + lon;
            xhr.open("GET", url, true);
            xhr.overrideMimeType("text/plain; charset=utf-8");
            xhr.send();
        } else {
            var addrElem = document.getElementById("c4g_brick_geopicker_address");
            if (addrElem) {
                addrElem.remove();
            }
        }
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
        done: function (data) {
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
  if (jQuery(document.getElementsByClassName(newTabId)).css("display") !== "none") {
      clickC4GTab(newTabId);
  } else {
     clickC4GTab(newTabId);
     switchTab(mode);
  }
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
                        //isVisible++;

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

                if (isVisible > 0) {
                    hide = false;
                }
            }

            if (hide) {
                hideElements[i] = jQuery(tabElements[i]);
            } else {
                showElements[i] = jQuery(tabElements[i]);
            }

        }

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

function resizeChosen(fieldId) {
  var chosenButton = document.getElementById(fieldId);
  var firstRun = true;
  jQuery(chosenButton).on('click', function(event) {

    var chosenDrop = chosenButton.getElementsByClassName('chosen-drop')[0];
    jQuery(this).on('mouseleave', function(event) {
      chosenDrop.style.display = 'none';
      chosenDrop.style.position = 'absolute';
    });
    var chosenSearch = chosenButton.getElementsByClassName('chosen-search')[0];
    jQuery(chosenSearch).on('click', function (event) {
      event.stopPropagation();
    });
    chosenDrop.style.display = 'block';
    chosenDrop.style.position = 'relative';
    if (firstRun) {
      jQuery(chosenDrop).on('click', function(event) {
        this.style.display = 'none';
        this.style.position = 'absolute';
        event.stopPropagation();
      });
      firstRun = false;
    }
  });
}

/**
 * Focuses ("selects") and scrolls to the element with the given id. The element must be visible, i.e. not hidden under an accordion, etc.
 * @param elementId
 */

function focusOnElement(elementId) {
    if (elementId === '') {
        return
    }
    elementId = document.getElementById(elementId);
    if (elementId === null) {
        return
    }
    elementId.focus();
    elementId.scrollIntoView(false);
}

function callActionViaAjax(action) {
    var gui = c4g.projects.C4GGui;
    var url = gui.options.ajaxUrl + '/' + gui.options.moduleId + '/' + action;
    jQuery.ajax({
        url: url
    }).done(function (data) {
        gui.fnHandleAjaxResponse(data, gui.options.moduleId);
    });
}

/**
 * Function that removes all the default accordion icons. Use it as an onLoadScript in DialogParams.
 */

function removeAccordionIcons() {
    var icons = document.getElementsByClassName('ui-accordion-header-icon');
    if (icons.length > 0) {
        var index = icons.length;
        while (index > 0) {
            index -= 1;
            icons.item(index).remove();
            var icon = icons.item(index);
        }
    } else {
        setTimeout(function(){removeAccordionIcons();}, 200);
    }
}

/**
 * Opens the accordion with the given index. Use 'all' to open all accordions.
 * @param index
 */

function openAccordion(index) {
    if (index === 'all') {
        setTimeout(function () {
            var accordions = document.getElementsByClassName('c4g_brick_headline');
            var event = new MouseEvent('click', {
                view: window,
                bubbles: true,
                cancelable: true
            });
            var length = accordions.length;
            while (length > 0) {
                length -= 1;
                accordions[length].dispatchEvent(event);
                // console.log('clicked ' + length);
            }
        }, 100);
    } else {
        setTimeout(function () {
            var accordions = document.getElementsByClassName('c4g_brick_headline');
            var target = accordions[index];
            var event = new MouseEvent('click', {
                view: window,
                bubbles: true,
                cancelable: true
            });
            target.dispatchEvent(event);
            // console.log('clicked ' + index);
        }, 100);
    }
}

/**
 * Method to remove data sets from the sub dialog (C4GSubDialogField)
 * @param button
 * @param event
 */

function removeSubDialog(button, event) {
    if (typeof(event) !== 'undefined') {
        event.stopPropagation();
    }
    showConfirmationDialog(button.dataset.message, 'Bestätigung', 'Ja', 'Nein',  function() {
        while ((button) && (button.parentNode) && (button.parentNode.firstChild)) {
            button.parentNode.removeChild(button.parentNode.firstChild);
        }
    });
}

/**
 * Method to add data sets to the sub dialog (C4GSubDialogField)
 * @param button
 * @param event
 */
function addSubDialog(button, event) {
    if (typeof(event) !== 'undefined') {
        event.stopPropagation();
    }
    var target = document.getElementById(button.dataset.target);
    button.dataset.index = parseInt(button.dataset.index, 10) + 1;
    var string = document.getElementById(button.dataset.template).innerHTML.split(button.dataset.wildcard).join(button.dataset.index);
    var newElement = document.createElement('div');
    newElement.classList.add('c4g_sub_dialog_set');
    newElement.classList.add('c4g_sub_dialog_set_new');
    newElement.innerHTML = string;
    var child;
    if ((target.firstChild !== null) && (button.dataset.insert === 'before')) {
        child = target.insertBefore(newElement, target.firstChild);
    } else {
        child = target.appendChild(newElement);
    }
    var inputs = child.getElementsByTagName('input');
    // console.log(inputs);
    var j = 0;
    while (j < inputs.length) {
        if (typeof(inputs[j]) !== 'undefined') {
            if (inputs[j].disabled === true) {
                inputs[j].disabled = false;
            }
            if (inputs[j].readOnly === true) {
                inputs[j].readOnly = false;
            }
        }
        j += 1;
    }
    var textareas = child.getElementsByTagName('textarea');
    // console.log(inputs);
    var k = 0;
    while (k < textareas.length) {
        if (typeof(textareas[k]) !== 'undefined') {
            if (textareas[k].disabled === true) {
                textareas[k].disabled = false;
            }
            if (textareas[k].readOnly === true) {
                textareas[k].readOnly = false;
            }
        }
        k += 1;
    }

    var butts = child.getElementsByClassName('js-sub-dialog-button');
    // console.log(inputs);
    var l = 0;
    while (l < butts.length) {
        if (typeof(butts[l]) !== 'undefined') {
            if (butts[l].disabled === true) {
                butts[l].disabled = false;
            }
            if (butts[l].readOnly === true) {
                butts[l].readOnly = false;
            }
            if (butts[l].style.display === 'none') {
                butts[l].style.display = 'unset';
            }
        }
        l += 1;
    }
}

function editSubDialog(button, event) {
    if (typeof(event) !== 'undefined') {
        event.stopPropagation();
    }
    var ids = button.dataset.fields.split(',');
    var index = 0;
    while (index < ids.length) {
        var element = document.getElementById('c4g_' + ids[index]);
        //console.log(element);
        if ((typeof(element) !== 'undefined')) {
            if (element.hasAttribute('disabled')) {
                element.removeAttribute('disabled');
            } else {
                element.setAttribute('disabled', '')
            }
            if (element.hasAttribute('readonly')) {
                element.removeAttribute('readonly');
            } else {
                element.setAttribute('readonly', '')
            }
        }
        var element2 = document.getElementById('c4g_uploadButton_' + ids[index]);
        //console.log(element);
        if ((typeof(element2) !== 'undefined') && element2 !== null) {
            if (element2.tagName === 'BUTTON') {
                if (element2.hasAttribute('disabled')) {
                    element2.removeAttribute('disabled');
                } else {
                    element2.setAttribute('disabled', '')
                }
                if (element2.hasAttribute('readonly')) {
                    element2.removeAttribute('readonly');
                } else {
                    element2.setAttribute('readonly', '')
                }
                if (element2.style.display === 'none') {
                    element2.style.display = 'unset';
                } else {
                    element2.style.display = 'none';
                }
            }
        }
        var element3 = document.getElementById('c4g_deleteButton_' + ids[index]);
        //console.log(element);
        if ((typeof(element3) !== 'undefined') && element3 !== null) {
            if (element2.tagName === 'BUTTON') {
                if (element3.hasAttribute('disabled')) {
                    element3.removeAttribute('disabled');
                } else {
                    element3.setAttribute('disabled', '')
                }
                if (element3.hasAttribute('readonly')) {
                    element3.removeAttribute('readonly');
                } else {
                    element3.setAttribute('readonly', '')
                }
                if (element3.style.display === 'none') {
                    element3.style.display = 'unset';
                } else {
                    element3.style.display = 'none';
                }
            }
        }
        // console.log(ids[index]);
        var elements = button.parentNode.getElementsByClassName(ids[index]);
        //console.log(elements);
        var i = 0;
        while (i < elements.length) {
            var inputs = elements[i].getElementsByTagName('input');
            var j = 0;
            while (j < inputs.length) {
                if (typeof(inputs[j]) !== 'undefined') {
                    if (inputs[j].hasAttribute('disabled')) {
                        inputs[j].removeAttribute('disabled');
                    } else {
                        inputs[j].setAttribute('disabled', '')
                    }
                    if (inputs[j].hasAttribute('readonly')) {
                        inputs[j].removeAttribute('readonly');
                    } else {
                        inputs[j].setAttribute('readonly', '')
                    }
                }
                j += 1;
            }
            var textareas = elements[i].getElementsByTagName('textarea');
            var k = 0;
            while (k < textareas.length) {
                if (typeof(textareas[k]) !== 'undefined') {
                    if (textareas[k].hasAttribute('disabled')) {
                        textareas[k].removeAttribute('disabled');
                    } else {
                        textareas[k].setAttribute('disabled', '')
                    }
                    if (textareas[k].hasAttribute('readonly')) {
                        textareas[k].removeAttribute('readonly');
                    } else {
                        textareas[k].setAttribute('readonly', '')
                    }
                }
                k += 1;
            }
            var butts = elements[i].getElementsByTagName('button');
            console.log(butts);
            var l = 0;
            while (l < butts.length) {
                if (typeof(butts[l]) !== 'undefined') {
                    butts[k].removeAttribute('disabled');
                    butts[k].removeAttribute('readonly');
                    if (butts[l].style.display === 'none') {
                        butts[l].style.display = 'unset';
                    } else {
                        butts[l].style.display = 'none';
                    }
                }
                l += 1;
            }
            i += 1;
        }
        index += 1;
    }
    var parent = button.parentNode;
    if (parent.classList.contains('c4g_sub_dialog_set_uneditable')) {
        parent.classList.remove('c4g_sub_dialog_set_uneditable');
        button.innerHTML = button.dataset.captionfinishediting;
    } else {
        parent.classList.add('c4g_sub_dialog_set_uneditable');
        button.innerHTML = button.dataset.captionbeginediting;
    }
}

function showConfirmationDialog(message,title,yesLabel, noLabel, yesCallback){
    jQuery('<div></div>').appendTo('body')
        .html('<div>'+message+'?</div>')
        .dialog({
            modal: true, title: title, zIndex: 10000, autoOpen: true,
            width: 'auto', resizable: false,
            buttons: [
                {
                    text: yesLabel,
                    click: function () {
                    jQuery(this).dialog("close");
                    yesCallback();
                    }
                },
                {
                    text: noLabel,
                    click: function () {
                        jQuery(this).dialog("close");
                    }
                }
            ],
            close: function (event, ui) {
                jQuery(this).remove();
            }
        });
}
