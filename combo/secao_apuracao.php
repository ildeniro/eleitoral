<?php

//------------------------------------------------------------------------------
include_once('../config/geral.php');
include_once('../config/funcoes.php');
$db = Conexao::getInstance();

$zona = $_POST['zona'];

$stmp = $db->prepare("SELECT e.NR_SECAO AS SECAO, e.AGREGA AS SECAO_AGREGADA_NUMERO    
                     FROM eleicoes_localidades_2022 e 
                     WHERE e.ZONA = ? AND e.DS_TIPO_SECAO_AGREGADA = 'Principal' 
                     ORDER BY e.NR_SECAO ASC");
$stmp->bindValue(1, $zona);
$stmp->execute();

if ($stmp->rowCount() == 0) {
    echo '<option value="">Selecione primeiro a zona</option>';
} else {
    echo '<option value="">' . 'Escolha uma seção' . '</option>';
    while ($row = $stmp->fetch(PDO::FETCH_ASSOC)) {
        if (vf_secaoo_apuracao($zona, $row['SECAO'])) {
            echo '<option disabled="true" value="' . $row['SECAO'] . '">' . $row['SECAO'] . ($row['SECAO_AGREGADA_NUMERO'] != '' ? ", " . $row['SECAO_AGREGADA_NUMERO'] : "") . ' - Essa seção já foi apurada</option>';
        } else {
            echo '<option value="' . $row['SECAO'] . '">' . $row['SECAO'] . ($row['SECAO_AGREGADA_NUMERO'] != '' ? ", " . $row['SECAO_AGREGADA_NUMERO'] : "") . '</option>';
        }
    }
}
//------------------------------------------------------------------------------
?>

