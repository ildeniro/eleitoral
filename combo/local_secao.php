<?php

//------------------------------------------------------------------------------
include_once('../config/geral.php');
$db = Conexao::getInstance();

$local = $_POST['local'];

$stmp = $db->prepare("SELECT NR_SECAO   
                      FROM 2024_secoes  
                      WHERE LOCAL_VOTACAO_ID = ? 
                      GROUP BY NR_SECAO 
                      ORDER BY NR_SECAO ASC");
$stmp->bindValue(1, $local);
$stmp->execute();

if ($stmp->rowCount() == 0) {
    echo '<option value="">Nenhuma seção encontrada</option>';
} else {
    echo '<option value="">' . 'Escolha uma seção' . '</option>';
    while ($row = $stmp->fetch(PDO::FETCH_ASSOC)) {
        echo '<option value="' . $row['NR_SECAO'] . '">' . $row['NR_SECAO'] . '</option>';
    }
}
//------------------------------------------------------------------------------
?>

