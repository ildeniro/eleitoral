<?php

$db = Conexao::getInstance();

$error = false;

$id = strip_tags(@$_POST['id']);

try {

    $db->beginTransaction();

    $stmt5 = $db->prepare("UPDATE spots SET status = 0 WHERE id = ?");
    $stmt5->bindValue(1, $id);
    $stmt5->execute();

    $db->commit();

    //MENSAGEM DE SUCESSO
    $msg['msg'] = 'success';
    $msg['retorno'] = 'Spot bloqueado com sucesso!';
    echo json_encode($msg);
    exit();
} catch (PDOException $e) {
    $db->rollback();
    $msg['msg'] = 'error';
    $msg['retorno'] = "Erro ao tentar bloquear o spot desejado:" . $e->getMessage();
    echo json_encode($msg);
    exit();
}
?>


