'use strict';
import "../scss/style.scss";
import "../scss/thesaurus.scss";
const routes = require( './Components/Routing/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';


//php bin/console fos:js-routing:dump --format=json --target=assets/js/Components/Routing/fos_js_routes.json

Routing.setRoutingData(routes);

/**
 * DEBUGGING SECTION
 */
console.log(routes);
/**
 * DEBUGGING END
 */

$(document).ready(function(){
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
            //send a request to get gestures matching the word - value
        }else{
            console.log("invalid format");
        }
    });
});