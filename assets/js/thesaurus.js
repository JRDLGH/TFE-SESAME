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
var gestures;
var currentStatus = 'waiting'; //ex: {'success':"x gestures found"}

$(document).ready(function(){
    /**
     * KEY PRESS WILL NEED TO DETECT ENTER
     * ALSO, HIT ENTER OR THE SEARCH BUTTON WILL LEAD TO SAME OPERATIONS
     */
    $('#search').keyup(function(evt){
        var value = this.value;
        var valuePosition = value.indexOf(previousValue['value']);

        //If new value, request database call
        if(valuePosition == -1 || previousValue['value'] == '' || valuePosition != previousValue['position']){
            previousValue['value'] = value;
            previousValue['position'] = value.indexOf(previousValue['value']);
            askGestures(value);
        }else{
            //Search for correspondance depending on search type
            //By default: trie sur l'ordre de pertinence, les mots commençant par la sélection
            console.log('Not a new value');
            console.log(gestures);
            if(gestures){
                orderByPertinence(gestures,value);
                // if(isValid(value) && getStatus() != 'waiting'){
                //     setStatus({'not_found':'No gesture found for: '+value});
                // }
                return false;
            }
        }


    });
});

//1: Select all gestures matched by name, sort them by name corresponding to the entire value in alphabetical order
//2: Select all the gesture matched by Tag:
/**
 * Filter the data depending on their match nature (by name or by tag).
 * @param data
 * @param value
 */
function orderByPertinence(data,value){
    console.log('*** FILTERING ***')
    // console.log(data);
    // console.log('ON   '+ value);
    // console.log('GESTURE MATCHED ON NAME: ' + data.matched.byName.length);
    // console.log('GESTURE MATCHED ON TAG: ' + data.matched.byTag.length);
    var nameMatched = data.matched.byName;
    nameMatched = matchNames(value,nameMatched);
    //If matched on name


}

function matchNames(value,nameMatched){
    var matched = [];
    if(Array.isArray(nameMatched) && nameMatched.length > 0 && name.length > 0){
        console.log(nameMatched);
        console.log('FILTERING ON NAME');

        matched = getGesturesByName(value,nameMatched);
        if(matched.length > 0){
            matched.sort(sortByName);
            console.log(matched);
            //x gestures matched by name
            setStatus({'success':matched.length+' geste(s) correspondant à <i>"'+value+'"</i>.'});
        }else{
            //No gesture matched
            setStatus({'not_found':'Aucun geste correspondant à "<i>'+value+'</i>".'});
        }
    }else{
        setStatus({'not_found':'Aucun nom correspondant à "<i>'+value+'</i>".'});
    }
    return matched;
}

function getGesturesByName(name,data){
    //startsWith is case sensitive!
    if(name){
        name = name.toLowerCase();

        return data.filter(function(gesture){
            if(gesture['name'].toLowerCase().startsWith(name)){
                return gesture;
            }
        });
    }
}

function sortByName(a,b){
    return a.name.localeCompare(b.name);
}

function splitIntoTags(tags){
    var nospace_regex = /\s\s+/g;
    tags = tags.replace(nospace_regex,' ');
    tags = tags.split(/\s/);
    return tags;
}

function askGestures(value){
    //REGEX -- ALLOW ONLY LETTERS
    if(isValid(value))
    {
        var keywords = splitIntoTags(value);
        console.log(keywords.length +' MOT: '+keywords[0] + ' Route: '+Routing.generate('thesaurus_search_tag', {tag: keywords[0]}));

        //contains one word
        if(keywords.length == 1)
        {
            console.log('REQUEST ACCEPTED: QUERYING DATABASE FOR: '+value);
            clear();
            setStatusMessage('waiting');

            //your code to be executed after 1 second
            $.ajax({
                url: Routing.generate('thesaurus_search_tag', {tag: keywords[0]}),
                type: 'GET',
                statusCode: {
                    404: function(data){
                        //RESOURCE NOT FOUND
                        console.log(data.responseJSON);
                        setStatus(data.responseJSON);
                    },
                    500: function(){
                        setStatusMessage('error','Une erreur est survenue, veuillez contacter l\'administrateur, si cela se reproduit.');
                    }
                }
            }).done(function(data){
                //MATCH HTTP_OK -- 200
                setGestures(data);
                setStatus(data.status);
            });

        }
        //send a request to get gestures matching the word - value
    }
}

function setGestures(data){
    if(data){
        gestures = data;
    }
}

// function getStatus(){
//     if(currentStatus){
//         return currentStatus;
//     }
//     return null;
// }

function isValid(value){
    var isValid= false;
    if(/\w/.test(value) && !/[0-9]/.test(value))
    {
        isValid = true;
    }else if(value != ''){
        setStatusMessage('invalid','Format invalide');
    }else{
        clearStatus();
    }
    return isValid;
}

function clear(){
    gestures = null;
}

//success, waiting, not_found
//Must recieve an array
function setStatus(status){
    var state =  Object.keys(status)[0];
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
    currentStatus = state;

    var $statusElement = $('.status');
    if(message){
        $statusElement.children('.status-message').html(message);
    }
    switch (state){
        case 'success': $statusElement.children('i').addClass('fa fa-check fa-2x');
            break;
        case 'waiting': $statusElement.children('i').addClass('fa fa-spinner fa-spin fa-2x');
            break;
        case 'not_found': $statusElement.children('i').addClass('fa fa-times fa-2x');
            break;
        case 'error': $statusElement.children('i').addClass('fa fa-exclamation-triangle fa-2x');
            break;
        case 'invalid': $statusElement.children('i').addClass('fa fa-ban fa-2x');
            break;
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