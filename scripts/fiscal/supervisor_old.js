//------------------------------------------------------------------------------------------------------
//Cadastrar
$(document).ready(function () {

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
        $("form#filtros").submit();
    });

    $("select#bairro").change(function () {
        $("form#filtros").submit();
    });

    $("select#local").change(function () {
        $("form#filtros").submit();
    });

    $("select#regional").change(function () {
        $("select#bairro").html('<option value="0">Carregando...</option>');
        $.post(PORTAL_URL + "combo/bairro.php",
                {regional: $(this).val()},
                function (valor) {
                    $("select#bairro").html(valor);
                    $("select#bairro").select2();
                });
    });
//----------------------------------------------------------------------------- 
    $('form#fiscal').submit(function () {

        if (formulario_validator("")) {

            $("#submit").attr("disabled", true);

            var pessoas = $('#pessoas').val();
            var regional = $("input#guard_regional").val();
            var bairro = $("input#guard_bairro").val();
            var local = $("input#guard_local").val();

            $.post(PORTAL_URL + "dao/fiscal/add_supervisor", {regional: regional, pessoas: pessoas, bairro: bairro, local: local}, function (data) {
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
                    postToURL(PORTAL_URL + 'view/fiscal/supervisor/?regional=' + regional + '&bairro=' + bairro + '&local=' + local);
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
    var bairro = $("input#guard_bairro_2").val();
    var local = $("input#guard_local_2").val();

    swal({
        title: "Deseja mesmo remover este supervisor deste bairro?",
        text: "Obs: Caso escolha remover, o supervisor vai ficar disponível novamente!",
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
                postToURL(PORTAL_URL + 'view/fiscal/supervisor/?regional=' + regional + '&bairro=' + bairro + '&local=' + local);
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
function carregar_dados(regional, bairro, local) {
    $("input#guard_bairro").val(bairro);
    $("input#guard_regional").val(regional);
    $("input#guard_local").val(local);
}
//------------------------------------------------------------------------------------------------------