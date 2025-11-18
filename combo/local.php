<?php

//------------------------------------------------------------------------------
include_once('../config/geral.php');
$db = Conexao::getInstance();

$regional = $_POST['regional'];

$stmp = $db->prepare("SELECT ID, NM_LOCAL_VOTACAO   
                     FROM 2024_locais_votacao  
                     WHERE REGIONAL_ID = ? 
                     GROUP BY NM_LOCAL_VOTACAO
                     ORDER BY NM_LOCAL_VOTACAO ASC");
$stmp->bindValue(1, $regional);
$stmp->execute();

if ($stmp->rowCount() == 0) {
    echo '<option value="">Nenhum local encontrado</option>';
} else {
    echo '<option value="">' . 'Escolha um local' . '</option>';
    while ($row = $stmp->fetch(PDO::FETCH_ASSOC)) {
        echo '<option value="' . $row['ID'] . '">' . $row['NM_LOCAL_VOTACAO'] . '</option>';
    }
}
//------------------------------------------------------------------------------
?>

