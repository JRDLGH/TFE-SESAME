const routes = require( './Components/Routing/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
import 'jquery.typewatch';
import '../scss/profileToGesture.scss';

Routing.setRoutingData(routes);

var gestures = [];
var selectedGestures = [];

$(document).ready(function () {
    //fonction pour form quand submit!!

    var options = {
        callback: getGestures,
        wait: 750,
        highlight: true,
        allowSubmit: false,
        captureLength: 2
    };
    $('.js-gesture-choice .search-input').typeWatch(options);

    $(document).on('click','.js-select-gesture',function(evt){
        console.log(this.dataset.id);
        select('gesture',this.dataset.id);
        return false;
    });
});


function getGestures(name){
    if(name){
        console.log(name);
        $.ajax({
            url: Routing.generate('profiling_search_gesture',{name : name}),
            type: 'GET',
            statusCode: {
                404: function(data){
                    //RESOURCE NOT FOUND
                    console.log('not found');
                },
                500: function(){
                    //ERROR BACKEND
                    console.log('error');

                }
            }
        }).done(function(data){
            //MATCH HTTP_OK -- 200
            console.log(data);
            console.log(Routing.generate('profiling_search_gesture',{name : name}));
            displayGestures(data);
        });
    }
}

function displayGestures(data){
    gestures = data;
    formatHTML(data,'gesture');
    //when click on a gesture, he is selected
}

function formatHTML(data,type){
    var output = '';
    var $container;
    if(data){
        switch (type){
            case 'profile':
                ;
                break;
            case 'gesture':
                data.forEach(function(gesture){
                    output += gestureHTML(gesture);
                });
                $container = getGestureContainer();
                ;
                break;
        }
    }else{
        console.log("not data available");
    }
    console.log(output);
    $container.html(output);
}

// function profileHTML(){}
function gestureHTML(gesture){
    var html = '';
    html +=
        '<div class="card-container js-select-gesture" data-id="' + gesture.id + '">' +
            '<div class="card">' +
                '<i class="fa fa-american-sign-language-interpreting card-icon"></i>' +
                '<p class="card-title">'+ gesture.name +'</p>' +
            '</div>' +
        '</div>';
    return html;
}

function getGestureContainer(){
    return $('#gesture-search-result .search-content');
}

function select(type,data){

    switch(type){

        case 'profile': console.log("you select a profile");
        break;

        case 'gesture': console.log("you select a gesture");
        break;

    }
}