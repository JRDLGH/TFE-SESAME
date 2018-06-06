import "../scss/doc.scss";
import theme from './Components/theme';
import $ from "jquery";

$(document).ready(function(){
    $('.expandable-link').click(function(){
        let $subList = $(this).parent().next('.sub-list');
        let hasSubList = hasSublist($subList);
        if(hasSubList){
           if(isAlreadyOpen($(this).parent())){
               $subList.slideUp();

           }else{
               $subList.slideDown();
           }
           $(this).parent().toggleClass('is-opened');
        }
    });

    $('.show-doc-nav').click(function () {
        $(this).next().addClass("show");
        $(this).addClass("is-opened");
        $('.display').addClass('active-sidebar');
    });

    $('.display').click(function(){
        let $target = $('.sidebar');
        if($target.hasClass("show")){
            $target.removeClass("show");
            $('.show-doc-nav').removeClass("is-opened");
            $(this).removeClass('active-sidebar');

        }
    });

    $('a[href^="#"]').click(function () {
        if($(this).hasClass('dropdown-toggle')){
            return true;
        }

        let target = $(this)[0].hash;
        if(target){
            navigateTo($(target));
        }
        return false;
    });

    $(window).resize(function(){
        showForComputer();
    });

    showForComputer();

});

function navigateTo($target){
    $('html,body').animate({
        scrollTop: $target.offset().top - 60
    },1000);
}


function showForComputer(){
    if(isComputerScreen()){
        show();
    }else{
        hide();
    }
}

function isComputerScreen(){
    return $(window).width() >= theme.breakpoints.lg;
}

function show(){
    let $target = $('.sidebar');
    if(!$target.hasClass('show')){
        $target.addClass('show');
    }
}

function hide(){
    let $target = $('.sidebar');
    if($target.hasClass('show')){
        $target.removeClass('show');
    }
}

function hasSublist($subList){
    return $subList.length !== 0;
}

function isAlreadyOpen($elem){
    return $elem.hasClass('is-opened');
}