'use strict';

import ArrayHelper from "../ArrayHelper";

const routes = require( '../Routing/fos_js_routes.json');
import Routing from '../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
import Status from "../Status";
import Scroller from "../Scroller";
import Paginator from "../Paginator";
import ASCIIFolder from "fold-to-ascii";

Routing.setRoutingData(routes);

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
        let nameMatched = data.matched.byName;
        let tagMatched = data.matched.byTag;

        let filteredNameMatched = Thesaurus.matchNames(value,nameMatched);

        tagMatched = Thesaurus.matchTags(value,tagMatched,nameMatched,filteredNameMatched);

        let matched = filteredNameMatched.concat(tagMatched);
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
        let title = gesture.name.charAt(0).toUpperCase() + gesture.name.slice(1);

        let profileVideo = '';
        let video = '';

        if(gesture.video !== '' && gesture.video != null && gesture.video !== undefined){
            video = this.createVideo("De face", gesture.video);
        }
        if(gesture.profileVideo !== '' && gesture.profileVideo !== null && gesture.profileVideo !== undefined){
            profileVideo = this.createVideo("De profil", gesture.profileVideo);
        }

        let description = gesture.description ? gesture.description : 'Aucune description disponible';

        return "<h1 class=\"gesture-details-header-title\">''"+ title +"''</h1>" +
            "<article class=\"gesture-details-content\">\n" +
            "    <div class=\"content\">\n" +
            "        <p class=\"description\">\n" +
            "            " + description + "\n" +
            "        </p>\n" +
            "    </div>\n" +
            "   <h2 class=\"gesture-details-video-title\">Vidéos</h2>" +
            "   <div id=\"gesture-videos\">" +
            video +
            profileVideo +
            "</div>" +
            this.backToSearchButton() +
            "</article>";

    }

    static createVideo(title,src){
        return "<div class=\"gesture-video\">" +
            "           <h3 class=\"gesture-video-title\">" + title + "</h3>" +
            "            <video class=\"js-gesture-video\" controls controlsList=\"nodownload\" preload='metadata'>" +
            "                   <source id=\"source1\" class=\"js-video-source\" src=\""+src+"\" type=\"video/mp4\" />\n" +
            "                   Votre navigateur n'est pas à jour. Veuillez le mettre à jour s'il vous plait." +
            "               </video>" +
            "       </div>";
    }

    static getVideoButton(hasVideos,description){
        let c = 'js-gesture-show';
        let text= 'Voir les vidéos';
        if(!hasVideos && (description === '' || description == null)){
            c = 'disabled';
            text = 'Aucune vidéo.';
        }else if(!hasVideos && (description != null || description !== '')){
            text = 'Voir la description';
        }
        return "<button class=\"btn btn-dark "+ c +"\">" +
            "<span>"+ text +"</span>" +
            "</button>";
    }

    /**
     * Format the HTML for gesture displayed in a list.
     * @param gesture
     * @return {string}
     */
    static listHTML(gesture) {
        let cover = gesture.cover;
        let title = gesture.name.charAt(0).toUpperCase() + gesture.name.slice(1);

        let videoButton = this.getVideoButton(gesture.hasVideos,gesture.description);

        return "<article class=\"gesture js-gesture\" data-id=\""+ gesture.id +"\">" +
                "<div class=\"gesture-content\">" +
                    "<div class='cover-container'>" +
                        "<img src=\""+ cover +"\" " + "alt=\"gesture-cover\" class=\"cover\">" +
                    "</div>" +

                    "<div class=\"content\">" +
                        "<h2 class=\"title\">"+ title +"</h2>" +
                    "</div>" +
                    videoButton +
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
            Thesaurus.resetSearch();
            this.getContainer().html('');
        }
    }

    static resetSearch(){
        if(this.paginator !== undefined){
            this.paginator.reset();
            this.paginator.hidePaginationButtons();
        }else{
            Paginator.hidePaginationButtons();
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

    static convertToASCII(value){
        return ASCIIFolder.fold(value);
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
                        let ASCIITag = ASCIIFolder.fold(tags[i]);
                        //if last tag
                        if(i === tags.length-1 && i >= 0){
                            //look if it begin or match the tag!
                            for(let j = 0; j < keywords.length; j++){
                                let ASCIIKeyword = ASCIIFolder.fold(keywords[j]);

                                if(!keywords[j].startsWith(tags[i]) && !ASCIIKeyword.startsWith(ASCIITag)){
                                    keep = false;
                                }else{
                                    keep = true;
                                    break;
                                }
                            }
                            break;
                        }else{
                            if(keywords.indexOf(tags[i]) === -1){
                                let ASCIIKeywords = Thesaurus.convertTagsToASCII(keywords);
                                if(ASCIIKeywords.indexOf(ASCIITag) === -1){
                                    keep = false;
                                    break;
                                }
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

    static convertTagsToASCII(tags){
        if(AHelper.isArray(tags)){
            let ASCIITags = [];
            tags.forEach((tag) => {
                    ASCIITags.push(this.convertToASCII(tag));
                }
            );
            return ASCIITags;
        }
    }

    static getGesturesByName(name,data){
        //startsWith is case sensitive!
        if(name){
            name = name.toLowerCase();
            let ASCIIName = ASCIIFolder.fold(name,null);

            return data.filter(function(gesture){
                if(gesture['name'].toLowerCase().startsWith(name)){
                    return gesture;
                }
                else{
                    //ASCII CONVERSION
                    let ASCIIGName = ASCIIFolder.fold(gesture['name'].toLowerCase(),null);

                    if(ASCIIGName.startsWith(ASCIIName)){
                        return gesture;
                    }
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
            let pattern = value;

            //contains one word or more
            if(pattern.length >= 1)
            {
                this.clear();
                this.getContainer().html('');
                StatusHandler.set('waiting');

                $.ajax({
                    url: Routing.generate(this.source, {tag: pattern}),
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
            // StatusHandler.clear();
            StatusHandler.set('invalid',"Votre recherche est invalide.");
            this.clear();
        }
    }

    /**
     * Set the gestures.
     * @param data
     */
    setGestures(data){
        if(data){
            this.paginator.reset();
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