//------------------------------------------------------------------------------------------------------
//Cadastrar
$(document).ready(function () {

    $('select').each(function () {
        $(this).select2();
        $('.select2').attr('style', 'width: 100%');
    });

    $('form#form_secao').submit(function () {

        if (formulario_validator("")) {

            $("#submit").attr("disabled", true);

            var id = $('#id').val();
            var secao_numero = $('#secao').val();
            var local = $('#local').val();
            var regional = $('#regional').val();
            var regiao = $('#regiao').val();
            var endereco = $('#endereco').val();
            var bairro = $('#bairro').val();
            var longitude = $('#longitude').val();
            var latitude = $('#latitude').val();
            var aptos = $('#qtd').val();
            var agregacao = $('#agregacao').val();

            $.post(PORTAL_URL + "dao/secoes/cadastrar", {id: id, regiao: regiao, secao_numero: secao_numero, local: local, regional: regional, endereco: endereco, bairro: bairro, longitude: longitude, latitude: latitude, aptos: aptos, agregacao: agregacao}, function (data) {
                if (isNaN(data)) {
                    swal({
                        title: "Formulário da Seção",
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
                    swal({
                        title: "Formulário da Seção",
                        text: "Ação realizada com sucesso!",
                        type: "success",
                        confirmButtonClass: "btn btn-success",
                        confirmButtonText: "Ok"
                    }).then(function () {
                        postToURL(PORTAL_URL + 'view/secoes/index');
                    });
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
//VALIDATOR DO LOGIN
function formulario_validator(obj) {
    var valido = true;

    var local = $('#local').val();
    var regional = $('#regional').val();
    var regiao = $('#regiao').val();
    var endereco = $('#endereco').val();
    var bairro = $('#bairro').val();
    var longitude = $('#longitude').val();
    var latitude = $('#latitude').val();
    var aptos = $('#qtd').val();
    var municipio = $('#municipio').val();

    var element = null;

    //LIMPA MENSAGENS DE ERRO
    $('label.error').each(function () {
        $(this).remove();
    });

    if (obj.tipo == "" || obj.tipo == null) {//VALIDAÇÃO SEM BANCO DE DADOS

        //VERIFICANDO SE O CAMPO NOME FOI INFORMADO

        if (aptos == "") {
            $('div#div_qtd').after('<label id="erro_qtd" class="error">Quantidade de aptos é obrigatório.</label>');
            valido = false;
            element = $('div#div_qtd');
        }

        if (longitude == "") {
            $('div#div_longitude').after('<label id="erro_longitude" class="error">Longitude é obrigatório.</label>');
            valido = false;
            element = $('div#div_longitude');
        }

        if (latitude == "") {
            $('div#div_latitude').after('<label id="erro_latitude" class="error">Latitude é obrigatório.</label>');
            valido = false;
            element = $('div#div_latitude');
        }

        if (regiao == "") {
            $('div#div_regiao').after('<label id="erro_regiao" class="error">Região é obrigatório.</label>');
            valido = false;
            element = $('div#div_regiao');
        }

        if (municipio == 94 && regional == "") {
            $('div#div_regional').after('<label id="erro_regional" class="error">Regional é obrigatório.</label>');
            valido = false;
            element = $('div#div_regional');
        }

        if (bairro == "") {
            $('div#div_bairro').after('<label id="erro_bairro" class="error">Bairro é obrigatório.</label>');
            valido = false;
            element = $('div#div_bairro');
        }

        if (endereco == "") {
            $('div#div_endereco').after('<label id="erro_endereco" class="error">Endereço é obrigatório.</label>');
            valido = false;
            element = $('div#div_endereco');
        }

        if (local == "") {
            $('div#div_local').after('<label id="erro_local" class="error">Local votação é obrigatório.</label>');
            valido = false;
            element = $('div#div_local');
        }

    } else if (obj.tipo == "nome") {//VALIDAÇÃO COM BANCO DE DADOS
        $('div#div_nome_fiscal').after('<label id="erro_nome_fiscal" class="error">' + obj.retorno + '</label>');
        valido = false;
        element = $('div#div_nome_fiscal');
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