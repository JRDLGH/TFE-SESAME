import ArrayHelper from "./ArrayHelper";

let HelperInstances = new WeakMap();

class Paginator{

    constructor(limit = 10,$container){
        this.limit = limit;
        this.currentPg = 0;
        this.nbPages = 0;
        this.pageMap = [];
        this.enabled = true;
        this.$container = $container;

        HelperInstances.set(this,new ArrayHelper());

    }

    paginate(data){
        let result = data;

        if(this.enabled && data.length > this.limit){
            this.breakIntoPage(data,this.limit);
            if(HelperInstances.get(this).isArray(this.pageMap)){
                result = this.pageMap[0];
                this.current = 0;
                this.showPaginationButtons();
            }
        }else if(this.enabled && this.currentPg > 0){

        }else{
            this.hidePaginationButtons();
        }
        return result;
    }

    hidePaginationButtons(){
        if(!this.getPaginationContainer().attr('style','display:none;')){
            this.getPaginationContainer().hide();
        }
    }

    showPaginationButtons(){
        if(this.getPaginationContainer().attr('style','display:none;')){
            this.getPaginationContainer().show();
        }
    }

    getPaginationContainer(){
        return this.$container;
    }

    static hidePaginationButtons(){
        let $container = $('.js-pagination-controls');
        if(!$container.attr('style','display:none;')){
            this.$container.hide();
        }
    }

    static showPaginationButtons(){
        let $container = $('.js-pagination-controls');
        if($container.attr('style','display:none;')){
            this.$container.show();
        }
    }

    next(){
        if(!Paginator.isDisabled(this.getNextButton())){
            if(this.currentPg < this.nbPages-1){
                //you can go to next page
                this.currentPg += 1;
                let data = this.pageMap[this.currentPg];
                this.showPaginationButtons();
                // scrollToContainer();
                if(this.currentPg === this.nbPages -1){
                    this.getNextButton().addClass("disabled");
                    this.enableButton('previous');
                }else{
                    this.enableButton('next');
                    this.enableButton('previous');
                }
                return data;
            }
        }
    }

    previous()
    {
        if(!Paginator.isDisabled(this.getPreviousButton())) {
            if (this.currentPg >= 1 && this.nbPages >= this.currentPg) {
                //you can go to previous page
                this.currentPg -= 1;
                let data = this.pageMap[this.currentPg];
                this.showPaginationButtons();
                // this.scrollToContainer();
                if (this.currentPg === 0) {
                    this.getPreviousButton().addClass("disabled");
                    this.enableButton('next');
                } else {
                    this.enableButton('next');
                    this.enableButton('previous');
                }
                return data;
            }
        }
    }

    enableButton(button){
        switch(button){
            case 'next':
                if(Paginator.isDisabled(this.getNextButton())){
                    this.getNextButton().removeClass('disabled');
                }
                break;
            case 'previous':
                if(Paginator.isDisabled(this.getPreviousButton())){
                    this.getPreviousButton().removeClass('disabled');
                }
                break;
            case 'init':
                this.getPreviousButton().addClass('disabled');
                this.getNextButton().removeClass('disabled');
                break;
        }
    }

    getNextButton()
    {
        return this.$container.children('.controls').children('.js-next-page');
    }

    getPreviousButton()
    {
        return this.$container.children('.controls').children('.js-previous-page');
    }

    static isDisabled($elem)
    {
        return $elem.hasClass('disabled');
    }

    /**
     * Break an array of data in mutliple array of data, each key represent a page.
     * @param data
     * @param limit
     */
    breakIntoPage(data,limit)
    {
        //Must be done only once per pagination
        if(HelperInstances.get(this).isArray(data) && limit >= 2){
            this.nbPages = Math.ceil(data.length/limit); //round up!
            this.pageMap = HelperInstances.get(this).splitArray(data,limit);
        }
    }

    reset()
    {
        this.nbPages = 0;
        this.pageMap = [];
        this.currentPg = 0;
        this.enableButton('init');
    }

}

export default Paginator;