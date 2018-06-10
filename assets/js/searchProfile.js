import Paginator from "./Components/Paginator";

const routes = require( './Components/Routing/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min';

import 'jquery.typewatch';
import '../scss/searchProfile.scss';
import Status from "./Components/Status";

Routing.setRoutingData(routes);

const Pagination = new Paginator(6,$('.js-pagination-controls'));
const StatusHandler = new Status($('.search-result .search-content').parent().find('.status'));


$(document).ready(function () {

    let profileOptions = {
        callback: getProfiles,
        wait: 1000,
        highlight: true,
        allowSubmit: true,
        captureLength: 2
    };

    $('.search-input').typeWatch(profileOptions);

    /**
     * PAGINATOR EVENT LISTENER
     */

    $('.js-previous-page').click(function(){
        displayProfiles(Pagination.previous());
        return false;
    });

    $('.js-next-page').click(function(){
        displayProfiles(Pagination.next());
        return false;
    });

    $(document).on('click','.js-select-profile',function () {
        let route = Routing.generate('profile_consult') + '/'+this.dataset.id;
        window.location = route;
    });

});
/** PROFILES **/

function profileHTML(profile){
    let age = getAge(profile.birthday);
    let html = '';
    html +=
        '<div class="card-container profile-card js-select-profile" data-id="' + profile.profile.id + '">' +
        '<div class="card">' +
        '<i class="fa fa-user card-icon"></i>' +
        '<p class="card-title">' + profile.lastname + ' ' + profile.firstname + ' - ' + age + 'ans' + '</p>' +
        '<div class="request-access">' +
        '   <a href="#" class="btn btn-primary">' +
            '<i class="fa fa-unlock"></i>' +
        '</a>' +
        '</div>' +
        '</div>' +
        '</div>';
    return html;
}

function getAge(birthday){
    let currentDate = new Date();
    let birthDate = new Date(birthday);
    let age = currentDate.getFullYear() - birthDate.getFullYear();
    let m = currentDate.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && currentDate.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
}

function getProfiles(pattern){
    if(pattern){
        StatusHandler.set('waiting');
        $.ajax({
            url: Routing.generate('profiling_search_profile',{pattern : pattern}),
            type: 'GET',
            statusCode: {
                404: function(){
                    //RESOURCE NOT FOUND
                    StatusHandler.set('not_found');
                },
                500: function(){
                    //ERROR BACKEND
                    StatusHandler.set('internal');

                }
            }
        }).done(function(data){
            //MATCH HTTP_OK -- 200
            StatusHandler.clear();
            display(data);
        });
    }
}

function getProfileContainer(){
    return $('.search-result .search-content');
}

function formatHTML(data,type){
    let output = '';
    let $container;
    if(data){
        switch (type){
            case 'profile':
                $container = getProfileContainer();
                data.forEach(function (profile) {
                    output += profileHTML(profile);
                });
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