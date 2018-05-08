const routes = require( './Components/Routing/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

import 'jquery.typewatch';
import '../scss/structure/card.scss';

Routing.setRoutingData(routes);

$(document).ready(function () {

    var profileOptions = {
        callback: getProfiles,
        wait: 1000,
        highlight: true,
        allowSubmit: true,
        captureLength: 2
    };

    $('.search-input').typeWatch(profileOptions);

});

/* STATUS*/

function clearStatusContainer($statusContainer){
    $statusContainer.html('');
}

function getStatusContainer($container){
    return $container.parent().find('.status');
}

function setWaiting($container){
    getStatusContainer($container).html('<i class="fa fa-spinner fa-spin"></i>');
}


/** PROFILES **/

function profileHTML(profile){
    var age = getAge(profile.birthday);
    var html = '';
    html +=
        '<div class="card-container js-select-profile" data-id="' + profile.profile.id + '">' +
        '<div class="card">' +
        '<i class="fa fa-user card-icon"></i>' +
        '<p class="card-title">' + profile.lastname + ' ' + profile.firstname + ' - ' + age + 'ans' + '</p>' +
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
    return $('search-result .search-content');
}

function addToSelectedProfiles(profile){
    if(profile){
        if(selectedProfiles.length == 0){
            getSelectedProfilesContainer().html('');
        }
        selectedProfiles.push(profile);
    }
}

function removeFromSelectedProfiles(profile){
    if(profile){
        var index = selectedProfiles.indexOf(profile);
        if(index != -1){
            selectedProfiles.splice(index,1);
            if(selectedProfiles.length == 0){
                getSelectedProfilesContainer().html('Aucun geste sélectionné.');
            }
        }
    }
}

function getSelectedProfilesContainer(){
    return $('.profiling-chosen-profile .chosen-content');
}