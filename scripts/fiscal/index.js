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

    var url = PORTAL_URL + "ajax/pesquisa_voluntario.php?pesquisa=" + pesquisa;
    ajax(url);

});
//------------------------------------------------------------------------------
function mais_detalhes(id) {
    var texto = $("#mais_detalhes_" + id).html();
    var collaps = $("#collapseSeven_" + id).attr("class");

    if (texto == "Mais detalhes" && collaps == "collapse") {
        $("#mais_detalhes_" + id).html("Menos detalhes");
    } else {
        $("#mais_detalhes_" + id).html("Mais detalhes");
    }
}
//------------------------------------------------------------------------------
function desativar_voluntario(id) {
    
            swal({
            title: "Deseja mesmo desativar?",
            text: "Obs: Caso escolha desativar, o voluntário vai ficar inativo no sistema!",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn btn-success",
            cancelButtonClass: "btn btn-danger m-l-10",
            confirmButtonText: "Sim, pode desativar!",
            cancelButtonText: "Não",
            html: '<center><br/><h3>Informe o tipo de desativação</h3>' +
                    '<select id="cancelamento_tipo" name="cancelamento_tipo" style="width: 90%;" class="form-control">' +
                    '<option value="">Escolha o tipo</option>' +
                    '<option value="2">Desistência</option>' +
                    '<option value="3">Saúde</option>' +
                    '<option value="4">TRE</option>' +
                    '<option value="5">Não foi Distribuido</option>' +
                    '<option value="0">Outros</option>' +
                    '</select><br/><h3>Informe o motivo</h3>' +
                    '<textarea id="motivo_cancelamento" name="motivo_cancelamento" style="width: 90%; height: 100px;"></textarea></center><br/>',
            preConfirm: function () {
                // Remove mensagens de erro anteriores
                $('#erro_tipo').remove();
                $('#erro_motivo').remove();

                var tipoCancelamento = $('#cancelamento_tipo').val();
                var motivoCancelamento = $('#motivo_cancelamento').val();

                var valid = true;

                // Verifica se os campos estão vazios e exibe mensagem de erro
                if (tipoCancelamento == "") {
                    valid = false;
                }
                if (motivoCancelamento == "") {
                    valid = false;
                }

                // Se a validação falhar, retorna uma Promise rejeitada para impedir o fechamento
                if (!valid) {
                    return Promise.reject('É obrigatório informar o tipo e o motivo do desativamento');
                } else {
                    projetouniversal.util.getjson({
                        url: PORTAL_URL + "dao/fiscal/desativar",
                        type: "POST",
                        data: {id: id, motivo: motivoCancelamento, tipo: tipoCancelamento},
                        enctype: 'multipart/form-data',
                        success: onSuccessSend,
                        error: onError
                    });
                }

            }
        }).then(function (result) {

        }).catch(function (dismiss) {
            if (dismiss === 'Validação falhou') {
                // Não faz nada; isso impede o fechamento automático do alerta quando a validação falha
            }
        });
}
//------------------------------------------------------------------------------
function ativar_voluntario(id) {

    swal({
        title: "Deseja mesmo ativar este voluntário?",
        text: "Obs: Caso escolha ativar, o voluntário vai ficar disponível no sistema novamente!",
        type: "warning",
        showCancelButton: !0,
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger m-l-10",
        confirmButtonText: "Sim, pode ativar!",
        cancelButtonText: "Não"
    }).then(function () {
        projetouniversal.util.getjson({
            url: PORTAL_URL + "dao/fiscal/ativar",
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
        postToURL(PORTAL_URL + 'view/fiscal/index');
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