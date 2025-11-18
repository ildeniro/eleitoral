//------------------------------------------------------------------------------
$(document).ready(function () {
    $('#tabela_1').DataTable();
});
//------------------------------------------------------------------------------
$('a#remover').click(function () {
    var id = $(this).attr('rel');
    swal({
        title: "Deseja mesmo bloquear está seção?",
        text: "Obs: Caso escolha bloquear, a seção vai ficar inativa no sistema!",
        type: "warning",
        showCancelButton: !0,
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger m-l-10",
        confirmButtonText: "Sim, pode bloquear!",
        cancelButtonText: "Não"
    }).then(function () {
        projetouniversal.util.getjson({
            url: PORTAL_URL + "dao/secoes/bloquear",
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
        postToURL(PORTAL_URL + 'view/secoes/index');
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