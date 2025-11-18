<?php

//------------------------------------------------------------------------------
include_once('../config/geral.php');
$db = Conexao::getInstance();

$municipio = $_POST['municipio'];

$stmp = $db->prepare("SELECT e.ID, e.NM_BAIRRO as BAIRRO   
                     FROM bsc_bairros e 
                     WHERE e.MUNICIPIO_ID = ?  
                     GROUP BY e.NM_BAIRRO
                     ORDER BY e.NM_BAIRRO ASC");
$stmp->bindValue(1, $municipio);
$stmp->execute();

if ($stmp->rowCount() == 0) {
    echo '<option value="">Nenhum bairro encontrado</option>';
} else {
    echo '<option value="">' . 'Escolha um bairro' . '</option>';
    while ($row = $stmp->fetch(PDO::FETCH_ASSOC)) {
        echo '<option value="' . $row['ID'] . '">' . $row['BAIRRO'] . '</option>';
    }
}
//------------------------------------------------------------------------------
?>

