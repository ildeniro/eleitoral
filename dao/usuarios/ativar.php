<?php

$db = Conexao::getInstance();

$error = false;

$id = strip_tags(@$_POST['id']);

try {

    $db->beginTransaction();

    $stmt5 = $db->prepare("UPDATE seg_usuarios SET status = 1 WHERE id = ?");
    $stmt5->bindValue(1, $id);
    $stmt5->execute();

    $db->commit();

    //MENSAGEM DE SUCESSO
    $msg['msg'] = 'success';
    $msg['retorno'] = 'Usuário ativado com sucesso!';
    echo json_encode($msg);
    exit();
} catch (PDOException $e) {
    $db->rollback();
    $msg['msg'] = 'error';
    $msg['retorno'] = "Erro ao tentar ativar o usuário desejado:" . $e->getMessage();
    echo json_encode($msg);
    exit();
}
?>


