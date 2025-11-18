<?php

$db = Conexao::getInstance();

$error = false;

$id = strip_tags(@$_POST['id']);
$motivo = isset($_POST['motivo']) ? $_POST['motivo'] : NULL;
$tipo = isset($_POST['tipo']) ? $_POST['tipo'] : NULL;

try {

    $db->beginTransaction();

    $stmt5 = $db->prepare("UPDATE 2024_voluntarios SET motivo = ?, resp_cancelamento = ?, status = ?, data_cancelamento = NOW() WHERE id = ?");
    $stmt5->bindValue(1, $motivo);
    $stmt5->bindValue(2, $_SESSION['id']);
    $stmt5->bindValue(3, $tipo);
    $stmt5->bindValue(4, $id);
    $stmt5->execute();

    $db->commit();

    //MENSAGEM DE SUCESSO
    $msg['msg'] = 'success';
    $msg['retorno'] = 'Voluntário desativado com sucesso!';
    echo json_encode($msg);
    exit();
} catch (PDOException $e) {
    $db->rollback();
    $msg['msg'] = 'error';
    $msg['retorno'] = "Erro ao tentar desativar o voluntário desejado:" . $e->getMessage();
    echo json_encode($msg);
    exit();
}
?>


