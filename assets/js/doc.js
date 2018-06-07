import "../scss/doc.scss";
import theme from './Components/theme';
import $ from "jquery";
const routes = require( './Components/Routing/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes);

let currentSection = 'begin';

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
        if($(this).hasClass("expandable-link")){
            return false;
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

    $('.chapter-link').click(function () {
        getDocSection(this.dataset.section);
    });

    showForComputer();

});

function navigateTo($target){
    $('html,body').animate({
        scrollTop: $target.offset().top - 60
    },1000);
    if(!isComputerScreen()){
            hide();
            $('.show-doc-nav').removeClass("is-opened");
            $('.display').removeClass('active-sidebar');
        }
}

function getDocSection(section) {
    if(section){
        if(isDifferent(section,currentSection)){
            currentSection = section;
            let route = Routing.generate('admin_documentation_get') + "/"+section;
            $.get(route,function (data) {
                let $target = $('.doc-content');
                $target.html(data);
            });
        }
    }
}

function showForComputer(){
    if(isComputerScreen()){
        show();
    }else{
        hide();
    }
}

function isDifferent(elem,elem2)
{
    return elem !== elem2;
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