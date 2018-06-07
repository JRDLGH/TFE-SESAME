import theme from "./theme";

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

    responsivePagination();

    $(window).resize(function () {
        responsivePagination();
    });

});

function responsivePagination() {
    let $prev = $('.pagination').children('li:first').children('a,span');
    let $next = $('.pagination').children('li:last').children('a,span');
    if($(window).width() >= theme.breakpoints.lg){

        if( !($prev.html() === "«&nbsp;Précédent") ){
            $prev.html("«&nbsp;Précédent");
        }

        if( !($next.html() === "Suivant&nbsp;»") ){
            $next.html("Suivant&nbsp;»");
        }

    }else{
        if( !($prev.html() === "«") ){
            $prev.html("«");
        }

        if( !($next.html() === "»") ){
            $next.html("»");
        }
    }
}

function navigate(page){
    if(page){
        $.ajax({
            url: page.href,
            type: 'GET'
        }).done(function (data) {
            getContainer().html(data);
            responsivePagination();
        });
    }
}


function getContainer(){
    return $('.pagination-content');
}

function getInputValue(){
    return $('.search-input input#search').val();
}

