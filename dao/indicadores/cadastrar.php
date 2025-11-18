<?php

$db = Conexao::getInstance();

$msg = array();
$error = false;

$id = isset($_POST['id']) && $_POST['id'] != "" ? $_POST['id'] : 0;
$nome = isset($_POST['nome']) ? $_POST['nome'] : NULL;
$funcao = isset($_POST['funcao']) ? $_POST['funcao'] : NULL;
$contato = isset($_POST['contato']) ? $_POST['contato'] : NULL;

//VERIFICAÇÃO SE O USUÁRIO INFORMADO JÁ EXISTE NA BASE DE DADOS
$codigo = pesquisa("id", "2024_indicacoes", "nome = ?", $nome, "", "", "", "", "", "", "");

if (is_numeric($codigo) && $codigo != $id) {
    $error = true;
    echo "O nome do indicador informado já existe no sistema.";
    exit();
}

if ($error == false) {
    try {

        $db->beginTransaction();

        if ($id == 0) {
            $sql = $db->prepare("INSERT INTO 2024_indicacoes (nome, funcao, telefone, data_update, responsavel_id, data_cadastro, status) VALUES (?, ?, ?, NOW(), ?, NOW(), 1)");
            $sql->bindValue(1, $nome);
            $sql->bindValue(2, $funcao);
            $sql->bindValue(3, $contato);
            $sql->bindValue(4, $_SESSION['id']);
            $sql->execute();

            $id = $db->lastInsertId();
        } else {
            $sql = $db->prepare("UPDATE 2024_indicacoes SET nome = ?, funcao = ?, telefone = ?, responsavel_id = ? WHERE id = ?");
            $sql->bindValue(1, $nome);
            $sql->bindValue(2, $funcao);
            $sql->bindValue(3, $contato);
            $sql->bindValue(4, $_SESSION['id']);
            $sql->bindValue(5, $id);
            $sql->execute();
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