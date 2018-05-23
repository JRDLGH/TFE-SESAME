'use strict';
import "../scss/thesaurus.scss";
const routes = require( './Components/Routing/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
import Status from './Components/Status';
import Paginator from "./Components/Paginator";
import Scroller from './Components/Scroller';
import ArrayHelper from './Components/ArrayHelper';
import Thesaurus from "./Components/Thesaurus/Thesaurus";

//php bin/console fos:js-routing:dump --format=json --target=assets/js/Components/Routing/fos_js_routes.json

Routing.setRoutingData(routes);

const StatusHandler = new Status();
const ScrollTool = new Scroller();
const Thes = new Thesaurus('thesaurus_search_tag',$('#gesture'),new Paginator(6,$('.js-pagination-controls')));

let previousValue = [];

$(document).ready(function(){

    const $searchInput = $('#search');

    $searchInput.on('click',function(){
        ScrollTool.scrollTo($(this),0,null,true);
    });
    /**
     * KEY PRESS WILL NEED TO DETECT ENTER
     * ALSO, HIT ENTER OR THE SEARCH BUTTON WILL LEAD TO SAME OPERATIONS
     */
    $searchInput.keyup(function(){
        let value = this.value.toLowerCase();
        let valuePosition = value.indexOf(previousValue['value']);

        Thes.currentlySearched = value;
        // if(isValid(value)){
            //If new value, request database call
            if((valuePosition === -1 || previousValue['value'] === '' || valuePosition !== previousValue['position']) && Thes.currentlySearched.length >= 2){
                previousValue['value'] = value;
                previousValue['position'] = value.indexOf(previousValue['value']);
                Thes.getGestures(value);
            }else if(Thes.currentlySearched === '' || Thes.currentlySearched === ' '){
                StatusHandler.clear();
                Thes.clear();
                Thes.getContainer().html('');
            }
            else{
                //update current value
                //Search for correspondance depending on search type
                //By default: trie sur l'ordre de pertinence, les mots commençant par la sélection
                if(Thes.gestures){
                    //reset pagination in order to avoid to be stuck inside a page
                    if(Thes.containsNewGestures(Thes.gestures)){
                        Thes.paginator.reset();
                    }
                    Thes.orderByPertinence(Thes.gestures,value);
                }
            }
        // }
        return false;
    });

    $(document).on('click','.js-gesture-show',function(){
        //Show a cursor pointer on gesture!
        Thes.showGesture($(this).closest('.gesture').data('id'));
        return false;
    });

    $(document).on('click','.js-previous-search',function(){
        //back to previous search
        Thes.containerDisplay('list');
        Thesaurus.getDetailsContainer().removeClass('opened');
        Thes.getContainer().removeClass('closed');
        Thes.getContainer().removeClass('closed');
        if(Thes.paginator.nbPages > 0){
            Thes.paginator.showPaginationButtons();
        }
        return false;
    });

    /**
     * PAGINATOR EVENT LISTENER
     */

    $('.js-previous-page').click(function(){
        Thes.display(Thes.paginator.previous(),false);
        ScrollTool.scrollTo(Thes.getContainer(),0,'',true);
        Thes.paginator.showPaginationButtons();
        return false;
    });

    $('.js-next-page').click(function(){
        let nextPageContent = Thes.paginator.next();
        Thes.display(nextPageContent,false,'');
        ScrollTool.scrollTo(Thes.getContainer(),0,'',true);
        Thes.paginator.showPaginationButtons();
        return false;
    });

});