<?php

$db = Conexao::getInstance();

$msg = array();
$error = false;

$id = isset($_POST['id']) && $_POST['id'] != "" ? $_POST['id'] : 0;
$nome_fiscal = isset($_POST['nome_fiscal']) ? $_POST['nome_fiscal'] : NULL;
$funcao_id = isset($_POST['funcao_id']) ? $_POST['funcao_id'] : NULL;
$pai = isset($_POST['pai']) ? $_POST['pai'] : NULL;
$mae = isset($_POST['mae']) ? $_POST['mae'] : NULL;
$whatsapp = isset($_POST['whatsapp']) ? $_POST['whatsapp'] : 0;
$deficiencia = isset($_POST['deficiencia']) ? $_POST['deficiencia'] : 3;
$cpf_fiscal = isset($_POST['cpf_fiscal']) ? $_POST['cpf_fiscal'] : NULL;
$celular_fiscal = isset($_POST['celular_fiscal']) ? $_POST['celular_fiscal'] : NULL;
$titulo_fiscal = isset($_POST['titulo_fiscal']) ? $_POST['titulo_fiscal'] : NULL;
$endereco_fiscal = isset($_POST['endereco_fiscal']) ? $_POST['endereco_fiscal'] : NULL;
$numero = isset($_POST['numero']) ? $_POST['numero'] : NULL;
$bairro = isset($_POST['bairro']) ? $_POST['bairro'] : NULL;

$indicacao = isset($_POST['indicacao']) ? $_POST['indicacao'] : NULL;

$nascimento = isset($_POST['nascimento']) ? $_POST['nascimento'] : NULL;
$zona = isset($_POST['zona']) ? $_POST['zona'] : NULL;
$secao = isset($_POST['secao']) ? $_POST['secao'] : NULL;
$local = isset($_POST['local']) ? $_POST['local'] : NULL;

$rg = isset($_POST['rg']) ? $_POST['rg'] : NULL;
$orgao_emissor = isset($_POST['orgao_emissor']) ? $_POST['orgao_emissor'] : NULL;
$cep = isset($_POST['cep']) ? $_POST['cep'] : NULL;
$estado = isset($_POST['estado']) ? $_POST['estado'] : NULL;
$cidade = isset($_POST['cidade']) ? $_POST['cidade'] : NULL;

$tipo = isset($_POST['tipo']) ? $_POST['tipo'] : 2;

$pix = 0;
$banco = isset($_POST['banco']) && $pix == 0 ? $_POST['banco'] : NULL;
$agencia = isset($_POST['agencia']) && $pix == 0 ? $_POST['agencia'] : NULL;
$conta = isset($_POST['conta']) && $pix == 0 ? $_POST['conta'] : NULL;

$sexo = isset($_POST['sexo']) && $_POST['sexo'] != "" ? $_POST['sexo'] : NULL;

//if ($tipo == 1 && $pix == 0) {
//    $vf_conta = pesquisa("id", "pessoas", "conta = ?", $conta, "AND agencia = ?", $agencia, "", "", "", "", "");
//
//    if (is_numeric($vf_conta) && $vf_conta != $id) {
//        $error = true;
//        echo "A agência e conta do fiscal informado já existe no sistema.";
//        exit();
//    }
//}

$treinamento = isset($_POST['treinamento']) && $_POST['treinamento'] != "" ? $_POST['treinamento'] : NULL;
$data_participacao = isset($_POST['data_participacao']) && $_POST['data_participacao'] != "" ? $_POST['data_participacao'] : NULL;
$motivo = isset($_POST['motivo']) && $_POST['motivo'] != "" ? $_POST['motivo'] : NULL;

if ($cpf_fiscal != "" && $cpf_fiscal != null) {
    $vf_cpf = pesquisa("id", "2024_voluntarios", "cpf = ?", $cpf_fiscal, "", "", "", "", "", "", "");

    if (is_numeric($vf_cpf) && $vf_cpf != $id) {
        $error = true;
        echo "O CPF do fiscal informado já existe no sistema.";
        exit();
    }

    if (valida_CPF($cpf_fiscal) == false) {
        $error = true;
        echo "O CPF informado é inválido!";
        exit();
    }
}

if ($error == false) {
    try {

        $db->beginTransaction();

        if ($id == 0) {
            $sql = $db->prepare("INSERT INTO 2024_voluntarios (nome, celular, cpf, funcao_id, nm_pai, nm_mae, whatsapp, titulo_eleitor, endereco, bsc_bairros_id, numero, nascimento, zona, secao_numero, local_votacao, rg, orgao_expedidor, cep, estado, cidade, tipo, pix, banco_id, conta, agencia, deficiencia, sexo, treinamento, treinamento_data_participacao, treinamento_motivo, data_update, usuario_id, data_cadastro, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, NOW(), 1)");
            $sql->bindValue(1, $nome_fiscal);
            $sql->bindValue(2, $celular_fiscal);
            $sql->bindValue(3, $cpf_fiscal);
            $sql->bindValue(4, $funcao_id);
            $sql->bindValue(5, $pai);
            $sql->bindValue(6, $mae);
            $sql->bindValue(7, $whatsapp);
            $sql->bindValue(8, $titulo_fiscal);
            $sql->bindValue(9, $endereco_fiscal);
            $sql->bindValue(10, $bairro);
            $sql->bindValue(11, $numero);

            $sql->bindValue(12, $nascimento);
            $sql->bindValue(13, $zona);
            $sql->bindValue(14, $secao);
            $sql->bindValue(15, $local);
            $sql->bindValue(16, $rg);
            $sql->bindValue(17, $orgao_emissor);
            $sql->bindValue(18, $cep);
            $sql->bindValue(19, $estado);
            $sql->bindValue(20, $cidade);

            $sql->bindValue(21, $tipo);
            $sql->bindValue(22, $pix);
            $sql->bindValue(23, $banco);
            $sql->bindValue(24, $conta);
            $sql->bindValue(25, $agencia);

            $sql->bindValue(26, $deficiencia);
            $sql->bindValue(27, $sexo);

            $sql->bindValue(28, $treinamento);
            $sql->bindValue(29, $data_participacao);
            $sql->bindValue(30, $motivo);

            $sql->bindValue(31, $_SESSION['id']);
            $sql->execute();

            $id = $db->lastInsertId();
        } else {
            $sql = $db->prepare("UPDATE 2024_voluntarios SET nome = ?, celular = ?, cpf = ?, funcao_id = ?, nm_pai = ?, nm_mae = ?, whatsapp = ?, titulo_eleitor = ?, endereco = ?, bsc_bairros_id = ?, numero = ?, nascimento = ?, zona = ?, secao_numero = ?, local_votacao = ?, rg = ?, orgao_expedidor = ?, cep = ?, estado = ?, cidade = ?, tipo = ?, pix = ?, banco_id = ?, conta = ?, agencia = ?, deficiencia = ?, sexo = ?, treinamento = ?, treinamento_data_participacao = ?, treinamento_motivo = ?, usuario_id = ? WHERE id = ?");
            $sql->bindValue(1, $nome_fiscal);
            $sql->bindValue(2, $celular_fiscal);
            $sql->bindValue(3, $cpf_fiscal);
            $sql->bindValue(4, $funcao_id);
            $sql->bindValue(5, $pai);
            $sql->bindValue(6, $mae);
            $sql->bindValue(7, $whatsapp);
            $sql->bindValue(8, $titulo_fiscal);
            $sql->bindValue(9, $endereco_fiscal);
            $sql->bindValue(10, $bairro);
            $sql->bindValue(11, $numero);

            $sql->bindValue(12, $nascimento);
            $sql->bindValue(13, $zona);
            $sql->bindValue(14, $secao);
            $sql->bindValue(15, $local);
            $sql->bindValue(16, $rg);
            $sql->bindValue(17, $orgao_emissor);
            $sql->bindValue(18, $cep);
            $sql->bindValue(19, $estado);
            $sql->bindValue(20, $cidade);

            $sql->bindValue(21, $tipo);
            $sql->bindValue(22, $pix);
            $sql->bindValue(23, $banco);
            $sql->bindValue(24, $conta);
            $sql->bindValue(25, $agencia);

            $sql->bindValue(26, $deficiencia);
            $sql->bindValue(27, $sexo);

            $sql->bindValue(28, $treinamento);
            $sql->bindValue(29, $data_participacao);
            $sql->bindValue(30, $motivo);

            $sql->bindValue(31, $_SESSION['id']);

            $sql->bindValue(32, $id);
            $sql->execute();
        }

        //Removendo todas as indicações Anteriores]
        $stmt22 = $db->prepare("DELETE FROM 2024_voluntarios_indicacoes WHERE voluntario_id = ?");
        $stmt22->bindValue(1, $id);
        $stmt22->execute();

        //Inserindo as Novas Indicações do Cadastro
        if (isset($indicacao)) {
            foreach ($indicacao as $key => $val) {
                if ($val != "") {
                    $stmt2 = $db->prepare("INSERT INTO 2024_voluntarios_indicacoes (voluntario_id, indicacao_id) VALUES (?, ?)");
                    $stmt2->bindValue(1, $id);
                    $stmt2->bindValue(2, $val);
                    $stmt2->execute();
                }
            }
        }

        $db->commit();

        $_SESSION['guard_id'] = $id;
    } catch (PDOException $e) {
        $db->rollback();
        $msg['msg'] = 'error';
        $msg['retorno'] = "Erro ao tentar realizar a ação desejada:" . $e->getMessage();
        echo json_encode($msg);
        exit();
    }
}
?>