$(document).ready(function(){
    $('#search').keyup(function(evt){
        //evt == event
        //this.value is the value
        var value = this.value;

        //MAKE A STRONG REGEX HERE!
        if(/\w/.test(value) && !/[0-9]/.test(value))
        {
            console.log(value + " is a string!");
        }else{
            console.log("invalid format");
        }
    });
});