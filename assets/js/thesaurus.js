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
/**
 * DEBUGGING END
 */

var previousValue = [];
var previousData;

$(document).ready(function(){
    /**
     * KEY PRESS WILL NEED TO DETECT ENTER
     * ALSO, HIT ENTER OR THE SEARCH BUTTON WILL LEAD TO SAME OPERATIONS
     */
    $('#search').keyup(function(evt){
        var value = this.value;
        var valuePosition = value.indexOf(previousValue['value']);

        if(valuePosition == -1 || previousValue['value'] == '' || valuePosition != previousValue['position']){
            //If new value, request database call
            previousValue['value'] = value;
            previousValue['position'] = value.indexOf(previousValue['value']);
            askGestures(value);
        }


    });
});

function splitIntoTags(tags){
    var nospace_regex = /\s\s+/g;
    tags = tags.replace(nospace_regex,' ');
    tags = tags.split(/\s/);
    return tags;
}

function orderByPertinence(data){
    console.log('Data recieved');
    console.log(data);
}

function askGestures(value){
    //REGEX -- ALLOW ONLY LETTERS
    if(/\w/.test(value) && !/[0-9]/.test(value))
    {
        //IMPROVE
        //PAS PRENDRE LE DELETE
        //NI L'ESPACE
        //

        var keywords = splitIntoTags(value);
        console.log(keywords.length +' MOT: '+keywords[0] + ' Route: '+Routing.generate('thesaurus_search_tag', {tag: keywords[0]}));
        if(keywords.length == 1)
        {
            console.log('Request accepted');
            setStatusMessage('waiting');
            $.ajax({
                url:Routing.generate('thesaurus_search_tag', {tag: keywords[0]}),
                type: 'GET',
                statusCode: {
                    404: function(data){
                        //RESOURCE NOT FOUND
                        console.log(data.responseJSON);
                        setStatus(data.responseJSON);
                    }
                }
            }).done(function(data){
                //MATCH HTTP_OK -- 200
                console.log('** RESPONSE :');
                previousData = data;
                setStatus(data.status);
                orderByPertinence(data);
            });
        }
        //send a request to get gestures matching the word - value
    }else{
        console.log("invalid format");
    }
}

//success, waiting, not_found
//Must recieve an array
function setStatus(status){
    var state =  Object.keys(status)[0];
    console.log(state);
    switch(state){
        case 'success':setStatusMessage(state,status[state]);
            break;
        case 'waiting':setStatusMessage(state,status[state]);
            break;
        case 'not_found':setStatusMessage(state,status[state]);
            break;
    }
}

function setStatusMessage(state,message){
    clearStatus();
    var $statusElement = $('.status');
    if(message){
        $statusElement.children('.status-message').html(message);
    }
    switch (state){
        case 'success': $statusElement.children('i').addClass('fa fa-check');
            break;
        case 'waiting': $statusElement.children('i').addClass('fa fa-spinner fa-spin');
            break;
        case 'not_found': $statusElement.children('i').addClass('fa fa-times');
            break;
        default: $statusElement.children('i').addClass('fa fa-exclamation-triangle');
    }
}

//Removes everything inside status elem
function clearStatus(){
    var $statusElement = $('.status');
    // clear message
    $statusElement.children('.status-message').html('');
    //clear icon
    $statusElement.children('i').attr('class','');
}