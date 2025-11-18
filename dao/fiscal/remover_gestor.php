<?php

$db = Conexao::getInstance();

$msg = array();
$error = false;

$pessoa = isset($_POST['id']) && $_POST['id'] != "" ? $_POST['id'] : 0;

if ($error == false) {
    try {

        $db->beginTransaction();

        $sql = $db->prepare("UPDATE spots SET responsavel = NULL, seg_usuario_id = ?, data_update = NOW() WHERE responsavel = ?");
        $sql->bindValue(1, $_SESSION['id']);
        $sql->bindValue(2, $pessoa);
        $sql->execute();

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