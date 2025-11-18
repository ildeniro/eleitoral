<?php

$db = Conexao::getInstance();

$msg = array();

$error = false;

$cpf = isset($_POST['cpf']) && $_POST['cpf'] != "" ? $_POST['cpf'] : "";

$nome = "";

if ($error == false) {
    try {

        $db->beginTransaction();

        $result2 = $db->prepare("SELECT * 
                                 FROM 2022_voluntarios 
                                 WHERE cpf = ?");
        $result2->bindValue(1, $cpf);
        $result2->execute();
        while ($beneficiarios = $result2->fetch(PDO::FETCH_ASSOC)) {
            $nome = $beneficiarios['nome'];
            $nascimento = $beneficiarios['nascimento'];
            $rg = $beneficiarios['rg'];
            $orgao_emissor = $beneficiarios['orgao_expedidor'];
            $mae = $beneficiarios['nm_mae'];
            $contato = $beneficiarios['celular'];
            $whatsapp = $beneficiarios['whatsapp'];
            $deficiencia = $beneficiarios['deficiencia'];
            $funcao = $beneficiarios['funcao_id'];
            $tipo = $beneficiarios['tipo'];
            $cep = $beneficiarios['cep'];
            //$cidade = $beneficiarios['bsc_municipios_id'];
            $endereco = $beneficiarios['endereco'];
            $numero = $beneficiarios['numero'];
            $bairro = $beneficiarios['bsc_bairros_id'];
            $zona = $beneficiarios['zona'];
            $secao = $beneficiarios['secao_numero'];
            $titulo_fiscal = $beneficiarios['titulo_eleitor'];
            $local_votacao = $beneficiarios['local_votacao'];
            $quem_indicou = $beneficiarios['indicacao'];
            $banco = $beneficiarios['banco_id'];
            $agencia = $beneficiarios['agencia'];
            $conta = $beneficiarios['conta'];

            $data_cadastro = obterDataBRTimestamp($beneficiarios['data_cadastro']);
        }

        if ($nome != "") {
            $msg['msg'] = 'warning';
            $msg['retorno'] = "O voluntário informado já existe na base de dados do sistema, o mesmo foi cadastrado na data <b>$data_cadastro</b>, então o sistema irá carregar suas informações para que sejam validadas.";
            $msg['nome'] = $nome;
            $msg['nascimento'] = $nascimento;
            $msg['rg'] = $rg;
            $msg['orgao_emissor'] = $orgao_emissor;
            $msg['mae'] = $mae;
            $msg['contato'] = $contato;
            $msg['whatsapp'] = $whatsapp;
            $msg['deficiencia'] = $deficiencia;
            $msg['funcao'] = $funcao;
            $msg['tipo'] = $tipo;
            $msg['cep'] = $cep;
            //$msg['cidade'] = $cidade;
            $msg['endereco'] = $endereco;
            $msg['numero'] = $numero;
            $msg['bairro'] = $bairro;
            $msg['zona'] = $zona;
            $msg['secao'] = $secao;
            $msg['titulo_fiscal'] = $titulo_fiscal;
            $msg['local_votacao'] = $local_votacao;
            $msg['quem_indicou'] = $quem_indicou;
            $msg['banco'] = $banco;
            $msg['agencia'] = $agencia;
            $msg['conta'] = $conta;

            echo json_encode($msg);
            exit();
        } else {
            $msg['msg'] = 'success';
            $msg['retorno'] = "Nenhum registro encontrado no banco de dados.";
            echo json_encode($msg);
            exit();
        }

        $db->commit();
    } catch (PDOException $e) {
        $db->rollback();
        $msg['msg'] = 'error';
        $msg['retorno'] = "Erro ao tentar realizar a ação desejada:" . $e->getMessage();
        echo json_encode($msg);
        exit();
    }
}
?>