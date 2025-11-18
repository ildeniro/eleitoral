<?php

//------------------------------------------------------------------------------
include_once('../config/geral.php');
$db = Conexao::getInstance();

$zona = $_POST['zona'];

$stmp = $db->prepare("SELECT e.REGIONAL_RBO, r.nome AS REGIONAL_NOME   
                     FROM eleicoes_localidades_2022 e 
                     LEFT JOIN regional AS r ON r.id = e.REGIONAL_RBO 
                     WHERE e.ZONA = ? 
                     GROUP BY r.id  
                     ORDER BY r.nome ASC");
$stmp->bindValue(1, $zona);
$stmp->execute();

if ($stmp->rowCount() == 0) {
    echo '<option value="">Selecione primeiro a zona</option>';
} else {
    echo '<option value="">' . 'Escolha uma regional' . '</option>';
    while ($row = $stmp->fetch(PDO::FETCH_ASSOC)) {
        echo '<option value="' . $row['REGIONAL_RBO'] . '">' . ($row['REGIONAL_NOME']) . '</option>';
    }
}
//------------------------------------------------------------------------------
?>

