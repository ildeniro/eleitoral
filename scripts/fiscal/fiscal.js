//------------------------------------------------------------------------------------------------------
//Cadastrar
$(document).ready(function () {

    $("body").addClass('sidebar-icon-only');

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

    $('select').each(function () {
        $(this).select2();
        $('.select2').attr('style', 'width: 100%');
    });

//    $("select#zona").change(function () {
//        $("select#regional").html('<option value="0">Carregando...</option>');
//        $("select#secao").html('<option value="0">Carregando...</option>');
//
//        $.post(PORTAL_URL + "combo/regional.php",
//                {zona: $(this).val()},
//                function (valor) {
//                    $("select#regional").html(valor);
//                    $("select#regional").select2();
//                });
//
//        $.post(PORTAL_URL + "combo/secao.php",
//                {zona: $(this).val()},
//                function (valor) {
//                    $("select#secao").html(valor);
//                    $("select#secao").select2();
//                });
//    });
//
//    $("select#regional").change(function () {
//        $("select#local").html('<option value="0">Carregando...</option>');
//        $("select#bairro").html('<option value="0">Carregando...</option>');
//
//        $.post(PORTAL_URL + "combo/local.php",
//                {regional: $(this).val()},
//                function (valor) {
//                    $("select#local").html(valor);
//                    $("select#local").select2();
//                });
//
//        $.post(PORTAL_URL + "combo/bairro.php",
//                {regional: $(this).val()},
//                function (valor2) {
//                    $("select#bairro").html(valor2);
//                    $("select#bairro").select2();
//                });
//    });
//
//    $("select#bairro").change(function () {
//        $("select#local").html('<option value="0">Carregando...</option>');
//        $.post(PORTAL_URL + "combo/bairro_local.php",
//                {bairro: $(this).val()},
//                function (valor) {
//                    $("select#local").html(valor);
//                    $("select#local").select2();
//                });
//    });
//
//    $("select#local").change(function () {
//        $("select#secao").html('<option value="0">Carregando...</option>');
//        $.post(PORTAL_URL + "combo/local_secao.php",
//                {local: $(this).val()},
//                function (valor) {
//                    $("select#secao").html(valor);
//                    $("select#secao").select2();
//                });
//    });

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
//----------------------------------------------------------------------------- 
    $('form#fiscal').submit(function () {

        if (formulario_validator("")) {

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
                    postToURL(PORTAL_URL + 'view/fiscal/fiscal');
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
    var bairro = $("input#guard_bairro_2").val();

    $("#submit").attr("disabled", true);

    swal({
        title: "Deseja mesmo remover este fiscal desta seção?",
        text: "Obs: Caso escolha remover, o fiscal vai ficar disponível novamente!",
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
                postToURL(PORTAL_URL + 'view/fiscal/fiscal/?regional=' + regional + '&bairro=' + bairro + '&local=' + local);
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
function carregar_dados(zona, secao, regional, local, bairro) {
    $("input#guard_secao").val(secao);
    $("input#guard_regional").val(regional);
    $("input#guard_local").val(local);
    $("input#guard_bairro").val(bairro);
    $("input#guard_zona").val(zona);
}
//------------------------------------------------------------------------------------------------------
window.onload = function () {
    $("#div_loader_2").hide();
};
//------------------------------------------------------------------------------------------------------
