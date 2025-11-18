<?php

$db = Conexao::getInstance();

$msg = array();
$error = false;

$pessoas = isset($_POST['pessoas']) && $_POST['pessoas'] != "" ? $_POST['pessoas'] : 0;
$spot = isset($_POST['spot']) ? $_POST['spot'] : NULL;

if ($error == false) {
    try {

        $db->beginTransaction();

        $sql = $db->prepare("UPDATE spots SET responsavel = ?, data_update = NOW(), seg_usuario_id = ? WHERE id = ?");
        $sql->bindValue(1, $pessoas);
        $sql->bindValue(2, $_SESSION['id']);
        $sql->bindValue(3, $spot);
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