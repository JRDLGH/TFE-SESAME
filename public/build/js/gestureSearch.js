$(document).ready(function(){
    $('#search').keyup(function(evt){
        //evt == event
        //this.value is the value
        var value = this.value;

        //MAKE A STRONG REGEX HERE!
        if(/\w/.test(value) && !/[0-9]/.test(value))
        {
            console.log(value + " is a string!");
            $.get( "http://127.0.0.1:8000/thesaurus/test", function( data ) {
                $( ".result" ).html( data );
                console.log(data);
              });
        }else{
            console.log("invalid format");
        }
    });
});