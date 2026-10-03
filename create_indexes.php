<?php
// Script para criar índices de otimização do dashboard3
// Execute este arquivo uma vez via navegador: http://localhost/eleitoral/create_indexes.php

require_once 'config/geral.php';
require_once 'config/Conexao.class.php';

$db = Conexao::getInstance();

echo "<h2>Criando índices para otimização do Dashboard3...</h2>";
echo "<pre>";

$indexes = [
    "CREATE INDEX IF NOT EXISTS idx_candidatos_cpf ON candidatos_detalhados(nr_cpf_candidato, ano_eleicao)",
    "CREATE INDEX IF NOT EXISTS idx_candidatos_titulo ON candidatos_detalhados(nr_titulo_eleitoral_candidato, ano_eleicao)",
    "CREATE INDEX IF NOT EXISTS idx_resultados_sq_ano ON resultados_eleitorais(sq_candidato, ano, qtd_votos)",
    "CREATE INDEX IF NOT EXISTS idx_resultados_cargo_num_ano ON resultados_eleitorais(cod_cargo, num_candidato, ano, qtd_votos)",
    "CREATE INDEX IF NOT EXISTS idx_resultados_ano ON resultados_eleitorais(ano)"
];

$success = 0;
$errors = 0;

foreach ($indexes as $sql) {
    try {
        $db->exec($sql);
        echo "✓ Índice criado com sucesso\n";
        $success++;
    } catch (PDOException $e) {
        echo "✗ Erro: " . $e->getMessage() . "\n";
        $errors++;
    }
}

echo "\n";
echo "========================================\n";
echo "Resumo:\n";
echo "  ✓ Sucessos: $success\n";
echo "  ✗ Erros: $errors\n";
echo "========================================\n";
echo "\nÍndices criados! A página dashboard3.php agora deve carregar muito mais rápido.\n";
echo "Você pode deletar este arquivo após a execução.\n";
echo "</pre>";
?>
