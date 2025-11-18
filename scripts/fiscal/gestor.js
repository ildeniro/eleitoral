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

    $("select#spot").change(function () {
        $("form#filtros").submit();
    });

//----------------------------------------------------------------------------- 
    $('form#fiscal').submit(function () {

        if (formulario_validator("")) {

            $("#submit").attr("disabled", true);

            var pessoas = $('#pessoas').val();
            var spot = $("input#guard_spot").val();

            $.post(PORTAL_URL + "dao/fiscal/add_gestor", {pessoas: pessoas, spot: spot}, function (data) {
                if (isNaN(data)) {
                    swal({
                        title: "Formulário do Gestor",
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
                    postToURL(PORTAL_URL + 'view/fiscal/gestor/?spot=' + spot);
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
    
    var spot = $("input#guard_spot_2").val();

    swal({
        title: "Deseja mesmo remover este gestor desta local?",
        text: "Obs: Caso escolha remover, o gestor vai ficar disponível novamente!",
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
                    title: "Formulário do Gestor",
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
                postToURL(PORTAL_URL + 'view/fiscal/gestor/?spot=' + spot);
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
function carregar_dados(spot) {
    $("input#guard_spot").val(spot);
}
//------------------------------------------------------------------------------------------------------