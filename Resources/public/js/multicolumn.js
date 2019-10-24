function onAddRowButtonClick(element, event) {
    addRow(element.parentNode.parentNode.parentNode.parentNode);
}

function onRemoveRowButtonClick(element, event) {
    removeRow(element.parentNode.parentNode);
}

function onMultiColumnInput(element, event) {
    updateInput(element.parentNode.parentNode.parentNode.parentNode);
}

function addRow(table) {
    let content = table.firstChild.childNodes[1].innerHTML;
    let tr = document.createElement('tr');
    tr.innerHTML = content;
    tr = table.firstChild.appendChild(tr);
    let inputs = tr.getElementsByTagName('input');
    let i = 0;
    while (i < inputs.length) {
        inputs.item(i).value = '';
        i += 1;
    }
    return inputs;
}

function removeRow(row) {
    if (row.parentNode.childNodes.length > 2) {
        row.remove();
    }
}

function removeAllRows(table) {
    while (table.firstChild.childNodes.length > 2) {
        table.firstChild.childNodes[2].remove();
    }
}

function updateInput(table) {
    let json = [];
    let tr = table.getElementsByTagName('tr');
    let i = 1;
    while (i < tr.length) {
        let index = json.push({}) - 1;
        let inputs = tr.item(i).getElementsByTagName('input');
        let j = 0;
        while (j < inputs.length) {
            let input = inputs.item(j);
            json[index][input.name] = input.value;
            j += 1;
        }
        i += 1;
    }
    json = JSON.stringify(json);
    json = json.replace(/"/g, '\'');
    table.parentNode.firstChild.value = json;
}

function readInitialValues() {
    let tables = document.getElementsByClassName('c4g_multicolumn');
    let t = 0;
    while (t < tables.length) {
        let table = tables.item(t);
        let values = JSON.parse(table.parentNode.firstChild.value.replace(/'/g, '"').substr(1).slice(0, -1));
        let ths = table.firstChild.firstChild.childNodes;
        let n = 0;
        removeAllRows(table);
        let i = 0;
        while (i < values.length) {
            let rowValues = values[i];
            let inputs;
            if (i === 0) {
                inputs = table.firstChild.childNodes[1].getElementsByTagName('input');
            } else {
                inputs = addRow(table);
            }
            let j = 0;
            while (j < inputs.length) {
                inputs.item(j).value = rowValues[inputs.item(j).name] || '';
                j += 1;
            }
            i += 1;
        }
        t += 1;
    }
}
