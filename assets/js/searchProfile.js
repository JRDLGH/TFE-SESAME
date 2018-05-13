import Paginator from "./Components/Paginator";

const routes = require( './Components/Routing/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

import 'jquery.typewatch';
import '../scss/searchProfile.scss';
// import '../scss/structure/card.scss';
// import '../scss/structure/search.scss';



Routing.setRoutingData(routes);
const Pagination = new Paginator(2,$('.js-pagination-controls'));


$(document).ready(function () {

    let profileOptions = {
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
        displayProfiles(Pagination.previous());
        return false;
        //enable previous
        //possibility of disabling next
    });

    $('.js-next-page').click(function(evt){
        evt.preventDefault();
        //enable previous
        //possibility of disabling prev
        displayProfiles(Pagination.next());
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
            display(data);
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

function display(data){
    data = Pagination.paginate(data);
    displayProfiles(data);
}

function displayProfiles(data) {
    formatHTML(data,'profile');
}