function ready(e){"loading"!=document.readyState?e():document.addEventListener&&document.addEventListener("DOMContentLoaded",e)}function eventFire(e,t){var n;e.fireEvent?e.fireEvent("on"+t):((n=document.createEvent("Events")).initEvent(t,!0,!1),e.dispatchEvent(n))}function C4GTimePicker(e,t,n){var a=new Date,i=String(a.getMinutes()),a=String(a.getHours()),e=document.getElementById(e),n=n.previousSibling;1==i.length&&(i="0"+i),1==a.length&&(a="0"+a),"gettime"==t&&(e.value=a+":"+i,n.value=a+":"+i)}function C4GDatePicker(i,e,t,o,s,d,n,l){if("date"==e){var e="",t=(t&&(e=new Date(1e3*t)),""),o=(o&&(t=new Date(1e3*o)),document.getElementById(i)),l=(o&&o.datepicker&&o.datepicker.destroy(),new Array,l.split(",")),r=new Array;for(a in r=n.split(","))r[a]=parseInt(r[a]);if(o&&window.Datepicker instanceof Function){let n=new window.Datepicker(o,{buttonClass:"c4g__btn",language:d||"de",format:s,datesDisabled:l,daysOfWeekDisabled:r,minDate:e,maxDate:t,weekStart:1,todayHighlight:!0,orientation:"auto left",autohide:!0,useCurrent:!0});o&&o.datepicker&&o.addEventListener("changeDate",function(e){eventFire(document.getElementById(i),"change");var t=i.indexOf("_picker");t&&0<t&&(t=i.substr(0,t),jQuery("#"+t).val(n.getDate(s)),jQuery("#"+t).trigger("change"))})}}}function C4GDateTimePicker(e){jQuery("#"+e).appendDtpicker({dateFormat:"DD.MM.YY - hh:mm",locale:"de",closeOnSelected:"true",todayButton:!1})}function C4GFilterButtonTiles(e){document.getElementsByClassName("c4g__btn-tile"),e.value}function C4GSearchTiles(e){var t,n,a=document.getElementsByClassName("c4g__btn-tile"),i=e.value.toLowerCase();if(i)for(var o=0;o<a.length;o+=1){n=!1,fields=a[o].children[0].children;for(var s=0;s<fields.length;s+=1)fields[s].innerHTML&&(t=fields[s].innerHTML.toLowerCase(),"-1"!=(searchedStatus=t.search(i)))&&(n=!0);a[o].style.display=1==n?"":"none"}else for(cTimer=0;cTimer<a.length;cTimer+=1)a[cTimer].style.display=""}function tileSort(e){var t=document.getElementsByClassName("c4g_brick_tiles")[0];"row wrap"==t.style.flexFlow?(t.style.flexFlow="row-reverse wrap-reverse",e.textContent=e.dataset.langDesc):(t.style.flexFlow="row wrap",e.textContent=e.dataset.langAsc)}function createNewPopupWindow(e){return jQuery.magnificPopup.open({items:{src:e.dataset.linkHref},type:"iframe"},0),!1}function closePopupWindow(){jQuery.magnificPopup.close()}function handleBoolSwitch(e,t,n){e&&t&&(e=e.id,t=t.id,e="checkbox"==document.getElementById(e).type?document.getElementById(e).checked:document.getElementById(e).value,document.getElementById(t).disabled="1"==n?e:!e)}function handleC4GBrickFile(e,t,n,a,i,o,s){if(document.getElementById("c4g_file")&&document.getElementById("c4g_file").getAttribute("dataURL")){e=[];for(var d=atob(document.getElementById("c4g_file").getAttribute("dataURL").split(",")[1]),l=[],r=0,r=0;r<d.length;r++)l.push(d.charCodeAt(r));var c=new Blob([new Uint8Array(l)],{type:"image/png"});c.name=i+".png",e[0]=c}var u,m;e&&e[0]&&document.getElementById(n+i).value!==e[0]&&((c=document.createElement("img")).file=e[0],c.name="img_0",c.classList.add("obj"),(u=new FileReader).onload=(m=c,function(e){m.src=e.target.result}),u.readAsDataURL(e[0]),C4GBrickFileUpload(e[0],t,n,a,i,o,s))}function deleteC4GBrickFile(e){var t=e.parentNode.getElementsByTagName("input"),n=t.item(0),t=t.item(1),a=e.parentNode.getElementsByClassName("c4g_uploadLink").item(0);t.value=n.value,n.value="",a.innerHTML="",e.style.display="none"}function deleteC4GBrickImage(e){var t,n=e.id;n&&0==n.indexOf("c4g_deleteButton_")&&(t="c4g_uploadLink_"+(n=n.substr(17)),(t=document.getElementById(t)).innerHTML="",(n=document.getElementById("c4g_"+n)).value="",n.defaultValue="",jQuery(n).trigger("change"),t.style.display="none",e.style.display="none")}function C4GBrickFileUpload(t,e,n,a,i,o,s){var d=new XMLHttpRequest,l=new FormData;l.append("File",t),l.append("Path",e),l.append("MimeTypes",s),l.append("REQUEST_TOKEN",c4g_rq),l.append("name",t.name),d.onreadystatechange=function(){var e;4===d.readyState&&200===d.status&&(e=JSON.parse(d.responseText)[0],document.getElementById(n+i).value=e,document.getElementById("c4g_uploadLink_"+i).innerHTML="<a href='"+e+"' target='_blank'>"+t.name.replace("C:\\fakepath\\","")+"</a>",document.getElementById("c4g_deleteButton_"+i)&&(document.getElementById("c4g_deleteButton_"+i).style="display:inline"),""!==a&&(document.getElementById(a+i).value=""),o)&&document.getElementsByClassName("c4g_"+o+"_src")[0]&&(document.getElementsByClassName("c4g_"+o+"_src")[0].getElementsByTagName("img")[0].src=e)},d.open("POST","con4gis/upload_file",!0),d.overrideMimeType("text/plain; charset=x-user-defined-binary"),d.send(l)}function C4GCheckConditionFields(e){for(var t=0;t<e.length;t++){var n=e[t];if(n.dataset.conditionName)for(var a=n.dataset.conditionName.split("~"),i=!0,o=0;o<a.length;o++)C4GRemoveConditionSettings(n,o),i=i&&C4GCheckConditionSettings(n,o)}}function handleBrickConditions(){for(var e=document.getElementsByClassName("c4g_brick_dialog"),t=0;t<e.length;t++)(i=e[t].children)&&C4GCheckConditionFields(i);var n=document.getElementsByClassName("c4gGuiCollapsible_target"),a=(n&&C4GCheckConditionFields(n),document.getElementsByClassName("c4gGuiTabContent"));if(a)for(var i,t=0;t<a.length;t++)C4GCheckConditionFields(i=a[t].children),checkC4GTab();return!0}function C4GCheckFieldTypes(e){return!(!e.className||e.classList.contains("noformdata")||e.classList.contains("datepicker"))}function C4GRemoveConditionClasses(e,t=1){if(C4GCheckFieldTypes(e)){jQuery(e).removeClass("formdata"),jQuery(e).hasClass("chzn-select")&&(jQuery(e).removeClass("chzn-select"),jQuery(e).addClass("chzn-select-disabled"),jQuery(e).style="display:none",jQuery(e).trigger("chosen:updated")),jQuery(e).hide(),jQuery(e).removeAttr("selected");var n=e.children;if(n&&t<5){t+=1;for(var a=0;a<n.length;a++)C4GRemoveConditionClasses(n[a],t)}}}function C4GRemoveConditionSettings(e,t){var n,a,i,o,s,d;e.dataset.conditionName&&e.dataset.conditionType&&(e.dataset.conditionValue&&e.dataset.conditionType.split("~").includes("value")||e.dataset.conditionFunction&&e.dataset.conditionType.split("~").includes("method"))&&(n=e.dataset.conditionName.split("~"),i=e.dataset.conditionValue?e.dataset.conditionValue.split("~"):[],a=e.dataset.conditionFunction?e.dataset.conditionFunction.split("~"):[],s=!1,"value"==(d=e.dataset.conditionType.split("~")[t])?(o="c4g_"+n[t],i=i[t],s=(s=!!document.getElementById(o)&&document.getElementById(o).value)===i):"method"==d&&(o="c4g_"+(i=n[t].split("--"))[0],d=window[a[t]],s=!!document.getElementById(o)&&document.getElementById(o).value,d instanceof Function?s=i[1]?d(s=s+"--"+i[1]):d(s):checkVlaue=!1),s||C4GRemoveConditionClasses(e))}function C4GCheckConditionClasses(e,t=1){if(C4GCheckFieldTypes(e)){jQuery(e).show(),jQuery(e).addClass("formdata"),jQuery(e).hasClass("c4g_display_none")?(jQuery(e).hasClass("chzn-select")&&(jQuery(e).removeClass("chzn-select"),jQuery(e).addClass("chzn-select-disabled")),jQuery(e).hide()):jQuery(e).hasClass("chzn-select-disabled")&&(jQuery(e).removeClass("chzn-select-disabled"),jQuery(e).addClass("chzn-select"),jQuery(e).hide());var n=e.children;if(n&&t<5){t+=1;for(var a=0;a<n.length;a++)C4GCheckConditionClasses(n[a],t)}}}function C4GCheckConditionSettings(e,t){var n,a,i,o,s,d,l=!0;return e.dataset.conditionName&&e.dataset.conditionType&&(e.dataset.conditionValue&&e.dataset.conditionType.split("~").includes("value")||e.dataset.conditionFunction&&e.dataset.conditionType.split("~").includes("method"))&&(n=e.dataset.conditionName.split("~"),i=e.dataset.conditionValue?e.dataset.conditionValue.split("~"):[],a=e.dataset.conditionFunction?e.dataset.conditionFunction.split("~"):[],d=l=!1,"value"==(s=e.dataset.conditionType.split("~")[t])?(o="c4g_"+n[t],i=i[t],d=(d=!!document.getElementById(o)&&document.getElementById(o).value)===i):"method"==s&&(o="c4g_"+(i=n[t].split("--"))[0],s=window[a[t]],d=!!document.getElementById(o)&&document.getElementById(o).value,d=s instanceof Function&&(i[1]?s(d=d+"--"+i[1]):s(d))),d)&&(l=!0,C4GCheckConditionClasses(e)),l}function C4GGeopickerAddress(e){var t,n,a,i;e&&(t=document.getElementById("c4g_brick_geopicker_geoy"),n=document.getElementById("c4g_brick_geopicker_geox"),t&&n?(t=document.getElementById("c4g_brick_geopicker_geoy").value,n=document.getElementById("c4g_brick_geopicker_geox").value,a=new XMLHttpRequest,(i=new FormData).append("Lat",t),i.append("Lon",n),i.append("Profile",e),i.append("REQUEST_TOKEN",c4g_rq),a.onreadystatechange=function(){4==a.readyState&&200==a.status&&(""!=a.responseText?document.getElementById("c4g_brick_geopicker_address").value=JSON.parse(a.responseText):document.getElementById("c4g_brick_geopicker_address").value="Adresse nicht ermittelbar.")},a.open("GET","/con4gis/get_address/"+e+"/"+t+"/"+n,!0),a.overrideMimeType("text/plain; charset=utf-8"),a.send()):(i=document.getElementById("c4g_brick_geopicker_address"))&&i.remove())}function stopwatch(n,a,i,o){var s=document.getElementById(n),d=setInterval(function(){var e=Math.round((a-30)/60),t=a%60;s.innerHTML=e+":"+(t=t<10?"0"+t:t),0==a?(clearInterval(d),""!=i&&""!=o&&(document.getElementById("c4g_brick_overlay_content").innerHTML="<video id='"+i+"_animation' autoplay><source src='"+o+"' type='video/mp4'></video>",jQuery("#"+i).click()),jQuery("#"+n+"_action").click()):a--},1e3)}function changeNumberFormat(e,t){for(var n="",a=t.replace(/\./,"").split(","),i=Math.floor(a[0].length/3),o=a[0].length-3*i,n=a[0].substr(0,o),s=1;s<=i;s++)1==s&&0==o||(n+="."),anfang=o+3*(s-1),n+=a[0].substr(anfang,3);1<a.length&&(n+=","+a[1]),document.getElementById(e).value=n}function C4GPopupHandler(e){jQuery.magnificPopup.close()}function showAnimation(e,t){var n,a,i,o,s,d,l=apiBaseUrl+"/c4g_brick_ajax";jQuery.ajax({dataType:"json",url:l+"/"+e+"/buttonclick:"+t+"?id=0",done:function(e){n=e.animation_name+"_animation",a=e.animation_source,i=e.animation_function,o=e.animation_param1,s=e.animation_param2,d=e.animation_param3,jQuery.magnificPopup.open({items:{src:"<video id="+n+" autoplay><source src="+a+" type=video/mp4></video>"},type:"inline"},0),document.getElementById(n).addEventListener("ended",C4GPopupHandler,!1),i&&(e=window[i],o&&s&&d?e(o,s,d):o&&s?e(o,s):o?e(o):e())}})}function clickC4GTab(e){jQuery(document.getElementsByClassName("c4gGuiTabLink")).removeClass("c4g__state-active"),jQuery(document.getElementsByClassName("c4gGuiTabLink")).addClass("c4g__state-default"),jQuery(document.getElementsByClassName("c4gGuiTabContent")).removeClass("current"),jQuery(document.getElementsByClassName(e)).removeClass("c4g__state-default"),jQuery(document.getElementsByClassName(e)).addClass("c4g__state-active"),jQuery(document.getElementsByClassName(e+"_content")).addClass("current")}function clickNextTab(){switchTab("+")}function clickPreviousTab(){switchTab("-")}function switchTab(e){var t=document.getElementsByClassName("c4g__state-active")[0].getAttribute("data-tab"),n=parseInt(t.substring(t.length-1,t.length),10);if("+"===e?n++:n--,n<0||n>=document.getElementsByClassName("c4gGuiTabLink").length)return!1;t=t.substr(0,t.length-2);t+="_"+n,"none"!==jQuery(document.getElementsByClassName(t)).css("display")?clickC4GTab(t):(clickC4GTab(t),switchTab(e))}function checkC4GTab(){var e,t,n=new Array,a=new Array,o=jQuery(document.getElementsByClassName("c4gGuiTabLink"));if(o){for(i=0;i<=o.length;i++){var s=!0,d="c4g_tab_"+i+"_content",l=jQuery(document.getElementsByClassName(d));if(l&&l[0]&&l[0].children){for(j=e=0;j<=l[0].children.length;j++)if((t=l[0].children[j])&&"none"!==jQuery(t).css("display"))for(k=0;k<=t.children.length;k++)childOfChildElement=jQuery(t.children[k]),(jQuery(childOfChildElement).hasClass("formdata")||jQuery(childOfChildElement).attr("for"))&&childOfChildElement&&"none"!==jQuery(childOfChildElement).css("display")&&!jQuery(childOfChildElement).hasClass("c4g__form-group")&&e++;0<e&&(s=!1)}s?n[i]=jQuery(o[i]):a[i]=jQuery(o[i])}for(i=0;i<=n.length;i++)jQuery(n[i]).hide();for(i=0;i<=a.length;i++)jQuery(a[i]).show()}if($chosenContainer=document.getElementsByClassName("chosen-container"))for(i=0;i<$chosenContainer.length;i++)"0px"!=$chosenContainer[i].style.width?jQuery($chosenContainer[i]).show():jQuery($chosenContainer[i]).hide()}function replaceC4GDialog(e){var t;-1!==e&&(t=document.getElementById("c4gGuiDialogbrickdialog"),e=document.getElementById("c4gGuiDialogbrickdialog"+e),t)&&e&&t.parentNode.removeChild(t)}function resizeChosen(e){var a=document.getElementById(e),i=!0;jQuery(a).on("click",function(e){var t=a.getElementsByClassName("chosen-drop")[0],n=(jQuery(this).on("mouseleave",function(e){t.style.display="none",t.style.position="absolute"}),a.getElementsByClassName("chosen-search")[0]);jQuery(n).on("click",function(e){e.stopPropagation()}),t.style.display="block",t.style.position="relative",i&&(jQuery(t).on("click",function(e){this.style.display="none",this.style.position="absolute",e.stopPropagation()}),i=!1)})}function focusOnElement(e){""!==e&&null!==(e=document.getElementById(e))&&(e.focus(),e.scrollIntoView(!1))}function callActionViaAjax(e){var t=c4g.projects.C4GGui,e=t.options.ajaxUrl+"/"+t.options.moduleId+"/"+e;jQuery.ajax({url:e}).done(function(e){t.fnHandleAjaxResponse(e,t.options.moduleId)})}function removeAccordionIcons(){var e=document.getElementsByClassName("c4g__accordion-header-icon");if(0<e.length)for(var t=e.length;0<t;){--t,e.item(t).remove();e.item(t)}else setTimeout(function(){removeAccordionIcons()},200)}function openAccordion(n){"all"===n?setTimeout(function(){for(var e=document.getElementsByClassName("c4g__form-headline"),t=new MouseEvent("click",{view:window,bubbles:!0,cancelable:!0}),n=e.length;0<n;)e[--n].dispatchEvent(t)},100):setTimeout(function(){var e=document.getElementsByClassName("c4g__form-headline")[n],t=new MouseEvent("click",{view:window,bubbles:!0,cancelable:!0});e.dispatchEvent(t)},100)}function removeSubDialog(e,t){void 0!==t&&t.stopPropagation(),(new window.AlertHandler).showConfirmDialog("Bestätigung",e.dataset.message,function(){for(;e&&e.parentNode&&e.parentNode.firstChild;)e.parentNode.removeChild(e.parentNode.firstChild)},function(){},"Ja","Nein","c4g__message_confirm")}function confirmRemoveSubDialog(e){for(;e&&e.parentNode&&e.parentNode.firstChild;)e.parentNode.removeChild(e.parentNode.firstChild)}function addSubDialog(e,t,n){void 0!==t&&t.stopPropagation();var t=document.getElementById(e.dataset.target),a=(e.dataset.index=parseInt(e.dataset.index,10)+1,document.getElementById(e.dataset.template).innerHTML.split(e.dataset.wildcard).join(e.dataset.index)),i=document.createElement("div");if(i.classList.add("c4g_sub_dialog_set"),i.classList.add("c4g_sub_dialog_set_new"),i.innerHTML=a,t.children.length<n){for(var o=(a=null!==t.firstChild&&"before"===e.dataset.insert?t.insertBefore(i,t.firstChild):t.appendChild(i)).getElementsByTagName("input"),s=0;s<o.length;)void 0!==o[s]&&(!0===o[s].disabled&&(o[s].disabled=!1),!0===o[s].readOnly)&&(o[s].readOnly=!1),s+=1;for(var d=a.getElementsByTagName("textarea"),l=0;l<d.length;)void 0!==d[l]&&(!0===d[l].disabled&&(d[l].disabled=!1),!0===d[l].readOnly)&&(d[l].readOnly=!1),l+=1;for(var r=a.getElementsByClassName("js-sub-dialog-button"),c=0;c<r.length;)void 0!==r[c]&&(!0===r[c].disabled&&(r[c].disabled=!1),!0===r[c].readOnly&&(r[c].readOnly=!1),"none"===r[c].style.display)&&(r[c].style.display="unset"),c+=1}}function editSubDialog(e,t){void 0!==t&&t.stopPropagation();for(var n=e.dataset.fields.split(","),a=0;a<n.length;){for(var i=document.getElementById("c4g_"+n[a]),i=(void 0!==i&&(i.hasAttribute("disabled")?i.removeAttribute("disabled"):i.setAttribute("disabled",""),i.hasAttribute("readonly")?i.removeAttribute("readonly"):i.setAttribute("readonly","")),document.getElementById("c4g_uploadButton_"+n[a])),o=(null!=i&&"BUTTON"===i.tagName&&(i.hasAttribute("disabled")?i.removeAttribute("disabled"):i.setAttribute("disabled",""),i.hasAttribute("readonly")?i.removeAttribute("readonly"):i.setAttribute("readonly",""),"none"===i.style.display?i.style.display="unset":i.style.display="none"),document.getElementById("c4g_deleteButton_"+n[a])),s=(null!=o&&"BUTTON"===i.tagName&&(o.hasAttribute("disabled")?o.removeAttribute("disabled"):o.setAttribute("disabled",""),o.hasAttribute("readonly")?o.removeAttribute("readonly"):o.setAttribute("readonly",""),"none"===o.style.display?o.style.display="unset":o.style.display="none"),e.parentNode.getElementsByClassName(n[a])),d=0;d<s.length;){for(var l=s[d].getElementsByTagName("input"),r=0;r<l.length;)void 0!==l[r]&&(l[r].hasAttribute("disabled")?l[r].removeAttribute("disabled"):l[r].setAttribute("disabled",""),l[r].hasAttribute("readonly")?l[r].removeAttribute("readonly"):l[r].setAttribute("readonly","")),r+=1;for(var c=s[d].getElementsByTagName("textarea"),u=0;u<c.length;)void 0!==c[u]&&(c[u].hasAttribute("disabled")?c[u].removeAttribute("disabled"):c[u].setAttribute("disabled",""),c[u].hasAttribute("readonly")?c[u].removeAttribute("readonly"):c[u].setAttribute("readonly","")),u+=1;for(var m=s[d].getElementsByTagName("button"),g=0;g<m.length;)void 0!==m[g]&&(m[u].removeAttribute("disabled"),m[u].removeAttribute("readonly"),"none"===m[g].style.display?m[g].style.display="unset":m[g].style.display="none"),g+=1;d+=1}a+=1}t=e.parentNode;t.classList.contains("c4g_sub_dialog_set_uneditable")?(t.classList.remove("c4g_sub_dialog_set_uneditable"),e.innerHTML=e.dataset.captionfinishediting):(t.classList.add("c4g_sub_dialog_set_uneditable"),e.innerHTML=e.dataset.captionbeginediting)}