$(document).ready(function(){
    $('#search').keyup(function(evt){
        //evt == event
        //this.value is the value
        var value = this.value;
        if(/\w/.test(value) && !/[0-9`~,.<>;':"/[\]|{}()=_+-]/.test(value))
        {
            console.log(value + " is a string!");
        }else{
            console.log("Format invalide");
        }
    });
});