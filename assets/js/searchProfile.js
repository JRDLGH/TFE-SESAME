const routes = require( './Components/Routing/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

import 'jquery.typewatch';
import '../scss/searchProfile.scss';
// import '../scss/structure/card.scss';
// import '../scss/structure/search.scss';

var paginator = {
    breakAt:    10,
    prevPg:     0,
    nextPg:     0,
    currentPg:  0,
    nbPages:    0,
    pageMap:    [],
    enabled:    true
}; //paginator conf



Routing.setRoutingData(routes);

$(document).ready(function () {

    var profileOptions = {
        callback: getProfiles,
        wait: 1000,
        highlight: true,
        allowSubmit: true,
        captureLength: 2
    };

    console.log(getProfileContainer());

    $('.search-input').typeWatch(profileOptions);

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

/* STATUS*/

function clearStatusContainer($statusContainer){
    $statusContainer.html('');
}

function getStatusContainer($container){
    return $container.parent().find('.status');
}

function setError(type,$container){
    switch (type){
        case 'not_found':
            getStatusContainer($container).html('Aucune correspondance trouvée.');

            break;
        case 'internal':
            getStatusContainer($container).html('Une erreur est survenue...');

            break;
        default:; //unkown
    }
    $container.html('');
}

function setWaiting($container){
    getStatusContainer($container).html('<i class="fa fa-spinner fa-spin"></i>');
}


/** PROFILES **/

function profileHTML(profile){
    var age = getAge(profile.birthday);
    var html = '';
    html +=
        '<div class="card-container profile-card js-select-profile" data-id="' + profile.profile.id + '">' +
        '<div class="card">' +
        '<i class="fa fa-user card-icon"></i>' +
        '<p class="card-title">' + profile.lastname + ' ' + profile.firstname + ' - ' + age + 'ans' + '</p>' +
        '<div class="request-access">Demander l\'accès</div>' +
        '</div>' +
        '</div>';
    return html;
}

function getAge(birthday){
    var currentDate = new Date();
    var birthDate = new Date(birthday);
    var age = currentDate.getFullYear() - birthDate.getFullYear();
    var m = currentDate.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && currentDate.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
}

function getProfiles(pattern){
    if(pattern){
        setWaiting(getProfileContainer());
        $.ajax({
            url: Routing.generate('profiling_search_profile',{pattern : pattern}),
            type: 'GET',
            statusCode: {
                404: function(data){
                    //RESOURCE NOT FOUND
                    setError('not_found',getProfileContainer());
                },
                500: function(){
                    //ERROR BACKEND
                    setError('internal',getProfileContainer());

                }
            }
        }).done(function(data){
            console.log(data);
            //MATCH HTTP_OK -- 200
            clearStatusContainer(getStatusContainer(getProfileContainer()));
            displayProfiles(data);
        });
    }
}

function getProfileContainer(){
    return $('.search-result .search-content');
}

function formatHTML(data,type){
    var output = '';
    var $container;
    if(data){
        switch (type){
            case 'profile':
                $container = getProfileContainer();
                data.forEach(function (profile) {
                    output += profileHTML(profile);
                })
                break;
            case 'gesture':
                data.forEach(function(gesture){
                    output += gestureHTML(gesture);
                });
                $container = getGestureContainer();
                ;
                break;
        }
        $container.html(output);
    }
}

function displayProfiles(data) {
    data = paginate(data);
    formatHTML(data,'profile');
}

/*
IS ARRAY
 */
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
        displayProfiles(paginator.pageMap[paginator.currentPg]);
        showPaginationButtons();
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
        displayProfiles(paginator.pageMap[paginator.currentPg]);
        showPaginationButtons();
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