'use strict';
import "../scss/thesaurus.scss";
const routes = require( './Components/Routing/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
import Status from './Components/Status';

var paginator = {
    breakAt:    6,
    prevPg:     0,
    nextPg:     0,
    currentPg:  0,
    nbPages:    0,
    pageMap:    [],
    enabled:    true
}; //paginator conf

//php bin/console fos:js-routing:dump --format=json --target=assets/js/Components/Routing/fos_js_routes.json

Routing.setRoutingData(routes);

const StatusHandler = new Status();

/**
 * DEBUGGING SECTION
 */
// console.log(routes);
/**
 * DEBUGGING END
 */

let previousValue = [];
let currentValue = '';
let gestures;
let lastMatched = []; // last gesture that matched

$(document).ready(function(){

    const $searchInput = $('#search');

    $searchInput.on('click',function(){
        $('body, html').animate({
            scrollTop: $(this).offset().top - $('header').outerHeight() - 50
        },1000);
    });
    /**
     * KEY PRESS WILL NEED TO DETECT ENTER
     * ALSO, HIT ENTER OR THE SEARCH BUTTON WILL LEAD TO SAME OPERATIONS
     */
    $searchInput.keyup(function(evt){
        let value = this.value.toLowerCase();
        let valuePosition = value.indexOf(previousValue['value']);

        currentValue = value;
        // if(isValid(value)){
            //If new value, request database call
            if((valuePosition == -1 || previousValue['value'] == '' || valuePosition != previousValue['position']) && currentValue.length >= 2){
                previousValue['value'] = value;
                previousValue['position'] = value.indexOf(previousValue['value']);
                askGestures(value);
            }else if(currentValue == '' || currentValue == ' '){
                // clearStatus();
                StatusHandler.clear();
                clear();
                getContainer().html('');
            }
            else{
                //update current value
                //Search for correspondance depending on search type
                //By default: trie sur l'ordre de pertinence, les mots commençant par la sélection
                if(gestures){
                    //reset pagination in order to avoid to be stuck inside a page
                    resetPagination();
                    orderByPertinence(gestures,value);
                }
            }
        // }
        return false;
    });

    $(document).on('click','.js-gesture-show',function(evt){
        //Show a cursor pointer on gesture!
        showGesture($(this).closest('.gesture').data('id'));
        return false;
    });

    $(document).on('click','.js-previous-search',function(evt){
        //back to previous search
        containerDisplay('list');
        getDetailsContainer().removeClass('opened');
        getContainer().removeClass('closed');
        if(paginator.nbPages > 0){
            showPaginationButtons();
        }
        return false;
    });

    /**
     * PAGINATOR EVENT LISTENER
     */

    $('.js-previous-page').click(function(evt){
        previous();
        return false;
        //enable previous        
        //possibility of disabling next
    });

    $('.js-next-page').click(function(evt){
        evt.preventDefault();
        //enable previous
        //possibility of disabling prev        
        next();
        return false;
    });

});

/**
 * jQuery animation that scrolls to gestures container.
 */
function scrollToContainer(){

    var space = '50';
    var position = getContainer().offset().top - $('header').outerHeight() - space;
        $('body, html').animate({
            scrollTop: position
        },1000);
}

/**
 * jQuery animation that scolls to details container.
 */
function scrollToDetailsContainer(){

    var space = '50';
    var position = getDetailsContainer().offset().top - $('header').outerHeight() - space;
    $('body, html').animate({
        scrollTop: position
    },1000);
}


/**
 * Get the gesture with id passed.
 * @param id
 */
function showGesture(id){
    $.ajax({
        url: Routing.generate('thesaurus_gesture_show_details', {id: id}),
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
        display(JSON.parse(data),'details');
    });
}

/**
 * Filter the data depending on their match nature (by name or by tag).
 * @param data
 * @param value
 */
function orderByPertinence(data,value){
    let matched,filteredNameMatched = [];
    let nameMatched = data.matched.byName;
    let tagMatched = data.matched.byTag;

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
    let matched = [];
    if(isArray(nameMatched) && pattern.length > 0){

        matched = getGesturesByName(pattern,nameMatched);

        if(matched.length > 0){
            matched.sort(sortByName);
        }
    }

    return matched;
}

/**
 * Format HTML for a gesture details.
 * @param gesture
 * @return {string}, the html details
 */
function formatHTML(gesture){
    let cover = gesture.cover ? gesture.cover : "default.jpg"; //TODO in backend!!
    cover = "/build/static/thesaurus/gestures/" + cover;
    let video = gesture.name;
    let video_path = "/build/static/thesaurus/gestures/videos/" + video + ".mp4";
    let title = gesture.name.charAt(0).toUpperCase() + gesture.name.slice(1);

    let html = "<h2 class=\"gesture-details-header-title\">Détails</h2>" +
        "<article class=\"gesture-details-content\">\n" +
        "    <img src=\"" + cover +"\" alt=\"gesture-cover\" class=\"cover\">\n" +
        "    <div class=\"content\">\n" +
        "        <h3 class=\"title\">"+ title +"</h3>\n" +
        "        <p class=\"description\">\n" +
        "            " + gesture.description + "\n" +
        "        </p>\n" +
        "    </div>\n" +
        "    <hr class=\"separator\">" +
        "   <h3 class=\"gesture-details-video-title\">Vidéos</h3>" +
        "   <div id=\"gesture-videos\">" +
        "       <div class=\"gesture-video\">" +
        "           <h4 class=\"gesture-video-title\">De profil</h4>" +
        "            <video class=\"js-gesture-video\" controls controlsList=\"nodownload\">" +
        "                   <source src=\""+video_path+"\" type=\"video/mp4\" />\n" +
        "                   Please update your browser." +
        "               </video>" +
        "       </div>" +
        "       <div class=\"gesture-video\">" +
        "           <h4 class=\"gesture-video-title\">De face</h4>" +
        "           <video class=\"js-gesture-video\" controls controlsList=\"nodownload\">" +
        "               <source src=\""+video_path+"\" type=\"video/mp4\" />\n" +
        "               Please update your browser." +
        "           </video>" +
        "       </div>" +
        "</div>" +
        backToSearchButton() +
        "</article>";
   return html;
}

/**
 * Format the HTML for gesture displayed in a list.
 * @param gesture
 * @return {string}
 */
function listHTML(gesture) {
    let cover = gesture.cover ? gesture.cover : "default.jpg"; //TODO in backend!!
    cover = "/build/static/thesaurus/gestures/" + cover;
    let title = gesture.name.charAt(0).toUpperCase() + gesture.name.slice(1);
    return "<article class=\"gesture js-gesture\" data-id=\""+ gesture.id +"\">" +
        "<div class=\"gesture-content\">" +
            "<img src=\""+ cover +"\" " + "alt=\"gesture-cover\" class=\"cover\">" +
            "<div class=\"content\">" +
                "<h3 class=\"title\">"+ title +"</h3>" +
            "</div>" +
            "<button class=\"btn btn-secondary js-gesture-show\">" +
                "<span>Voir les vidéos</span>" +
            "</button>" +
        "</div>" +
    "</article>";
}

/**
 * Display gestures depending on the data given and the type of display.
 * @param data
 * @param type
 */
function display(data,type){

    let gIds = getGestureId(data);
    let display = false;

    if(!isArray(lastMatched) || !compareArray(gIds,lastMatched) || isContainerEmpty()){
        lastMatched = gIds; //must only contains id of last matched gestures
        display = true;
    }

    let content = '';
    if(isArray(data)){
        switch (type){
            case 'details': content = formatHTML(data[0]);
                containerDisplay('details');
                getDetailsContainer().html(content);
                hidePaginationButtons();

            break;
            default: //list
            if(display){
                data = paginate(data);
                data.forEach(function (gesture){
                    content += listHTML(gesture);
                });
                containerDisplay('list');
                getContainer().html(content);
            }
        }
        StatusHandler.clear();
    }else{
        //Not found
        StatusHandler.set('not_found','Aucun geste ne correspond à votre recherche');
        getContainer().html('');
    }
}

function isContainerEmpty() {
    if(getContainer().html() === null || getContainer().html() == '' || getContainer().html() == undefined){
        return true;
    }
    return false;

}

/**
 * Compare two array, if they are exactly the same, return true;
 * @param {*} array1 
 * @param {*} array2 
 * @returns boolean
 */
function compareArray(array1,array2){
    return JSON.stringify(array1)==JSON.stringify(array2);
}

/**
 * Return the id of all the gestures inside the array.
 * @param gArray
 * @return {*}
 */
function getGestureId(gArray){
    if(isArray(gArray)){
        var ids = gArray.map(function(gesture){
            return gesture.id;
        });
        return ids;
    }
}

/**
 * Display/hide a container depending on the container to display.
 * @param container
 */
function containerDisplay(container){
    switch (container){
        case 'details':
            getContainer().addClass('closed').on('transitionend',showDetails());
        break;
        case 'list':
            getDetailsContainer().removeClass('opened').removeClass('display-block');
            getContainer().removeClass('closed').removeClass('display-none');
        break;
    }
}


function showDetails(){
    getContainer().addClass('display-none');
    getDetailsContainer().addClass('display-block').addClass('opened');
    scrollToDetailsContainer();
}

/**
 * Allow you to go back on search.
 * @return {string}
 */
function backToSearchButton() {
    return '<button class="btn btn-primary js-previous-search">Retourner à la recherche</button>';
}

/**
 * HTML element that holds list of gestures.
 * @return {*|jQuery|HTMLElement}
 */
function getContainer(){
    return $('#gesture');
}

/**
 * HTML element that holds one gesture and his details.
 * @return {*|jQuery|HTMLElement}
 */
function getDetailsContainer(){
    return $('.gesture-details');
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

    let tags = splitIntoTags(pattern);
    tags = tags.filter(getUniqueTags);
    let result = [];
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
                result = result.concat(getGesturesByTags(tags,tagMatched));
            }else{
                result = getGesturesByTags(tags,tagMatched);
            }
        break;
        //Look into tagMatched
        case 'tag': result = getGesturesByTags(tags,tagMatched);
        break;
        //Look into namedMatched
        case 'name': result = getGesturesByTags(tags,nameMatched);
        break;
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
    let array_diff = array_a.filter(function (cell_a) {
        let keep = true;
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
    let isArray = false;
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
    let blankPositions = [];
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
    let matched = [];
    if(isArray(tags) && isArray(data)){
        matched = data.filter(function(gesture){
            //First, eliminate gesture that does not have enough tags
            if(tags.length <= gesture.tags.length){
                let keywords = mapTag(gesture.tags);
                let keep = true;
                for(let i =0; i < tags.length ; i++){
                    //if last tag
                    if(i === tags.length-1 && i >= 0){
                        //look if it begin or match the tag!
                        for(let j = 0; j < keywords.length; j++){
                            if(!keywords[j].startsWith(tags[i])){
                                keep = false;
                            }else{
                                keep = true;
                                break;
                            }
                        }
                        break;
                    }else{
                        if(keywords.indexOf(tags[i]) === -1){
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
    let tagArray = [];
    if(Array.isArray(tags)){
        let keys = Object.keys(tags[0]);
        tags.forEach(function(tag){
            keys.forEach(function (){
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
    let nospace_regex = /\s\s+/g;
    tags = tags.replace(nospace_regex,' ');
    tags = tags.split(/\s/);
    return tags;
}

/**
 * Verify if the value given is correct and if it is, query the database with.
 * @param value
 */
function askGestures(value){
    //REGEX -- ALLOW ONLY LETTERS
    if(isValid(value))
    {
        let keywords = splitIntoTags(value);

        //contains one word
        if(keywords.length === 1)
        {
            clear();
            getContainer().html('');
            StatusHandler.set('waiting');

            $.ajax({
                url: Routing.generate('thesaurus_search_tag', {tag: keywords[0]}),
                type: 'GET',
                statusCode: {
                    404: function(data){
                        //RESOURCE NOT FOUND
                        if(isArray(data)){
                            // StatusHandler.set();
                            console.log(data.responseJSON);
                        }else{
                            StatusHandler.set('not_found');
                        }
                    },
                    500: function(){
                        //ERROR BACKEND
                        StatusHandler.set('error');
                    }
                }
            }).done(function(data){
                //MATCH HTTP_OK -- 200
                setGestures(data);
                orderByPertinence(data,currentValue);
            });

        }
        //send a request to get gestures matching the word - value
    }else{
        //Nothing entered
        getContainer().html('');
        StatusHandler.clear();
        clear();
    }
}

function setGestures(data){
    if(data){
        gestures = data;
    }
}

function isValid(value){
    let isValid= false;
    if(/\w/.test(value) && !/[0-9]/.test(value))
    {
        isValid = true;
    }else if(value !== ''){
        StatusHandler.set('invalid','Format invalide');
    }else{
        StatusHandler.clear();
    }
    return isValid;
}

function clear(){
    gestures = null;
    hidePaginationButtons();
}

/*
PAGINATOR -- AUTHOR: JORDAN LGH
 */



// IF THERE'S MORE THAN 10 RESULT, BREAK INTO SMALLER ARRAYS
// DISPLAY 2 BUTTONS. PREVS AND NEXT

/**
 * Do pagination.
 * @param data
 */
function paginate(data){
    var result = data;

    if(paginator.enabled && data.length > paginator.breakAt){
        breakIntoPage(data,paginator.breakAt);
        if(isArray(paginator.pageMap)){
            result = paginator.pageMap[0];
            paginator.current = 0;
            showPaginationButtons();
        }
    }else{
        hidePaginationButtons();
    }
    return result;
}

function hidePaginationButtons(){
    if(!getPaginationContainer().attr('style','display:none;')){
        getPaginationContainer().hide();
    }
}

function showPaginationButtons(){
    if(getPaginationContainer().attr('style','display:none;')){
        getPaginationContainer().show();
    }
}

function getPaginationContainer(){
    return $('.js-pagination-controls');
}

function next(){
    //go to next page IF there's a next page
    if(paginator.currentPg < paginator.nbPages-1){
        //you can go to next page
        paginator.currentPg += 1;
        display(paginator.pageMap[paginator.currentPg]);
        showPaginationButtons();
        scrollToContainer();
        if(paginator.currentPg == paginator.nbPages -1){
            $('.js-next-page').addClass("disabled");
            enableButton('previous');
        }else{
            enableButton('next');
            enableButton('previous');
        }
    }else{
        //disabled
    }
}

function enableButton(button){
    switch(button){
        case 'next': 
            if(isDisbaled($('.js-next-page'))){
                $('.js-next-page').removeClass('disabled');
            }
        break;
        case 'previous': 
            if(isDisbaled($('.js-previous-page'))){
                $('.js-previous-page').removeClass('disabled');
            }
        break;
        case 'init':
            $('.js-previous-page').addClass('disabled');
            $('.js-next-page').removeClass('disabled');
        break;
    }
}

function isDisbaled(elem){
    return elem.hasClass('disabled');
}

function previous(){
    if(paginator.currentPg >= 1 && paginator.nbPages >= paginator.currentPg){
        //you can go to previous page
        paginator.currentPg -= 1;
        display(paginator.pageMap[paginator.currentPg]);
        showPaginationButtons();
        scrollToContainer();
        if(paginator.currentPg == 0){
            $('.js-previous-page').addClass("disabled");
            enableButton('next');            
        }else{
            enableButton('next');
            enableButton('previous');
        }
    }else{
        //disabled
    }
}

/**
 * Break an array of data in mutliple array of data, each key represent a page.
 * @param data
 * @param limit
 */
function breakIntoPage(data,limit){
    //Must be done only once per pagination
    if(isArray(data) && limit >= 2){
        paginator.nbPages = Math.ceil(data.length/limit); //round up!
        paginator.pageMap = splitArray(data,limit);
    }
}

/**
*   Split an array in mutliple array of x cells.
 *   @param array
 *   @param limit, the number of element per array
 */
function splitArray(array,limit){
    var splitArray = [];
    while(array.length > 0){
        splitArray.push(array.splice(0,limit));
    }
    return splitArray;
}

function resetPagination(){
    paginator.prevPg = 0;
    paginator.nextPg = 0;
    paginator.currentPg = 0;
    paginator.nbPages = 0;
    paginator.pageMap = 0;
    enableButton('init');
}