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
    /**
     * KEY PRESS WILL NEED TO DETECT ENTER
     * ALSO, HIT ENTER OR THE SEARCH BUTTON WILL LEAD TO SAME OPERATIONS
     */
    $('#search').keypress(function(evt){
        var value = this.value;

        //REGEX -- ALLOW ONLY LETTERS
        if(/\w/.test(value) && !/[0-9]/.test(value))
        {
            console.log(value);
        }else{
            console.log("invalid format");
        }
    });
});

function formatGestures(gestures){
    var content = null;
    if(gestures){
        //Array
        if($.isArray(gestures)){
            //then loop on format
            gestures.forEach(function(gesture){
               content = formatGesture(gesture);
            });
        //Not array
        }else{
            content = formatGesture(gestures);
        }
    }
    return content;
}

function formatGesture(gesture){
    var html = '<article class="gesture">\n' +
        '    <img src="" alt="gesture-cover" class="cover">\n' +
        '    <div class="content">\n' +
        '        <h3 class="title">'+ gesture.name +'</h3>\n' +
        '        <p class="description">\n' +
        '            '+ gesture.id +'\n' +
        '        </p>\n' +
        '    </div>\n' +
        '</article>';
    return html;
}