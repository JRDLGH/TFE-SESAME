const routes = require( './Components/Routing/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

import 'jquery.typewatch';

import '../scss/profileToGesture.scss';


Routing.setRoutingData(routes);

var gestures = [];
var profiles = [];

var selectedGestures = [];
var selectedProfiles = [];

$(document).ready(function () {
    //fonction pour form quand submit!!

    var gestureOptions = {
        callback: getGestures,
        wait: 1000,
        highlight: true,
        allowSubmit: true,
        captureLength: 2
    };

    $('.js-gesture-choice .search-input').typeWatch(gestureOptions);

    var profileOptions = {
        callback: getProfiles,
        wait: 1000,
        highlight: true,
        allowSubmit: true,
        captureLength: 2
    };

    $('.js-profile-choice .search-input').typeWatch(profileOptions);

    $('#gesture-profile-form').on('submit',function(t){
        $.post(Routing.generate('management_profile_gesture'),{profiles:selectedProfiles,gestures:selectedGestures}).
        done(function(data){
            console.log(data);
        }).fail(function(error){
            console.log(error)
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



function getGestures(name){
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
    }else{
        console.log("no data available");

    }
}

// function profileHTML(){}
function gestureHTML(gesture){
    var html = '';
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
    var output = '';
    var $container;
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
    console.log(selectedProfiles,selectedGestures);
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
        var index = selectedGestures.indexOf(gesture);
        if(index != -1){
            selectedGestures.splice(index,1);
            if(selectedGestures.length == 0){
                getSelectedGesturesContainer().html('Aucun geste sélectionné.');
            }
        }else{
            console.log('not found!');
        }
    }
}

function isInArray(array,id){
    var result = false;
    if(array.length > 0){
        result = (array.indexOf(id) != -1) ? true:false;
    }
    return result;
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
        var index = selectedProfiles.indexOf(profile);
        if(index != -1){
            selectedProfiles.splice(index,1);
            if(selectedProfiles.length == 0){
                getSelectedProfilesContainer().html('Aucun geste sélectionné.');
            }
        }else{
            console.log('not found!');
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