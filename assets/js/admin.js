import swal from 'sweetalert';
import './Components/filter';

import 'typeahead.js/dist/typeahead.jquery.min';
import Bloodhound from 'bloodhound-js';
import 'bootstrap-tagsinput';

import 'bootstrap-tagsinput/dist/bootstrap-tagsinput';

import 'bootstrap-tagsinput/dist/bootstrap-tagsinput.css';
import 'bootstrap-tagsinput/dist/bootstrap-tagsinput-typeahead.css';

import '../scss/structure/admin/tags.scss';
import '../scss/structure/admin.scss';



$(document).ready(function(){
    var $input = $('input[data-role="tagsinput"]');

    var tags = [];
    $.get('/admin/thesaurus/gesture/tags',function(data){
        tags=data;
    });
    var source = new Bloodhound({
        prefetch: '/admin/thesaurus/gesture/tags',
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('keyword'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
    });
    if ($input.length) {
        console.log('ok');
        $input.tagsinput({
            trimValue: true,
            focusClass: 'focus',
            typeaheadjs: [
                {
                    hint: true,
                    highlight: true,
                    minLength: 1
                },
                {
                    source: ['value1','value2']
                }
            ]
        });
    }
    $(document).on('keyup','.bootstrap-tagsinput input',typeah);

    $(document).on('submit','.js-delete-gesture',confirmDelete);
});

function typeah(elem){
    console.log(elem);
}

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