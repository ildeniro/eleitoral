//------------------------------------------------------------------------------
$(document).ready(function () {

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    $('#tabela_1').DataTable({
        dom: 'Bfrtip',
        pageLength: 100,
        buttons: [
            'copy', // Mantém o botão de copiar
            'excel', // Mantém o botão de exportar para Excel
            'pdf', // Mantém o botão de exportar para PDF
            'colvis' // Mantém o botão de visibilidade de colunas
        ],
        initComplete: function () {
            // Ajustar o campo de busca (search box)
            $('.dt-search').css({
                'float': 'right',
                'margin': '0px 0px 30px'
            });

            // Ajustar a área dos botões de exportação
            $('.dt-buttons').css({
                'margin-bottom': '-40px'
            });

            // Ajustar a área de informações (info)
            $('#tabela_1_info').css({
                'margin-top': '15px'
            });

            // Ajustar a área de paginação (paging)
            $('#tabela_1_paginate').css({
                'margin-top': '15px'
            });

            // Cursor 'pointer' para os headers (tr > th)
            $('#tabela_1 thead th').css({
                'cursor': 'pointer'
            });
        }
    });


    $('#tabela_2').DataTable({
        dom: 'Bfrtip',
        pageLength: 100,
        buttons: [
            'copy', // Mantém o botão de copiar
            'excel', // Mantém o botão de exportar para Excel
            'pdf', // Mantém o botão de exportar para PDF
            'colvis' // Mantém o botão de visibilidade de colunas
        ],
        initComplete: function () {
            // Ajustar o campo de busca (search box)
            $('.dt-search').css({
                'float': 'right',
                'margin': '0px 0px 30px'
            });

            // Ajustar a área dos botões de exportação
            $('.dt-buttons').css({
                'margin-bottom': '-40px'
            });

            // Ajustar a área de informações (info)
            $('#tabela_2_info').css({
                'margin-top': '15px'
            });

            // Ajustar a área de paginação (paging)
            $('#tabela_2_paginate').css({
                'margin-top': '15px'
            });

            // Cursor 'pointer' para os headers (tr > th)
            $('#tabela_2 thead th').css({
                'cursor': 'pointer'
            });
        }
    });

    $('select').each(function () {
        $(this).select2();
        $('.select2').attr('style', 'width: 100%');
    });

    $("#tipo_voluntario").change(function () {
        var tipo_voluntario = $("#tipo_voluntario").val();
        var tipo_eleicao = $("#tipo_eleicao").val();

        $("input#tipo_voluntario_guard").val(tipo_voluntario);
        $("input#tipo_eleicao_guard").val(tipo_eleicao);

        if (tipo_voluntario != "" && tipo_eleicao != "") {
            vf_div_voluntarios(tipo_voluntario, tipo_eleicao);
        } else {
            $("div#div_resultado_municipal").hide();
            $("div#div_resultado_estadual").hide();
            $("div#div_fiscais_secoes").hide();
            $("div#div_distribuicao_geral_fiscal").hide();
            $("div#div_distribuicao_geral_supervisor").hide();
            $("div#div_tipo_voluntario").attr("class", "col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12");
            $("div#div_tipo_eleicao").attr("class", "col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12");
        }

    });

    $("#tipo_eleicao").change(function () {
        var tipo_voluntario = $("#tipo_voluntario").val();
        var tipo_eleicao = $("#tipo_eleicao").val();

        $("input#tipo_voluntario_guard").val(tipo_voluntario);
        $("input#tipo_eleicao_guard").val(tipo_eleicao);

        if (tipo_voluntario != "" && tipo_eleicao != "") {
            vf_div_voluntarios(tipo_voluntario, tipo_eleicao);
        } else {
            $("div#div_resultado_municipal").hide();
            $("div#div_resultado_estadual").hide();
            $("div#div_fiscais_secoes").hide();
            $("div#div_distribuicao_geral_fiscal").hide();
            $("div#div_distribuicao_geral_supervisor").hide();
            $("div#div_tipo_voluntario").attr("class", "col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12");
            $("div#div_tipo_eleicao").attr("class", "col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12");
        }
    });

    $("select#regional").change(function () {
        $("select#local").html('<option value="0">Carregando...</option>');
        $("select#bairro").html('<option value="0">Carregando...</option>');

        $.post(PORTAL_URL + "combo/local.php",
                {regional: $(this).val()},
                function (valor) {
                    $("select#local").html(valor);
                    $("select#local").select2();
                });

        $.post(PORTAL_URL + "combo/bairro.php",
                {regional: $(this).val()},
                function (valor2) {
                    $("select#bairro").html(valor2);
                    $("select#bairro").select2();
                });
    });

    $("select#regional2").change(function () {
        $("select#local2").html('<option value="0">Carregando...</option>');
        $("select#bairro2").html('<option value="0">Carregando...</option>');

        $.post(PORTAL_URL + "combo/local.php",
                {regional: $(this).val()},
                function (valor) {
                    $("select#local2").html(valor);
                    $("select#local2").select2();
                });


        $.post(PORTAL_URL + "combo/bairro.php",
                {regional: $(this).val()},
                function (valor2) {
                    $("select#bairro2").html(valor2);
                    $("select#bairro2").select2();
                });
    });

    $("select#bairro").change(function () {
        $("select#local").html('<option value="0">Carregando...</option>');
        $.post(PORTAL_URL + "combo/bairro_local.php",
                {bairro: $(this).val()},
                function (valor) {
                    $("select#local").html(valor);
                    $("select#local").select2();
                });
    });

    $("select#bairro2").change(function () {
        $("select#local2").html('<option value="0">Carregando...</option>');
        $.post(PORTAL_URL + "combo/bairro_local.php",
                {bairro: $(this).val()},
                function (valor) {
                    $("select#local2").html(valor);
                    $("select#local2").select2();
                });
    });

    $("select#local").change(function () {
        $("select#secao").html('<option value="0">Carregando...</option>');
        $.post(PORTAL_URL + "combo/local_secao.php",
                {local: $(this).val()},
                function (valor) {
                    $("select#secao").html(valor);
                    $("select#secao").select2();
                });
    });

    $("select#zona").change(function () {
        $("form#filtros").submit();
    });

    $("select#regional").change(function () {
        $("form#filtros").submit();
    });

    $("select#local").change(function () {
        $("form#filtros").submit();
    });

    $("select#bairro").change(function () {
        $("form#filtros").submit();
    });

    $("select#secao").change(function () {
        $("form#filtros").submit();
    });

    $("select#regional2").change(function () {
        $("form#filtros2").submit();
    });

    $("select#local2").change(function () {
        $("form#filtros2").submit();
    });

    $("select#bairro2").change(function () {
        $("form#filtros2").submit();
    });

    //----------------------------------------------------------------------------- 
    $('form#fiscal').submit(function () {

        $("#submit").attr("disabled", true);

        var pessoas = $('#pessoas').val();
        var secao = $("input#guard_secao").val();
        var regional = $("input#guard_regional").val();
        var local = $("input#guard_local").val();
        var bairro = $("input#guard_bairro").val();
        var zona = $("input#guard_zona").val();

        $.post(PORTAL_URL + "dao/fiscal/add", {zona: zona, pessoas: pessoas, secao: secao, regional: regional, local: local, bairro: bairro}, function (data) {
            if (isNaN(data)) {
                swal({
                    title: "Formulário do Fiscal",
                    html: data,
                    type: "error",
                    showCancelButton: false,
                    confirmButtonColor: "#8CD4F5",
                    confirmButtonText: "OK",
                    closeOnConfirm: false
                });
                $("#submit").attr("disabled", false);
                return false;
            } else {
                atualizar_fiscal();
            }
        }
        , "html");
        return false;
    });
    //----------------------------------------------------------------------------- 
    $('form#supervisor').submit(function () {

        $("#submit").attr("disabled", true);

        var pessoas = $('#pessoas_supervisor').val();
        var secao = $("input#guard_secao_supervisor").val();
        var regional = $("input#guard_regional_supervisor").val();
        var local = $("input#guard_local_supervisor").val();

        $.post(PORTAL_URL + "dao/fiscal/add_supervisor", {pessoas: pessoas, secao: secao, regional: regional, local: local}, function (data) {
            if (isNaN(data)) {
                swal({
                    title: "Formulário do Supervisor",
                    html: data,
                    type: "error",
                    showCancelButton: false,
                    confirmButtonColor: "#8CD4F5",
                    confirmButtonText: "OK",
                    closeOnConfirm: false
                });
                $("#submit").attr("disabled", false);
                return false;
            } else {
                atualizar_supervisor();
            }
        }
        , "html");
        return false;
    });
});
//------------------------------------------------------------------------------
function remover(id) {

    $("#submit").attr("disabled", true);

    swal({
        title: "Deseja mesmo remover este fiscal desta seção?",
        text: "Obs: Caso escolha remover, o fiscal vai ficar disponível para distribuição novamente!",
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
                    title: "Formulário do Fiscal",
                    html: data,
                    type: "error",
                    showCancelButton: false,
                    confirmButtonColor: "#8CD4F5",
                    confirmButtonText: "OK",
                    closeOnConfirm: false
                });
                $("#submit").attr("disabled", false);
                return false;
            } else {
                atualizar_fiscal();
            }
        }
        , "html");
        return false;
    });
}
//------------------------------------------------------------------------------------------------------
function vf_div_voluntarios(tipo_voluntario, tipo_eleicao) {

    if (tipo_voluntario == 4) {
        $("div#div_supervisores").show();
        $("div#div_fiscais").hide();
        $("div#div_advogados").hide();

        $("div#div_supervisor_regionais").show();
        $("div#div_fiscais_secoes").hide();
        $("div#div_advogado_locais").hide();

        $("div#div_supervisores_card").show();
        $("div#div_fiscais_card").hide();
        $("div#div_advogados_card").hide();

        $("div#div_distribuicao_geral_fiscal").hide();
        $("div#div_distribuicao_geral_supervisor").show();
        $("div#div_tipo_voluntario").attr("class", "col-xl-5 col-lg-5 col-md-12 col-sm-12 col-12");
        $("div#div_tipo_eleicao").attr("class", "col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12");
    } else if (tipo_voluntario == 5) {
        $("div#div_fiscais").show();
        $("div#div_supervisores").hide();
        $("div#div_advogados").hide();

        $("div#div_fiscais_secoes").show();
        $("div#div_supervisor_regionais").hide();
        $("div#div_advogado_locais").hide();

        $("div#div_tipo_voluntario").attr("class", "col-xl-5 col-lg-5 col-md-12 col-sm-12 col-12");
        $("div#div_tipo_eleicao").attr("class", "col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12");
        $("div#div_distribuicao_geral_fiscal").show();
        $("div#div_distribuicao_geral_supervisor").hide();

        $("div#div_fiscais_card").show();
        $("div#div_supervisores_card").hide();
        $("div#div_advogados_card").hide();
    } else if (tipo_voluntario == 2) {
        $("div#div_advogados").show();
        $("div#div_fiscais").hide();
        $("div#div_supervisores").hide();

        $("div#div_advogado_locais").show();
        $("div#div_supervisor_regionais").hide();
        $("div#div_fiscais_secoes").hide();

        $("div#div_distribuicao_geral_fiscal").hide();
        $("div#div_distribuicao_geral_supervisor").hide();

        $("div#div_advogados_card").show();
        $("div#div_fiscais_card").hide();
        $("div#div_supervisores_card").hide();

        $("div#div_tipo_voluntario").attr("class", "col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12");
        $("div#div_tipo_eleicao").attr("class", "col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12");
    } else {
        $("div#div_advogados").hide();
        $("div#div_fiscais").hide();
        $("div#div_supervisores").hide();

        $("div#div_advogados_card").hide();
        $("div#div_fiscais_card").hide();
        $("div#div_supervisores_card").hide();
        $("div#div_distribuicao_geral_fiscal").hide();
        $("div#div_distribuicao_geral_supervisor").hide();
        $("div#div_tipo_voluntario").attr("class", "col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12");
        $("div#div_tipo_eleicao").attr("class", "col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12");
    }

    if (tipo_eleicao == 1) {
        $("div#div_resultado_municipal").show();
        $("div#div_resultado_estadual").hide();
    } else {
        $("div#div_resultado_municipal").hide();
        $("div#div_resultado_estadual").show();
        $("div#div_distribuicao_geral_fiscal").hide();
        $("div#div_distribuicao_geral_supervisor").hide();
        $("div#div_tipo_voluntario").attr("class", "col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12");
        $("div#div_tipo_eleicao").attr("class", "col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12");
    }
}
//------------------------------------------------------------------------------------------------------
function carregar_dados(zona, secao, regional, local, bairro) {
    $("input#guard_secao").val(secao);
    $("input#guard_regional").val(regional);
    $("input#guard_local").val(local);
    $("input#guard_bairro").val(bairro);
    $("input#guard_zona").val(zona);
}
//------------------------------------------------------------------------------------------------------
function carregar_dados_supervisor(secao, regional, local) {
    $("input#guard_secao_supervisor").val(secao);
    $("input#guard_regional_supervisor").val(regional);
    $("input#guard_local_supervisor").val(local);
}
//------------------------------------------------------------------------------------------------------
function gerar() {

    $("#gerar").attr("disabled", true);

    swal({
        title: "Deseja mesmo gerar a distribuição automaticamente?",
        text: "Obs: Caso escolha gerar, o sistema vai vincular os fiscais as seções automaticamente!",
        type: "warning",
        showCancelButton: !0,
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger m-l-10",
        confirmButtonText: "Sim, pode gerar!",
        cancelButtonText: "Não"
    }).then(function () {

        $("#loading").removeClass("hidden");

        $.post(PORTAL_URL + "dao/fiscal/gerar", {}, function (data) {
            if (isNaN(data)) {

                $("#loading").addClass("hidden");

                swal({
                    title: "Formulário do Distribuição",
                    html: "Distribuição gerada com sucesso!",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#8CD4F5",
                    confirmButtonText: "OK",
                    closeOnConfirm: false
                }).then(function () {
                    atualizar_fiscal();
                });

                return false;
            } else {

                swal({
                    title: "Formulário do Distribuição",
                    html: "Erro ao tentar gerar a distribuição desejada!",
                    type: "error",
                    showCancelButton: false,
                    confirmButtonColor: "#8CD4F5",
                    confirmButtonText: "OK",
                    closeOnConfirm: false
                });

                $("#gerar").attr("disabled", false);

                return false;

            }
        }
        , "html");
        return false;
    });
}
//------------------------------------------------------------------------------------------------------
function desfazer() {

    $("#desfazer").attr("disabled", true);

    swal({
        title: "Deseja mesmo desfazer a distribuição automaticamente?",
        text: "Obs: Caso escolha desfazer, o sistema vai desvincular todos os fiscais e seções automaticamente!",
        type: "warning",
        showCancelButton: !0,
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger m-l-10",
        confirmButtonText: "Sim, pode desfazer!",
        cancelButtonText: "Não"
    }).then(function () {

        $("#loading").removeClass("hidden");

        $.post(PORTAL_URL + "dao/fiscal/desfazer", {}, function (data) {
            if (isNaN(data)) {

                $("#loading").addClass("hidden");

                swal({
                    title: "Formulário do Distribuição",
                    html: "A distribuição foi desfeita com sucesso!",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#8CD4F5",
                    confirmButtonText: "OK",
                    closeOnConfirm: false
                }).then(function () {
                    atualizar_fiscal();
                });

                return false;
            } else {

                swal({
                    title: "Formulário do Distribuição",
                    html: "Erro ao tentar desfazer a distribuição desejada!",
                    type: "error",
                    showCancelButton: false,
                    confirmButtonColor: "#8CD4F5",
                    confirmButtonText: "OK",
                    closeOnConfirm: false
                });

                $("#desfazer").attr("disabled", false);

                return false;

            }
        }
        , "html");
        return false;
    });
}
//------------------------------------------------------------------------------------------------------
function gerar_supervisor() {

    $("#gerar").attr("disabled", true);

    swal({
        title: "Deseja mesmo gerar a distribuição automaticamente?",
        text: "Obs: Caso escolha gerar, o sistema vai vincular os supervisores aos seus locais automaticamente!",
        type: "warning",
        showCancelButton: !0,
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger m-l-10",
        confirmButtonText: "Sim, pode gerar!",
        cancelButtonText: "Não"
    }).then(function () {

        $("#loading").removeClass("hidden");

        $.post(PORTAL_URL + "dao/fiscal/gerar_supervisor", {}, function (data) {
            if (isNaN(data)) {

                $("#loading").addClass("hidden");

                swal({
                    title: "Formulário do Distribuição",
                    html: "Distribuição gerada com sucesso!",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#8CD4F5",
                    confirmButtonText: "OK",
                    closeOnConfirm: false
                }).then(function () {
                    atualizar_fiscal();
                });

                return false;
            } else {

                swal({
                    title: "Formulário do Distribuição",
                    html: "Erro ao tentar gerar a distribuição desejada!",
                    type: "error",
                    showCancelButton: false,
                    confirmButtonColor: "#8CD4F5",
                    confirmButtonText: "OK",
                    closeOnConfirm: false
                });

                $("#gerar").attr("disabled", false);

                return false;

            }
        }
        , "html");
        return false;
    });
}
//------------------------------------------------------------------------------------------------------
function desfazer_supervisor() {

    $("#desfazer").attr("disabled", true);

    swal({
        title: "Deseja mesmo desfazer a distribuição automaticamente?",
        text: "Obs: Caso escolha desfazer, o sistema vai desvincular todos os supervisores dos seus locais automaticamente!",
        type: "warning",
        showCancelButton: !0,
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger m-l-10",
        confirmButtonText: "Sim, pode desfazer!",
        cancelButtonText: "Não"
    }).then(function () {

        $("#loading").removeClass("hidden");

        $.post(PORTAL_URL + "dao/fiscal/desfazer_supervisor", {}, function (data) {
            if (isNaN(data)) {

                $("#loading").addClass("hidden");

                swal({
                    title: "Formulário do Distribuição",
                    html: "A distribuição foi desfeita com sucesso!",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#8CD4F5",
                    confirmButtonText: "OK",
                    closeOnConfirm: false
                }).then(function () {
                    atualizar_fiscal();
                });

                return false;
            } else {

                swal({
                    title: "Formulário do Distribuição",
                    html: "Erro ao tentar desfazer a distribuição desejada!",
                    type: "error",
                    showCancelButton: false,
                    confirmButtonColor: "#8CD4F5",
                    confirmButtonText: "OK",
                    closeOnConfirm: false
                });

                $("#desfazer").attr("disabled", false);

                return false;

            }
        }
        , "html");
        return false;
    });
}
//------------------------------------------------------------------------------
function remover_supervisor(id) {

    swal({
        title: "Deseja mesmo remover este supervisor deste local de votação?",
        text: "Obs: Caso escolha remover, o supervisor vai ficar disponível novamente para distribuição!",
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
                    title: "Formulário do Supervisor",
                    html: data,
                    type: "error",
                    showCancelButton: false,
                    confirmButtonColor: "#8CD4F5",
                    confirmButtonText: "OK",
                    closeOnConfirm: false
                });
                $("#submit").attr("disabled", false);
                return false;
            } else {
                atualizar_supervisor();
            }
        }
        , "html");
        return false;
    });
}
//------------------------------------------------------------------------------
function atualizar_fiscal() {
    var tipo_voluntario_guard = $("#tipo_voluntario_guard").val();
    var tipo_eleicao_guard = $("#tipo_eleicao_guard").val();
    var regional = $("#regional").val();
    var bairro = $("#bairro").val();
    var local = $("#local").val();
    var secao = $("#secao").val();

    postToURL(PORTAL_URL + 'view/fiscal/distribuicao_supervisor?tipo_voluntario_guard=' + tipo_voluntario_guard + '&tipo_eleicao_guard=' + tipo_eleicao_guard + '&regional=' + regional + '&bairro=' + bairro + '&local=' + local + "&secao=" + secao);
}
//------------------------------------------------------------------------------
function atualizar_supervisor() {
    var tipo_voluntario_guard = $("#tipo_voluntario_guard").val();
    var tipo_eleicao_guard = $("#tipo_eleicao_guard").val();
    var regional2 = $("#regional2").val();
    var bairro2 = $("#bairro2").val();
    var local2 = $("#local2").val();

    postToURL(PORTAL_URL + 'view/fiscal/distribuicao_supervisor?tipo_voluntario_guard=' + tipo_voluntario_guard + '&tipo_eleicao_guard=' + tipo_eleicao_guard + '&regional2=' + regional2 + '&bairro2=' + bairro2 + '&local2=' + local2);
}
//------------------------------------------------------------------------------
// Usando jQuery
$('.dropdown').hover(
        function () {
            $(this).find('.dropdown-menu').addClass('show'); // Mostra o dropdown
        },
        function () {
            $(this).find('.dropdown-menu').removeClass('show'); // Esconde o dropdown quando o mouse sai
        }
);
//------------------------------------------------------------------------------