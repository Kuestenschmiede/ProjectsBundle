/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2022, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

/**
 *
 * @param map
 * @param importLayer
 * @param data
 * @param setStyleHelper
 */
function livePositions(map, importLayer, data, setStyleHelper) {
    var timeout;
    var importLayer = importLayer;

    var layerKey = importLayer.key;

    map.events.register('changelayer', null, function(evt){
       if(evt.property === "visibility") {
         if (evt.layer.key == layerKey) {
           if (evt.layer.visibility) {
             liveRequest();
           } else {
             window.clearTimeout(timeout)
           }
         }
       }
    });

    var fnLiveCallback = function urlRequestHandler(request) {
        var requestData = JSON.parse(request.responseText);
        var i,
            j,
            k,
            reqFeature,
            popupInfo,
            find,
            options,
            importFormat,
            featureList,
            idxFound,
            idString,
            endOfString,
            idxStart,
            idxEnd,
            findFeature,
            id;
        if (!requestData.error)
        {
          options = {
              internalProjection : map.getProjectionObject(),
              externalProjection : new OpenLayers.Projection('EPSG:4326')
          };

          importFormat = new OpenLayers.Format.GeoJSON(options);
          featureList = importFormat.read(requestData);

          for (i=0; i < featureList.length; i+=1) {
              reqFeature = featureList[i];
              if (reqFeature) {
                  findFeature = false;
                 for (j = 0; j < importLayer.children.length; j += 1) {
                     for (k = 0; k < importLayer.children[j].features.length; k += 1) {
                         if (importLayer.children[j].features[k]) {
                             popupInfo = importLayer.children[j].features[k].data.popupInfo;
                             if (popupInfo) {
                                 find = 'id="c4g_brick_popup_id"';
                                 idxFound = popupInfo.search(find);
                                 if ((idxFound) && (idxFound > 0)) {
                                     idString = popupInfo.substr(idxFound, 50);
                                     if (idString) {
                                         find = 'value="';
                                         idxFound = idString.search(find);
                                         if ((idxFound) && (idxFound > 0)) {
                                             idString = idString.substr(idxFound);
                                             if (idString) {
                                                 idxStart = idString.indexOf('="') + 2;
                                                 endOfString = idString.substring( idxStart );
                                                 idxEnd = endOfString.indexOf('">');
                                                 id = endOfString.substring( 0 , idxEnd );
                                                 if (id && id == reqFeature.data.id) {
                                                     var feature = importLayer.children[j].features[k];
                                                     feature.lonlat.lon = reqFeature.geometry.x;
                                                     feature.lonlat.lat = reqFeature.geometry.y;
                                                     if (reqFeature.data.popupInfo) {
                                                         feature.attributes.popupInfo = feature.data.popupInfo + reqFeature.data.popupInfo;
                                                     }
                                                     feature.geometry.move(reqFeature.geometry.x - feature.geometry.x, reqFeature.geometry.y - feature.geometry.y);
                                                     importLayer.children[j].redraw();
                                                     findFeature = true;
                                                     break;
                                                 }

                                             }
                                         }
                                     }
                                 }
                             }
                         }
                     }
                     if (findFeature) {
                        break;
                     }
                  }
                }
            }
        }
        timeout = window.setTimeout(function(){liveRequest()}, 10000);
    }

    var url = 'src/con4gis/CoreBundle/src/Resources/api/index.php/'+data.type+'Service?method=getPositions';

    var liveRequest = function() {
        OpenLayers.Request.GET({
            url : url,
            callback: fnLiveCallback
        });
    };

    if (importLayer.visibility) {
      liveRequest();
    }

}

/**
 *
 * @param request
 */
var fnPushCallback = function urlRequestHandler(request) {
    document.getElementById('c4g_push_message').value = '';
}

/**
 *
 * @param type
 * @param token
 * @param device
 * @param conf
 */
function onPushMessage(type, token, device, conf) {
    var content = document.getElementById('c4g_push_message').value;
    var url = '/con4gis/'+type+'Service/push?method=pushMessage&conf=' + conf + '&device=' + device + '&token=' + token + '&content=' + content;

    var pushMessage = function() {
        var client = new XMLHttpRequest();
        client.open('GET', url);
        client.setRequestHeader('push', content);
        client.send();
    };
    pushMessage();
};