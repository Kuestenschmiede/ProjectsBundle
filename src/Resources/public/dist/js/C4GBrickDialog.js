function C4GTimePicker(e,t,n){var i=new Date,a=String(i.getMinutes()),i=String(i.getHours()),e=document.getElementById(e),n=n.previousSibling;1==a.length&&(a="0"+a),1==i.length&&(i="0"+i),"gettime"==t&&(e.value=i+":"+a,n.value=i+":"+a)}function C4GDatePicker(n,e,t,i,l,r,d,o){if("date"==e){e="";t&&(e=new Date(1e3*t));t="";i&&(t=new Date(1e3*i));const c=document.querySelector("#"+n);c.datepicker&&c.datepicker.destroy();var i=new Array,i=o.split(","),s=new Array;for(a in s=d.split(","))s[a]=parseInt(s[a]);const u=new window.Datepicker(c,{buttonClass:"c4g__btn",language:r||"de",format:l,datesDisabled:i,daysOfWeekDisabled:s,minDate:e,maxDate:t,weekStart:1,todayHighlight:!0,orientation:"auto left",autohide:!0,useCurrent:!0});c.datepicker&&c.addEventListener("changeDate",function(e){jQuery("#"+n).trigger("change");var t=n.indexOf("_picker");t&&0<t&&(t=n.substr(0,t),jQuery("#"+t).val(u.getDate(l)),jQuery("#"+t).trigger("change"))})}}function C4GDateTimePicker(e){jQuery("#"+e).appendDtpicker({dateFormat:"DD.MM.YY - hh:mm",locale:"de",closeOnSelected:"true",todayButton:!1})}function C4GFilterButtonTiles(e){document.getElementsByClassName("c4g__btn-tile"),e.value}function C4GSearchTiles(e){var t=document.getElementsByClassName("c4g__btn-tile");if(text,value=e.value.toLowerCase(),founded,value)for(aTimer=0;aTimer<t.length;aTimer+=1){for(founded=!1,fields=t[aTimer].children[0].children,fTimer=0;fTimer<fields.length;fTimer+=1)fields[fTimer].innerHTML&&(text=fields[fTimer].innerHTML.toLowerCase(),searchedStatus=text.search(value),"-1"==searchedStatus||(founded=!0));1==founded?t[aTimer].style.display="":t[aTimer].style.display="none"}else for(cTimer=0;cTimer<t.length;cTimer+=1)t[cTimer].style.display=""}function tileSort(e){var t=document.getElementsByClassName("c4g_brick_tiles")[0];"row wrap"==t.style.flexFlow?(t.style.flexFlow="row-reverse wrap-reverse",e.textContent=e.dataset.langDesc):(t.style.flexFlow="row wrap",e.textContent=e.dataset.langAsc)}function createNewPopupWindow(e){return jQuery.magnificPopup.open({items:{src:e.dataset.linkHref},type:"iframe"},0),!1}function closePopupWindow(){jQuery.magnificPopup.close()}function handleBoolSwitch(e,t,n){e&&t&&(e=e.id,t=t.id,e="checkbox"==document.getElementById(e).type?document.getElementById(e).checked:document.getElementById(e).value,document.getElementById(t).disabled="1"==n?e:!e)}function handleC4GBrickFile(e,t,n,i,a,l,r){if(document.getElementById("c4g_file")&&document.getElementById("c4g_file").getAttribute("dataURL")){e=[];for(var d=atob(document.getElementById("c4g_file").getAttribute("dataURL").split(",")[1]),o=[],s=0,s=0;s<d.length;s++)o.push(d.charCodeAt(s));var c=new Blob([new Uint8Array(o)],{type:"image/png"});c.name=a+".png",e[0]=c}var u,h;e&&e[0]&&document.getElementById(n+a).value!==e[0]&&((u=document.createElement("img")).file=e[0],u.name="img_0",u.classList.add("obj"),(c=new FileReader).onload=(h=u,function(e){h.src=e.target.result}),c.readAsDataURL(e[0]),C4GBrickFileUpload(e[0],t,n,i,a,l,r))}function deleteC4GBrickFile(e){var t=e.parentNode.getElementsByTagName("input"),n=t.item(0),i=t.item(1),t=e.parentNode.getElementsByClassName("c4g_uploadLink").item(0);i.value=n.value,n.value="",t.innerHTML="",e.style.display="none"}function deleteC4GBrickImage(e){e.parentNode.removeChild(e.parentNode.firstChild)}function C4GBrickFileUpload(t,e,n,i,a,l,r){var d=new XMLHttpRequest,o=new FormData;o.append("File",t),o.append("Path",e),o.append("MimeTypes",r),o.append("REQUEST_TOKEN",c4g_rq),o.append("name",t.name),d.onreadystatechange=function(){var e;4===d.readyState&&200===d.status&&(e=JSON.parse(d.responseText)[0],document.getElementById(n+a).value=e,document.getElementById("c4g_uploadLink_"+a).innerHTML="<a href='"+e+"' target='_blank'>"+t.name.replace("C:\\fakepath\\","")+"</a>",document.getElementById("c4g_deleteButton_"+a)&&(document.getElementById("c4g_deleteButton_"+a).style="display:inline"),""!==i&&(document.getElementById(i+a).value=""),l&&document.getElementsByClassName("c4g_"+l+"_src")[0]&&(document.getElementsByClassName("c4g_"+l+"_src")[0].getElementsByTagName("img")[0].src=e))},d.open("POST","con4gis/upload_file",!0),d.overrideMimeType("text/plain; charset=x-user-defined-binary"),d.send(o)}function C4GCallOnChange(e){var t=document.getElementsByClassName("c4g_brick_dialog")[0].children;t=C4GSortConditionFields(t);for(var n=0;n<t.length;n++)C4GCheckCondition(t[n]);var i=document.getElementsByClassName("c4gGuiCollapsible_target");if(i)for(n=0;n<i.length;n++)for(t=C4GSortConditionFields(t=i[n].children),j=0;j<t.length;j++)C4GCheckCondition(t[j]);var a=document.getElementsByClassName("c4gGuiTabContent");if(a)for(n=0;n<a.length;n++){for(t=C4GSortConditionFields(t=a[n].children),j=0;j<t.length;j++)C4GCheckCondition(t[j]);checkC4GTab()}}function C4GCallOnChangeMethodswitchFunction(e){var t=document.getElementsByClassName("c4g_brick_dialog")[0].children;t=C4GSortConditionMethodswitchFields(t);for(var n=0;n<t.length;n++)C4GCheckMethodswitchCondition(t[n]);var i=document.getElementsByClassName("c4gGuiCollapsible_target");if(i)for(n=0;n<i.length;n++)for(t=C4GSortConditionMethodswitchFields(t=i[n].children),j=0;j<t.length;j++)C4GCheckMethodswitchCondition(t[j]);var a=document.getElementsByClassName("c4gGuiTabContent");if(a)for(n=0;n<a.length;n++){for(t=C4GSortConditionMethodswitchFields(t=a[n].children),j=0;j<t.length;j++)C4GCheckMethodswitchCondition(t[j]);checkC4GTab()}}function C4GCheckCondition(e){var t,n,i=e.dataset.conditionName.split("~"),a=e.dataset.conditionValue.split("~"),l=e.dataset.conditionType.split("~");let r=0,d=0;for(f=0;f<i.length;f++)if(t="c4g_"+i[f],n=a[f],"method"!=l[f]&&(d++,checkValue=!!document.getElementById(t)&&document.getElementById(t).value,checkValue||!checkValue))if(checkValue!=n){for(o=0;o<e.children.length;o++)try{for(jQuery(e.children[o]).removeClass("formdata"),jQuery(e.children[o]).hasClass("chzn-select")&&(jQuery(e.children[o]).removeClass("chzn-select"),jQuery(e.children[o]).addClass("chzn-select-disabled"),jQuery(e.children[o]).style="display:none",jQuery(e.children[o]).trigger("chosen:updated")),jQuery(e.children[o]).hide(),jQuery(e.children[o]).removeAttr("selected"),p=0;p<e.children[o].children.length;p++)jQuery(e.children[o].children[p]).hasClass("datepicker")||(jQuery(e.children[o].children[p]).removeClass("formdata"),jQuery(e.children[o].children[p]).hasClass("chzn-select")&&(jQuery(e.children[o].children[p]).removeClass("chzn-select"),jQuery(e.children[o].children[p]).addClass("chzn-select-disabled"),jQuery(e.children[o].children[p]).style="display:none",jQuery(e.children[o].children[p]).trigger("chosen:updated")),jQuery(e.children[o].children[p]).hide(),jQuery(e.children[o].children[p]).removeAttr("selected"))}catch(e){}try{jQuery(e).removeClass("formdata"),jQuery(e).hasClass("chzn-select")&&(jQuery(e).removeClass("chzn-select"),jQuery(e).addClass("chzn-select-disabled"),jQuery(e).style="display:none",jQuery(e).trigger("chosen:updated")),jQuery(e.hide()),jQuery(e.removeAttr("selected"))}catch(e){}}else r++;for(f=0;f<i.length;f++)if(t="c4g_"+i[f],n=a[f],"method"!=l[f]&&(checkValue=!!document.getElementById(t)&&document.getElementById(t).value,checkValue&&checkValue==n&&r==d)){for(o=0;o<e.children.length;o++)try{for(jQuery(e.children[o]).show(),jQuery(e.children[o]).addClass("formdata"),jQuery(e.children[o]).hasClass("c4g_display_none")?(jQuery(e.children[o]).hasClass("chzn-select")&&(jQuery(e.children[o]).removeClass("chzn-select"),jQuery(e.children[o]).addClass("chzn-select-disabled")),jQuery(e.children[o]).hide()):jQuery(e.children[o]).hasClass("chzn-select-disabled")&&(jQuery(e.children[o]).removeClass("chzn-select-disabled"),jQuery(e.children[o]).addClass("chzn-select"),jQuery(e.children[o]).hide()),p=0;p<e.children[o].children.length;p++)jQuery(e.children[o].children[p]).hasClass("datepicker")||(jQuery(e.children[o].children[p]).show(),jQuery(e.children[o].children[p]).addClass("formdata"),jQuery(e.children[o].children[p]).hasClass("c4g_display_none")?(jQuery(e.children[o].children[p]).hasClass("chzn-select")&&(jQuery(e.children[o].children[p]).removeClass("chzn-select"),jQuery(e.children[o].children[p]).addClass("chzn-select-disabled")),jQuery(e.children[o].children[p]).hide()):jQuery(e.children[o].children[p]).hasClass("chzn-select-disabled")&&(jQuery(e.children[o].children[p]).removeClass("chzn-select-disabled"),jQuery(e.children[o].children[p]).addClass("chzn-select"),jQuery(e.children[o].children[p]).hide()))}catch(e){}try{jQuery(e).show(),jQuery(e).addClass("formdata"),jQuery(e).hasClass("c4g_display_none")?(jQuery(e).hasClass("chzn-select")&&(jQuery(e).removeClass("chzn-select"),jQuery(e).addClass("chzn-select-disabled")),jQuery(e).hide()):jQuery(e).hasClass("chzn-select-disabled")&&(jQuery(e).removeClass("chzn-select-disabled"),jQuery(e).addClass("chzn-select"),jQuery(e).hide())}catch(e){}}}function C4GCheckMethodswitchCondition(e){var t=e.dataset.conditionName.split("~"),n=e.dataset.conditionFunction.split("~"),i=e.dataset.conditionType.split("~");for(f=0;f<t.length;f++){var a="c4g_"+(d=t[f].split("--"))[0],l=window[n[f]],r=i[f];if("value"!=r&&(l&&(checkValue=!!document.getElementById(a)&&document.getElementById(a).value,(checkValue||!checkValue)&&(d[1]&&(checkValue=checkValue+"--"+d[1]),!l(checkValue)))))for(o=0;o<e.children.length;o++){try{jQuery(e.children[o]).removeClass("formdata"),jQuery(e.children[o]).hasClass("chzn-select")&&(jQuery(e.children[o]).removeClass("chzn-select"),jQuery(e.children[o]).addClass("chzn-select-disabled")),jQuery(e.children[o]).removeAttr("selected"),jQuery(e.children[o]).removeAttr("required"),jQuery(e.children[o]).hide()}catch(e){}if(jQuery(e.children[o]).children)for(p=0;p<jQuery(e.children[o].children).length;p++)try{if(jQuery(e.children[o].children[p]).hasClass("datepicker"))continue;jQuery(e.children[o].children[p]).removeClass("formdata"),jQuery(e.children[o].children[p]).hasClass("chzn-select")&&(jQuery(e.children[o].children[p]).removeClass("chzn-select"),jQuery(e.children[o].children[p]).addClass("chzn-select-disabled")),jQuery(e.children[o].children[p]).removeAttr("selected"),jQuery(e.children[o].children[p]).removeAttr("required"),jQuery(e.children[o].children[p]).hide()}catch(e){}}}for(f=0;f<t.length;f++){var d=t[f].split("--");if(a="c4g_"+d[0],l=window[n[f]],"value"!=(r=i[f])&&(l&&(checkValue=!!document.getElementById(a)&&document.getElementById(a).value,checkValue&&(d[1]&&(checkValue=checkValue+"--"+d[1]),l(checkValue)))))for(o=0;o<e.children.length;o++){try{jQuery(e.children[o]).show(),jQuery(e.children[o]).addClass("formdata"),jQuery(e.children[o]).hasClass("c4g_display_none")?(jQuery(e.children[o]).hasClass("chzn-select")&&(jQuery(e.children[o]).removeClass("chzn-select"),jQuery(e.children[o]).addClass("chzn-select-disabled")),jQuery(e.children[o]).removeAttr("selected"),jQuery(e.children[o]).removeAttr("required"),jQuery(e.children[o]).hide()):jQuery(e.children[o]).hasClass("chzn-select-disabled")&&(jQuery(e.children[o]).removeClass("chzn-select-disabled"),jQuery(e.children[o]).addClass("chzn-select"),jQuery(e.children[o]).hide())}catch(e){}if(jQuery(e.children[o]).children)for(p=0;p<jQuery(e.children[o].children).length;p++)try{if(jQuery(e.children[o].children[p]).hasClass("datepicker"))continue;jQuery(e.children[o].children[p]).show(),jQuery(e.children[o].children[p]).addClass("formdata"),jQuery(e.children[o].children[p]).hasClass("c4g_display_none")?(jQuery(e.children[o].children[p]).hasClass("chzn-select")&&(jQuery(e.children[o].children[p]).removeClass("chzn-select"),jQuery(e.children[o].children[p]).addClass("chzn-select-disabled")),jQuery(e.children[o].children[p]).removeAttr("selected"),jQuery(e.children[o].children[p]).removeAttr("required"),jQuery(e.children[o].children[p]).hide()):jQuery(e.children[o].children[p]).hasClass("chzn-select-disabled")&&(jQuery(e.children[o].children[p]).removeClass("chzn-select-disabled"),jQuery(e.children[o].children[p]).addClass("chzn-select"),jQuery(e.children[o].children[p]).hide())}catch(e){}}}}function C4GSortConditionFields(e){var t=new Array,n=0;if(e)for(i=0;i<e.length;i++)e[i].dataset.conditionName&&e[i].dataset.conditionValue&&e[i].dataset.conditionType&&e[i].dataset.conditionType.split("~").includes("value")&&(t[n]=e[i],n++);return t}function C4GSortConditionMethodswitchFields(e){var t=new Array,n=0;if(e)for(i=0;i<e.length;i++)e[i].dataset.conditionName&&e[i].dataset.conditionFunction&&e[i].dataset.conditionType&&e[i].dataset.conditionType.split("~").includes("method")&&(t[n]=e[i],n++);return t}function C4GGeopickerAddress(e){var t,n,i,a;e&&(t=document.getElementById("c4g_brick_geopicker_geoy"),i=document.getElementById("c4g_brick_geopicker_geox"),t&&i?(t=document.getElementById("c4g_brick_geopicker_geoy").value,a=document.getElementById("c4g_brick_geopicker_geox").value,n=new XMLHttpRequest,(i=new FormData).append("Lat",t),i.append("Lon",a),i.append("Profile",e),i.append("REQUEST_TOKEN",c4g_rq),n.onreadystatechange=function(){4==n.readyState&&200==n.status&&(""!=n.responseText?document.getElementById("c4g_brick_geopicker_address").value=JSON.parse(n.responseText):document.getElementById("c4g_brick_geopicker_address").value="Adresse nicht ermittelbar.")},n.open("GET","/con4gis/get_address/"+e+"/"+t+"/"+a,!0),n.overrideMimeType("text/plain; charset=utf-8"),n.send()):(a=document.getElementById("c4g_brick_geopicker_address"))&&a.remove())}function stopwatch(n,i,a,l){var r=document.getElementById(n),d=setInterval(function(){var e=Math.round((i-30)/60),t=i%60;r.innerHTML=e+":"+(t=t<10?"0"+t:t),0==i?(clearInterval(d),""!=a&&""!=l&&(document.getElementById("c4g_brick_overlay_content").innerHTML="<video id='"+a+"_animation' autoplay><source src='"+l+"' type='video/mp4'></video>",jQuery("#"+a).click()),jQuery("#"+n+"_action").click()):i--},1e3)}function changeNumberFormat(e,t){for(var n="",i=t.replace(/\./,"").split(","),a=Math.floor(i[0].length/3),l=i[0].length-3*a,n=i[0].substr(0,l),r=1;r<=a;r++)1==r&&0==l||(n+="."),anfang=l+3*(r-1),n+=i[0].substr(anfang,3);1<i.length&&(n+=","+i[1]),document.getElementById(e).value=n}function C4GPopupHandler(e){jQuery.magnificPopup.close()}function showAnimation(e,t){var n,i,a,l,r,d,o=apiBaseUrl+"/c4g_brick_ajax";jQuery.ajax({dataType:"json",url:o+"/"+e+"/buttonclick:"+t+"?id=0",done:function(e){n=e.animation_name+"_animation",i=e.animation_source,a=e.animation_function,l=e.animation_param1,r=e.animation_param2,d=e.animation_param3,jQuery.magnificPopup.open({items:{src:"<video id="+n+" autoplay><source src="+i+" type=video/mp4></video>"},type:"inline"},0),document.getElementById(n).addEventListener("ended",C4GPopupHandler,!1),a&&(e=window[a],l&&r&&d?e(l,r,d):l&&r?e(l,r):l?e(l):e())}})}function clickC4GTab(e){jQuery(document.getElementsByClassName("c4gGuiTabLink")).removeClass("c4g__state-active"),jQuery(document.getElementsByClassName("c4gGuiTabLink")).addClass("c4g__state-default"),jQuery(document.getElementsByClassName("c4gGuiTabContent")).removeClass("current"),jQuery(document.getElementsByClassName(e)).removeClass("c4g__state-default"),jQuery(document.getElementsByClassName(e)).addClass("c4g__state-active"),jQuery(document.getElementsByClassName(e+"_content")).addClass("current")}function clickNextTab(){switchTab("+")}function clickPreviousTab(){switchTab("-")}function switchTab(e){var t=document.getElementsByClassName("c4g__state-active")[0].getAttribute("data-tab"),n=parseInt(t.substring(t.length-1,t.length),10);if("+"===e?n++:n--,n<0||n>=document.getElementsByClassName("c4gGuiTabLink").length)return!1;t=t.substr(0,t.length-2);t+="_"+n,"none"!==jQuery(document.getElementsByClassName(t)).css("display")?clickC4GTab(t):(clickC4GTab(t),switchTab(e))}function checkC4GTab(){var e,t,n=new Array,a=new Array,l=jQuery(document.getElementsByClassName("c4gGuiTabLink"));if(l){for(i=0;i<=l.length;i++){var r=!0,d="c4g_tab_"+i+"_content",o=jQuery(document.getElementsByClassName(d));if(o&&o[0]&&o[0].children){for(j=e=0;j<=o[0].children.length;j++)if((t=o[0].children[j])&&"none"!==jQuery(t).css("display"))for(k=0;k<=t.children.length;k++)childOfChildElement=jQuery(t.children[k]),(jQuery(childOfChildElement).hasClass("formdata")||jQuery(childOfChildElement).attr("for"))&&childOfChildElement&&"none"!==jQuery(childOfChildElement).css("display")&&!jQuery(childOfChildElement).hasClass("c4g__form-group")&&e++;0<e&&(r=!1)}r?n[i]=jQuery(l[i]):a[i]=jQuery(l[i])}for(i=0;i<=n.length;i++)jQuery(n[i]).hide();for(i=0;i<=a.length;i++)jQuery(a[i]).show()}if($chosenContainer=document.getElementsByClassName("chosen-container"),$chosenContainer)for(i=0;i<$chosenContainer.length;i++)"0px"!=$chosenContainer[i].style.width?jQuery($chosenContainer[i]).show():jQuery($chosenContainer[i]).hide()}function replaceC4GDialog(e){var t;-1!==e&&(t=document.getElementById("c4gGuiDialogbrickdialog"),e=document.getElementById("c4gGuiDialogbrickdialog"+e),t&&e&&t.parentNode.removeChild(t))}function resizeChosen(e){var i=document.getElementById(e),a=!0;jQuery(i).on("click",function(e){var t=i.getElementsByClassName("chosen-drop")[0];jQuery(this).on("mouseleave",function(e){t.style.display="none",t.style.position="absolute"});var n=i.getElementsByClassName("chosen-search")[0];jQuery(n).on("click",function(e){e.stopPropagation()}),t.style.display="block",t.style.position="relative",a&&(jQuery(t).on("click",function(e){this.style.display="none",this.style.position="absolute",e.stopPropagation()}),a=!1)})}function focusOnElement(e){""!==e&&null!==(e=document.getElementById(e))&&(e.focus(),e.scrollIntoView(!1))}function callActionViaAjax(e){var t=c4g.projects.C4GGui,e=t.options.ajaxUrl+"/"+t.options.moduleId+"/"+e;jQuery.ajax({url:e}).done(function(e){t.fnHandleAjaxResponse(e,t.options.moduleId)})}function removeAccordionIcons(){var e=document.getElementsByClassName("c4g__accordion-header-icon");if(0<e.length)for(var t=e.length;0<t;){--t,e.item(t).remove();e.item(t)}else setTimeout(function(){removeAccordionIcons()},200)}function openAccordion(n){"all"===n?setTimeout(function(){for(var e=document.getElementsByClassName("c4g__form-headline"),t=new MouseEvent("click",{view:window,bubbles:!0,cancelable:!0}),n=e.length;0<n;)e[--n].dispatchEvent(t)},100):setTimeout(function(){var e=document.getElementsByClassName("c4g__form-headline")[n],t=new MouseEvent("click",{view:window,bubbles:!0,cancelable:!0});e.dispatchEvent(t)},100)}function removeSubDialog(e,t){void 0!==t&&t.stopPropagation(),(new window.AlertHandler).showConfirmDialog("Bestätigung",e.dataset.message,function(){for(;e&&e.parentNode&&e.parentNode.firstChild;)e.parentNode.removeChild(e.parentNode.firstChild)},function(){},"Ja","Nein","c4g__message_confirm")}function confirmRemoveSubDialog(e){for(;e&&e.parentNode&&e.parentNode.firstChild;)e.parentNode.removeChild(e.parentNode.firstChild)}function addSubDialog(e,t,n){void 0!==t&&t.stopPropagation();var i=document.getElementById(e.dataset.target);e.dataset.index=parseInt(e.dataset.index,10)+1;var a=document.getElementById(e.dataset.template).innerHTML.split(e.dataset.wildcard).join(e.dataset.index),t=document.createElement("div");if(t.classList.add("c4g_sub_dialog_set"),t.classList.add("c4g_sub_dialog_set_new"),t.innerHTML=a,i.children.length<n){for(var l=(t=null!==i.firstChild&&"before"===e.dataset.insert?i.insertBefore(t,i.firstChild):i.appendChild(t)).getElementsByTagName("input"),r=0;r<l.length;)void 0!==l[r]&&(!0===l[r].disabled&&(l[r].disabled=!1),!0===l[r].readOnly&&(l[r].readOnly=!1)),r+=1;for(var d=t.getElementsByTagName("textarea"),o=0;o<d.length;)void 0!==d[o]&&(!0===d[o].disabled&&(d[o].disabled=!1),!0===d[o].readOnly&&(d[o].readOnly=!1)),o+=1;for(var s=t.getElementsByClassName("js-sub-dialog-button"),c=0;c<s.length;)void 0!==s[c]&&(!0===s[c].disabled&&(s[c].disabled=!1),!0===s[c].readOnly&&(s[c].readOnly=!1),"none"===s[c].style.display&&(s[c].style.display="unset")),c+=1}}function editSubDialog(e,t){void 0!==t&&t.stopPropagation();for(var n=e.dataset.fields.split(","),i=0;i<n.length;){var a=document.getElementById("c4g_"+n[i]);void 0!==a&&(a.hasAttribute("disabled")?a.removeAttribute("disabled"):a.setAttribute("disabled",""),a.hasAttribute("readonly")?a.removeAttribute("readonly"):a.setAttribute("readonly",""));var l=document.getElementById("c4g_uploadButton_"+n[i]);null!=l&&"BUTTON"===l.tagName&&(l.hasAttribute("disabled")?l.removeAttribute("disabled"):l.setAttribute("disabled",""),l.hasAttribute("readonly")?l.removeAttribute("readonly"):l.setAttribute("readonly",""),"none"===l.style.display?l.style.display="unset":l.style.display="none");a=document.getElementById("c4g_deleteButton_"+n[i]);null!=a&&"BUTTON"===l.tagName&&(a.hasAttribute("disabled")?a.removeAttribute("disabled"):a.setAttribute("disabled",""),a.hasAttribute("readonly")?a.removeAttribute("readonly"):a.setAttribute("readonly",""),"none"===a.style.display?a.style.display="unset":a.style.display="none");for(var r=e.parentNode.getElementsByClassName(n[i]),d=0;d<r.length;){for(var o=r[d].getElementsByTagName("input"),s=0;s<o.length;)void 0!==o[s]&&(o[s].hasAttribute("disabled")?o[s].removeAttribute("disabled"):o[s].setAttribute("disabled",""),o[s].hasAttribute("readonly")?o[s].removeAttribute("readonly"):o[s].setAttribute("readonly","")),s+=1;for(var c=r[d].getElementsByTagName("textarea"),u=0;u<c.length;)void 0!==c[u]&&(c[u].hasAttribute("disabled")?c[u].removeAttribute("disabled"):c[u].setAttribute("disabled",""),c[u].hasAttribute("readonly")?c[u].removeAttribute("readonly"):c[u].setAttribute("readonly","")),u+=1;for(var h=r[d].getElementsByTagName("button"),m=0;m<h.length;)void 0!==h[m]&&(h[u].removeAttribute("disabled"),h[u].removeAttribute("readonly"),"none"===h[m].style.display?h[m].style.display="unset":h[m].style.display="none"),m+=1;d+=1}i+=1}t=e.parentNode;t.classList.contains("c4g_sub_dialog_set_uneditable")?(t.classList.remove("c4g_sub_dialog_set_uneditable"),e.innerHTML=e.dataset.captionfinishediting):(t.classList.add("c4g_sub_dialog_set_uneditable"),e.innerHTML=e.dataset.captionbeginediting)}