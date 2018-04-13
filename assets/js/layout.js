'use strict';
//This file will be used in every other js files

import $ from 'jquery'; // load jQuery module
// import Routing from '';
// import menu from 'Components/menu.js';
import '../scss/style.scss';

$(document).ready(function(){
    $('.menu-icon').click(function(evt){
        evt.preventDefault();
        //if screens are smaller than x px
        if($(window).width() < 1024 ){
            //if already open
            if($(this).hasClass('opened')){
                //close
                $(this).removeClass('opened');
                $('.nav-links').animate({
                    duration: 1000,
                    height: 'toggle',
                    easing: 'swing'
                });
            }else{
                //open
                $(this).addClass('opened');
                $('.nav-links').animate({
                    duration: 1000,
                    height: 'toggle',
                    easing: 'swing'
                });
            }
        }
    });
    $(window).resize(function(){
        if($(this).width() >= 1024)
        {
            if($('.nav-links').css('display') == 'none' || $('.nav-links').css('display') == 'block'){
                $('.nav-links').removeAttr('style');
                $('.menu-icon').removeClass('opened');
            }
        }
    });
});