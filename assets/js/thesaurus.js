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
    $('#search').keypress(function(evt){
        var value = this.value;

        //REGEX -- ALLOW ONLY LETTERS
        if(/\w/.test(value) && !/[0-9]/.test(value))
        {
            clearStatus();
            findGestureByName(value);
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
        gestureMatched = gestures.filter(function(gesture){
            // console.log(gesture);
            if(gesture.name == name){
                return true;
            }else if(gesture.keyword == name){
                return true;
            }else{
                return false;
            }
        });
        if(gestureMatched.length > 0){
            console.log(gestureMatched);
            //format gestures
            $('#gesture').html(formatGestures(gestureMatched));
            setStatus('success');
        }else{
            console.log('Not found');
            setStatus();
        }
    }else{
        console.log('ERROR: No gesture loaded.');
        setStatus();
    }
}

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

// success, waiting, not found, error
function setStatus(status){
    if(!status){
        status = '';
    }
    switch(status){
        case 'waiting':
            loadStatus();
            break;
        case 'success':
            successStatus();
            break;
        default:
                gestureNotFound();
            ; //not found
    }
}

function isLoading(){
    console.log('Is loading?' + $('.gestures-container .status i').hasClass('fa-spinner'));
    if($('.gestures-container .status i').hasClass('fa-spinner')){
        return true;
    }
    return false;
}

function loadStatus(){
    if(!isLoading()){
        $('.gestures-container .status i').addClass('fa fa-spinner fa-3x')
            .addClass('fa-spin');
    }
}

function removeLoadStatus(){
    if(isLoading()) {
        $('.gestures-container .status i').removeClass('fa fa-spinner fa-3x')
            .removeClass('fa-spin');
    }
}

function successStatus(){
    removeLoadStatus();
    if(!$('.gestures-container .status .status-message').hasClass('alert-success')){
        $('.gestures-container .status .status-message').addClass('alert-success').append('x geste(s) trouvés pour <b>'
            + $('#search').val() + '</b>' );
    }
}



function gestureNotFound(){
    console.log('going into nfound');
    if(!$('.gestures-container .status .status-message').hasClass('alert-not-found')){
        console.log('NOT FKING FOUND');
        $('.gestures-container .status .status-message').addClass('alert-not-found').append('Aucun geste trouvé pour <b>'
            + $('#search').val() + '</b>' );
    }
}

function clearStatus(){
    $('.gestures-container .status .status-message').html('');
    $('.gestures-container .status .status-message').attr('class','status-message');
}