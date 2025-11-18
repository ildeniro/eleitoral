//------------------------------------------------------------------------------------------------------
//Cadastrar
$(document).ready(function () {

    $('#form_usuario').submit(function () {

        if (formulario_validator("")) {

            $("#submit").attr("disabled", true);

            var id = $('#id').val();
            var nome = $('#nome').val();
            var email = $('#email').val();
            var login = $('#login').val();
            var senha = $('#senha').val();
            var nivel = $('#my-select').val();
            var contato = $('#contato').val();

            $.post(PORTAL_URL + "dao/usuarios/cadastrar", {id: id, nivel: nivel, nome: nome, email: email, login: login, senha: senha, contato: contato}, function (data) {
                if (isNaN(data)) {
                    swal({
                        title: "Formulário de Usuário",
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
                        title: "Formulário de Usuário",
                        text: "Ação realizada com sucesso!",
                        type: "success",
                        confirmButtonClass: "btn btn-success",
                        confirmButtonText: "Ok"
                    }).then(function () {
                        postToURL(PORTAL_URL + 'view/usuarios/index');
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
    var senha = $("#senha").val();
    var conf_senha = $("#confirmar_senha").val();
    var login = $("#login").val();
    var contato = $("#contato").val();

    var element = null;

    //LIMPA MENSAGENS DE ERRO
    $('label.error').each(function () {
        $(this).remove();
    });

    if (obj.tipo == "" || obj.tipo == null) {//VALIDAÇÃO SEM BANCO DE DADOS
        //VERIFICANDO SE O CAMPO LOGIN FOI INFORMADO
        if (senha == "" && id == "") {
            $('div#div_senha').after('<label id="erro_senha" class="error">A senha é obrigatório.</label>');
            valido = false;
            element = $('div#div_senha');
        } else if (conf_senha == "" && id == "") {
            $('div#div_conf_senha').after('<label id="erro_conf_senha" class="error">Confirmação de senha é obrigatório.</label>');
            valido = false;
            element = $('div#div_conf_senha');
        } else {
            //VERIFICANDO SE O CAMPO LOGIN FOI INFORMADO
            if (senha != conf_senha && senha != "" && conf_senha != "") {
                valido = false;

                $('div#div_senha').after('<label id="erro_senha" class="error">A senha e confirmação de senha não coincidem.</label>');
                $('div#div_conf_senha').after('<label id="erro_conf_senha" class="error">A senha e confirmação de senha não coincidem.</label>');

                element = $('div#div_conf_senha');
            }
        }

        if (login == "") {
            $('div#div_login').after('<label id="erro_login" class="error">Login é obrigatório.</label>');
            valido = false;
            element = $('div#div_login');
        }

        //VERIFICANDO SE O CAMPO NOME FOI INFORMADO
        if (contato == "") {
            $('div#div_contato').after('<label id="erro_contato" class="error">Contato é obrigatório.</label>');
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