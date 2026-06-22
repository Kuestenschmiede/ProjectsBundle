/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @author con4gis contributors (see "authors.md")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2026, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */


function historyPush(state, history, gui) {
    gui.pushingState = true;
    let url = new URL(window.location.href);
    let params = new URLSearchParams(url.search);
    
    // update state in search params
    if (state) {
        params.set('state', state);
    } else {
        params.delete('state');
    }
    
    url.search = params.toString();
    
    // Contao 5 / Routing often uses fragments for state if configured or via specific JS.
    // If the user sees "#?state=", it means the state is in the hash.
    if (url.hash && url.hash.indexOf('state=') !== -1) {
        let hashPart = url.hash.substring(1); // remove #
        if (hashPart.indexOf('?') === 0) {
            hashPart = hashPart.substring(1);
        }
        let hashParams = new URLSearchParams(hashPart);
        if (state) {
            hashParams.set('state', state);
        } else {
            hashParams.delete('state');
        }
        let newHash = hashParams.toString();
        url.hash = newHash ? '?' + newHash : '';
    }

    history.pushState(null, document.title, url.toString());

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