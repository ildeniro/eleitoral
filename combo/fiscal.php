<?php

//------------------------------------------------------------------------------
include_once('../config/geral.php');
$db = Conexao::getInstance();

$tipo = $_POST['tipo'];

if ($tipo == 1) {
    $stmp = $db->prepare("SELECT nome, id 
                         FROM funcoes
                         WHERE status = 1 AND id NOT IN(4, 6)  
                         ORDER BY nome ASC");
    $stmp->execute();
} else if ($tipo == 2) {
    $stmp = $db->prepare("SELECT nome, id 
                      FROM funcoes
                      WHERE status = 1
                      ORDER BY nome ASC");
    $stmp->execute();
}

echo '<option value="">' . 'Selecione a função' . '</option>';
while ($row = $stmp->fetch(PDO::FETCH_ASSOC)) {
    echo '<option value="' . $row['id'] . '">' . $row['nome'] . '</option>';
}
//------------------------------------------------------------------------------
?>

