//------------------------------------------------------------------------------------------------------
//Cadastrar
$(document).ready(function () {

    //COMBO LOCAL E BAIRRO
    $("select#local_votacao").change(function () {
        $("select#bairro").html('<option value="0">Carregando...</option>');
        $.post(PORTAL_URL + "combo/local_bairro.php",
                {local: $(this).val()},
                function (valor) {
                    $("select#bairro").html(valor);
                    $("select#bairro").select2();
                });
    });

    //COMBO LOCAL E BAIRRO
    $("select#bairro").change(function () {
        $("select#local_votacao").html('<option value="0">Carregando...</option>');
        $.post(PORTAL_URL + "combo/bairro_local_2.php",
                {bairro: $(this).val()},
                function (valor) {
                    $("select#local_votacao").html(valor);
                    $("select#local_votacao").select2();
                });
    });

    $("#add_outros").click(function () {
        $("select#demandante").val("");
        $("select#demandante").select2();
        $("div#div_demandante").hide();
        $("div#div_outros").show();
        $("div#div_outros_contato").show();

        $("#add_outros").hide();
        $("#lista_demandante").show();

    });

    $("#lista_demandante").click(function () {
        $("input#outros").val("");
        $("input#outros_contato").val("");
        $("div#div_demandante").show();
        $("div#div_outros").hide();
        $("div#div_outros_contato").hide();

        $("#add_outros").show();
        $("#lista_demandante").hide();

    });

    $('#form_usuario').submit(function () {

        if (formulario_validator("")) {

            $("#submit").attr("disabled", true);

            var id = $('#id').val();
            var descricao_ocorrencia = $('#descricao_ocorrencia').val();
            var local_votacao = $('#local_votacao').val();
            var bairro = $('#bairro').val();
            var situacao = $('#situacao').val();

            var botao = document.getElementById('add_outros');

            var demandante = botao.style.display === 'block' || botao.style.display === '' ? $('#demandante').val() : "";
            var outros = botao.style.display !== 'block' && botao.style.display !== '' ? $('#outros').val() : "";
            var outros_contato = botao.style.display !== 'block' && botao.style.display !== '' ? $('#outros_contato').val() : "";

            $.post(PORTAL_URL + "dao/fiscal/cadastrar-ocorrencia", {id: id, outros: outros, outros_contato: outros_contato, demandante: demandante, descricao_ocorrencia: descricao_ocorrencia, local_votacao: local_votacao, bairro: bairro, situacao: situacao}, function (data) {
                if (isNaN(data)) {
                    swal({
                        title: "Formulário de Ocorrência",
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
                        title: "Formulário de Ocorrência",
                        text: "Ação realizada com sucesso!",
                        type: "success",
                        confirmButtonClass: "btn btn-success",
                        confirmButtonText: "Ok"
                    }).then(function () {
                        postToURL(PORTAL_URL + 'view/fiscal/ocorrencias');
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
    var id = $("#id").val();
    var demandante = $("#demandante").val();
    var descricao_ocorrencia = $("#descricao_ocorrencia").val();
    var bairro = $("#bairro").val();

    var outros = $('#outros').val();
    var outros_contato = $('#outros_contato').val();

    var botao = document.getElementById('add_outros');

    var element = null;

    //LIMPA MENSAGENS DE ERRO
    $('label.error').each(function () {
        $(this).remove();
    });

    if (obj.tipo == "" || obj.tipo == null) {//VALIDAÇÃO SEM BANCO DE DADOS
        if (bairro == "") {
            $('div#div_bairro').after('<label id="erro_bairro" class="error">Bairro é obrigatório.</label>');
            valido = false;
            element = $('div#div_bairro');
        }

        //VERIFICANDO SE O CAMPO NOME FOI INFORMADO
        if (descricao_ocorrencia == "") {
            $('div#div_descricao_ocorrencia').after('<label id="erro_descricao_ocorrencia" class="error">Descrição é obrigatório.</label>');
            valido = false;
            element = $('div#div_descricao_ocorrencia');
        }

        if (botao.style.display === 'block' || botao.style.display === '') {
            //VERIFICANDO SE O CAMPO NOME FOI INFORMADO
            if (demandante == "") {
                $('div#div_demandantes').after('<label id="erro_demandantes" class="error">Demandante é obrigatório.</label>');
                valido = false;
                element = $('div#div_demandantes');
            }
        } else {

            //VERIFICANDO SE O CAMPO NOME FOI INFORMADO
            if (outros_contato == "") {
                $('div#div_outros_contatos').after('<label id="erro_outros_contatos" class="error">Contato é obrigatório.</label>');
                valido = false;
                element = $('div#div_outros_contatos');
            }

            //VERIFICANDO SE O CAMPO NOME FOI INFORMADO
            if (outros == "") {
                $('div#div_outros_demandante').after('<label id="erro_outros_demandante" class="error">Nome é obrigatório.</label>');
                valido = false;
                element = $('div#div_outros_demandante');
            }
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