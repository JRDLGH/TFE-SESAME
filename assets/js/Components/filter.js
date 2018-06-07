const routes = require( './Routing/fos_js_routes.json');
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes);

$(document).ready(function () {
    $(document).on('click','th a.sortable',function (evt) {
        evt.preventDefault();
        navigate(this);
    });
    $(document).on('click','th a.desc',function (evt) {
        evt.preventDefault();
        navigate(this);
    });
    $(document).on('click','th a.asc',function (evt) {
        evt.preventDefault();
        navigate(this);
    });
    $(document).on('click','a.page-link',function (evt) {
            evt.preventDefault();
            navigate(this);
    });
    $('.search-action').on('click',function(evt){
        evt.preventDefault();
            this.href = Routing.generate('admin_gesture_index', {filter: getInputValue()});
            navigate(this);
    });
});


function navigate(page){
    if(page){
        $.ajax({
            url: page.href,
            type: 'GET'
        }).done(function (data) {
            getContainer().html(data);
        });
    }
}


function getContainer(){
    return $('.pagination-content');
}

function getInputValue(){
    return $('.search-input input#search').val();
}

