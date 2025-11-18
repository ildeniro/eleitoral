<?php

//------------------------------------------------------------------------------
include_once('../config/geral.php');
$db = Conexao::getInstance();

$regional = $_POST['regional'];

$stmp = $db->prepare("SELECT ID, NM_BAIRRO   
                     FROM bsc_bairros  
                     WHERE REGIONAL_ID = ? 
                     GROUP BY NM_BAIRRO
                     ORDER BY NM_BAIRRO ASC");
$stmp->bindValue(1, $regional);
$stmp->execute();

if ($stmp->rowCount() == 0) {
    echo '<option value="">Nenhum bairro encontrado</option>';
} else {
    echo '<option value="">' . 'Escolha um bairro' . '</option>';
    while ($row = $stmp->fetch(PDO::FETCH_ASSOC)) {
        echo '<option value="' . $row['ID'] . '">' . $row['NM_BAIRRO'] . '</option>';
    }
}
//------------------------------------------------------------------------------
?>

