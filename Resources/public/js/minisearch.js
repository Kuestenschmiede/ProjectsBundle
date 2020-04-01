const minisearch = {};

function initSearch() {
    let lists = document.getElementsByClassName('c4g_brick_list');
    minisearch.documents = [];
    minisearch.minisearch = new MiniSearch({fields: ['text']});
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
            if (columns.item(c).tagName === 'LI') {
                text += columns.item(c).innerText.split(/(?=[A-Z])/).join(' ') + ', ';
                text += getTitles(columns.item(c));
            }
            c += 1;
        }
        minisearch.documents.push({'id': r + 1, 'text': text});
        r += 1;
    }
    minisearch.minisearch.addAll(minisearch.documents);
}

function search(input, event) {
    let result;
    if (input.value.length > 2) {
        if (typeof minisearch.documents === 'undefined' || document.getElementsByClassName('c4g_brick_list').length > 1) {
            initSearch();
        }
        result = minisearch.minisearch.search(input.value, {'prefix': true, 'combineWith': 'AND'});
    }
    let rows = document.getElementsByClassName('c4g_brick_list_row');
    let r = 0;

    if (typeof result !== 'undefined') {
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
