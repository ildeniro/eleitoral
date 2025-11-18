//------------------------------------------------------------------------------------------------------
//Cadastrar
$(document).ready(function () {

    $('#form_indicador').submit(function () {

        if (formulario_validator("")) {

            $("#submit").attr("disabled", true);

            var id = $('#id').val();
            var nome = $('#nome').val();
            var funcao = $('#funcao').val();
            var contato = $('#contato').val();

            $.post(PORTAL_URL + "dao/indicadores/cadastrar", {id: id, funcao: funcao, nome: nome, contato: contato}, function (data) {
                if (isNaN(data)) {
                    swal({
                        title: "Formulário de Indicador",
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
                        title: "Formulário de Indicador",
                        text: "Ação realizada com sucesso!",
                        type: "success",
                        confirmButtonClass: "btn btn-success",
                        confirmButtonText: "Ok"
                    }).then(function () {
                        postToURL(PORTAL_URL + 'view/indicadores/index');
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
    var nome = $("#nome").val();
    var funcao = $("#funcao").val();
    var contato = $("#contato").val();

    var element = null;

    //LIMPA MENSAGENS DE ERRO
    $('label.error').each(function () {
        $(this).remove();
    });

    if (obj.tipo == "" || obj.tipo == null) {//VALIDAÇÃO SEM BANCO DE DADOS
        //VERIFICANDO SE O CAMPO LOGIN FOI INFORMADO

        if (funcao == "") {
            $('div#div_funcao').after('<label id="erro_funcao" class="error">Função é obrigatório.</label>');
            valido = false;
            element = $('div#div_funcao');
        }

        //VERIFICANDO SE O CAMPO NOME FOI INFORMADO
        if (contato == "") {
            $('div#div_contato').after('<label id="erro_contato" class="error">Telefone é obrigatório.</label>');
            valido = false;
            element = $('div#div_contato');
        }

        //VERIFICANDO SE O CAMPO NOME FOI INFORMADO
        if (nome == "") {
            $('div#div_nome').after('<label id="erro_nome" class="error">Nome é obrigatório.</label>');
            valido = false;
            element = $('div#div_nome');
        }

    } else if (obj.tipo == "cpf") {//VALIDAÇÃO COM BANCO DE DADOS
        $('div#div_cpf').after('<label id="erro_cpf" class="error">' + obj.retorno + '</label>');
        valido = false;
        element = $('div#div_cpf');
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