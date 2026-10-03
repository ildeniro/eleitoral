<?php
include_once('../config/geral.php');
include_once('../config/funcoes.php');
$db = Conexao::getInstance();

$q = $_GET['q'] ?? '';

if (strlen($q) < 3) {
    echo json_encode([]);
    exit;
}

// Proteção básica
$q = "%" . $q . "%";

try {
    // Mesma query do dashboard3.php, mas com filtro WHERE
    // Removido nm_candidato pois pode não existir na tabela candidatos_detalhados
    // Melhoria no GROUP BY para juntar candidatos com mesmo CPF mas nomes diferentes
    $sql = "
        SELECT 
        -- Pega o nome mais recente (baseado no ano da eleição)
        SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT nm_urna_candidato ORDER BY ano_eleicao DESC SEPARATOR '||'), '||', 1) AS nome_urna,
        MAX(nr_cpf_candidato) AS cpf,
        MAX(nr_titulo_eleitoral_candidato) AS titulo,
        MAX(dt_nascimento) AS dt_nascimento,
        GROUP_CONCAT(DISTINCT ano_eleicao ORDER BY ano_eleicao DESC SEPARATOR ', ') AS anos_eleicoes,
        COUNT(*) AS total_eleicoes,
        MIN(ano_eleicao) AS primeira_eleicao,
        MAX(ano_eleicao) AS ultima_eleicao
    FROM candidatos_detalhados cd
    WHERE nm_urna_candidato LIKE ?
    GROUP BY 
        -- Regra de desduplicação: Prioriza Título > CPF > Nome+Nascimento
        -- Normaliza removendo pontos e traços
        COALESCE(
            NULLIF(TRIM(REPLACE(REPLACE(REPLACE(nr_titulo_eleitoral_candidato, '.', ''), '-', ''), ' ', '')), ''),
            NULLIF(TRIM(REPLACE(REPLACE(REPLACE(nr_cpf_candidato, '.', ''), '-', ''), ' ', '')), ''),
            NULLIF(TRIM(REPLACE(REPLACE(REPLACE(nr_cpf_candidato, '.', ''), '-', ''), ' ', '')), '00000000000'),
            CONCAT(UPPER(TRIM(nm_urna_candidato)), COALESCE(NULLIF(TRIM(dt_nascimento), ''), ''))
        )
    HAVING total_eleicoes >= 1
    ORDER BY 
        total_eleicoes DESC, 
        ultima_eleicao DESC, 
        nome_urna ASC
    LIMIT 50";

    $stmt = $db->prepare($sql);
    $stmt->execute([$q]);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($resultados);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
