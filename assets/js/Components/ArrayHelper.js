class ArrayHelper
{
    /**
     * Checks if the parameter is an array and is not empty.
     * @param array
     * @return {boolean}
     */
    isArray(array)
    {
        var isArray = false;
        if(Array.isArray(array) && array.length > 0){
            isArray = true;
        }
        return isArray;
    }

    /**
     * Remove blanks cells and empty cells in array given.
     * @param tags
     * @return {*}
     */
    removeBlanks(tags)
    {
        var blankPositions = [];
        if(Array.isArray(tags) && tags.length > 0){
            tags.forEach(function(tag,index){
                if(tag == '' || tag == ' ' || tag == undefined || /\s\s+/g.test(tag)){
                    blankPositions.push(index);
                }
            });
            if(blankPositions.length > 0){
                blankPositions.forEach(function (pos) {
                    tags.splice(pos,1);
                });
            }
        }
        return tags;
    }

    mapValues(object)
    {
        let valuesArray = [];
        if(this.isArray(object)){
            let keys = Object.keys(object[0]);
            object.forEach(function(value){
                keys.forEach(function (){
                    valuesArray.push(value[keys[0]].toLowerCase());
                });
            });
        }
        return valuesArray;
    }

    /**
     *   Split an array in mutliple array of x cells.
     *   @param array
     *   @param limit, the number of element per array
     */
    splitArray(array,limit)
    {
        let splitArray = [];
        while(array.length > 0){
            splitArray.push(array.splice(0,limit));
        }
        return splitArray;
    }
}

export default ArrayHelper;