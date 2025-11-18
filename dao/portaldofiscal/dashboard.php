<?php

$db = Conexao::getInstance();

$error = false;

try {

    $db->beginTransaction();

    if (isset($_SESSION['voluntario_id']) && is_numeric($_SESSION['voluntario_id'])) {
        $stmt5 = $db->prepare("UPDATE 2024_voluntarios SET confirmacao = 1 WHERE id = ?");
        $stmt5->bindValue(1, $_SESSION['voluntario_id']);
        $stmt5->execute();

        $db->commit();

        //MENSAGEM DE SUCESSO
        $msg['msg'] = 'success';
        $msg['retorno'] = 'Fiscal confirmado com sucesso!';
        echo json_encode($msg);
        exit();
    } else {
        //MENSAGEM DE ERROR
        $msg['msg'] = 'error';
        $msg['retorno'] = 'Suas informações de sessão expiraram, por favor refaça login novamente para confirmar sua presença!';
        echo json_encode($msg);
        exit();
    }
} catch (PDOException $e) {
    $db->rollback();
    $msg['msg'] = 'error';
    $msg['retorno'] = "Erro ao tentar confirmar a sua presença, por favor tente novamente ou contate a central:" . $e->getMessage();
    echo json_encode($msg);
    exit();
}
?>


