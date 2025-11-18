//------------------------------------------------------------------------------
$(document).ready(function () {
    $('#example').DataTable({
        dom: 'Bfrtip',
        initComplete: function () {
            $('div.fg-toolbar:first').append('<span>Titulo</span>');
        },
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
});
//------------------------------------------------------------------------------
$('button#remover').click(function () {

    var id = $('input#id').val();
    var codigo = $(this).attr('rel');

    swal({
        title: "Deseja mesmo remover este arquivo?",
        text: "Obs: Caso escolha remover, não será mais possível recuperar o arquivo!",
        type: "warning",
        showCancelButton: !0,
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger m-l-10",
        confirmButtonText: "Sim, pode remover!",
        cancelButtonText: "Não"
    }).then(function () {
        projetouniversal.util.getjson({
            url: PORTAL_URL + "dao/fiscal/remover_arquivo",
            type: "POST",
            data: {id: id, codigo: codigo},
            enctype: 'multipart/form-data',
            success: onSuccessSend,
            error: onError
        });
        return false;
    });
});
//------------------------------------------------------------------------------
function onSuccessSend(obj) {

    swal({
        title: "Sucesso!",
        text: "" + obj.retorno + "",
        type: "success",
        confirmButtonClass: "btn btn-success",
        confirmButtonText: "Ok"
    }).then(function () {
        postToURL(PORTAL_URL + 'view/fiscal/anexar_arquivos/' + obj.id);
    });

    return false;
}
//------------------------------------------------------------------------------    
/* ERRO AO ENVIAR AJAX */
function onError(args) {
    swal("Error!", "" + args.retorno + "", "error");
    return false;
}
//------------------------------------------------------------------------------