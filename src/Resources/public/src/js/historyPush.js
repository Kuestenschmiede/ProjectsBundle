/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */


function historyPush(state, history, gui) {
    gui.pushingState = true;
    let newHref = window.location.href;
    let index = newHref.indexOf('?state=');
    if (index !== -1) {
        newHref = newHref.substr(0, index);
    }
    let queryString = '';
    if (newHref.indexOf('?') !== -1) {
        queryString = '&state=';
        index = newHref.indexOf('&state=');
        if (index !== -1) {
            newHref = newHref.substr(0, index);
        }
    } else {
        queryString = '?state='
    }
    if (document.location.hash) {
        history.pushState(null, document.title, newHref + queryString + state + document.location.hash);
    } else {
        history.pushState(null, document.title, newHref + queryString + state);
    }

    // strange workaround for Opera >= 11.60 bug
    // TODO kann raus ?
    if (typeof(document.getElement) !== 'undefined') {
        var head = document.getElement("head");
        if (typeof(head) === 'object') {
            var base = head.getElement('base');
            if (typeof(base) === 'object') {
                base.href = base.href;
            }
        }
    }

    gui.pushingState = false;
}