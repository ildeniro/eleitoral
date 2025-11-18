<?php

//------------------------------------------------------------------------------
include_once('../config/geral.php');
include_once('../config/funcoes.php');
$db = Conexao::getInstance();

$estado = $_POST['estado'];

$stmp = $db->prepare("SELECT * 
                      FROM bsc_municipios
                      WHERE estado_id = ?
                      ORDER BY nome ASC");
$stmp->bindValue(1, $estado);
$stmp->execute();

if ($stmp->rowCount() == 0) {
    echo '<option value="">Nenhuma cidade encontrada</option>';
} else {
    echo '<option value="">' . 'Escolha uma cidade' . '</option>';
    while ($row = $stmp->fetch(PDO::FETCH_ASSOC)) {
        echo '<option value="' . $row['id'] . '">' . ($row['nome']) . '</option>';
    }
}
//------------------------------------------------------------------------------
?>

