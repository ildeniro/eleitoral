<?php

$db = Conexao::getInstance();

$msg = array();
$error = false;

$id = isset($_POST['id']) && $_POST['id'] != "" ? $_POST['id'] : 0;
$demandante = isset($_POST['demandante']) && $_POST['demandante'] != "" ? $_POST['demandante'] : NULL;
$descricao_ocorrencia = isset($_POST['descricao_ocorrencia']) && $_POST['descricao_ocorrencia'] != "" ? $_POST['descricao_ocorrencia'] : NULL;
$local_votacao = isset($_POST['local_votacao']) && $_POST['local_votacao'] != "" ? $_POST['local_votacao'] : NULL;
$bairro = isset($_POST['bairro']) && $_POST['bairro'] != "" ? $_POST['bairro'] : NULL;
$situacao = isset($_POST['situacao']) && $_POST['situacao'] != "" && is_numeric($_POST['situacao']) ? $_POST['situacao'] : 0;

$outros = isset($_POST['outros']) && $_POST['outros'] != "" ? $_POST['outros'] : NULL;
$outros_contato = isset($_POST['outros_contato']) && $_POST['outros_contato'] != "" ? $_POST['outros_contato'] : NULL;

if ($error == false) {
    try {

        $db->beginTransaction();

        if ($id == 0) {
            $sql = $db->prepare("INSERT INTO 2024_voluntarios_ocorrencias (voluntario_id, ocorrencia, local_votacao_id, bairro_id, situacao, outros, outros_contato, data_update, responsavel_id, data_cadastro, status) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, NOW(), 1)");
            $sql->bindValue(1, $demandante);
            $sql->bindValue(2, $descricao_ocorrencia);
            $sql->bindValue(3, $local_votacao);
            $sql->bindValue(4, $bairro);
            $sql->bindValue(5, $situacao);
            $sql->bindValue(6, $outros);
            $sql->bindValue(7, $outros_contato);
            $sql->bindValue(8, $_SESSION['id']);
            $sql->execute();

            $id = $db->lastInsertId();
        } else {
            $sql = $db->prepare("UPDATE 2024_voluntarios_ocorrencias SET voluntario_id = ?, ocorrencia = ?, local_votacao_id = ?, bairro_id = ?, situacao = ?, outros = ?, outros_contato = ?, responsavel_id = ? WHERE id = ?");
            $sql->bindValue(1, $demandante);
            $sql->bindValue(2, $descricao_ocorrencia);
            $sql->bindValue(3, $local_votacao);
            $sql->bindValue(4, $bairro);
            $sql->bindValue(5, $situacao);
            $sql->bindValue(6, $outros);
            $sql->bindValue(7, $outros_contato);
            $sql->bindValue(8, $_SESSION['id']);
            $sql->bindValue(9, $id);
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