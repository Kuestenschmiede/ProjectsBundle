function ConditionalFieldDisplay(i){this.fields=[],this.moduleId=i,this.condition=!1,this.valueForDisplay=0,this.isDisplayed=!0;var o=this;this.create=function(i,t,e,n){this.fields=i,t&&(this.condition=t,this.valueForDisplay=e,this.condition.callbacks=this.condition.callbacks||[],this.condition.callbacks.push(function(){var i=o.condition.value;return"checkbox"===o.condition.type?(i=o.condition.checked?1:0,o.isDisplayed=i==o.valueForDisplay):o.isDisplayed=i===o.valueForDisplay,d(o.fields,o.isDisplayed?"":"none",n)}),this.condition.onchange=function(i){var t,e={};for(t in this.callbacks)this.callbacks.hasOwnProperty(t)&&this.value&&jQuery.extend(e,this.callbacks[t]());a(e)})};var d=function(i,t,e){for(var n,o={},d={},a=0;a<i.length;a++)if("none"===((n=i[a]).style.display=t)){if(n.childNodes&&e)for(var s in n.childNodes)n.childNodes.hasOwnProperty(s)&&"INPUT"===n.childNodes[s].tagName&&(n.childNodes[s].removeAttribute("required"),o.display=!1,d[n.childNodes[s].id]=o)}else if(n.childNodes&&e)for(var l in n.childNodes)n.childNodes.hasOwnProperty(l)&&"INPUT"===n.childNodes[l].tagName&&(n.childNodes[l].setAttribute("required",!0),o.display=!0,d[n.childNodes[l].id]=o);return d},a=function(i){var t="con4gis/brick_ajax_api/"+o.moduleId+"/C4GChangeFieldAction";jQuery.post(t,i,function(i){})}}var initDisplayConditions=function(i){var t,e,n=document.querySelectorAll("[data-condition-field]"),o=[];for(e in n)if(n&&n.hasOwnProperty(e)&&!(t=n[e]).hasAttribute("data-condition-handled")){var d=t.getAttribute("data-condition-field"),a=t.getAttribute("data-condition-value");new ConditionalFieldDisplay(i).create([t],document.getElementById(d),a,!0);for(var s=0;s<=o.length&&(!o[s]||o[s]!==d);s++)if(s===o.length){o.push(d);break}t.setAttribute("data-condition-handled",!0)}for(s=0;s<o.length;s++)document.getElementById(o[s]).onchange()};