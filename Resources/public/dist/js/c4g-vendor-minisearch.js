/*! For license information please see c4g-vendor-minisearch.js.LICENSE.txt */
(()=>{"use strict";var e=function(){return e=Object.assign||function(e){for(var t,n=1,r=arguments.length;n<r;n++)for(var i in t=arguments[n])Object.prototype.hasOwnProperty.call(t,i)&&(e[i]=t[i]);return e},e.apply(this,arguments)};function t(e){var t="function"==typeof Symbol&&Symbol.iterator,n=t&&e[t],r=0;if(n)return n.call(e);if(e&&"number"==typeof e.length)return{next:function(){return e&&r>=e.length&&(e=void 0),{value:e&&e[r++],done:!e}}};throw new TypeError(t?"Object is not iterable.":"Symbol.iterator is not defined.")}function n(e,t){var n="function"==typeof Symbol&&e[Symbol.iterator];if(!n)return e;var r,i,u=n.call(e),o=[];try{for(;(void 0===t||t-- >0)&&!(r=u.next()).done;)o.push(r.value)}catch(e){i={error:e}}finally{try{r&&!r.done&&(n=u.return)&&n.call(u)}finally{if(i)throw i.error}}return o}function r(e,t,n){if(n||2===arguments.length)for(var r,i=0,u=t.length;i<u;i++)!r&&i in t||(r||(r=Array.prototype.slice.call(t,0,i)),r[i]=t[i]);return e.concat(r||t)}var i,u="KEYS",o="VALUES",s="",c=function(){function e(e,t){var n=e._tree,r=Object.keys(n);this.set=e,this._type=t,this._path=r.length>0?[{node:n,keys:r}]:[]}return e.prototype.next=function(){var e=this.dive();return this.backtrack(),e},e.prototype.dive=function(){if(0===this._path.length)return{done:!0,value:void 0};var e=d(this._path),t=e.node,n=e.keys;return d(n)===s?{done:!1,value:this.result()}:(this._path.push({node:t[d(n)],keys:Object.keys(t[d(n)])}),this.dive())},e.prototype.backtrack=function(){0!==this._path.length&&(d(this._path).keys.pop(),d(this._path).keys.length>0||(this._path.pop(),this.backtrack()))},e.prototype.key=function(){return this.set._prefix+this._path.map((function(e){var t=e.keys;return d(t)})).filter((function(e){return e!==s})).join("")},e.prototype.value=function(){return d(this._path).node[""]},e.prototype.result=function(){return this._type===o?this.value():this._type===u?this.key():[this.key(),this.value()]},e.prototype[Symbol.iterator]=function(){return this},e}(),d=function(e){return e[e.length-1]},h=function(){function e(e,t){void 0===e&&(e={}),void 0===t&&(t=""),this._tree=e,this._prefix=t}return e.prototype.atPrefix=function(t){var r;if(!t.startsWith(this._prefix))throw new Error("Mismatched prefix");var i=n(a(this._tree,t.slice(this._prefix.length)),2),u=i[0],o=i[1];if(void 0===u){var c=n(g(o),2),d=c[0],h=c[1],l=Object.keys(d).find((function(e){return e!==s&&e.startsWith(h)}));if(void 0!==l)return new e(((r={})[l.slice(h.length)]=d[l],r),t)}return new e(u||{},t)},e.prototype.clear=function(){delete this._size,this._tree={}},e.prototype.delete=function(e){return delete this._size,v(this._tree,e)},e.prototype.entries=function(){return new c(this,"ENTRIES")},e.prototype.forEach=function(e){var r,i;try{for(var u=t(this),o=u.next();!o.done;o=u.next()){var s=n(o.value,2);e(s[0],s[1],this)}}catch(e){r={error:e}}finally{try{o&&!o.done&&(i=u.return)&&i.call(u)}finally{if(r)throw r.error}}},e.prototype.fuzzyGet=function(e,t){return function(e,t,r){for(var i=[{distance:0,i:0,key:"",node:e}],u={},o=[],c=function(){var e=i.pop(),c=e.node,d=e.distance,h=e.key,a=e.i,l=e.edit;Object.keys(c).forEach((function(e){if(e===s){var f=d+(t.length-a),p=n(u[h]||[null,1/0],2)[1];f<=r&&f<p&&(u[h]=[c[e],f])}else(function(e,t,n,r,i,u){u.push({distance:0,ia:r,ib:0,edit:i});for(var o=[];u.length>0;){var s=u.pop(),c=s.distance,d=s.ia,h=s.ib,a=s.edit;if(h!==t.length)if(e[d]===t[h])u.push({distance:c,ia:d+1,ib:h+1,edit:0});else{if(c>=n)continue;2!==a&&u.push({distance:c+1,ia:d,ib:h+1,edit:3}),d<e.length&&(3!==a&&u.push({distance:c+1,ia:d+1,ib:h,edit:2}),3!==a&&2!==a&&u.push({distance:c+1,ia:d+1,ib:h+1,edit:1}))}else o.push({distance:c,i:d,edit:a})}return o})(t,e,r-d,a,l,o).forEach((function(t){var n=t.distance,r=t.i,u=t.edit;i.push({node:c[e],distance:d+n,key:h+e,i:r,edit:u})}))}))};i.length>0;)c();return u}(this._tree,e,t)},e.prototype.get=function(e){var t=l(this._tree,e);return void 0!==t?t[""]:void 0},e.prototype.has=function(e){var t=l(this._tree,e);return void 0!==t&&t.hasOwnProperty(s)},e.prototype.keys=function(){return new c(this,u)},e.prototype.set=function(e,t){if("string"!=typeof e)throw new Error("key must be a string");return delete this._size,f(this._tree,e)[""]=t,this},Object.defineProperty(e.prototype,"size",{get:function(){var e=this;return this._size||(this._size=0,this.forEach((function(){e._size+=1}))),this._size},enumerable:!1,configurable:!0}),e.prototype.update=function(e,t){if("string"!=typeof e)throw new Error("key must be a string");delete this._size;var n=f(this._tree,e);return n[""]=t(n[""]),this},e.prototype.values=function(){return new c(this,o)},e.prototype[Symbol.iterator]=function(){return this.entries()},e.from=function(r){var i,u,o=new e;try{for(var s=t(r),c=s.next();!c.done;c=s.next()){var d=n(c.value,2),h=d[0],a=d[1];o.set(h,a)}}catch(e){i={error:e}}finally{try{c&&!c.done&&(u=s.return)&&u.call(s)}finally{if(i)throw i.error}}return o},e.fromObject=function(t){return e.from(Object.entries(t))},e}(),a=function(e,t,n){if(void 0===n&&(n=[]),0===t.length||null==e)return[e,n];var r=Object.keys(e).find((function(e){return e!==s&&t.startsWith(e)}));return void 0===r?(n.push([e,t]),a(void 0,"",n)):(n.push([e,r]),a(e[r],t.slice(r.length),n))},l=function(e,t){if(0===t.length||null==e)return e;var n=Object.keys(e).find((function(e){return e!==s&&t.startsWith(e)}));return void 0!==n?l(e[n],t.slice(n.length)):void 0},f=function(e,t){var n;if(0===t.length||null==e)return e;var r=Object.keys(e).find((function(e){return e!==s&&t.startsWith(e)}));if(void 0===r){var i=Object.keys(e).find((function(e){return e!==s&&e.startsWith(t[0])}));if(void 0!==i){var u=p(t,i);return e[u]=((n={})[i.slice(u.length)]=e[i],n),delete e[i],f(e[u],t.slice(u.length))}return e[t]={},e[t]}return f(e[r],t.slice(r.length))},p=function(e,t,n,r,i){return void 0===n&&(n=0),void 0===r&&(r=Math.min(e.length,t.length)),void 0===i&&(i=""),n>=r||e[n]!==t[n]?i:p(e,t,n+1,r,i+e[n])},v=function(e,t){var r=n(a(e,t),2),i=r[0],u=r[1];if(void 0!==i){delete i[""];var o=Object.keys(i);0===o.length&&m(u),1===o.length&&_(u,o[0],i[o[0]])}},m=function(e){if(0!==e.length){var t=n(g(e),2),r=t[0];delete r[t[1]];var i=Object.keys(r);0===i.length&&m(e.slice(0,-1)),1===i.length&&i[0]!==s&&_(e.slice(0,-1),i[0],r[i[0]])}},_=function(e,t,r){if(0!==e.length){var i=n(g(e),2),u=i[0],o=i[1];u[o+t]=r,delete u[o]}},g=function(e){return e[e.length-1]},F=function(){function t(t){if(null==(null==t?void 0:t.fields))throw new Error('MiniSearch: option "fields" must be provided');this._options=e(e(e({},w),t),{searchOptions:e(e({},C),t.searchOptions||{})}),this._index=new h,this._documentCount=0,this._documentIds={},this._fieldIds={},this._fieldLength={},this._averageFieldLength={},this._nextId=0,this._storedFields={},this.addFields(this._options.fields)}return t.prototype.add=function(e){var t=this,n=this._options,r=n.extractField,i=n.tokenize,u=n.processTerm,o=n.fields,s=n.idField,c=r(e,s);if(null==c)throw new Error('MiniSearch: document does not have ID field "'+s+'"');var d=this.addDocumentId(c);this.saveStoredFields(d,e),o.forEach((function(n){var o=r(e,n);if(null!=o){var s=i(o.toString(),n);t.addFieldLength(d,t._fieldIds[n],t.documentCount-1,s.length),s.forEach((function(e){var r=u(e,n);r&&t.addTerm(t._fieldIds[n],d,r)}))}}))},t.prototype.addAll=function(e){var t=this;e.forEach((function(e){return t.add(e)}))},t.prototype.addAllAsync=function(e,t){var n=this;void 0===t&&(t={});var r=t.chunkSize,i=void 0===r?10:r,u={chunk:[],promise:Promise.resolve()},o=e.reduce((function(e,t,r){var u=e.chunk,o=e.promise;return u.push(t),(r+1)%i==0?{chunk:[],promise:o.then((function(){return new Promise((function(e){return setTimeout(e,0)}))})).then((function(){return n.addAll(u)}))}:{chunk:u,promise:o}}),u),s=o.chunk;return o.promise.then((function(){return n.addAll(s)}))},t.prototype.remove=function(e){var t=this,r=this._options,i=r.tokenize,u=r.processTerm,o=r.extractField,s=r.fields,c=r.idField,d=o(e,c);if(null==d)throw new Error('MiniSearch: document does not have ID field "'+c+'"');var h=n(Object.entries(this._documentIds).find((function(e){var t=n(e,2);t[0];var r=t[1];return d===r}))||[],1),a=h[0];if(null==a)throw new Error("MiniSearch: cannot remove document with ID "+d+": it is not in the index");s.forEach((function(n){var r=o(e,n);if(null!=r){var s=i(r.toString(),n);s.forEach((function(e){var r=u(e,n);r&&t.removeTerm(t._fieldIds[n],a,r)})),t.removeFieldLength(a,t._fieldIds[n],t.documentCount,s.length)}})),delete this._storedFields[a],delete this._documentIds[a],delete this._fieldLength[a],this._documentCount-=1},t.prototype.removeAll=function(e){var t=this;if(e)e.forEach((function(e){return t.remove(e)}));else{if(arguments.length>0)throw new Error("Expected documents to be present. Omit the argument to remove all documents.");this._index=new h,this._documentCount=0,this._documentIds={},this._fieldLength={},this._averageFieldLength={},this._storedFields={},this._nextId=0}},t.prototype.search=function(e,t){var r=this;void 0===t&&(t={});var i=this.executeQuery(e,t);return Object.entries(i).reduce((function(e,i){var u=n(i,2),o=u[0],s=u[1],c=s.score,d=s.match,h=s.terms,a={id:r._documentIds[o],terms:x(h),score:c,match:d};return Object.assign(a,r._storedFields[o]),(null==t.filter||t.filter(a))&&e.push(a),e}),[]).sort((function(e,t){return e.score<t.score?1:-1}))},t.prototype.autoSuggest=function(t,r){void 0===r&&(r={}),r=e(e({},k),r);var i=this.search(t,r).reduce((function(e,t){var n=t.score,r=t.terms,i=r.join(" ");return null==e[i]?e[i]={score:n,terms:r,count:1}:(e[i].score+=n,e[i].count+=1),e}),{});return Object.entries(i).map((function(e){var t=n(e,2),r=t[0],i=t[1],u=i.score;return{suggestion:r,terms:i.terms,score:u/i.count}})).sort((function(e,t){return e.score<t.score?1:-1}))},Object.defineProperty(t.prototype,"documentCount",{get:function(){return this._documentCount},enumerable:!1,configurable:!0}),t.loadJSON=function(e,n){if(null==n)throw new Error("MiniSearch: loadJSON should be given the same options used when serializing the index");return t.loadJS(JSON.parse(e),n)},t.getDefault=function(e){if(w.hasOwnProperty(e))return y(w,e);throw new Error('MiniSearch: unknown option "'+e+'"')},t.loadJS=function(e,n){var r=e.index,i=e.documentCount,u=e.nextId,o=e.documentIds,s=e.fieldIds,c=e.fieldLength,d=e.averageFieldLength,a=e.storedFields,l=new t(n);return l._index=new h(r._tree,r._prefix),l._documentCount=i,l._nextId=u,l._documentIds=o,l._fieldIds=s,l._fieldLength=c,l._averageFieldLength=d,l._fieldIds=s,l._storedFields=a||{},l},t.prototype.executeQuery=function(t,n){var r=this;if(void 0===n&&(n={}),"string"==typeof t)return this.executeSearch(t,n);var i=t.queries.map((function(i){var u=e(e(e({},n),t),{queries:void 0});return r.executeQuery(i,u)}));return this.combineResults(i,t.combineWith)},t.prototype.executeSearch=function(t,n){var r=this;void 0===n&&(n={});var i=this._options,u=i.tokenize,o=i.processTerm,s=i.searchOptions,c=e(e({tokenize:u,processTerm:o},s),n),d=c.tokenize,h=c.processTerm,a=d(t).map((function(e){return h(e)})).filter((function(e){return!!e})).map(E(c)).map((function(e){return r.executeQuerySpec(e,c)}));return this.combineResults(a,c.combineWith)},t.prototype.executeQuerySpec=function(t,r){var i=this,u=e(e({},this._options.searchOptions),r),o=(u.fields||this._options.fields).reduce((function(t,n){var r;return e(e({},t),((r={})[n]=y(t,n)||1,r))}),u.boost||{}),s=u.boostDocument,c=u.weights,d=e(e({},C.weights),c),h=d.fuzzy,a=d.prefix,l=this.termResults(t.term,o,s,this._index.get(t.term));if(!t.fuzzy&&!t.prefix)return l;var f=[l];if(t.prefix&&this._index.atPrefix(t.term).forEach((function(e,n){var r=.3*(e.length-t.term.length)/e.length;f.push(i.termResults(e,o,s,n,a,r))})),t.fuzzy){var p=!0===t.fuzzy?.2:t.fuzzy,v=p<1?Math.round(t.term.length*p):p;Object.entries(this._index.fuzzyGet(t.term,v)).forEach((function(e){var t=n(e,2),r=t[0],u=n(t[1],2),c=u[0],d=u[1]/r.length;f.push(i.termResults(r,o,s,c,h,d))}))}return f.reduce(A.or,{})},t.prototype.combineResults=function(e,t){if(void 0===t&&(t="or"),0===e.length)return{};var n=t.toLowerCase();return e.reduce(A[n],null)||{}},t.prototype.toJSON=function(){return{index:this._index,documentCount:this._documentCount,nextId:this._nextId,documentIds:this._documentIds,fieldIds:this._fieldIds,fieldLength:this._fieldLength,averageFieldLength:this._averageFieldLength,storedFields:this._storedFields}},t.prototype.termResults=function(e,t,r,i,u,o){var s=this;return void 0===o&&(o=0),null==i?{}:Object.entries(t).reduce((function(t,u){var c=n(u,2),d=c[0],h=c[1],a=s._fieldIds[d],l=i[a]||{ds:{}},f=l.df,p=l.ds;return Object.entries(p).forEach((function(i){var u=n(i,2),c=u[0],l=u[1],p=r?r(s._documentIds[c],e):1;if(p){var v=s._fieldLength[c][a]/s._averageFieldLength[a];t[c]=t[c]||{score:0,match:{},terms:[]},t[c].terms.push(e),t[c].match[e]=y(t[c].match,e)||[],t[c].score+=p*b(l,f,s._documentCount,v,h,o),t[c].match[e].push(d)}})),t}),{})},t.prototype.addTerm=function(t,n,r){this._index.update(r,(function(r){var i,u=(r=r||{})[t]||{df:0,ds:{}};return null==u.ds[n]&&(u.df+=1),u.ds[n]=(u.ds[n]||0)+1,e(e({},r),((i={})[t]=u,i))}))},t.prototype.removeTerm=function(t,n,r){var i=this;this._index.has(r)?(this._index.update(r,(function(u){var o,s=u[t];if(null==s||null==s.ds[n])return i.warnDocumentChanged(n,t,r),u;if(s.ds[n]<=1){if(s.df<=1)return delete u[t],u;s.df-=1}return s.ds[n]<=1?(delete s.ds[n],u):(s.ds[n]-=1,e(e({},u),((o={})[t]=s,o)))})),0===Object.keys(this._index.get(r)).length&&this._index.delete(r)):this.warnDocumentChanged(n,t,r)},t.prototype.warnDocumentChanged=function(e,t,r){if(null!=console&&null!=console.warn){var i=Object.entries(this._fieldIds).find((function(e){var r=n(e,2);return r[0],r[1]===t}))[0];console.warn("MiniSearch: document with ID "+this._documentIds[e]+' has changed before removal: term "'+r+'" was not present in field "'+i+'". Removing a document after it has changed can corrupt the index!')}},t.prototype.addDocumentId=function(e){var t=this._nextId.toString(36);return this._documentIds[t]=e,this._documentCount+=1,this._nextId+=1,t},t.prototype.addFields=function(e){var t=this;e.forEach((function(e,n){t._fieldIds[e]=n}))},t.prototype.addFieldLength=function(e,t,n,r){this._averageFieldLength[t]=this._averageFieldLength[t]||0;var i=this._averageFieldLength[t]*n+r;this._fieldLength[e]=this._fieldLength[e]||{},this._fieldLength[e][t]=r,this._averageFieldLength[t]=i/(n+1)},t.prototype.removeFieldLength=function(e,t,n,r){var i=this._averageFieldLength[t]*n-r;this._averageFieldLength[t]=i/(n-1)},t.prototype.saveStoredFields=function(e,t){var n=this,r=this._options,i=r.storeFields,u=r.extractField;null!=i&&0!==i.length&&(this._storedFields[e]=this._storedFields[e]||{},i.forEach((function(r){var i=u(t,r);void 0!==i&&(n._storedFields[e][r]=i)})))},t}(),y=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)?e[t]:void 0},A=((i={}).or=function(e,t){return Object.entries(t).reduce((function(e,t){var i,u=n(t,2),o=u[0],s=u[1],c=s.score,d=s.match,h=s.terms;return null==e[o]?e[o]={score:c,match:d,terms:h}:(e[o].score+=c,e[o].score*=1.5,(i=e[o].terms).push.apply(i,r([],n(h))),Object.assign(e[o].match,d)),e}),e||{})},i.and=function(t,i){return null==t?i:Object.entries(i).reduce((function(i,u){var o=n(u,2),s=o[0],c=o[1],d=c.score,h=c.match,a=c.terms;return void 0===t[s]||(i[s]=i[s]||{},i[s].score=t[s].score+d,i[s].match=e(e({},t[s].match),h),i[s].terms=r(r([],n(t[s].terms)),n(a))),i}),{})},i),b=function(e,t,n,r,i,u){var o,s;return i/(1+.333*i*u)*(o=t,s=n,e*Math.log(s/o))/r},E=function(e){return function(t,n,r){return{term:t,fuzzy:"function"==typeof e.fuzzy?e.fuzzy(t,n,r):e.fuzzy||!1,prefix:"function"==typeof e.prefix?e.prefix(t,n,r):!0===e.prefix}}},x=function(e){return e.filter((function(e,t,n){return n.indexOf(e)===t}))},w={idField:"id",extractField:function(e,t){return e[t]},tokenize:function(e,t){return e.split(D)},processTerm:function(e,t){return e.toLowerCase()},fields:void 0,searchOptions:void 0,storeFields:[]},C={combineWith:"or",prefix:!1,fuzzy:!1,boost:{},weights:{fuzzy:.9,prefix:.75}},k={prefix:function(e,t,n){return t===n.length-1}},D=/[\n\r -#%-*,-/:;?@[-\]_{}\u00A0\u00A1\u00A7\u00AB\u00B6\u00B7\u00BB\u00BF\u037E\u0387\u055A-\u055F\u0589\u058A\u05BE\u05C0\u05C3\u05C6\u05F3\u05F4\u0609\u060A\u060C\u060D\u061B\u061E\u061F\u066A-\u066D\u06D4\u0700-\u070D\u07F7-\u07F9\u0830-\u083E\u085E\u0964\u0965\u0970\u09FD\u0A76\u0AF0\u0C77\u0C84\u0DF4\u0E4F\u0E5A\u0E5B\u0F04-\u0F12\u0F14\u0F3A-\u0F3D\u0F85\u0FD0-\u0FD4\u0FD9\u0FDA\u104A-\u104F\u10FB\u1360-\u1368\u1400\u166E\u1680\u169B\u169C\u16EB-\u16ED\u1735\u1736\u17D4-\u17D6\u17D8-\u17DA\u1800-\u180A\u1944\u1945\u1A1E\u1A1F\u1AA0-\u1AA6\u1AA8-\u1AAD\u1B5A-\u1B60\u1BFC-\u1BFF\u1C3B-\u1C3F\u1C7E\u1C7F\u1CC0-\u1CC7\u1CD3\u2000-\u200A\u2010-\u2029\u202F-\u2043\u2045-\u2051\u2053-\u205F\u207D\u207E\u208D\u208E\u2308-\u230B\u2329\u232A\u2768-\u2775\u27C5\u27C6\u27E6-\u27EF\u2983-\u2998\u29D8-\u29DB\u29FC\u29FD\u2CF9-\u2CFC\u2CFE\u2CFF\u2D70\u2E00-\u2E2E\u2E30-\u2E4F\u3000-\u3003\u3008-\u3011\u3014-\u301F\u3030\u303D\u30A0\u30FB\uA4FE\uA4FF\uA60D-\uA60F\uA673\uA67E\uA6F2-\uA6F7\uA874-\uA877\uA8CE\uA8CF\uA8F8-\uA8FA\uA8FC\uA92E\uA92F\uA95F\uA9C1-\uA9CD\uA9DE\uA9DF\uAA5C-\uAA5F\uAADE\uAADF\uAAF0\uAAF1\uABEB\uFD3E\uFD3F\uFE10-\uFE19\uFE30-\uFE52\uFE54-\uFE61\uFE63\uFE68\uFE6A\uFE6B\uFF01-\uFF03\uFF05-\uFF0A\uFF0C-\uFF0F\uFF1A\uFF1B\uFF1F\uFF20\uFF3B-\uFF3D\uFF3F\uFF5B\uFF5D\uFF5F-\uFF65]+/u;const O=F,z={};function I(e){let t=document.getElementsByClassName("c4g_brick_list");for(z.documents=[],z.minisearch=new O({fields:["text"]});t.length>1;)t.item(0).remove();let n=document.getElementsByClassName("c4g_brick_list_row"),r=0;for(;r<n.length;){let t=n.item(r).getElementsByClassName("c4g_brick_list_row_column"),i=0,u="";for(;i<t.length;)"LI"!==t.item(i).tagName||!e&&t.item(i).classList.contains("searchInfoSubordinate")||(u+=t.item(i).textContent.split(/(?=[A-Z])/).join(" ")+", ",u+=L(t.item(i))),i+=1;z.documents.push({id:r+1,text:u}),r+=1}z.minisearch.addAll(z.documents)}function L(e){let t=[e.title||""];for(let n=e.firstChild;null!==n;n=n.nextSibling)t.push(L(n));return t=t.filter((function(e){return""!==e})),t.length>0?t.join(" ")+", ":""}window.search=function(e,t){let n;e.value.length>2&&(I(!1),n=z.minisearch.search(e.value,{prefix:!0,combineWith:"AND"}),0===n.length&&(I(!0),n=z.minisearch.search(e.value,{prefix:!0,combineWith:"AND"})));let r=document.getElementsByClassName("c4g_brick_list_row"),i=0;if(void 0!==n){if(0===n.length)try{document.getElementsByClassName("c4g_brick_list_mini_no_results_note")[0].style.display="block"}catch(e){}else try{document.getElementsByClassName("c4g_brick_list_mini_no_results_note")[0].style.display="none"}catch(e){}for(;i<r.length;)r.item(i).parentNode.classList.add("c4g_list_search_hide"),i+=1;for(i=0;i<r.length;)void 0!==n[i]&&void 0!==n[i].id&&r.item(n[i].id-1).parentNode.classList.remove("c4g_list_search_hide"),i+=1}else{for(;i<r.length;)r.item(i).parentNode.classList.remove("c4g_list_search_hide"),i+=1;try{document.getElementsByClassName("c4g_brick_list_mini_no_results_note")[0].style.display="none"}catch(e){}}}})();