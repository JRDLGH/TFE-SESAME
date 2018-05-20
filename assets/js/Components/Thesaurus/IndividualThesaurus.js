import Thesaurus from "./Thesaurus";
import ArrayHelper from "../ArrayHelper";
import Scroller from "../Scroller";
import Status from "../Status";

const routes = require( '../Routing/fos_js_routes.json');
import Routing from '../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes);

const StatusHandler = new Status();
const AHelper = new ArrayHelper();

class IndividualThesaurus extends Thesaurus{
    constructor(source,$container,Paginator = null)
    {
        super(source,$container,Paginator);
        StatusHandler.messages.not_found = "Ce geste n'existe pas.";
        this.message.not_learned = "Ce geste n'a pas encore été appris";
        console.log(StatusHandler.messages);
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
                    url: Routing.generate(this.source, {tag: keywords[0], profile: 8}),
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
                    data = this.convertToGestures(data);
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

    convertToGestures(data)
    {
        if(data)
        {
            let byTag = [];
            let byName = [];
            let keys = Object.keys(data);
            keys.forEach((key) => {
                if(key !== 'status'){
                    for(let matchType in data[key]){
                        data[key][matchType].forEach((pg) => {
                            console.log(pg['gesture']);
                            if(matchType === 'byName')
                            {
                                byName.push(pg['gesture']);
                            }
                            else if(matchType === 'byTag')
                            {
                                byTag.push(pg['gesture']);
                            }
                        });
                    }
                }
            });
            if(AHelper.isArray(byName)) data['matched']['byName'] = byName;
            if(AHelper.isArray(byTag)) data['matched']['byTag'] = byTag;


            console.log(data);
            return data;
        }
        return null;
    }
}

export default IndividualThesaurus;