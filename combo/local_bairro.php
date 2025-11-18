<?php

//------------------------------------------------------------------------------
include_once('../config/geral.php');
include_once('../config/funcoes.php');
$db = Conexao::getInstance();

$local = $_POST['local'];

$condicao = "1";

if (is_numeric($local)) {
    $condicao = "lv.ID = ?";
}

$stmp = $db->prepare("SELECT lv.CD_BAIRRO, lv.NM_BAIRRO    
                     FROM 2024_locais_votacao AS lv 
                     WHERE $condicao   
                     ORDER BY lv.NM_BAIRRO ASC");

if (is_numeric($local)) {
    $stmp->bindValue(1, $local);
}

$stmp->execute();

if ($stmp->rowCount() == 0) {
    echo '<option value="">Nenhum bairro encontrado</option>';
} else {
    while ($row = $stmp->fetch(PDO::FETCH_ASSOC)) {
        echo '<option value="' . $row['CD_BAIRRO'] . '">' . ctexto($row['NM_BAIRRO'], "pri") . '</option>';
    }
}
//------------------------------------------------------------------------------
?>

