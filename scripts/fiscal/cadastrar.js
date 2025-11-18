//------------------------------------------------------------------------------------------------------
//Cadastrar
$(document).ready(function () {

    //COMBO ESTADO E MUNICIPIO
    $("select#indicacao").change(function () {
        if ($(this).val() != "") {
            $("div#div_indicador").show();
        } else {
            $("div#div_indicador").hide();
        }
    });

    $("#treinamento_sim").click(function () {
        $("div#div_data_participacao").show();
        $("div#div_motivo").hide();
        $("textarea#motivo").val('');

        //LIMPA MENSAGENS DE ERRO
        $('label.error').each(function () {
            $(this).remove();
        });

    });

    $("#treinamento_nao").click(function () {
        $("div#div_motivo").show();
        $("div#div_data_participacao").hide();
        $("input#data_participacao").val('');

        //LIMPA MENSAGENS DE ERRO
        $('label.error').each(function () {
            $(this).remove();
        });

    });

    $("#cpf_fiscal").change(function () {
        var cpf = $(this).val();

        projetouniversal.util.getjson({
            url: PORTAL_URL + "dao/fiscal/carregar_dados",
            type: "POST",
            data: {cpf: cpf},
            enctype: 'multipart/form-data',
            success: onSuccessSend3,
            error: onError3
        });

        return false;

    });

    //COMBO ESTADO E MUNICIPIO
    $("select#estado").change(function () {
        $("select#cidade").html('<option value="0">Carregando...</option>');
        $.post(PORTAL_URL + "combo/estado_cidades.php",
                {estado: $(this).val()},
                function (valor) {
                    $("select#cidade").html(valor);
                    $("select#cidade").select2();
                });
    });

    //COMBO MUNICIPIO E BAIRRO
    $("select#cidade").change(function () {

        if ($(this).val() == 94) {
            $("div#div_bairro").show();
        } else {
            $("div#div_bairro").hide();
        }

        $("select#bairro").html('<option value="0">Carregando...</option>');
        $.post(PORTAL_URL + "combo/bairro_municipio.php",
                {municipio: $(this).val()},
                function (valor) {
                    $("select#bairro").html(valor);
                    $("select#bairro").select2();
                });
    });

    $("select#zona").change(function () {
        $("select#secao").html('<option value="0">Carregando...</option>');
        $.post(PORTAL_URL + "combo/secao_apuracao_2024_fiscal.php",
                {zona: $(this).val()},
                function (valor) {
                    $("select#secao").html(valor);
                    $("select#secao").select2();
                });
    });

    $("select#secao").change(function () {

        var zona = $("select#zona").val();

        $.post(PORTAL_URL + "combo/local_2024.php",
                {secao: $(this).val(), zona: zona},
                function (valor) {
                    $("div#div_local").show();
                    $("div#div_local_texto").html(valor);
                });
    });

    $("select#tipo").change(function () {
        var tipo = $(this).val();

        if (tipo != "" && tipo > 0) {
            $("div#div_fiscal").show();

            if (tipo == 1) {
                $("div#div_informacoes_bancariais").show();
                $("div#div_dados_bancarios").show();

                $.post(PORTAL_URL + "combo/fiscal.php",
                        {tipo: tipo},
                        function (valor) {
                            $("select#funcao_id").html(valor);
                        });

            } else if (tipo == 2) {

                $.post(PORTAL_URL + "combo/fiscal.php",
                        {tipo: tipo},
                        function (valor2) {
                            $("select#funcao_id").html(valor2);
                        });

                $("div#div_informacoes_bancariais").hide();
                $("div#div_dados_bancarios").hide();
            }

        } else {
            $("div#div_fiscal").hide();
        }

    });

    $('select').each(function () {
        $(this).select2();
        $('.select2').attr('style', 'width: 100%');
    });

    $("select#zona").change(function () {
        $("select#secao").html('<option value="0">Carregando...</option>');
        $.post(PORTAL_URL + "combo/secao.php",
                {zona: $(this).val()},
                function (valor) {
                    $("select#secao").html(valor);
                    $("select#secao").select2();
                });
    });

    $("select#secao").change(function () {

        var tipo = $(this).find('option:selected').attr('tipo');
        var zona = $("select#zona").val();

        $("select#local").html('<option value="0">Carregando...</option>');
        $.post(PORTAL_URL + "combo/local2.php",
                {secao: $(this).val(), zona: zona},
                function (valor) {
                    $("select#local").html(valor);
                    $("select#local").select2();

                    if (tipo == "") {
                        $("input#secao_agregada").val("");
                        $("div#agregada").hide();
                    } else {
                        $("input#secao_agregada").val(tipo);
                        $("div#agregada").show();
                    }

                });
    });

    $("#salvar_modal").click(function () {

        var nome = $("#modal_nome").val();
        var funcao = $("#modal_funcao").val();
        var telefone = $("#modal_telefone").val();

        if (formulario_validator_modal(nome, funcao, telefone)) {

            var selecionados = $('select#indicacao').val();

            $.post(PORTAL_URL + "combo/add_indicacao.php",
                    {selecionados: selecionados, nome: nome, funcao: funcao, telefone: telefone},
                    function (valor) {
                        if (valor == "nome" || valor == "telefone") {
                            swal({
                                title: "Referência de Indicação",
                                html: "O " + valor + " informado já existe no sistema",
                                type: "error",
                                showCancelButton: false,
                                confirmButtonColor: "#8CD4F5",
                                confirmButtonText: "OK",
                                closeOnConfirm: false
                            });
                            return false;
                        } else {
                            swal({
                                title: "Referência de Indicação",
                                text: "Ação realizada com sucesso!",
                                type: "success",
                                confirmButtonClass: "btn btn-success",
                                confirmButtonText: "Ok"
                            }).then(function () {
                                $("select#indicacao").html(valor);
                                $("select#indicacao").select2();
                                $('div#exampleModal').modal('hide');
                                $('div.modal-backdrop').hide();
                            });
                        }
                    });
        } else {
            return false;
        }
    });

    $('form#form_fiscalizacao').submit(function () {

        if (formulario_validator("")) {

            $("#submit").attr("disabled", true);

            var id = $('#id').val();
            var mae = $('#mae').val();
            var nome_fiscal = $('#nome_fiscal').val();
            var cpf_fiscal = $('#cpf_fiscal').val();
            var celular_fiscal = $('#celular_fiscal').val();
            var titulo_fiscal = $('#titulo_fiscal').val();
            var endereco_fiscal = $('#endereco_fiscal').val();
            var numero = $('#numero').val();
            var bairro = $('#bairro').val();
            var pai = $('#pai').val();
            var indicacao = $('#indicacao').val();
            var whatsapp = 1;

            var funcao_id = $("#funcao:checked").val();
            var deficiencia = $("#condicao:checked").val();

            var nascimento = $('#nascimento').val();
            var zona = $('#zona').val();
            var secao = $('#secao').val();
            var local = $("div#div_local_texto").html();

            var tipo = 2;//Sem Ajuda de Custo Pré Definido

            var rg = $('#rg').val();
            var orgao_emissor = $('#orgao_emissor').val();
            var cep = $('#cep').val();
            var estado = 1;//Acre
            var cidade = 94;//Rio Branco
            var sexo = $('#sexo').val();

            var treinamento = $("#treinamento_sim:checked").val() == 1 ? 1 : ($("#treinamento_nao:checked").val() == 0 ? 0 : '');
            var data_participacao = $('#data_participacao').val();
            var motivo = $('#motivo').val();
            
            var distribuicao  = $('#distribuicao').val();

            $.post(PORTAL_URL + "dao/fiscal/cadastrar", {id: id, distribuicao: distribuicao, treinamento: treinamento, data_participacao: data_participacao, motivo: motivo, sexo: sexo, tipo: tipo, rg: rg, orgao_emissor: orgao_emissor, cep: cep, estado: estado, cidade: cidade, zona: zona, secao: secao, local: local, nascimento: nascimento, indicacao: indicacao, whatsapp: whatsapp, deficiencia: deficiencia, funcao_id: funcao_id, pai: pai, mae: mae, nome_fiscal: nome_fiscal, cpf_fiscal: cpf_fiscal, celular_fiscal: celular_fiscal, titulo_fiscal: titulo_fiscal, endereco_fiscal: endereco_fiscal, numero: numero, bairro: bairro}, function (data) {
                if (isNaN(data)) {
                    swal({
                        title: "Formulário do Voluntário(a)",
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
                        title: "Formulário do Voluntário(a)",
                        text: "Ação realizada com sucesso!",
                        type: "success",
                        confirmButtonClass: "btn btn-success",
                        confirmButtonText: "Ok"
                    }).then(function () {
                        postToURL(PORTAL_URL + 'view/fiscal');
                        //postToURL(PORTAL_URL + 'view/fiscal/anexar_arquivos');
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
function formulario_validator_modal(nome, funcao, telefone) {
    var valido = true;
    var element = null;

    //LIMPA MENSAGENS DE ERRO
    $('label.error').each(function () {
        $(this).remove();
    });

    if (telefone == "") {
        $('div#div_modal_telefone').after('<label id="erro_modal_telefone" class="error">Telefone é obrigatório.</label>');
        valido = false;
        element = $('div#div_modal_telefone');
    }

    if (funcao == "") {
        $('div#div_modal_funcao').after('<label id="erro_modal_funcao" class="error">Função é obrigatório.</label>');
        valido = false;
        element = $('div#div_modal_funcao');
    }

    if (nome == "") {
        $('div#div_modal_nome').after('<label id="erro_modal_nome" class="error">Nome é obrigatório.</label>');
        valido = false;
        element = $('div#div_modal_nome');
    }

    if (element != null) {
        var topPosition = element.offset().top - 135;
        $('html, body').animate({
            scrollTop: topPosition
        }, 800);
    }
    return valido;
}
//------------------------------------------------------------------------------
//VALIDATOR DO LOGIN
function formulario_validator(obj) {
    var valido = true;
    
    var id = $('#id').val();
    var funcao = $('#funcao_id').val();
    var nome_fiscal = $('#nome_fiscal').val();
    var cpf = $('#cpf_fiscal').val();
    var endereco_fiscal = $('#endereco_fiscal').val();
    var bairro = $('#bairro').val();
    var estado = $('#estado').val();
    var cidade = $('#cidade').val();
    var zona = $('#zona').val();
    var secao = $('#secao').val();
    var celular_fiscal = $('#celular_fiscal').val();
    var nascimento = $('#nascimento').val();
    var mae = $('#mae').val();

    var treinamento = $("#treinamento_sim:checked").val() == 1 ? 1 : ($("#treinamento_nao:checked").val() == 0 ? 0 : '');
    var data_participacao = $('#data_participacao').val();
    var motivo = $('#motivo').val();
    
    var distribuicao  = $('#distribuicao').val();

    var element = null;

    //LIMPA MENSAGENS DE ERRO
    $('label.error').each(function () {
        $(this).remove();
    });

    if (obj.tipo == "" || obj.tipo == null) {//VALIDAÇÃO SEM BANCO DE DADOS

        if (id != "" && treinamento != "" || id != "" && treinamento == 0 || id != "" && treinamento == 1) {
            if (treinamento == 1 && data_participacao == "") {
                $('div#div_data_participacao').after('<label id="erro_data_participacao" class="error treinamento-padding">Data é obrigatório.</label>');
                valido = false;
                element = $('div#div_data_participacao');
            } else if (treinamento == 0 && motivo == "") {
                $('div#div_motivo').after('<label id="erro_motivo" class="error treinamento-padding">Motivo é obrigatório.</label>');
                valido = false;
                element = $('div#div_motivo');
            }
        }

        if (cidade == 94 && bairro == "") {
            $('div#div_bairro').after('<label id="erro_bairro" class="error">Bairro é obrigatório.</label>');
            valido = false;
            element = $('div#div_bairro');
        }

        if (endereco_fiscal == "") {
            $('div#div_endereco_fiscal').after('<label id="erro_endereco_fiscal" class="error">Endereço Fiscal é obrigatório.</label>');
            valido = false;
            element = $('div#div_endereco_fiscal');
        }

//        //VERIFICANDO SE O CAMPO NOME FOI INFORMADO
//        if (indicacao == "") {
//            $('div#div_indicacao').after('<label id="erro_indicacao" class="error">Indicação é obrigatório.</label>');
//            valido = false;
//            element = $('div#div_indicacao');
//        }

//        //VERIFICANDO SE O CAMPO NOME FOI INFORMADO
//        if (local == "") {
//            $('div#div_local').after('<label id="erro_local" class="error">Local de votação é obrigatório.</label>');
//            valido = false;
//            element = $('div#div_local');
//        }

        //VERIFICANDO SE O CAMPO NOME FOI INFORMADO
        if (secao == "" && distribuicao == false) {
            $('div#div_secao').after('<label id="erro_secao" class="error">Seção é obrigatório.</label>');
            valido = false;
            element = $('div#div_secao');
        }

        //VERIFICANDO SE O CAMPO NOME FOI INFORMADO
        if (zona == "" && distribuicao == false) {
            $('div#div_zona').after('<label id="erro_zona" class="error">Zona é obrigatório.</label>');
            valido = false;
            element = $('div#div_zona');
        }

        if (cidade == "" && distribuicao == false) {
            $('div#div_cidade').after('<label id="erro_cidade" class="error">Cidade é obrigatório.</label>');
            valido = false;
            element = $('div#div_cidade');
        }

        if (estado == "" && distribuicao == false) {
            $('div#div_estado').after('<label id="erro_estado" class="error">Estado é obrigatório.</label>');
            valido = false;
            element = $('div#div_estado');
        }

        /*if (tipo == "") {
         $('div#div_tipo').after('<label id="erro_tipo" class="error">Tipo é obrigatório.</label>');
         valido = false;
         element = $('div#div_tipo');
         }*/

        //VERIFICANDO SE O CAMPO NOME FOI INFORMADO
        if (funcao == "") {
            $('div#div_funcao_id').after('<label id="erro_funcao_id" class="error">Função é obrigatório.</label>');
            valido = false;
            element = $('div#div_funcao_id');
        }

        if (celular_fiscal == "") {
            $('div#div_celular_fiscal').after('<label id="erro_celular_fiscal" class="error">Contato é obrigatório.</label>');
            valido = false;
            element = $('div#div_celular_fiscal');
        }

        if (mae == "") {
            $('div#div_mae').after('<label id="erro_mae" class="error">Nome da mãe é obrigatório.</label>');
            valido = false;
            element = $('div#div_mae');
        }

        if (nascimento == "") {
            $('div#div_nascimento').after('<label id="erro_nascimento" class="error">Data é obrigatório.</label>');
            valido = false;
            element = $('div#div_nascimento');
        }

        if (nome_fiscal == "") {
            $('div#div_nome_fiscal').after('<label id="erro_nome_fiscal" class="error">Nome é obrigatório.</label>');
            valido = false;
            element = $('div#div_nome_fiscal');
        }

        if (cpf == "") {
            $('div#div_cpf_fiscal').after('<label id="erro_cpf_fiscal" class="error">CPF é obrigatório.</label>');
            valido = false;
            element = $('div#div_cpf_fiscal');
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
//------------------------------------------------------------------------------
function onError3(args) {
    $("#enviar").show();
    swal({
        title: "Formulário de Doações",
        text: "" + args.retorno + "",
        type: "error",
        confirmButtonClass: "btn btn-danger",
        confirmButtonText: "Ok"
    });
}
//------------------------------------------------------------------------------
function onSuccessSend3(obj) {
    if (obj.msg == 'warning') {

        swal({
            title: "Formulário de Voluntários",
            text: "" + obj.retorno + "",
            type: "warning",
            confirmButtonClass: "btn btn-danger",
            confirmButtonText: "Ok"
        });

        $("#nome_fiscal").val(obj.nome);
        $("#nascimento").val(obj.nascimento);
        //$("#rg").val(obj.rg);
        //$("#orgao_emissor").val(obj.orgao_emissor);
        $("#mae").val(obj.mae);
        $("#celular_fiscal").val(obj.contato);

        //var myCheckbox = document.getElementById('whatsapp');

        //if (obj.whatsapp == 1) {
        //myCheckbox.checked = true;
        //} else {
        //myCheckbox.checked = false;
        //}

        //var myCheckbox2 = document.getElementById('deficiencia');

        //if (obj.deficiencia == 1) {
        //myCheckbox2.checked = true;
        //} else {
        //myCheckbox2.checked = false;
        //}

        //$("select#funcao_id").val(obj.funcao);
        //$("select#funcao_id").select2();

        //$("select#tipo").val(obj.tipo);
        //$("select#tipo").select2();

//        $("select#estado").val(obj.estado);
//        $("select#estado").select2();
//
//        $("select#cidade").val(obj.cidade);
//        $("select#cidade").select2();

        $("select#bairro").val(obj.bairro);
        $("select#bairro").select2();

        $("select#zona").val(obj.zona);
        $("select#zona").select2();

        $.post(PORTAL_URL + "combo/secao_apuracao_2024_fiscal.php",
                {zona: obj.zona},
                function (valor) {
                    $("select#secao").html(valor);
                    $("select#secao").val(obj.secao);
                    $("select#secao").select2();
                });

        $("div#div_local").show();
        $("div#div_local_texto").html(obj.local_votacao);

        //$.post(PORTAL_URL + "combo/local2.php",
        //{secao: obj.secao, zona: obj.zona},
        //function (valor) {
        //$("select#local").html(valor);
        //$("select#local").val(obj.local_votacao);
        //$("select#local").select2();
        //});

        //$("select#banco").val(obj.banco);
        //$("select#banco").select2();

        //$("#cep").val(obj.cep);
        $("#endereco_fiscal").val(obj.endereco);
        $("#numero").val(obj.numero);
        $("#titulo_fiscal").val(obj.titulo_fiscal);
        //$("#indicacao").val(obj.quem_indicou);
        //$("#agencia").val(obj.agencia);
        //$("#conta").val(obj.conta);

    }
    return false;
}
//------------------------------------------------------------------------------------------------------