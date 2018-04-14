'use strict';

import "../scss/thesaurus.scss";
const routes = require( './Components/Routing/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';


// php bin/console fos:js-routing:dump --format=json --target=assets/js/Components/Routing/fos_js_routes.
// json
console.log(routes);
Routing.setRoutingData(routes);

$(document).ready(function(){
    $('#search').keyup(function(evt){
        //evt == event
        //this.value is the value
        var value = this.value;

        console.log(Routing.generate('thesaurus_test'));
        //REGEX -- ALLOW ONLY LETTERS
        if(/\w/.test(value) && !/[0-9]/.test(value))
        {
            $.get( Routing.generate('thesaurus_test'), function( data ) {
                $( ".result" ).html( data );
                console.log(data);
              });
        }else{
            console.log("invalid format");
        }
    });
});