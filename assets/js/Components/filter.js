const routes = require( './Routing/fos_js_routes.json');
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes);

$(document).ready(function () {
    // $('th a.sortable').click(function (evt){
    //     evt.preventDefault();
    //     console.log('sortable');
    // });
    // $('th a.desc').click(function (evt) {
    //     evt.preventDefault();
    //     console.log('desc');
    // });
    // $('th a.asc').click(function (evt) {
    //     evt.preventDefault();
    //     console.log('asc');
    // });
    // $(document).on('click','.sortable',function (evt) {
    //     evt.preventDefault();
    //     console.log("h");
    // });
    // $('a.page-link').click(function (evt) {
    //     evt.preventDefault();
    //     console.log(this.href);
    //     navigate(this);
    //
    // });
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
        if(getInputValue()){
            this.href = Routing.generate('thesaurus_gesture_index', {filter: getInputValue()});
            navigate(this);
        }
        // navigate()
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

