'use strict';

import swal from 'sweetalert';

$(document).ready(function(){
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