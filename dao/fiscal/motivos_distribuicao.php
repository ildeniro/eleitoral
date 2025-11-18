<?php

$db = Conexao::getInstance();

$error = false;

$id = strip_tags(@$_POST['id']);
$motivo = isset($_POST['motivo']) ? $_POST['motivo'] : NULL;

try {

    $db->beginTransaction();

    $stmt55 = $db->prepare("INSERT INTO 2024_voluntarios_removidos (voluntario_id, motivo, responsavel_id, data_cadastro) VALUES (?, ?, ?, NOW())");
    $stmt55->bindValue(1, $id);
    $stmt55->bindValue(2, $motivo);
    $stmt55->bindValue(3, $_SESSION['id']);
    $stmt55->execute();

    $db->commit();

    //MENSAGEM DE SUCESSO
    $msg['msg'] = 'success';
    $msg['retorno'] = 'Informações adicionadas ao histórico do fiscal com sucesso!';
    echo json_encode($msg);
    exit();
} catch (PDOException $e) {
    $db->rollback();
    $msg['msg'] = 'error';
    $msg['retorno'] = "Erro ao tentar adicionar as informações desejadas:" . $e->getMessage();
    echo json_encode($msg);
    exit();
}
?>


