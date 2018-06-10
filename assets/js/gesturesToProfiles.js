const routes = require( './Components/Routing/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

import 'jquery.typewatch';

import '../scss/profileToGesture.scss';


Routing.setRoutingData(routes);

let gestures = [];
let profiles = [];

let selectedGestures = [];
let selectedProfiles = [];

$(document).ready(function () {
    $('.search-form').submit(function () {
        return false;
    });

    let profileOptions = {
        callback: getProfiles,
        wait: 1000,
        highlight: true,
        allowSubmit: true,
        captureLength: 2
    };

    $('input#search-profile').typeWatch(profileOptions);

    let gestureOptions = {
        callback: getGestures,
        wait: 1000,
        highlight: true,
        allowSubmit: true,
        captureLength: 2
    };

    $('input#search-gesture').typeWatch(gestureOptions);



    $('#gesture-profile-form').on('submit',function(t){
        $.post(Routing.generate('management_profile_gesture'),{profiles:selectedProfiles,gestures:selectedGestures})
            .done(function(data){

            displayFormStatus(Object.keys(data),data.success);
            focusOn(getErrorContainer());
            redirectTo(Routing.generate('management_profile_index'));

            }).fail(function(error){
                displayFormStatus('danger',error['responseJSON']['Error']);
                focusOn(getErrorContainer());
            });
        return false;
    });

    $(document).on('click','.js-select-gesture',function(evt){
        select('gesture',this);
        return false;
    });

    $(document).on('click','.js-select-profile',function(evt){
        select('profile',this);
        return false;
    });
});

function displayFormStatus(type,message){
    let html=
        '<div class="alert alert-'+ type + '">' +
        '<p>'+ message +'</p>' +
        '</div>';
    getErrorContainer().html(html);
}

function redirectTo(page){
    setTimeout(function () {
        window.location.href = page;
    }, 2000);
}

function getErrorContainer(){
    return $('.error-container');
}

function focusOn($container){
    if($container.offset().top != undefined && $container.offset().top >= 0){
        $('html, body').animate({
            scrollTop: $container.offset().top - 150
        },500);
    }

}

function getGestures(name){
    console.log("hello");
    if(name){
        setWaiting(getGestureContainer());
        $.ajax({
            url: Routing.generate('profiling_search_gesture',{name : name}),
            type: 'GET',
            statusCode: {
                404: function(data){
                    //RESOURCE NOT FOUND
                    setError('not_found',getGestureContainer());
                },
                500: function(){
                    //ERROR BACKEND
                    setError('internal',getGestureContainer());

                }
            }
        }).done(function(data){
            //MATCH HTTP_OK -- 200
            clearStatusContainer(getStatusContainer(getGestureContainer()));
            displayGestures(data);
        });
    }
}

function displayProfiles(data) {
    profiles = data;
    formatHTML(data,'profile');
}

function displayGestures(data){
    gestures = data;
    formatHTML(data,'gesture');
    //when click on a gesture, he is selected
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
            case 'gesture':
                data.forEach(function(gesture){
                    output += gestureHTML(gesture);
                });
                $container = getGestureContainer();
                break;
        }
        $container.html(output);
    }
}

// function profileHTML(){}
function gestureHTML(gesture){
    let html = '';
    html +=
        '<div class="card-container js-select-gesture" data-id="' + gesture.id + '">' +
            '<div class="card">' +
                '<i class="fa fa-american-sign-language-interpreting card-icon"></i>' +
                '<p class="card-title">'+ gesture.name +'</p>' +
            '</div>' +
        '</div>';
    return html;
}

function getGestureContainer(){
    return $('#gesture-search-result .search-content');
}

function select(type,data){
    let output = '';
    let $container;
    switch(type){

        case 'profile':
            if(!$(data).hasClass('selected') && !isInArray(selectedProfiles,data.dataset.id)){
                addToSelectedProfiles(data.dataset.id);
                output = data;
                $(data).addClass('selected');
                $container = getSelectedProfilesContainer();
            }else if($(data).hasClass('selected')){
                $(data).removeClass('selected');
                $(data).remove();
                removeFromSelectedProfiles(data.dataset.id);
            }else if(isInArray(selectedProfiles,data.dataset.id)){
                //already selected
            };
        break;

        case 'gesture':
                //remove it from list
            // toggleSelected('gesture',data);
            if(!$(data).hasClass('selected') && !isInArray(selectedGestures,data.dataset.id)){
                addToSelectedGestures(data.dataset.id);
                output = data;
                $(data).addClass('selected');
                $container = getSelectedGesturesContainer();
            }else if($(data).hasClass('selected')){
                $(data).removeClass('selected');
                $(data).remove();
                removeFromSelectedGestures(data.dataset.id);
            }else if(isInArray(selectedGestures,data.dataset.id)){
                //already selected
            }



        break;

    }
    if(output){
        $container.append(output);
    }
}

function getSelectedGesturesContainer(){
    return $('.profiling-chosen-gesture .chosen-content');
}

function addToSelectedGestures(gesture){
    if(gesture){
        if(selectedGestures.length == 0){
            getSelectedGesturesContainer().html('');
        }
        selectedGestures.push(gesture);
    }
}

function removeFromSelectedGestures(gesture){
    if(gesture){
        let index = selectedGestures.indexOf(gesture);
        if(index !== -1){
            selectedGestures.splice(index,1);
            if(selectedGestures.length === 0){
                getSelectedGesturesContainer().html('Aucun geste sélectionné.');
            }
        }
    }
}

function isInArray(array,id){
    let result = false;
    if(array.length > 0){
        result = (array.indexOf(id) !== -1) ? true:false;
    }
    return result;
}

/** PROFILES **/

function profileHTML(profile){
    let age = getAge(profile.birthday);
    let html = '';
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
            //MATCH HTTP_OK -- 200
            clearStatusContainer(getStatusContainer(getProfileContainer()));
            displayProfiles(data);
        });
    }
}

function getProfileContainer(){
    return $('#profile-search-result .search-content');
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
        let index = selectedProfiles.indexOf(profile);
        if(index !== -1){
            selectedProfiles.splice(index,1);
            if(selectedProfiles.length === 0){
                getSelectedProfilesContainer().html('Aucun profil sélectionné.');
            }
        }
    }
}

function getSelectedProfilesContainer(){
    return $('.profiling-chosen-profile .chosen-content');
}

/** STATUS **/
function clearStatusContainer($statusContainer){
    $statusContainer.html('');
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

function getStatusContainer($container){
    return $container.parent().find('.status');
}

function setWaiting($container){
    getStatusContainer($container).html('<i class="fa fa-spinner fa-spin"></i>');
}