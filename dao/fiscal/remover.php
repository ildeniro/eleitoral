<?php

$db = Conexao::getInstance();

$msg = array();
$error = false;

$pessoas = isset($_POST['id']) && $_POST['id'] != "" ? $_POST['id'] : 0;

if ($error == false) {
    try {

        $db->beginTransaction();

        $sql = $db->prepare("UPDATE 2024_voluntarios SET zona_2 = NULL, secao_numero_2 = NULL, regional_2 = NULL, municipio_2 = NULL, local_votacao_2 = NULL, bairro_2 = NULL, usuario_id_2 = ? WHERE id = ?");
        $sql->bindValue(1, $_SESSION['id']);
        $sql->bindValue(2, $pessoas);
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