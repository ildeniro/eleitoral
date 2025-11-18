//------------------------------------------------------------------------------------------------------
//Cadastrar
$(document).ready(function () {

    $('select').each(function () {
        $(this).select2();
        $('.select2').attr('style', 'width: 100%');
    });

    $('a#clonar').click(function () {

        var guard_lat = "";
        var guard_long = "";

        $('div#div_clone').find("div#sub_div_clone").find("div.col-md-4").find("a#clonar").hide();
        $('div#div_clone').find("div#sub_div_clone").find("div.col-md-4").find("a#remover").show();

        guard_lat = $('div#div_clone').find("div#sub_div_clone").find("div.col-md-4").find("div#div_latitude").find("input#latitude").val();
        guard_long = $('div#div_clone').find("div#sub_div_clone").find("div.col-md-4").find("div#div_longitude").find("input#longitude").val();

        $('div#div_clone').find("div#sub_div_clone").find("div.col-md-4").find("input#latitude").val("");
        $('div#div_clone').find("div#sub_div_clone").find("div.col-md-4").find("input#longitude").val("");

        var clone = $('div#div_clone').find('div#sub_div_clone:last').clone();

        $('div#div_clonados').append(clone);

        $('div#div_clone').find("div#sub_div_clone").find("div.col-md-4").find("a#clonar").show();
        $('div#div_clone').find("div#sub_div_clone").find("div.col-md-4").find("a#remover").hide();

        $('div#div_clone').find("div#sub_div_clone").find("div.col-md-4").find("div#div_latitude").find("input#latitude").val(guard_lat);
        $('div#div_clone').find("div#sub_div_clone").find("div.col-md-4").find("div#div_longitude").find("input#longitude").val(guard_long);

    });

    $('form#form_spot').submit(function () {

        if (formulario_validator("")) {

            $("#submit").attr("disabled", true);

            window.onbeforeunload = null;
            projetouniversal.util.getjson({
                url: PORTAL_URL + "dao/spots/cadastrar",
                type: "POST",
                data: $('#form_spot').serialize(),
                enctype: 'multipart/form-data',
                success: onSuccessSend,
                error: onError
            });
            return false;
        } else {
            return false;
        }
    });

});
//------------------------------------------------------------------------------F
function onSuccessSend(obj) {
    swal({
        title: "Formulário da Spot",
        text: "Ação realizada com sucesso!",
        type: "success",
        confirmButtonClass: "btn btn-success",
        confirmButtonText: "Ok"
    }).then(function () {
        postToURL(PORTAL_URL + 'view/spots/index');
    });
}
//------------------------------------------------------------------------------
/* ERRO AO ENVIAR AJAX */
function onError(args) {
    swal({
        title: "Formulário da Spot",
        html: args.retorno,
        type: "error",
        showCancelButton: false,
        confirmButtonColor: "#8CD4F5",
        confirmButtonText: "OK"
    });
    $("#submit").attr("disabled", false);
    return false;
}
//------------------------------------------------------------------------------
//REMOVER OBJETO
function remover(obj) {
    $(obj).parents("div#sub_div_clone").remove();
}
//------------------------------------------------------------------------------
//VALIDATOR DO LOGIN
function formulario_validator(obj) {
    var valido = true;

    var nome = $('#nome').val();

    var element = null;

    //LIMPA MENSAGENS DE ERRO
    $('label.error').each(function () {
        $(this).remove();
    });

    if (obj.tipo == "" || obj.tipo == null) {//VALIDAÇÃO SEM BANCO DE DADOS

        if (nome == "") {
            $('div#div_nome').after('<label id="erro_nome" class="error">Nome é obrigatório.</label>');
            valido = false;
            element = $('div#div_nome');
        }

    } else if (obj.tipo == "nome") {//VALIDAÇÃO COM BANCO DE DADOS
        $('div#div_nome').after('<label id="erro_nome" class="error">' + obj.retorno + '</label>');
        valido = false;
        element = $('div#div_nome');
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