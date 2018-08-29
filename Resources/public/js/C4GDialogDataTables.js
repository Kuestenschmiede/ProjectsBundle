/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */

$(document).ready( function () {
    var tables = document.getElementsByClassName('c4g_datatables');
    var index = 0;
    var dialogId = document.getElementById('c4gGuiContentWrapper' + c4g.projects.c4gGui.options.moduleId);
    var ajax = 'con4gis/brick_ajax_api/' + c4g.projects.c4gGui.options.moduleId + '/datatable:' + dialogId;
    var editors = [];

    while (index < tables.length) {
        let table = tables.item(index);
        let dataNames = table.dataset.columnNames.split(',');
        let dataLabels = table.dataset.columnLabels.split(',');
        let i = 0;
        let fields = [];
        let columns = [];
        while (i < dataNames.length) {
            fields.push({ label: dataLabels[i], name: dataNames[i] });
            columns.push({ data: dataLabels[i] });
            i += 1;
        }


        editors['editor_' + table.id] = new $.fn.dataTable.Editor( {
            table: table.id,
            fields: fields
        } );
        table.DataTable( {
            ajax: ajax + ':' + index,
            dom: 'Bfrtip',
            columns: columns,
            select: true,
            buttons: [
                { extend: 'create', editor: editors[index] },
                { extend: 'edit',   editor: editors[index] },
                { extend: 'remove', editor: editors[index] }
            ]
        } );
        index += 1;
    }
} );