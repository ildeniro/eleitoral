<?php

//------------------------------------------------------------------------------
include_once('../config/geral.php');
include_once('../config/funcoes.php');
$db = Conexao::getInstance();

$ano = $_POST['ano'];
$tipo = $_POST['tipo'];

$stmp = $db->prepare("SELECT eab.NR_VOTAVEL, eab.NM_VOTAVEL
                     FROM eleicoes_todos_anos AS eab  
                     WHERE eab.DS_CARGO_PERGUNTA = ? AND eab.NR_VOTAVEL NOT IN(95, 96) AND eab.ANO_ELEICAO = ?  
                     GROUP BY eab.NR_VOTAVEL");
$stmp->bindValue(1, $tipo);
$stmp->bindValue(2, $ano);
$stmp->execute();

if ($stmp->rowCount() == 0) {
    echo '<option value="">Nenhuma candidato encontrado</option>';
} else {
    echo '<option value="">' . 'Escolha uma candidato' . '</option>';
    while ($row = $stmp->fetch(PDO::FETCH_ASSOC)) {
        echo '<option value="' . $row['NR_VOTAVEL'] . '">' . $row['NR_VOTAVEL'] . " - " . $row['NM_VOTAVEL'] . '</option>';
    }
}
//------------------------------------------------------------------------------
?>

