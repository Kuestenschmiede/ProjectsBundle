/*
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

if (!Modernizr.touch || !Modernizr.inputtypes.date) {
    $('input[type=date]')
        .attr('type', 'text')
        .datepicker({
            // Consistent format with the HTML5 picker
            dateFormat: 'yy-mm-dd'
        });
}

// This must be defined before including ckeditor.js:
var removeButtons = "FileUpload,Image";