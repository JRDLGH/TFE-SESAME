import swal from 'sweetalert';
import './Components/filter';

import "typeahead.js";
import Bloodhound from 'bloodhound-js';
import 'bootstrap-tagsinput';
import Scroller from './Components/Scroller';

import 'bootstrap-tagsinput/dist/bootstrap-tagsinput';

import 'bootstrap-tagsinput/dist/bootstrap-tagsinput.css';
import 'bootstrap-tagsinput/dist/bootstrap-tagsinput-typeahead.css';

import 'bootstrap-fileinput';
import 'bootstrap-fileinput/css/fileinput.min.css';
import 'bootstrap-fileinput/themes/fa/theme.min';
import 'bootstrap-fileinput/js/locales/fr';

import '../scss/structure/admin/tags.scss';
import '../scss/structure/admin.scss';

$(document).ready(function(){
    let $input = $('input[data-toggle="tagsinput"]');

    $('#search').submit(function () {
        return false;
    });

    focusError();

    let tags = [];
    $.get('/admin/thesaurus/gesture/tags',function(data){
        tags=data;
    });

    let source = new Bloodhound({
        prefetch: {
            url: '/admin/thesaurus/gesture/tags',
            filter: function (response) {
                let tags = [];
                response.forEach(function(tag){
                    tags.push(tag.keyword);
                });
                return tags;
            }
        },
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
    });

    let fileOptions = {
        theme: 'fa',
        language: 'fr',
        showUpload: false,
        maxFileCount: 1,
        autoReplace: true,
        allowedFileTypes: ['image'],
        allowedFileExtensions: ['jpg'],
        allowedPreviewTypes: ['image'],
        msgPlaceholder: 'Sélectionner un fichier',
        dropZoneTitle: "Déposez votre fichier ici",
        maxFileSize: 1000,
    };

    fileOptions.allowedFileExtensions = ['mp4'];
    fileOptions.allowedFileTypes = ['video'];
    fileOptions.allowedPreviewTypes = ['video'];
    fileOptions.msgPlaceholder = "Sélectionner une vidéo de profil";
    fileOptions.dropZoneTitle = "Déposez la vidéo de profil ici...";
    $('#gesture_profileVideoFile_file').fileinput(
        fileOptions
    );

    fileOptions.allowedFileExtensions = ['mp4'];
    fileOptions.allowedFileTypes = ['video'];
    fileOptions.allowedPreviewTypes = ['video'];
    fileOptions.msgPlaceholder = "Sélectionner une vidéo de face";
    fileOptions.dropZoneTitle = "Déposez la vidéo de face ici...";
    $('#gesture_videoFile_file').fileinput(
        fileOptions
    );

    fileOptions.allowedFileExtensions = ['jpg','jpeg'];
    fileOptions.allowedFileTypes = ['image'];
    fileOptions.allowedPreviewTypes = ['image'];
    fileOptions.msgPlaceholder = "Sélectionner une image";
    fileOptions.dropZoneTitle = "Déposez l'image ici...";
    $('#gesture_coverFile_file').fileinput(
        fileOptions
    );

    source.initialize();

    if ($input.length) {
        $input.tagsinput({
            trimValue: true,
            focusClass: 'focus',
            typeaheadjs: [
                {
                    highlight: true,
                    minLength: 2
                },
                {
                    source: source.ttAdapter()
                },
            ]
        });
    }

    $('.menu span').click(function () {
        let $fileinput = $(this).parent().next();
        $fileinput.slideToggle();
    });

    $(document).on('submit','.js-delete-gesture',confirmDelete);
});


function confirmDelete(){
    swal({
        title: "Êtes-vous sûr?",
        text: "Une fois supprimé, ce geste ne sera plus récupérable. ",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
        .then((willDelete) => {
            if (willDelete) {
                swal("Le geste a été supprimé.", {
                    icon: "success",
                    buttons:false,
                });
                this.submit();
            }
        });
    return false;
}

function focusError(){
    let error = $('.invalid-feedback')[0];

    if(error !== undefined){
        let scroll = new Scroller();
        let errorPosition = $(error).offset().top;
        let windowHeight = $(window).height();
        let position = errorPosition + windowHeight;

        scroll.scrollTo(null,position);
    }
}