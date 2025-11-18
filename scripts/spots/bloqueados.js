//------------------------------------------------------------------------------
$(document).ready(function () {
    $('#tabela_1').DataTable();
});
//------------------------------------------------------------------------------
$('a#ativar').click(function () {
    var id = $(this).attr('rel');
    swal({
        title: "Deseja mesmo ativar este spot?",
        text: "Obs: Caso escolha ativar, o spot vai ficar ativo no sistema novamente!",
        type: "warning",
        showCancelButton: !0,
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger m-l-10",
        confirmButtonText: "Sim, pode ativar!",
        cancelButtonText: "Não"
    }).then(function () {
        projetouniversal.util.getjson({
            url: PORTAL_URL + "dao/spots/ativar",
            type: "POST",
            data: {id: id},
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
        postToURL(PORTAL_URL + 'view/spots/bloqueados');
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