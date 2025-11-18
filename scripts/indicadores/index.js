//------------------------------------------------------------------------------
$('input#pesquisar').keyup(function () {

    var pesquisa = $(this).val().replace("'", "´");

    var ativo = $("#customCheck10:checked").val() == 1 ? 1 : "";
    var cancelado = $("#customCheck11:checked").val() == 1 ? 0 : "";

    var url = PORTAL_URL + "ajax/pesquisa_indicadores.php?pesquisa=" + pesquisa + "&ativo=" + ativo + "&cancelado=" + cancelado;
    ajax(url);

});
//------------------------------------------------------------------------------
$('a#filtrar').click(function () {

    var pesquisa = $('input#pesquisar').val().replace("'", "´");

    var ativo = $("#customCheck10:checked").val() == 1 ? 1 : "";
    var cancelado = $("#customCheck11:checked").val() == 1 ? 0 : "";

    var url = PORTAL_URL + "ajax/pesquisa_indicadores.php?pesquisa=" + pesquisa + "&ativo=" + ativo + "&cancelado=" + cancelado;
    ajax(url);

});
//------------------------------------------------------------------------------
function ativar_indicador(id) {
    swal({
        title: "Deseja mesmo ativar este indicador?",
        text: "Obs: Caso escolha ativar, o indicador vai ter acesso ao sistema novamente!",
        type: "warning",
        showCancelButton: !0,
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger m-l-10",
        confirmButtonText: "Sim, pode ativar!",
        cancelButtonText: "Não"
    }).then(function () {
        projetouniversal.util.getjson({
            url: PORTAL_URL + "dao/indicadores/ativar",
            type: "POST",
            data: {id: id},
            enctype: 'multipart/form-data',
            success: onSuccessSend,
            error: onError
        });
        return false;
    });
}
//------------------------------------------------------------------------------
function cancelar_indicador(id) {
    swal({
        title: "Deseja mesmo cancelar este indicador?",
        text: "Obs: Caso escolha cancelar, o indicador vai ficar inativo no sistema!",
        type: "warning",
        showCancelButton: !0,
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger m-l-10",
        confirmButtonText: "Sim, pode cancelar!",
        cancelButtonText: "Não"
    }).then(function () {
        projetouniversal.util.getjson({
            url: PORTAL_URL + "dao/indicadores/desativar",
            type: "POST",
            data: {id: id},
            enctype: 'multipart/form-data',
            success: onSuccessSend,
            error: onError
        });
        return false;
    });
}
//------------------------------------------------------------------------------
function onSuccessSend(obj) {

    swal({
        title: "Sucesso!",
        text: "" + obj.retorno + "",
        type: "success",
        confirmButtonClass: "btn btn-success",
        confirmButtonText: "Ok"
    }).then(function () {
        postToURL(PORTAL_URL + 'view/indicadores/index');
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