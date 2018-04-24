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
var currentValue = '';
var gestures;
var currentStatus = 'waiting'; //ex: {'success':"x gestures found"}

$(document).ready(function(){
    /**
     * KEY PRESS WILL NEED TO DETECT ENTER
     * ALSO, HIT ENTER OR THE SEARCH BUTTON WILL LEAD TO SAME OPERATIONS
     */
    $('#search').keyup(function(evt){
        var value = this.value.toLowerCase();
        var valuePosition = value.indexOf(previousValue['value']);

        currentValue = value;
        // if(isValid(value)){
            //If new value, request database call
            if(valuePosition == -1 || previousValue['value'] == '' || valuePosition != previousValue['position']){
                previousValue['value'] = value;
                previousValue['position'] = value.indexOf(previousValue['value']);
                askGestures(value);
            }else{
                //update current value
                //Search for correspondance depending on search type
                //By default: trie sur l'ordre de pertinence, les mots commençant par la sélection
                if(gestures){
                    orderByPertinence(gestures,value);
                }
            }
        // }
        return false;
    });

    $(document).on('click','.gesture.js-gesture',function(evt){
        //Show a cursor pointer on gesture!
        showGesture($(this).data('id'));
        return false;
    });
});

/**
 * Get the gesture with id passed.
 * @param id
 */
function showGesture(id){
    console.log(id);
    $.ajax({
        url: Routing.generate('thesaurus_gesture_show', {id: id}),
        type: 'GET',
        statusCode: {
            404: function(data){
                //RESOURCE NOT FOUND
            },
            500: function(){
                //ERROR BACKEND
            }
        }
    }).done(function(data){
        //MATCH HTTP_OK -- 200
        console.log(data);
    });
}

/**
 * Filter the data depending on their match nature (by name or by tag).
 * @param data
 * @param value
 */
function orderByPertinence(data,value){
    var matched,filteredNameMatched = [];
    var nameMatched = data.matched.byName;
    var tagMatched = data.matched.byTag;

    filteredNameMatched = matchNames(value,nameMatched);

    tagMatched = matchTags(value,tagMatched,nameMatched,filteredNameMatched);

    matched = filteredNameMatched.concat(tagMatched);
    display(matched);
}

/**
 * This function returns gesture's name that matches the pattern entered.
 * @param pattern
 * @param nameMatched
 * @return {Array}
 */
function matchNames(pattern,nameMatched){
    var matched = [];
    if(isArray(nameMatched) && pattern.length > 0){

        matched = getGesturesByName(pattern,nameMatched);

        if(matched.length > 0){
            matched.sort(sortByName);
        }
    }

    return matched;
}

function formatHTML(gesture){
    var cover = gesture.cover ? gesture.cover : "default.jpg"; //TODO in backend!!
    var title = gesture.name.charAt(0).toUpperCase() + gesture.name.slice(1);
    cover = "/build/static/thesaurus/gestures/" + cover;
    var html = "<article class=\"gesture js-gesture\" data-id=\"" + gesture.id + "\">\n" +
    "    <img src=\"" + cover +"\" alt=\"gesture-cover\" class=\"cover\">\n" +
    "    <div class=\"content\">\n" +
    "        <h3 class=\"title\">"+ title +"</h3>\n" +
    "        <p class=\"description\">\n" +
    "            " + gesture.description + "\n" +
    "        </p>\n" +
    "    </div>\n" +
    "</article>";
    return html;
}

function display(data){
    var content = '';
    if(isArray(data)){
        data.forEach(function (gesture){
            content += formatHTML(gesture);
        });
        console.log(data);
        getContainer().html(content);
        clearStatus();
    }else{
        //Not found
        setStatus({'not_found':'Aucun geste ne correspond à votre recherche'});
        getContainer().html('');
    }
}

/**
 *
 * @return {*|jQuery|HTMLElement}
 */
function getContainer(){
    return $('#gesture');
}

/**
 * This function returns gesture matched by tags with several differents options.
 * By default, this function look into both arrays and compare them.
 * @param pattern
 * @param tagMatched
 * @param nameMatched
 * @param option
 * @return array containing all gestures matching the pattern.
 */
function matchTags(pattern,tagMatched,nameMatched,sortedNameMatched,option){
    if(!option){
        option = 'both';
    }

    var tags = splitIntoTags(pattern);
    tags = tags.filter(getUniqueTags);
    console.log(tags);
    var result = [];
    //DELETE USELESS PATTERNS like 'de', 'le', 'la'
    //TODO
    tags = removeBlanksFromArray(tags);

    switch (option){
        //Look into namedMatched & tagMatched
        case 'both':
            result = getGesturesByTags(tags,nameMatched);
            if(isArray(result)){
                //Delete duplicates and merge
                result = arrayDiff(result,sortedNameMatched);
                console.log(sortedNameMatched);

                result = result.concat(getGesturesByTags(tags,tagMatched));
            }else{
                result = getGesturesByTags(tags,tagMatched);
            }
        break;
        //Look into tagMatched
        case 'tag': result = getGesturesByTags(tags,tagMatched);
        break;
        //Look into namedMatched
        case 'name': result = getGesturesByTags(tags,nameMatched);;
        break;
        default: 'Error';
    }
    return result;
}

/**
 * Returns cells that are unique inside array_a
 * @param array_a
 * @param array_b
 * @return {*}
 */
function arrayDiff(array_a,array_b){
    var array_diff = array_a.filter(function (cell_a) {
        var keep = true;
        array_b.forEach(function (cell_b) {
            if(cell_a === cell_b){
                keep = false;
            }
        });
        if(keep){
            return cell_a;
        }
    });
    return array_diff;

}

/**
 * Delete duplicates.
 * This code has been taken from:
 * https://stackoverflow.com/questions/1960473/get-all-unique-values-in-an-array-remove-duplicates
 * @param value
 * @param index
 * @param self
 * @return {boolean}
 */

function getUniqueTags(value, index, self) {
    if(self.indexOf(value) === index){
        return value.toLowerCase();
    };
}


/**
 * Checks if the parameter is an array and is not empty.
 * @param array
 * @return {boolean}
 */
function isArray(array){
    var isArray = false;
    if(Array.isArray(array) && array.length > 0){
        isArray = true;
    }
    return isArray;
}

/**
 * Remove blanks cells and empty cells in array given.
 * @param tags
 * @return {*}
 */
function removeBlanksFromArray(tags){
    var blankPositions = [];
    if(Array.isArray(tags) && tags.length > 0){
        tags.forEach(function(tag,index){
            if(tag == '' || tag == ' ' || tag == undefined || /\s\s+/g.test(tag)){
                blankPositions.push(index);
            }
        });
        if(blankPositions.length > 0){
            blankPositions.forEach(function (pos) {
                tags.splice(pos,1);
            });
        }
    }
    return tags;
}

/**
 * Get all gestures that are tagged by each tag inside tags array.
 * @param data, the gestures array
 * @param tags, the tags array
 */
function getGesturesByTags(tags,data){
    var matched = [];
    if(isArray(tags) && isArray(data)){
        matched = data.filter(function(gesture){
            //First, eliminate gesture that does not have enough tags
            if(tags.length <= gesture.tags.length){
                var keywords = mapTag(gesture.tags);
                var keep = true;
                console.log(keywords);
                for(var i =0; i < tags.length ; i++){
                    //if last tag
                    if(i == tags.length-1 && i >= 0){
                        //look if it begin or match the tag!
                        for(var j = 0; j < keywords.length; j++){
                            console.log(tags);
                            if(!keywords[j].startsWith(tags[i])){
                                keep = false;
                            }else{
                                keep = true;
                                break;
                            }
                        }
                        break;
                    }else{
                        if(keywords.indexOf(tags[i]) == -1){
                            keep = false;
                            break;
                        }
                    }
                }
                if(keep){
                    return gesture;
                }
            }
        });
    }
    return matched;

}
//Convert array of object in array of value
function mapTag(tags){
    var tagArray = [];
    if(Array.isArray(tags)){
        var keys = Object.keys(tags[0]);
        tags.forEach(function(tag){
            keys.forEach(function (key){
                tagArray.push(tag[keys[0]].toLowerCase());
            });
        });
    }
    return tagArray;
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

        //contains one word
        if(keywords.length == 1)
        {
            clear();
            getContainer().html('');
            setStatusMessage('waiting');

            $.ajax({
                url: Routing.generate('thesaurus_search_tag', {tag: keywords[0]}),
                type: 'GET',
                statusCode: {
                    404: function(data){
                        //RESOURCE NOT FOUND
                        setStatus(data.responseJSON);
                    },
                    500: function(){
                        //ERROR BACKEND
                        setStatusMessage('error','Une erreur est survenue, veuillez contacter l\'administrateur, si cela se reproduit.');
                    }
                }
            }).done(function(data){
                //MATCH HTTP_OK -- 200
                setGestures(data);
                console.log(value);
                console.log(currentValue);
                orderByPertinence(data,currentValue);
            });

        }
        //send a request to get gestures matching the word - value
    }else{
        //Nothing entered
        getContainer().html('');
        clearStatus();
        clear();
    }
}

function setGestures(data){
    if(data){
        gestures = data;
    }
}

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
        case 'init':setStatusMessage(state,status[state]);
        break;
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