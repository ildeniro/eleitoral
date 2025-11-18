<?php

//------------------------------------------------------------------------------
include_once('../config/geral.php');
include_once('../config/funcoes.php');
$db = Conexao::getInstance();

$zona = $_POST['zona'];

$stmp = $db->prepare("SELECT e.NR_SECAO AS SECAO     
                     FROM 2024_secoes e 
                     WHERE e.NR_ZONA = ? AND e.TIPO = 'Principal' 
                     ORDER BY e.NR_SECAO ASC");
$stmp->bindValue(1, $zona);
$stmp->execute();

if ($stmp->rowCount() == 0) {
    echo '<option value="">Selecione primeiro a zona</option>';
} else {
    echo '<option value="">' . 'Escolha uma seção' . '</option>';
    while ($row = $stmp->fetch(PDO::FETCH_ASSOC)) {
        if (vf_secaoo_apuracao_2024($zona, $row['SECAO'])) {
            echo '<option disabled="true" value="' . $row['SECAO'] . '">' . $row['SECAO'] . (is_numeric(pesquisar("ID", "2024_secoes", 'NR_SECAO_PRINCIPAL', "=", $row['SECAO'], "")) ? retornar_agregacao($zona, $row['SECAO']) : "") . ' - Essa seção já foi apurada</option>';
        } else {
            echo '<option value="' . $row['SECAO'] . '">' . $row['SECAO'] . (is_numeric(pesquisar("ID", "2024_secoes", 'NR_SECAO_PRINCIPAL', "=", $row['SECAO'], "")) ? retornar_agregacao($zona, $row['SECAO']) : "") . '</option>';
        }
    }
}
//------------------------------------------------------------------------------
?>

