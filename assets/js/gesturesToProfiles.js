const routes = require( './Components/Routing/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
import 'jquery.typewatch';

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
    console.log(gestures);
    //when click on a gesture, he is selected
}