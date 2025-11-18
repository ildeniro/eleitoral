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

    $("select#municipio").change(function () {
        $("select#local").html('<option value="0">Carregando...</option>');
        $.post(PORTAL_URL + "combo/local_municipio.php",
                {municipio: $(this).val()},
                function (valor) {
                    $("select#local").html(valor);
                    $("select#local").select2();
                });
    });

    $("select#municipio").change(function () {
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
            var municipio = $("input#guard_municipio").val();
            var local = $("input#guard_local").val();

            $.post(PORTAL_URL + "dao/fiscal/add_coordenador_municipio", {pessoas: pessoas, secao: secao, municipio: municipio, local: local}, function (data) {
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
                    postToURL(PORTAL_URL + 'view/fiscal/coordenador_municipal/?municipio=' + municipio + '&local=' + local);
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
    var municipio = $("input#guard_municipio_2").val();
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
                postToURL(PORTAL_URL + 'view/fiscal/coordenador_municipal/?municipio=' + municipio + '&local=' + local);
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
function carregar_dados(secao, municipio, local) {
    $("input#guard_secao").val(secao);
    $("input#guard_municipio").val(municipio);
    $("input#guard_local").val(local);
}
//------------------------------------------------------------------------------------------------------