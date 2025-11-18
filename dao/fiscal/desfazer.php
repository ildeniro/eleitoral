<?php

$db = Conexao::getInstance();

$error = false;

try {

    $db->beginTransaction();

    $sql1 = $db->prepare("UPDATE 2024_voluntarios SET zona_2 = NULL, secao_numero_2 = NULL, regional_2 = NULL, local_votacao_2 = NULL, bairro_2 = NULL, municipio_2 = NULL, usuario_id_2 = NULL, data_update_2 = NULL  WHERE funcao_id = 5");
    $sql1->execute();

    $sql2 = $db->prepare("UPDATE sys_configuracoes SET fiscal = 0 WHERE id = 1");
    $sql2->execute();

    $db->commit();

    //MENSAGEM DE SUCESSO
    $msg['msg'] = 'success';
    $msg['retorno'] = 'Distribuição desfeita com sucesso!';
    echo json_encode($msg);
    exit();
} catch (PDOException $e) {
    $db->rollback();
    $msg['msg'] = 'error';
    $msg['retorno'] = "Erro ao tentar desfazer a distribuição desejada:" . $e->getMessage();
    echo json_encode($msg);
    exit();
}
?>


