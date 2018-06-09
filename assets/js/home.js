import '../scss/home.scss';

$(document).ready(function(){
        $(window).scroll(function(){
            let $target = $('header');
            let targetHeight = $target.outerHeight();
            let position = $(document).scrollTop();

            if(position > targetHeight && !$target.hasClass("fixed"))
            {
                $target.addClass("fixed");

            }else if(position < targetHeight)
            {
                $target.removeClass("fixed");
            }
        });
    }
);