class Scroller{

    constructor($header = $('header')){
        this.$header = $header;
        if(this.$header){
            this.headerHeight = this.getHeight(this.$header);
        }
    }

    scrollTo($container = null,position = 0,space = 50,header = false)
    {
        if($container !== null && position === 0){
            position = this.getPosition($container,space,header);
        }

        Scroller.scroll(position);

    }

    /**
     * Calculate the position of a container and adding
     * additional space to it, if needed.
     * @param $container
     * @param space
     * @return {number}
     */
    getPosition($container,space,header)
    {
        let position = 0;
        if($container !== null && $container !== undefined){
            position = $container.offset().top;

            if(position !== 0 && position !== undefined && space > 0){
                position -= space;
            }
            if(header){
                position -= this.headerHeight;
            }

        }
        return position;
    }

    /**
     * Scroll to the position given.
     * @param position, an integer
     * @param time, in ms
     */
    static scroll(position,time = 1000){
        $('body, html').animate({
            scrollTop: position
        },time);
    }

    /**
     * Get height of a container.
     * @param $container
     * @return {*}
     */
    getHeight($container){
        return $container.outerHeight();
    }
}

export default Scroller;