<?php

$db = Conexao::getInstance();

$error = false;

$motivo = isset($_POST['motivo']) ? $_POST['motivo'] : NULL;

try {

    $db->beginTransaction();

    if (isset($_SESSION['voluntario_id']) && is_numeric($_SESSION['voluntario_id']) && $motivo != "") {
        
        $stmt5 = $db->prepare("UPDATE 2024_voluntarios SET confirmacao = 2, motivo_desconfirmacao = ? WHERE id = ?");
        $stmt5->bindValue(1, $motivo);
        $stmt5->bindValue(2, $_SESSION['voluntario_id']);
        $stmt5->execute();

        $db->commit();

        //MENSAGEM DE SUCESSO
        $msg['msg'] = 'success';
        $msg['retorno'] = 'Confirmação de desistência realizada com sucesso!';
        echo json_encode($msg);
        exit();
    } else {
        //MENSAGEM DE ERROR
        $msg['msg'] = 'error';
        $msg['retorno'] = 'Suas informações de sessão expiraram, por favor refaça login novamente para confirmar sua desistência!';
        echo json_encode($msg);
        exit();
    }
} catch (PDOException $e) {
    $db->rollback();
    $msg['msg'] = 'error';
    $msg['retorno'] = "Erro ao tentar confirmar sua desistência, por favor tente novamente ou contate a central:" . $e->getMessage();
    echo json_encode($msg);
    exit();
}
?>


