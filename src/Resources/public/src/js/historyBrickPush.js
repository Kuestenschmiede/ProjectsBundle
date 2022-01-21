/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2022, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */


function historyPush(state, history, gui) {
    gui.pushingState = true;
    let newHref = window.location.href;
    let removeGetParam = false;

    if (state == 'list-1') {
        let idx = newHref.indexOf('?');
        if (idx !== -1) {
            newHref = newHref.substr(0, idx);
        }
        removeGetParam = true;
    }

    let indexList = newHref.indexOf('?list-1');
    if (indexList !== -1) {
        newHref = newHref.substr(0, indexList);
    }

    let param = '?'+state;

    let index = newHref.indexOf('?state=');
    let indexState = newHref.indexOf('?'+state);

    if (index !== -1) {
        newHref = newHref.substr(0, index);
    } else if (indexState !== -1) {
        newHref = newHref.substr(0, indexState);
    }

    let queryString = '';
    if (newHref.indexOf('?') !== -1) {
        queryString = param;
        let index = newHref.indexOf('&state=');
        let indexState = newHref.indexOf('&'+state);
        if (index !== -1) {
            newHref = newHref.substr(0, index);
        } else if (indexState !== -1) {
            newHref = newHref.substr(0, indexState);
        }
    } else {
        queryString = param;
    }

    if (document.location.hash) {
        history.pushState(null, document.title, newHref + queryString + document.location.hash);
    } else {
        history.pushState(null, document.title, newHref + queryString);
    }

    if (removeGetParam) {
        if (state == 'list-1') {
            let idx = newHref.indexOf('?list-1');
            if (idx !== -1) {
                newHref = newHref.substr(0, idx);
            }
            let removeGetParam = true;
        }

        if (document.location.hash) {
            history.pushState(null, document.title, newHref + document.location.hash);
        } else {
            history.pushState(null, document.title, newHref);
        }
    }

    gui.pushingState = false;
}