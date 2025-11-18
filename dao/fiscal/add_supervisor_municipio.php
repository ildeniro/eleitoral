<?php

$db = Conexao::getInstance();

$msg = array();
$error = false;

$pessoas = isset($_POST['pessoas']) && $_POST['pessoas'] != "" ? $_POST['pessoas'] : 0;
$bairro = isset($_POST['bairro']) ? $_POST['bairro'] : NULL;
$municipio = isset($_POST['municipio']) ? $_POST['municipio'] : NULL;

if ($error == false) {
    try {

        $db->beginTransaction();

        $sql = $db->prepare("UPDATE pessoas SET bairro_2 = ?, municipio_2 = ?, usuario_id_2 = ?, data_update_2 = NOW() WHERE id = ?");
        $sql->bindValue(1, $bairro);
        $sql->bindValue(2, $municipio);
        $sql->bindValue(3, $_SESSION['id']);
        $sql->bindValue(4, $pessoas);
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