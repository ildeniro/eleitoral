//------------------------------------------------------------------------------
$(document).ready(function () {
    $('#tabela_1').DataTable();
});
//------------------------------------------------------------------------------
$('a#remover').click(function () {
    var id = $(this).attr('rel');
    swal({
        title: "Deseja mesmo bloquear este spot?",
        text: "Obs: Caso escolha bloquear, o spot vai ficar inativo no sistema!",
        type: "warning",
        showCancelButton: !0,
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger m-l-10",
        confirmButtonText: "Sim, pode bloquear!",
        cancelButtonText: "Não"
    }).then(function () {
        projetouniversal.util.getjson({
            url: PORTAL_URL + "dao/spots/bloquear",
            type: "POST",
            data: { id: id },
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
        postToURL(PORTAL_URL + 'view/spots/index');
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


//------------------------------------------------------------------------------    
/* ERRO AO ENVIAR AJAX */
// Simula o carregamento de dados
document.getElementById('loadDataBtn').addEventListener('click', function () {
    // Exibe o loader
    document.getElementById('loading').classList.remove('hidden');

    // Simula carregamento de dados
    setTimeout(function () {
        // Oculta o loader após 2 segundos
        document.getElementById('loading').classList.add('hidden');

        // Adicionar ações aqui.

    }, 2000); // Simula 2 segundos de carregamento
});
//------------------------------------------------------------------------------
