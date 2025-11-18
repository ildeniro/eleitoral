<?php

$db = Conexao::getInstance();

$msg = array();
$error = false;

$pessoas = isset($_POST['pessoas']) && $_POST['pessoas'] != "" ? $_POST['pessoas'] : 0;
$secao = isset($_POST['secao']) ? $_POST['secao'] : NULL;
$regional = isset($_POST['regional']) ? $_POST['regional'] : NULL;
$local = isset($_POST['local']) ? $_POST['local'] : NULL;

$zona = pesquisar("NR_ZONA", "2024_locais_votacao", "ID", "=", $local, "");
$bairro = pesquisar("CD_BAIRRO", "2024_locais_votacao", "ID", "=", $local, "");
$municipio = pesquisar("MUNICIPIO_ID", "2024_locais_votacao", "ID", "=", $local, "");

if ($error == false) {
    try {

        $db->beginTransaction();

        $sql = $db->prepare("UPDATE 2024_voluntarios SET zona_2 = ?, bairro_2 = ?, municipio_2 = ?, secao_numero_2 = ?, regional_2 = ?, local_votacao_2 = ?, usuario_id_2 = ?, data_update_2 = NOW() WHERE id = ?");
        $sql->bindValue(1, $zona);
		$sql->bindValue(2, $bairro);
		$sql->bindValue(3, $municipio);
		$sql->bindValue(4, $secao);
        $sql->bindValue(5, $regional);
        $sql->bindValue(6, $local);
        $sql->bindValue(7, $_SESSION['id']);
        $sql->bindValue(8, $pessoas);
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