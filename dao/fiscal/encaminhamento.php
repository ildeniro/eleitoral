<?php

$db = Conexao::getInstance();

$msg = array();
$error = false;

$ocorrencia_id = isset($_POST['ocorrencia_id']) && $_POST['ocorrencia_id'] != "" ? $_POST['ocorrencia_id'] : 0;
$encaminhamento = isset($_POST['encaminhamento']) && $_POST['encaminhamento'] != "" ? $_POST['encaminhamento'] : NULL;

if ($error == false) {
    try {

        $db->beginTransaction();

        $sql = $db->prepare("INSERT INTO 2024_voluntarios_encaminhamentos (descricao, ocorrencia_id, responsavel_id, data_cadastro, data_update, status) VALUES (?, ?, ?, NOW(), NOW(), 1)");
        $sql->bindValue(1, $encaminhamento);
        $sql->bindValue(2, $ocorrencia_id);
        $sql->bindValue(3, $_SESSION['id']);
        $sql->execute();

        $db->commit();

        $msg['msg'] = 'success';
        $msg['retorno'] = "Encaminhamento salvo com sucesso!";
        echo json_encode($msg);
        exit();
    } catch (PDOException $e) {
        $db->rollback();
        $msg['msg'] = 'error';
        $msg['retorno'] = "Erro ao tentar realizar a ação desejada:" . $e->getMessage();
        echo json_encode($msg);
        exit();
    }
}
?>