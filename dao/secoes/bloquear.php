<?php

$db = Conexao::getInstance();

$error = false;

$id = strip_tags(@$_POST['id']);

try {

    $db->beginTransaction();

    $stmt5 = $db->prepare("UPDATE eleicoes_localidades_2022 SET STATUS = 0 WHERE ID = ?");
    $stmt5->bindValue(1, $id);
    $stmt5->execute();

    $db->commit();

    //MENSAGEM DE SUCESSO
    $msg['msg'] = 'success';
    $msg['retorno'] = 'Seção bloqueada com sucesso!';
    echo json_encode($msg);
    exit();
} catch (PDOException $e) {
    $db->rollback();
    $msg['msg'] = 'error';
    $msg['retorno'] = "Erro ao tentar bloquear a seção desejada:" . $e->getMessage();
    echo json_encode($msg);
    exit();
}
?>


