<?php

$db = Conexao::getInstance();

$msg = array();
$error = false;

$pessoas = isset($_POST['pessoas']) && $_POST['pessoas'] != "" ? $_POST['pessoas'] : 0;
$secao = isset($_POST['secao']) ? $_POST['secao'] : NULL;
$municipio = isset($_POST['municipio']) ? $_POST['municipio'] : NULL;
$local = isset($_POST['local']) ? $_POST['local'] : NULL;
$zona = isset($_POST['zona']) ? $_POST['zona'] : NULL;
$bairro = isset($_POST['bairro']) ? $_POST['bairro'] : NULL;

if ($error == false) {
    try {

        $db->beginTransaction();

        $sql = $db->prepare("UPDATE pessoas SET secao_numero_2 = ?, municipio_2 = ?, local_votacao_2 = ?, usuario_id_2 = ?, zona_2 = ?, bairro_2 = ?, data_update_2 = NOW() WHERE id = ?");
        $sql->bindValue(1, $secao);
        $sql->bindValue(2, $municipio);
        $sql->bindValue(3, $local);
        $sql->bindValue(4, $_SESSION['id']);
        $sql->bindValue(5, $zona);
        $sql->bindValue(6, $bairro);
        $sql->bindValue(7, $pessoas);
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