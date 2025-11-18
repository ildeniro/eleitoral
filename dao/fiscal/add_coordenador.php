<?php

$db = Conexao::getInstance();

$msg = array();
$error = false;

$pessoas = isset($_POST['pessoas']) && $_POST['pessoas'] != "" ? $_POST['pessoas'] : 0;
$secao = isset($_POST['secao']) ? $_POST['secao'] : NULL;
$regional = isset($_POST['regional']) ? $_POST['regional'] : NULL;
$local = isset($_POST['local']) ? $_POST['local'] : NULL;

if ($error == false) {
    try {

        $db->beginTransaction();

        $sql = $db->prepare("UPDATE pessoas SET secao_numero_2 = ?, regional_2 = ?, local_votacao_2 = ?, usuario_id_2 = ?, data_update_2 = NOW() WHERE id = ?");
        $sql->bindValue(1, $secao);
        $sql->bindValue(2, $regional);
        $sql->bindValue(3, $local);
        $sql->bindValue(4, $_SESSION['id']);
        $sql->bindValue(5, $pessoas);
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