<?php
require_once 'config/geral.php';
require_once 'config/funcoes.php';

$db = Conexao::getInstance();

try {
    echo "Sample names for 2012:\n";
    $stmt = $db->query("SELECT nr_local_votacao, nm_local_votacao FROM locais_votacao_mestre WHERE ano_primeira_eleicao = 2012 LIMIT 10");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($rows);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
