<?php

$db = Conexao::getInstance();

$msg = array();
$error = false;

$pessoas = isset($_POST['pessoas']) && $_POST['pessoas'] != "" ? $_POST['pessoas'] : 0;
$municipio = isset($_POST['municipio']) ? $_POST['municipio'] : NULL;

if ($error == false) {
    try {

        $db->beginTransaction();

        $sql = $db->prepare("UPDATE pessoas SET municipio_2 = ?, usuario_id_2 = ?, data_update_2 = NOW() WHERE id = ?");
        $sql->bindValue(1, $municipio);
        $sql->bindValue(2, $_SESSION['id']);
        $sql->bindValue(3, $pessoas);
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