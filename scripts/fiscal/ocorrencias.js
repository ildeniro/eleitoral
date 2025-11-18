//------------------------------------------------------------------------------
$(document).ready(function () {

    $('select').each(function () {
        $(this).select2();
        $('.select2').attr('style', 'width: 100%');
    });

    $("select#zona").change(function () {
        $("select#secao").html('<option value="0">Carregando...</option>');
        $.post(PORTAL_URL + "combo/secao.php",
                {zona: $(this).val()},
                function (valor) {
                    $("select#secao").html(valor);
                    $("select#secao").select2();
                });
    });

    $("select#secao").change(function () {

        var zona = $("select#zona").val();

        $("select#local").html('<option value="0">Carregando...</option>');
        $.post(PORTAL_URL + "combo/local2.php",
                {secao: $(this).val(), zona: zona},
                function (valor) {
                    $("select#local").html(valor);
                    $("select#local").select2();
                });
    });

    $("a#filtrar").click(function () {
        $("form#filtros").submit();
    });

});
//------------------------------------------------------------------------------
$('input#pesquisar').keyup(function () {

    var pesquisa = $(this).val().replace("'", "´");

    var url = PORTAL_URL + "ajax/pesquisa_ocorrencias.php?pesquisa=" + pesquisa;
    ajax(url);

});
//------------------------------------------------------------------------------
function mais_detalhes_ocorrencia(id) {
    var texto = $("#mais_detalhes_" + id).html();
    var collaps = $("#collapseSeven_" + id).attr("class");

    if (texto == "Mais detalhes" && collaps == "row collapse" || texto == "Mais detalhes" && collaps == "collapse row") {
        $("#mais_detalhes_" + id).html("Menos detalhes");
    } else {
        $("#mais_detalhes_" + id).html("Mais detalhes");
    }
}

//------------------------------------------------------------------------------
function add_encaminhamento(ocorrencia_id) {
    swal({
        title: "Informe o encaminhamento",
        type: "info",
        showCancelButton: !0,
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger m-l-10",
        confirmButtonText: "Salvar",
        cancelButtonText: "Cancelar",
        html: '<br/><textarea id="encaminhamento" style="width: 90%; height: 100px;">'
    }).then(function () {

        if ($('#encaminhamento').val() == "") {
            swal("É obrigatório informar o encaminhamento para salvar!");
            return false;
        } else {
            projetouniversal.util.getjson({
                url: PORTAL_URL + "dao/fiscal/encaminhamento",
                type: "POST",
                data: {ocorrencia_id: ocorrencia_id, encaminhamento: $('#encaminhamento').val()},
                enctype: 'multipart/form-data',
                success: onSuccessSend,
                error: onError
            });
            return false;
        }
    });
}

//------------------------------------------------------------------------------
function remover(id) {

    swal({
        title: "Deseja mesmo remover essa ocorrência?",
        text: "Obs: Caso escolha remover, não será mais possível recuperar!",
        type: "warning",
        showCancelButton: !0,
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger m-l-10",
        confirmButtonText: "Sim, pode remover!",
        cancelButtonText: "Não"
    }).then(function () {
        projetouniversal.util.getjson({
            url: PORTAL_URL + "dao/fiscal/desativar_ocorrencia",
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
        postToURL(PORTAL_URL + 'view/fiscal/ocorrencias');
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