//------------------------------------------------------------------------------------------------------
//Cadastrar
$(document).ready(function () {

    $("body").addClass('sidebar-icon-only');

    $('select').each(function () {
        $(this).select2();
        $('.select2').attr('style', 'width: 100%');
    });

    $('#tabela_1').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copyHtml5',
                exportOptions: {
                    columns: [0, ':visible']
                }
            },
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdfHtml5',
                exportOptions: {
                    columns: ':visible'
                }
            },
            'colvis'
        ]
    });

    $("select#regional").change(function () {
        $("select#local").html('<option value="0">Carregando...</option>');
        $.post(PORTAL_URL + "combo/local.php",
                {regional: $(this).val()},
                function (valor) {
                    $("select#local").html(valor);
                    $("select#local").select2();
                });
    });

    $("select#regional").change(function () {
        $("form#filtros").submit();
    });

    $("select#local").change(function () {
        $("form#filtros").submit();
    });
//----------------------------------------------------------------------------- 
    $('form#fiscal').submit(function () {

        if (formulario_validator("")) {

            $("#submit").attr("disabled", true);

            var pessoas = $('#pessoas').val();
            var secao = $("input#guard_secao").val();
            var regional = $("input#guard_regional").val();
            var local = $("input#guard_local").val();

            $.post(PORTAL_URL + "dao/fiscal/add_coordenador", {pessoas: pessoas, secao: secao, regional: regional, local: local}, function (data) {
                if (isNaN(data)) {
                    swal({
                        title: "Formulário do Coordenador",
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
                    postToURL(PORTAL_URL + 'view/fiscal/coordenador/?regional=' + regional + '&local=' + local);
                }
            }
            , "html");
            return false;
        } else {
            return false;
        }
    });
});
//------------------------------------------------------------------------------
function remover(id) {
    var regional = $("input#guard_regional_2").val();
    var local = $("input#guard_local_2").val();

    swal({
        title: "Deseja mesmo remover este coordenador desta seção?",
        text: "Obs: Caso escolha remover, o fiscal vai ficar disponível novamente!",
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
                    title: "Formulário do Coordenador",
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
                postToURL(PORTAL_URL + 'view/fiscal/coordenador/?regional=' + regional + '&local=' + local);
            }
        }
        , "html");
        return false;
    });
}
//------------------------------------------------------------------------------
//VALIDATOR DO LOGIN
function formulario_validator(obj) {
    var valido = true;
    var pessoas = $('#pessoas').val();

    var element = null;

    //LIMPA MENSAGENS DE ERRO
    $('label.error').each(function () {
        $(this).remove();
    });

    if (obj.tipo == "" || obj.tipo == null) {//VALIDAÇÃO SEM BANCO DE DADOS

        if (pessoas == "") {
            $('div#div_pessoas').after('<label id="erro_pessoas" class="error">Fiscal é obrigatório.</label>');
            valido = false;
            element = $('div#div_pessoas');
        }

    }

    if (element != null) {
        var topPosition = element.offset().top - 135;
        $('html, body').animate({
            scrollTop: topPosition
        }, 800);
    }
    return valido;
}
//------------------------------------------------------------------------------------------------------
function carregar_dados(secao, regional, local) {
    $("input#guard_secao").val(secao);
    $("input#guard_regional").val(regional);
    $("input#guard_local").val(local);
}
//------------------------------------------------------------------------------------------------------
function gerar() {

    $("#gerar").attr("disabled", true);

    swal({
        title: "Deseja mesmo gerar a distribuição automaticamente?",
        text: "Obs: Caso escolha gerar, o sistema vai vincular os coordenadores aos locais de votação automaticamente!",
        type: "warning",
        showCancelButton: !0,
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger m-l-10",
        confirmButtonText: "Sim, pode gerar!",
        cancelButtonText: "Não"
    }).then(function () {

        $("#div_loader_2").show();

        $.post(PORTAL_URL + "dao/fiscal/gerar_coordenador", {}, function (data) {
            if (isNaN(data)) {

                swal({
                    title: "Formulário do Distribuição",
                    html: "Distribuição de coordenadores gerada com sucesso!",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#8CD4F5",
                    confirmButtonText: "OK",
                    closeOnConfirm: false
                }).then(function () {
                    postToURL(PORTAL_URL + 'view/fiscal/coordenador');
                });

                return false;
            } else {

                swal({
                    title: "Formulário do Distribuição",
                    html: "Erro ao tentar gerar a distribuição de coordenadores desejado!",
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
        text: "Obs: Caso escolha desfazer, o sistema vai desvincular todos os coordenadores e seus locais de votação automaticamente!",
        type: "warning",
        showCancelButton: !0,
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger m-l-10",
        confirmButtonText: "Sim, pode desfazer!",
        cancelButtonText: "Não"
    }).then(function () {

        $("#div_loader_2").show();

        $.post(PORTAL_URL + "dao/fiscal/desfazer_coordenador", {}, function (data) {
            if (isNaN(data)) {

                swal({
                    title: "Formulário do Distribuição",
                    html: "A distribuição de coordenadores foi desfeita com sucesso!",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#8CD4F5",
                    confirmButtonText: "OK",
                    closeOnConfirm: false
                }).then(function () {
                    postToURL(PORTAL_URL + 'view/fiscal/coordenador');
                });

                return false;
            } else {

                swal({
                    title: "Formulário do Distribuição",
                    html: "Erro ao tentar desfazer a distribuição de coordenadores desejada!",
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