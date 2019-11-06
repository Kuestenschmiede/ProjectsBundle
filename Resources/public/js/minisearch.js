const minisearch = {};
minisearch.documents = [];
minisearch.minisearch = new MiniSearch({fields: ['text']});

function initSearch() {
    let rows = document.getElementsByClassName('c4g_brick_list_row');
    let r = 0;
    while (r < rows.length) {
        let columns = rows.item(r).getElementsByClassName('c4g_brick_list_row_column');
        let c = 0;
        let text = '';
        while (c < columns.length) {
            if (columns.item(c).tagName === 'LI') {
                text += columns.item(c).innerText + ', ';
            }
            c += 1;
        }
        minisearch.documents.push({'id': r + 1, 'text': text});
        r += 1;
    }
    minisearch.minisearch.addAll(minisearch.documents);
}

function search(input, event) {
    if (minisearch.documents.length === 0) {
        initSearch();
    }
    let result;
    if (input.value.length > 2) {
        result = minisearch.minisearch.search(input.value, {'prefix': true});
    }
    let rows = document.getElementsByClassName('c4g_brick_list_row');
    let r = 0;

    if (typeof result !== 'undefined') {
        while (r < rows.length) {
            rows.item(r).classList.add('c4g_list_search_hide');
            r += 1;
        }
        r = 0;
        while (r < rows.length) {
            if (typeof result[r] !== 'undefined' && typeof result[r].id !== 'undefined') {
                rows.item(result[r].id - 1).classList.remove('c4g_list_search_hide');
            }
            r += 1;
        }
    } else {
        while (r < rows.length) {
            rows.item(r).classList.remove('c4g_list_search_hide');
            r += 1;
        }
    }
}
