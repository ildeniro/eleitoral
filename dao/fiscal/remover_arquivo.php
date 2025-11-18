<?php

$db = Conexao::getInstance();

$error = false;

$id = isset($_POST['id']) && $_POST['id'] != "" ? $_POST['id'] : 0;
$codigo = isset($_POST['codigo']) && $_POST['codigo'] != "" ? $_POST['codigo'] : 0;

try {

    $db->beginTransaction();

    $stmt5 = $db->prepare("DELETE FROM pessoas_arquivos WHERE id = ?");
    $stmt5->bindValue(1, $codigo);
    $stmt5->execute();

    $db->commit();

    //MENSAGEM DE SUCESSO
    $msg['id'] = $id;
    $msg['msg'] = 'success';
    $msg['retorno'] = 'Arquivo removio com sucesso!';
    echo json_encode($msg);
    exit();
} catch (PDOException $e) {
    $db->rollback();
    $msg['msg'] = 'error';
    $msg['retorno'] = "Erro ao tentar remover o arquivo desejado:" . $e->getMessage();
    echo json_encode($msg);
    exit();
}
?>


