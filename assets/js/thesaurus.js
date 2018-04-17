'use strict';

import "../scss/thesaurus.scss";
const routes = require( './Components/Routing/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';


//php bin/console fos:js-routing:dump --format=json --target=assets/js/Components/Routing/fos_js_routes.json

Routing.setRoutingData(routes);

/**
 * DEBUGGING SECTION
 */
console.log(routes);
console.log(Routing.generate('thesaurus_gesture_list'));
/**
 * DEBUGGING END
 */


// load gestures
let gestures = '';

$(document).ready(function(){
    loadGestures();
    $('#search').keyup(function(evt){
        var value = this.value;

        //REGEX -- ALLOW ONLY LETTERS
        if(/\w/.test(value) && !/[0-9]/.test(value))
        {
            $('.gestures-container .status i').addClass('fa fa-spinner fa-3x')
                .addClass('fa-spin');
            findGestureByName(value);
            // $.get( Routing.generate('thesaurus_gesture_list'), function( data ) {
            //     // $( ".result" ).html( data );
            //     console.log(data);
            //   });
        }else{
            console.log("invalid format");
        }
    });
});

function loadGestures(){
    $.get(Routing.generate('thesaurus_gesture_list'),function (data){
        //On success, load into gesture
        gestures = JSON.parse(data);
    });
}

//Look if the name matches name of gesture
//Top of the list
//if gesture name match, then, it must be the first to appear
//Otherwise, if the name match a tag, it must be on top on the list but not always first
//And last case, if the name hit partially a tag or a gesture name, display him
function findGestureByName(name){
    if(gestures){
        var gestureMatched = [];
        for(var i = 0; i < gestures.length; i++){
            //Look if the name matches name of gesture
            if(gestures[i].name == name){
                //Top of the list
                //if gesture name match, then, it must be the first to appear
                console.log('It\'s a match with: ');
                console.log(gestures[i]);
                gestureMatched.unshift(gesture[i].id);
                //ressortir tous les gestes ayant un rapport avec le mot clé utilisé
                //faire un find
                //ensuite un sort pour prendre du plus pertinent au moins pertinent
                //ensuite faire un push et rajouter après
            }

            //Otherwise, if the name match a tag, it must be on top on the list but not always first
            //And last case, if the name hit partially a tag or a gesture name, display him
            if(i == 2){
                break;
            }
        }
    }else{
        console.log('No gesture found.');
    }
}
