<?php

//------------------------------------------------------------------------------
include_once('../config/geral.php');
$db = Conexao::getInstance();

$zona = $_POST['zona'];
$secao = $_POST['secao'];

$stmp = $db->prepare("SELECT e.MUNICIPIO, e.LOCAL_VOTACAO   
                     FROM eleicoes_localidades_2022 e 
                     WHERE e.ZONA = ? AND e.NR_SECAO = ? 
                     GROUP BY e.LOCAL_VOTACAO
                     ORDER BY e.LOCAL_VOTACAO ASC");
$stmp->bindValue(1, $zona);
$stmp->bindValue(2, $secao);
$stmp->execute();

if ($stmp->rowCount() == 0) {
    echo '';
} else {
    while ($row = $stmp->fetch(PDO::FETCH_ASSOC)) {
        echo $row['MUNICIPIO'] . "<br/>" . $row['LOCAL_VOTACAO'];
    }
}
//------------------------------------------------------------------------------
?>

