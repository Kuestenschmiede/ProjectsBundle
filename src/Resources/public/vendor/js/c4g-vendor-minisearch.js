/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 10
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2025, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

import MiniSearch from 'minisearch';

const mini = {};

function initSearch(includeSubordinateData) {
    let lists = document.getElementsByClassName('c4g_brick_list');
    mini.documents = [];
    mini.minisearch = new MiniSearch({fields: ['text']});
    while (lists.length > 1) {
        lists.item(0).remove();
    }
    let rows = document.getElementsByClassName('c4g_brick_list_row');
    let r = 0;
    while (r < rows.length) {
        let columns = rows.item(r).getElementsByClassName('c4g_brick_list_row_column');
        let c = 0;
        let text = '';
        while (c < columns.length) {
            if (columns.item(c).tagName === 'LI' && (includeSubordinateData || !(columns.item(c).classList.contains('searchInfoSubordinate')))) {
                text += columns.item(c).textContent.split(/(?=[A-Z])/).join(' ') + ', ';
                text += getTitles(columns.item(c));
            }
            c += 1;
        }
        mini.documents.push({'id': r + 1, 'text': text});
        r += 1;
    }
    mini.minisearch.addAll(mini.documents);
}

function search(input, event) {
    let result;
    if (input.value.length > 2) {
        initSearch(false);
        result = mini.minisearch.search(input.value, {'prefix': true, 'combineWith': 'AND'});
        if (result.length === 0) {
            initSearch(true);
            result = mini.minisearch.search(input.value, {'prefix': true, 'combineWith': 'AND'});
        }
    }
    let rows = document.getElementsByClassName('c4g_brick_list_row');
    let r = 0;

    if (typeof result !== 'undefined') {
        if (result.length === 0) {
            try {
                let notice = document.getElementsByClassName('c4g_brick_list_mini_no_results_note')[0];
                notice.style.display = 'block';
            } catch (e) {}
        } else {
            try {
                let notice = document.getElementsByClassName('c4g_brick_list_mini_no_results_note')[0];
                notice.style.display = 'none';
            } catch (e) {}
        }
        while (r < rows.length) {
            rows.item(r).parentNode.classList.add('c4g_list_search_hide');
            r += 1;
        }
        r = 0;
        while (r < rows.length) {
            if (typeof result[r] !== 'undefined' && typeof result[r].id !== 'undefined') {
                rows.item(result[r].id - 1).parentNode.classList.remove('c4g_list_search_hide');
            }
            r += 1;
        }
    } else {
        while (r < rows.length) {
            rows.item(r).parentNode.classList.remove('c4g_list_search_hide');
            r += 1;
        }
        try {
            let notice = document.getElementsByClassName('c4g_brick_list_mini_no_results_note')[0];
            notice.style.display = 'none';
        } catch (e) {}
    }
}

function getTitles(element) {
    let titles = [element.title || ''];
    for(let child=element.firstChild; child!==null; child=child.nextSibling) {
        titles.push(getTitles(child));
    }
    titles = titles.filter(function(val) {
        return val !== '';
    });
    if (titles.length > 0) {
        return titles.join(' ') + ', ';
    } else {
        return '';
    }
}

window.search = search;