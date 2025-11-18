//------------------------------------------------------------------------------
$(document).ready(function () {
    $("#confirmar_presenca").click(function () {
        swal({
            title: "",
            text: "Você confirma sua presença no local designado\n no dia da eleição?",
            type: "question",
            showCancelButton: !0,
            confirmButtonClass: "btn btn-success",
            cancelButtonClass: "btn btn-danger m-l-10",
            confirmButtonText: "Sim, pode confirmar!",
            cancelButtonText: "Não"
        }).then(function () {
            projetouniversal.util.getjson({
                url: PORTAL_URL + "dao/portaldofiscal/dashboard",
                type: "POST",
                data: {},
                enctype: 'multipart/form-data',
                success: onSuccessSend,
                error: onError
            });
            return false;
        });
    });

    $("#cancelar_confirmacao").click(function () {

        swal({
            title: "",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn btn-success",
            cancelButtonClass: "btn btn-danger m-l-10",
            confirmButtonText: "Sim, pode confirmar!",
            cancelButtonText: "Não",
            html: '<h4 style="font-size: 18px;">Você confirma que deseja desistir de sua participação como fiscal eleitoral?</h4><center><br/>' +
                    '<h3>Descreva o motivo</h3>' +
                    '<textarea id="motivo_cancelamento" name="motivo_cancelamento" style="width: 90%; height: 100px;"></textarea></center><br/>',
            preConfirm: function () {
                // Remove mensagens de erro anteriores
                $('#erro_motivo').remove();

                var motivoCancelamento = $('#motivo_cancelamento').val();

                var valid = true;

                // Verifica se os campos estão vazios e exibe mensagem de erro
                if (motivoCancelamento == "") {
                    valid = false;
                }

                // Se a validação falhar, retorna uma Promise rejeitada para impedir o fechamento
                if (!valid) {
                    return Promise.reject('É obrigatório informar o motivo');
                } else {
                    projetouniversal.util.getjson({
                        url: PORTAL_URL + "dao/portaldofiscal/cancelamento",
                        type: "POST",
                        data: {motivo: motivoCancelamento},
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
        postToURL(PORTAL_URL + 'portaldofiscal/dashboard');
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