function requestListContentFromServer() {
    var pattern = new RegExp(this.pattern);
    var value = this.value;
    var result = pattern.test(value);

    if (result) {
        var id = this.id.replace('c4g_list_search_');
        jQuery.get({url:'/con4gis/projectsbundle/ajax/' + value}).done(function(data) {
            var list = document.getElementById('c4g_list_' + id);

            //remove list items
            while (list.firstChild) {
                list.removeChild(list.firstChild);
            }

            var index = 0;
            while (index < data.length) {
                var listItem = document.createElement('li');
                listItem.classList.add('c4g_list_item');
                listItem.innerHTML = '<a href="'+ data[index]['href'] + '">' + data[index]['caption'] + '</a>';
                list.appendChild(listItem);
            }

        });
    }
}

function registerListModuleEvents() {
    var listSearchFields = document.getElementsByClassName('c4g_list_search_field');
    listSearchFields.addEventListener('input', requestListContentFromServer(), false);
}

registerListModuleEvents();