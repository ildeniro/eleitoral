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

    var url = PORTAL_URL + "ajax/pesquisa_distribuidos.php?pesquisa=" + pesquisa;
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
function add_info(id) {

    swal({
        title: "Deseja mesmo adicionar informações ao histórico do fiscal?",
        text: "Obs: Caso escolha adicionar, essas informações serão encaminhadas para o histórico do fiscal localizado em mais detalhes!",
        type: "info",
        showCancelButton: !0,
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger m-l-10",
        confirmButtonText: "Sim, pode salvar!",
        cancelButtonText: "Não",
        html: '<br/><textarea id="input-field" style="width: 90%; height: 100px;">'
    }).then(function () {

        if ($('#input-field').val() == "") {
            swal("É obrigatório informar as informações para salvar!");
            return false;
        } else {
            projetouniversal.util.getjson({
                url: PORTAL_URL + "dao/fiscal/motivos_distribuicao",
                type: "POST",
                data: {id: id, motivo: $('#input-field').val()},
                enctype: 'multipart/form-data',
                success: onSuccessSend,
                error: onError
            });
            return false;
        }
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
        postToURL(PORTAL_URL + 'view/fiscal/distribuidos');
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
function distribuido(municipio, spot, id, funcao_id, local, zona, secao, bairro, regional) {

    if (funcao_id == 5) {//Fiscal de Seção
        swal({
            title: "Deseja remover essa distribuição?",
            text: "Função: Fiscal de Seção<br/>Município: " + municipio + "<br/>Regional: " + regional + "<br/>Bairro: " + bairro + "<br/>Zona: " + zona + "<br/>Seção: " + secao + "<br/>Local: " + local,
            type: "warning",
            showCancelButton: !0,
            confirmButtonClass: "btn btn-success",
            cancelButtonClass: "btn btn-danger m-l-10",
            confirmButtonText: "Sim, pode remover!",
            cancelButtonText: "Não"
        }).then(function () {
            $.post(PORTAL_URL + "dao/fiscal/remover", {id: id}, function (data) {
                if (isNaN(data)) {
                    swal({
                        title: "Formulário do Distribuição",
                        html: data,
                        type: "error",
                        showCancelButton: false,
                        confirmButtonColor: "#8CD4F5",
                        confirmButtonText: "OK",
                        closeOnConfirm: false
                    });
                    return false;
                } else {
                    swal({
                        title: "Sucesso!",
                        text: "Distribuição removida com sucesso!",
                        type: "success",
                        confirmButtonClass: "btn btn-success",
                        confirmButtonText: "Ok"
                    }).then(function () {
                        postToURL(PORTAL_URL + 'view/fiscal/distribuidos');
                    });
                }
            }
            , "html");
            return false;
        });
    } else if (funcao_id == 2) {//advogado

        swal({
            title: "Deseja remover essa distribuição?",
            text: "Função: Advogado<br/>Regional: " + regional,
            type: "warning",
            showCancelButton: !0,
            confirmButtonClass: "btn btn-success",
            cancelButtonClass: "btn btn-danger m-l-10",
            confirmButtonText: "Sim, pode remover!",
            cancelButtonText: "Não"
        }).then(function () {
            $.post(PORTAL_URL + "dao/fiscal/remover_advogado", {id: id}, function (data) {
                if (isNaN(data)) {
                    swal({
                        title: "Formulário do Distribuição",
                        html: data,
                        type: "error",
                        showCancelButton: false,
                        confirmButtonColor: "#8CD4F5",
                        confirmButtonText: "OK",
                        closeOnConfirm: false
                    });
                    return false;
                } else {
                    swal({
                        title: "Sucesso!",
                        text: "Distribuição removida com sucesso!",
                        type: "success",
                        confirmButtonClass: "btn btn-success",
                        confirmButtonText: "Ok"
                    }).then(function () {
                        postToURL(PORTAL_URL + 'view/fiscal/distribuidos');
                    });
                }
            }
            , "html");
            return false;
        });

    } else if (funcao_id == 6) {//Coordenador

        swal({
            title: "Deseja remover essa distribuição?",
            text: "Função: Coordenador de Local<br/>Regional: " + regional + "<br/>Bairro: " + bairro + "<br/>Local: " + local,
            type: "warning",
            showCancelButton: !0,
            confirmButtonClass: "btn btn-success",
            cancelButtonClass: "btn btn-danger m-l-10",
            confirmButtonText: "Sim, pode remover!",
            cancelButtonText: "Não"
        }).then(function () {
            $.post(PORTAL_URL + "dao/fiscal/remover_coordenador", {id: id}, function (data) {
                if (isNaN(data)) {
                    swal({
                        title: "Formulário do Distribuição",
                        html: data,
                        type: "error",
                        showCancelButton: false,
                        confirmButtonColor: "#8CD4F5",
                        confirmButtonText: "OK",
                        closeOnConfirm: false
                    });
                    return false;
                } else {
                    swal({
                        title: "Sucesso!",
                        text: "Distribuição removida com sucesso!",
                        type: "success",
                        confirmButtonClass: "btn btn-success",
                        confirmButtonText: "Ok"
                    }).then(function () {
                        postToURL(PORTAL_URL + 'view/fiscal/distribuidos');
                    });
                }
            }
            , "html");
            return false;
        });

    } else if (funcao_id == 4) {//Supervisor

        swal({
            title: "Deseja remover essa distribuição?",
            text: "Função: Supervisor (Fiscal de Regional)<br/>Regional: " + regional + "<br/>Bairro: " + bairro + "<br/>Local: " + local,
            type: "warning",
            showCancelButton: !0,
            confirmButtonClass: "btn btn-success",
            cancelButtonClass: "btn btn-danger m-l-10",
            confirmButtonText: "Sim, pode remover!",
            cancelButtonText: "Não"
        }).then(function () {
            $.post(PORTAL_URL + "dao/fiscal/remover_supervisor", {id: id}, function (data) {
                if (isNaN(data)) {
                    swal({
                        title: "Formulário do Distribuição",
                        html: data,
                        type: "error",
                        showCancelButton: false,
                        confirmButtonColor: "#8CD4F5",
                        confirmButtonText: "OK",
                        closeOnConfirm: false
                    });
                    return false;
                } else {
                    swal({
                        title: "Sucesso!",
                        text: "Distribuição removida com sucesso!",
                        type: "success",
                        confirmButtonClass: "btn btn-success",
                        confirmButtonText: "Ok"
                    }).then(function () {
                        postToURL(PORTAL_URL + 'view/fiscal/distribuidos');
                    });
                }
            }
            , "html");
            return false;
        });

    } else if (funcao_id == 7) {//Gestor

        swal({
            title: "Deseja remover essa distribuição?",
            text: "Função: Gestor de Spot<br/>Spot: " + spot,
            type: "warning",
            showCancelButton: !0,
            confirmButtonClass: "btn btn-success",
            cancelButtonClass: "btn btn-danger m-l-10",
            confirmButtonText: "Sim, pode remover!",
            cancelButtonText: "Não"
        }).then(function () {
            $.post(PORTAL_URL + "dao/fiscal/remover_gestor", {id: id}, function (data) {
                if (isNaN(data)) {
                    swal({
                        title: "Formulário do Distribuição",
                        html: data,
                        type: "error",
                        showCancelButton: false,
                        confirmButtonColor: "#8CD4F5",
                        confirmButtonText: "OK",
                        closeOnConfirm: false
                    });
                    return false;
                } else {
                    swal({
                        title: "Sucesso!",
                        text: "Distribuição removida com sucesso!",
                        type: "success",
                        confirmButtonClass: "btn btn-success",
                        confirmButtonText: "Ok"
                    }).then(function () {
                        postToURL(PORTAL_URL + 'view/fiscal/distribuidos');
                    });
                }
            }
            , "html");
            return false;
        });

    }

}
//------------------------------------------------------------------------------