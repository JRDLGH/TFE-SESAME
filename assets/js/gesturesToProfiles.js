const routes = require( './Components/Routing/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
import 'jquery.typewatch';
import '../scss/profileToGesture.scss';

Routing.setRoutingData(routes);

var gestures = [];
var selectedGestures = [];
var selectedProfiles = [];

$(document).ready(function () {
    //fonction pour form quand submit!!

    var options = {
        callback: getGestures,
        wait: 750,
        highlight: true,
        allowSubmit: false,
        captureLength: 2
    };
    $('.js-gesture-choice .search-input').typeWatch(options);

    $(document).on('click','.js-select-gesture',function(evt){
        select('gesture',this);
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

function clearStatusContainer($statusContainer){
    $statusContainer.html('');
}

function setError(type,$container){
    console.log(getStatusContainer($container));
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

function displayGestures(data){
    gestures = data;
    formatHTML(data,'gesture');
    //when click on a gesture, he is selected
}

function setWaiting($container){
    getStatusContainer($container).html('<i class="fa fa-spinner fa-spin"></i>');
}

function formatHTML(data,type){
    var output = '';
    var $container;
    if(data){
        switch (type){
            case 'profile':
                ;
                break;
            case 'gesture':
                data.forEach(function(gesture){
                    output += gestureHTML(gesture);
                });
                $container = getGestureContainer();
                ;
                break;
        }
    }else{
        console.log("not data available");
    }
    $container.html(output);
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

        case 'profile': console.log("you select a profile");
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
        console.log(result);
    }
    return result;
}