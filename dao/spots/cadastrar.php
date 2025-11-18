<?php

$db = Conexao::getInstance();

$msg = array();

$error = false;

$id = isset($_POST['id']) && $_POST['id'] != "" ? $_POST['id'] : 0;
$nome = isset($_POST['nome']) ? $_POST['nome'] : NULL;
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];

//VERIFICAÇÃO SE O USUÁRIO INFORMADO JÁ EXISTE NA BASE DE DADOS
$codigo = pesquisar("id", "spots", "nome", "=", $nome, "");

if (is_numeric($codigo) && $codigo != $id) {
    $error = true;
    echo "O nome do spot informado já existe no sistema.";
    exit();
}

if ($error == false) {
    try {

        $db->beginTransaction();

        if ($id == 0) {
            $sql = $db->prepare("INSERT INTO spots (nome, seg_usuario_id, data_cadastro) VALUES (?, ?, NOW())");
            $sql->bindValue(1, $nome);
            $sql->bindValue(2, $_SESSION['id']);
            $sql->execute();

            $id = $db->lastInsertId();
        } else {
            $sql = $db->prepare("UPDATE spots SET nome = ?, seg_usuario_id = ? WHERE id = ?");
            $sql->bindValue(1, $nome);
            $sql->bindValue(2, $_SESSION['id']);
            $sql->bindValue(3, $id);
            $sql->execute();

            $sql2 = $db->prepare("DELETE FROM spots_localizacao WHERE spot_id = ?");
            $sql2->bindValue(1, $id);
            $sql2->execute();

            if ($latitude != null) {
                foreach ($latitude AS $key => $val) {
                    if ($latitude[$key] != null && $latitude[$key] != "" || $longitude[$key] != null && $longitude[$key] != "") {
                        $sql3 = $db->prepare("INSERT INTO spots_localizacao (latitude, longitude, spot_id) VALUES (?, ?, ?)");
                        $sql3->bindValue(1, $latitude[$key]);
                        $sql3->bindValue(2, $longitude[$key]);
                        $sql3->bindValue(3, $id);
                        $sql3->execute();
                    }
                }
            }
        }

        $db->commit();

        //MENSAGEM DE SUCESO
        $msg['id'] = $id;
        $msg['msg'] = 'success';
        $msg['retorno'] = 'Spot cadastrado com sucesso!';
        echo json_encode($msg);
    } catch (PDOException $e) {
        $db->rollback();
        $msg['msg'] = 'error';
        $msg['retorno'] = "Erro ao tentar realizar a ação desejada:" . $e->getMessage();
        echo json_encode($msg);
        exit();
    }
}
?>