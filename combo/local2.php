<?php

//------------------------------------------------------------------------------
include_once('../config/geral.php');
$db = Conexao::getInstance();

$zona = $_POST['zona'];
$secao = $_POST['secao'];

$stmp = $db->prepare("SELECT lv.ID, lv.NM_LOCAL_VOTACAO   
                     FROM 2024_locais_votacao AS lv 
                     INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = lv.ID 
                     WHERE s.NR_ZONA = ? AND s.NR_SECAO = ? 
                     GROUP BY lv.NM_LOCAL_VOTACAO
                     ORDER BY lv.NM_LOCAL_VOTACAO ASC");
$stmp->bindValue(1, $zona);
$stmp->bindValue(2, $secao);
$stmp->execute();

if ($stmp->rowCount() == 0) {
    echo '<option value="">Nenhum local encontrado</option>';
} else {
    while ($row = $stmp->fetch(PDO::FETCH_ASSOC)) {
        echo '<option selected="true" value="' . $row['ID'] . '">' . $row['NM_LOCAL_VOTACAO'] . '</option>';
    }
}
//------------------------------------------------------------------------------
?>

