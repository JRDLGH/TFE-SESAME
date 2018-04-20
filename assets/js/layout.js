'use strict';
//This file will be used in every other js files

import $ from 'jquery'; // load jQuery module
//import Routing from '';
//Import sweetalert module
//import swal from 'sweetalert';
import '../scss/style.scss';
import theme from './Components/theme';
import 'font-awesome/css/font-awesome.min.css';

$(document).ready(function(){
    //MENU
    $('.menu-icon').click(function(evt){
        evt.preventDefault();
        console.log(this);
        //if screens are smaller than x px
        console.log($(window).width());
        console.log(theme.breakpoints.lg);
        console.log('Pass ? ' + $(window).width() < theme.breakpoints.lg);
        if($(window).width() < theme.breakpoints.lg ){
            console.log('pass ');
            //if already open
            if($(this).hasClass('opened')){
                console.log('opened');
                //close
                $(this).removeClass('opened');
                $('.nav-links').animate({
                    duration: 1000,
                    height: 'toggle',
                    easing: 'swing'
                });
            }else{
                console.log('open');                
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
        if($(this).width() >= theme.breakpoints.lg)
        {
            if($('.nav-links').css('display') == 'none' || $('.nav-links').css('display') == 'block'){
                $('.nav-links').removeAttr('style');
                $('.menu-icon').removeClass('opened');
            }
        }
    });
    //END MENU
});