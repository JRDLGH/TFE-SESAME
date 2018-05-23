'use strict';

import ArrayHelper from "../ArrayHelper";

const routes = require( '../Routing/fos_js_routes.json');
import Routing from '../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
import Paginator from "../Paginator";
import Status from "../Status";
import Scroller from "../Scroller";

Routing.setRoutingData(routes);

// const Pagination = new Paginator(6,$('.js-pagination-controls'));
const StatusHandler = new Status();
const AHelper = new ArrayHelper();
const ScrollTool = new Scroller();


class Thesaurus{
    constructor(source,$container,Paginator = null){
        this.source = source;
        this.currentlySearched = '';
        this.$container = $container;
        this.gestures = undefined;
        this.lastMatchedGesture = [];
        this.paginator = Paginator;
    }

    /**
     * Get the gesture with id passed.
     * @param id
     */
    showGesture(id){
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
        }).done((data) => {
            //MATCH HTTP_OK -- 200
            this.display(JSON.parse(data),null,'details');
        });
    }

    /**
     * Filter the data depending on their match nature (by name or by tag).
     * @param data
     * @param value
     */
    orderByPertinence(data,value){
        let matched = [];
        let filteredNameMatched = [];
        let nameMatched = data.matched.byName;
        let tagMatched = data.matched.byTag;

        filteredNameMatched = Thesaurus.matchNames(value,nameMatched);

        tagMatched = Thesaurus.matchTags(value,tagMatched,nameMatched,filteredNameMatched);

        matched = filteredNameMatched.concat(tagMatched);
        this.display(matched);
    }

    /**
     * This function returns gesture's name that matches the pattern entered.
     * @param pattern
     * @param nameMatched
     * @return {Array}
     */
    static matchNames(pattern,nameMatched){
        let matched = [];
        if(AHelper.isArray(nameMatched) && pattern.length > 0){

            matched = Thesaurus.getGesturesByName(pattern,nameMatched);

            if(matched.length > 0){
                matched.sort(Thesaurus.sortByName);
            }
        }

        return matched;
    }

    /**
     * Format HTML for a gesture details.
     * @param gesture
     * @return {string}, the html details
     */
    static formatHTML(gesture){
        let cover = gesture.cover ? gesture.cover : "default.jpg"; //TODO in backend!!
        let video = gesture.name;
        let video_path = "/build/static/thesaurus/gestures/videos/" + video + ".mp4";
        let title = gesture.name.charAt(0).toUpperCase() + gesture.name.slice(1);

        return "<h2 class=\"gesture-details-header-title\">Détails</h2>" +
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
            Thesaurus.backToSearchButton() +
            "</article>";
    }

    /**
     * Format the HTML for gesture displayed in a list.
     * @param gesture
     * @return {string}
     */
    static listHTML(gesture) {
        let cover = gesture.cover ? gesture.cover : "default.jpg"; //TODO in backend!!
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

    containsNewGestures(data)
    {
        let gIds = Thesaurus.getGestureId(data);
        let display = false;

        if(!AHelper.isArray(this.lastMatched) || !AHelper.compareArray(gIds,this.lastMatched)
            || this.isContainerEmpty()){
            this.lastMatched = gIds; //must only contains id of last matched gestures
            display = true;
        }
        return display;
    }

    /**
     * Display gestures depending on the data given and the type of display.
     * @param data
     * @param pagination
     * @param type
     */
    display(data,pagination=true,type){
        let display = this.containsNewGestures(data);

        let content = '';
        if(AHelper.isArray(data)){
            switch (type){
                case 'details': content = Thesaurus.formatHTML(data[0]);
                    this.containerDisplay('details');
                    Thesaurus.getDetailsContainer().html(content);
                    this.paginator.hidePaginationButtons();

                    break;
                default: //list
                    if(display){
                        data = this.paginator.paginate(data);
                        data.forEach(function (gesture){
                            content += Thesaurus.listHTML(gesture);
                        });
                        this.containerDisplay('list');
                        this.getContainer().html(content);
                    }
            }
            StatusHandler.clear();
        }else{
            //Not found
            StatusHandler.set('not_found');
            this.getContainer().html('');
        }
    }

    isContainerEmpty($container = this.getContainer()) {
        if($container.html() === null || $container.html() === '' ||
            $container.html() === undefined){
            return true;
        }
        return false;

    }

    /**
     * Return the id of all the gestures inside the array.
     * @param gArray
     * @return {*}
     */
    static getGestureId(gArray){
        if(AHelper.isArray(gArray)){
            return gArray.map(function(gesture){
                return gesture.id;
            });
        }
    }

    /**
     * Display/hide a container depending on the container to display.
     * @param container
     */
    containerDisplay(container){
        switch (container){
            case 'details':
                this.getContainer().addClass('closed').on('transitionend',this.showDetails());
                break;
            case 'list':
                Thesaurus.getDetailsContainer().removeClass('opened').removeClass('display-block');
                this.getContainer().removeClass('closed').removeClass('display-none');
                break;
        }
    }

    showDetails(){
        this.getContainer().addClass('display-none');
        Thesaurus.getDetailsContainer().addClass('display-block').addClass('opened');
        ScrollTool.scrollTo(Thesaurus.getDetailsContainer(),0,null,true);
    }

    /**
     * Allow you to go back on search.
     * @return {string}
     */
    static backToSearchButton() {
        return '<button class="btn btn-primary js-previous-search">Retourner à la recherche</button>';
    }

    /**
     * HTML element that holds list of gestures.
     * @return {*|jQuery|HTMLElement}
     */
    getContainer(){
        return this.$container;
    }

    /**
     * HTML element that holds one gesture and his details.
     * @return {*|jQuery|HTMLElement}
     */
    static getDetailsContainer(){
        return $('.gesture-details');
    }

    /**
     * This function returns gesture matched by tags with several differents options.
     * By default, this function look into both arrays and compare them.
     * @param pattern
     * @param tagMatched
     * @param nameMatched
     * @param sortedNameMatched
     * @param option
     * @return array containing all gestures matching the pattern.
     */
    static matchTags(pattern,tagMatched,nameMatched,sortedNameMatched,option){
        if(!option){
            option = 'both';
        }

        let tags = Thesaurus.splitIntoTags(pattern);
        tags = tags.filter(Thesaurus.getUniqueTags);
        let result = [];
        //DELETE USELESS PATTERNS like 'de', 'le', 'la'
        //TODO
        tags = AHelper.removeBlanks(tags);

        switch (option){
            //Look into namedMatched & tagMatched
            case 'both':
                result = Thesaurus.getGesturesByTags(tags,nameMatched);
                if(AHelper.isArray(result)){
                    //Delete duplicates and merge
                    result = AHelper.arrayDiff(result,sortedNameMatched);
                    result = result.concat(Thesaurus.getGesturesByTags(tags,tagMatched));
                }else{
                    result = Thesaurus.getGesturesByTags(tags,tagMatched);
                }
                break;
            //Look into tagMatched
            case 'tag': result = Thesaurus.getGesturesByTags(tags,tagMatched);
                break;
            //Look into namedMatched
            case 'name': result = Thesaurus.getGesturesByTags(tags,nameMatched);
                break;
        }
        return result;
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
    static getUniqueTags(value, index, self) {
        if(self.indexOf(value) === index){
            return value.toLowerCase();
        }
    }

    /**
     * Get all gestures that are tagged by each tag inside tags array.
     * @param data, the gestures array
     * @param tags, the tags array
     */
    static getGesturesByTags(tags,data){
        let matched = [];
        if(AHelper.isArray(tags) && AHelper.isArray(data)){
            matched = data.filter(function(gesture){
                //First, eliminate gesture that does not have enough tags
                if(tags.length <= gesture.tags.length){
                    let keywords = AHelper.mapValues(gesture.tags);
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

    static getGesturesByName(name,data){
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

    static sortByName(a,b){
        return a.name.localeCompare(b.name);
    }

    static splitIntoTags(tags){
        let nospace_regex = /\s\s+/g;
        tags = tags.replace(nospace_regex,' ');
        tags = tags.split(/\s/);
        return tags;
    }

    /**
     * Verify if the value given is correct and if it is, query the database with and get
     * gestures.
     * @param value
     */
    getGestures(value){
        //REGEX -- ALLOW ONLY LETTERS
        if(Thesaurus.isValid(value))
        {
            let keywords = Thesaurus.splitIntoTags(value);

            //contains one word
            if(keywords.length === 1)
            {
                this.clear();
                this.getContainer().html('');
                StatusHandler.set('waiting');

                $.ajax({
                    url: Routing.generate(this.source, {tag: keywords[0]}),
                    type: 'GET',
                    statusCode: {
                        404: function(data){
                            //RESOURCE NOT FOUND
                            if(AHelper.isArray(data) || typeof(data) === 'object'){
                                let state = Object.keys(data.responseJSON)[0];
                                let msg = data.responseJSON[state];
                                StatusHandler.set(state,msg);
                            }else{
                                StatusHandler.set('not_found');
                            }
                        },
                        500: function(){
                            //ERROR BACKEND
                            StatusHandler.set('error');
                        }
                    }
                }).done((data) => {
                    //MATCH HTTP_OK -- 200
                    this.setGestures(data);
                    this.orderByPertinence(data,this.currentlySearched);
                });

            }
            //send a request to get gestures matching the word - value
        }else{
            //Nothing entered
            this.getContainer().html('');
            StatusHandler.clear();
            clear();
        }
    }

    /**
     * Set the gestures.
     * @param data
     */
    setGestures(data){
        if(data){
            this.gestures = data;
        }
    }

    /**
     * Verify if the search entry is valid and contains letters.
     * @param value
     * @return {boolean}
     */
    static isValid(value){
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

    clear(){
        this.gestures = null;
        this.paginator.hidePaginationButtons();
    }

}

export default Thesaurus;