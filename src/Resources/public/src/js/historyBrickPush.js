/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @author con4gis contributors (see "authors.md")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2026, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

function backWithRefresh() {
    //this one for external pages
    var prevPage = window.location.href;
    history.go(-2);

    //and this needed if called per brick list
    setTimeout(function(){
        historyPush('', history, false);
        location.reload();
    }, 100);
}

function historyPush(state, history, gui) {
    if (gui) {
        gui.pushingState = true;
    }
    let url = new URL(window.location.href);
    let originalHash = url.hash;

    if ((state === 'list-1') || (state === 'list:-1')) {
        url.search = '';
    } else if (state) {
        // historyBrickPush seems to use state as a direct query key or value
        // based on existing logic: param = '?'+state
        // If it's not a key=value pair, URLSearchParams might handle it differently.
        // But the original code just appended it.
        url.search = '?' + state;
    }

    history.pushState(null, document.title, url.toString());

    if (gui) {
        gui.pushingState = false;
    }
}