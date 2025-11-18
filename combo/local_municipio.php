<?php

//------------------------------------------------------------------------------
include_once('../config/geral.php');
$db = Conexao::getInstance();

$municipio = $_POST['municipio'];

$stmp = $db->prepare("SELECT e.LOCAL_VOTACAO   
                     FROM eleicoes_localidades_2022 e 
                     WHERE e.MUNICIPIO = ? 
                     GROUP BY e.LOCAL_VOTACAO
                     ORDER BY e.LOCAL_VOTACAO ASC");
$stmp->bindValue(1, $municipio);
$stmp->execute();

if ($stmp->rowCount() == 0) {
    echo '<option value="">Nenhum local encontrado</option>';
} else {
    echo '<option value="">' . 'Escolha um local' . '</option>';
    while ($row = $stmp->fetch(PDO::FETCH_ASSOC)) {
        echo '<option value="' . $row['LOCAL_VOTACAO'] . '">' . $row['LOCAL_VOTACAO'] . '</option>';
    }
}
//------------------------------------------------------------------------------
?>

