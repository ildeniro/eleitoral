<?php

$db = Conexao::getInstance();

$msg = array();

$error = false;

$id = isset($_POST['id']) && $_POST['id'] != "" ? $_POST['id'] : 0;

$secao_numero = isset($_POST['secao_numero']) ? $_POST['secao_numero'] : NULL;
$local = isset($_POST['local']) ? $_POST['local'] : NULL;
$regional = isset($_POST['regional']) ? $_POST['regional'] : NULL;
$endereco = isset($_POST['endereco']) ? $_POST['endereco'] : NULL;
$regiao = isset($_POST['regiao']) ? $_POST['regiao'] : NULL;
$bairro = isset($_POST['bairro']) ? $_POST['bairro'] : NULL;
$longitude = isset($_POST['longitude']) ? $_POST['longitude'] : NULL;
$latitude = isset($_POST['latitude']) ? $_POST['latitude'] : NULL;
$aptos = isset($_POST['aptos']) ? $_POST['aptos'] : NULL;
$agregacao = isset($_POST['agregacao']) ? $_POST['agregacao'] : "";

if ($error == false) {
    try {

        $db->beginTransaction();

        if ($id == 0) {
            $sql = $db->prepare("INSERT INTO eleicoes_localidades_2022 (NR_SECAO, LOCAL_VOTACAO, REGIAO, ENDERECO, REGIONAL_RBO, BAIRRO, LONGITUDE, LATITUDE, QT_ELEITOR, AGREGA) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $sql->bindValue(1, $secao_numero);
            $sql->bindValue(2, $local);
            $sql->bindValue(3, $regiao);
            $sql->bindValue(4, $endereco);
            $sql->bindValue(5, $regional);
            $sql->bindValue(6, $bairro);
            $sql->bindValue(7, $longitude);
            $sql->bindValue(8, $latitude);
            $sql->bindValue(9, $aptos);
            $sql->bindValue(10, $agregacao);
            $sql->execute();

            $id = $db->lastInsertId();
        } else {
            $sql = $db->prepare("UPDATE eleicoes_localidades_2022 SET NR_SECAO = ?, LOCAL_VOTACAO = ?, REGIAO = ?, ENDERECO = ?, REGIONAL_RBO = ?, BAIRRO = ?, LONGITUDE = ?, LATITUDE = ?, QT_ELEITOR = ?, AGREGA = ? WHERE ID = ?");
            $sql->bindValue(1, $secao_numero);
            $sql->bindValue(2, $local);
            $sql->bindValue(3, $regiao);
            $sql->bindValue(4, $endereco);
            $sql->bindValue(5, $regional);
            $sql->bindValue(6, $bairro);
            $sql->bindValue(7, $longitude);
            $sql->bindValue(8, $latitude);
            $sql->bindValue(9, $aptos);
            $sql->bindValue(10, $agregacao);
            $sql->bindValue(11, $id);
            $sql->execute();
        }

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