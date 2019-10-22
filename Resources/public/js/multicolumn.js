function addRow(table) {
    let content = table.element.childNodes[1].innerHTML;
    let tr = document.createElement('tr');
    tr.innerHTML = content;
    let inputs = tr.getElementsByTagName('input');
    let i = 0;
    while (i < inputs.length) {
        inputs.item[i].value = '';
        i += 1;
    }
    return inputs;
}

function removeRow(row) {
    row.remove();
}

function updateInput(table) {
    let json = {};
    let tr = table.getElementsByTagName('tr');
    let i = 1;
    while (i < tr.length) {
        json[String(i)] = {};
        let inputs = tr.getElementsByTagName('input');
        let j = 0;
        while (j < inputs.length) {
            let input = inputs.item(j);
            json[String(i)][input.name] = input.value;
            j += 1;
        }
        i += 1;
    }
    table.parentNode.firstChild.value = JSON.stringify(json);
}
