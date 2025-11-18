<?php

//------------------------------------------------------------------------------
include_once('../config/geral.php');
include_once('../config/funcoes.php');
$db = Conexao::getInstance();

$zona = $_POST['zona'];

$stmp = $db->prepare("SELECT s.NR_ZONA, s.NR_SECAO       
                     FROM 2024_secoes s 
                     WHERE s.NR_ZONA = ? AND s.TIPO = 'Principal' 
                     ORDER BY s.NR_SECAO ASC");
$stmp->bindValue(1, $zona);
$stmp->execute();

if ($stmp->rowCount() == 0) {
    echo '<option tipo="" value="">Selecione primeiro a zona</option>';
} else {
    echo '<option value="">' . 'Escolha uma seção' . '</option>';
    while ($secao = $stmp->fetch(PDO::FETCH_ASSOC)) {
        echo '<option value="' . $secao['NR_SECAO'] . '">' . $secao['NR_SECAO'] . "" . (is_numeric(pesquisar("ID", "2024_secoes", 'NR_SECAO_PRINCIPAL', "=", $secao['NR_SECAO'], "")) ? retornar_agregacao($secao['NR_ZONA'], $secao['NR_SECAO']) : "") . '</option>';
    }
}
//------------------------------------------------------------------------------
?>

