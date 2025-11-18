<?php

//------------------------------------------------------------------------------
include_once('../config/geral.php');
$db = Conexao::getInstance();

$zona = $_POST['zona'];
$secao = $_POST['secao'];

$stmp = $db->prepare("SELECT m.nome AS MUNICIPIO, l.NM_LOCAL_VOTACAO   
                     FROM 2024_secoes e 
                     INNER JOIN 2024_locais_votacao AS l ON l.ID = e.LOCAL_VOTACAO_ID  
                     INNER JOIN bsc_municipios AS m ON m.cod_tse = l.CD_MUNICIPIO 
                     WHERE e.NR_ZONA = ? AND e.NR_SECAO = ? 
                     GROUP BY l.NM_LOCAL_VOTACAO
                     ORDER BY l.NM_LOCAL_VOTACAO ASC");
$stmp->bindValue(1, $zona);
$stmp->bindValue(2, $secao);
$stmp->execute();

if ($stmp->rowCount() == 0) {
    echo 'Nenhum Local de Votação Encontrado';
} else {
    while ($row = $stmp->fetch(PDO::FETCH_ASSOC)) {
        echo $row['NM_LOCAL_VOTACAO'];
    }
}
//------------------------------------------------------------------------------
?>

