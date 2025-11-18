<?php

@session_start();

include_once('../config/geral.php');
include_once('../config/funcoes.php');
$db = Conexao::getInstance();

$error = false;
$mensagem = "";

$quemvai = $_POST['selecionados'];

$array_indicadores = "";

if (isset($quemvai) && $quemvai != "") {
    if (sizeof($quemvai) > 0) {
        foreach ($quemvai as $value) {
            $array_indicadores .= $value . ",";
        }
    }
}

if ($array_indicadores == "") {
    $array_indicadores = "0";
} else {
    $array_indicadores .= "0";
}

$nome = isset($_POST['nome']) ? $_POST['nome'] : NULL;
$funcao = isset($_POST['funcao']) ? $_POST['funcao'] : NULL;
$telefone = isset($_POST['telefone']) ? $_POST['telefone'] : NULL;

if ($nome != NULL && $funcao != NULL && $telefone != NULL) {

    //VERIFICA SE O O CELULAR PRINCIPAL DA DEMANDA JÁ FOI INFORMADO
    $id_contato2 = pesquisar("id", "2024_indicacoes", "telefone", "=", $telefone, "");

    if (is_numeric($id_contato2)) {
        $error = true;
        $mensagem = "telefone";
    }

    //VERIFICA SE O NOME DA DEMANDA JÁ FOI INFORMADO
    $id_contato = pesquisar("id", "2024_indicacoes", "nome", "=", $nome, "");

    if (is_numeric($id_contato)) {
        $error = true;
        $mensagem = "nome";
    }

    if ($error == false) {
        $db->beginTransaction();

        $stmt1 = $db->prepare("INSERT INTO 2024_indicacoes (nome, telefone, funcao, responsavel_id, data_cadastro, status) VALUES (?, ?, ?, ?, NOW(), 1)");
        $stmt1->bindValue(1, $nome);
        $stmt1->bindValue(2, $telefone);
        $stmt1->bindValue(3, $funcao);
        $stmt1->bindValue(4, $_SESSION['id']);
        $stmt1->execute();

        $db->commit();
    } else {
        echo $mensagem;
        exit();
    }
}

$stmp = $db->prepare("SELECT id, nome  
                      FROM 2024_indicacoes
                      WHERE id IN($array_indicadores) 
                      ORDER by nome");
$stmp->execute();

while ($row = $stmp->fetch(PDO::FETCH_ASSOC)) {
    echo '<option selected="true" value="' . $row['id'] . '">' . $row['nome'] . '</option>';
}

$stmp3 = $db->prepare("SELECT e.id, e.nome  
                      FROM 2024_indicacoes e
                      WHERE e.status = 1 AND e.id NOT IN($array_indicadores) 
                      ORDER by e.nome");
$stmp3->execute();

while ($row3 = $stmp3->fetch(PDO::FETCH_ASSOC)) {
    echo '<option value="' . $row3['id'] . '">' . $row3['nome'] . '</option>';
}
?>

