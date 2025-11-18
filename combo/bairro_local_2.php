<?php

//------------------------------------------------------------------------------
include_once('../config/geral.php');
$db = Conexao::getInstance();

$bairro = $_POST['bairro'];

$stmp = $db->prepare("SELECT lv.ID, lv.NM_LOCAL_VOTACAO    
                     FROM 2024_locais_votacao AS lv 
                     WHERE lv.CD_BAIRRO = ? 
                     ORDER BY lv.NM_LOCAL_VOTACAO ASC");
$stmp->bindValue(1, $bairro);
$stmp->execute();

if ($stmp->rowCount() == 0) {
    echo '<option value="">Nenhum local encontrado</option>';
} else {
    while ($row = $stmp->fetch(PDO::FETCH_ASSOC)) {
        echo '<option value="' . $row['ID'] . '">' . $row['NM_LOCAL_VOTACAO'] . '</option>';
    }
}
//------------------------------------------------------------------------------
?>

