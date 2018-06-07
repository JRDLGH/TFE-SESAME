import swal from 'sweetalert';
import './Components/filter';
import theme from './Components/theme';

import "typeahead.js";
import Bloodhound from 'bloodhound-js';
import 'bootstrap-tagsinput';

import 'bootstrap-tagsinput/dist/bootstrap-tagsinput';

import 'bootstrap-tagsinput/dist/bootstrap-tagsinput.css';
import 'bootstrap-tagsinput/dist/bootstrap-tagsinput-typeahead.css';

import '../scss/structure/admin/tags.scss';
import '../scss/structure/admin.scss';

$(document).ready(function(){
    let $input = $('input[data-toggle="tagsinput"]');

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

    source.initialize();

    if ($input.length) {
        console.log('ok');
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

    $(document).on('submit','.js-delete-gesture',confirmDelete);
});


function confirmDelete(){
    swal({
        title: "Êtes-vous sûr?",
        text: "Une fois supprimé, ce geste ne sera plus récupérable. " +
        "Si vous souhaitez simplement qu'il ne s'affiche plus dans le dictionnaire, " +
        "veuillez décocher la case \"publié\" du geste.",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
        .then((willDelete) => {
            if (willDelete) {
                swal("Le geste a été supprimé.", {
                    icon: "success",
                });
                this.submit();
            }
        });
    return false;
}