<?php
/**
 * leitura_2026.php
 * Parser e persistência de dados dos Boletins de Urna (BUs decodificados em JSON)
 * para a tabela `2026_resultados`
 */

$root_dir = dirname(__DIR__);
require_once $root_dir . '/config/geral.php';

function normalizarCodigoCargo($rawCargo) {
    if (is_array($rawCargo)) {
        $rawCargo = reset($rawCargo);
    }
    $rawCargo = strtolower(trim((string)$rawCargo));
    
    // Mapeamento padrão TSE
    $map = [
        'governador' => 'governador',
        'vice-governador' => 'governador',
        'senador' => 'senador',
        'deputadofederal' => 'deputado federal',
        'deputado-federal' => 'deputado federal',
        'deputadoestadual' => 'deputado estadual',
        'deputado-estadual' => 'deputado estadual',
        'presidente' => 'presidente',
        'vice-presidente' => 'presidente',
        'prefeito' => 'prefeito',
        'vereador' => 'vereador',
        '1' => 'presidente',
        '3' => 'governador',
        '5' => 'senador',
        '6' => 'deputado federal',
        '7' => 'deputado estadual',
        '11' => 'prefeito',
        '13' => 'vereador'
    ];

    return $map[$rawCargo] ?? $rawCargo;
}

function processarArquivoBUJson($arquivoJson, $db) {
    if (!file_exists($arquivoJson)) {
        return 0;
    }

    $json_content = file_get_contents($arquivoJson);
    if (!$json_content) {
        return 0;
    }

    $data = json_decode($json_content, true);
    if (!$data || !isset($data['EntidadeBoletimUrna'])) {
        return 0;
    }

    $bu = $data['EntidadeBoletimUrna'];
    $identificacaoSecao = $bu['identificacaoSecao'] ?? [];
    $municipioZona = $identificacaoSecao['municipioZona'] ?? [];

    $municipio = isset($municipioZona['municipio']) ? sprintf('%05d', (int)$municipioZona['municipio']) : '00000';
    $zona      = isset($municipioZona['zona']) ? (int)$municipioZona['zona'] : 0;
    $secao     = isset($identificacaoSecao['secao']) ? (int)$identificacaoSecao['secao'] : 0;
    $local     = isset($identificacaoSecao['local']) ? (string)$identificacaoSecao['local'] : '';

    $eleicoes = $bu['resultadosVotacaoPorEleicao'] ?? [];
    $registrosInseridos = 0;

    $sql = "
        INSERT INTO 2026_resultados 
            (ANO_ELEICAO, COD_ELEICAO, UF, COD_MUNICIPIO_TSE, TURNO, ZONA, SECAO, LOCAL_VOTACAO, COD_CARGO, NUM_CANDIDATO, QTD_VOTOS, TIPO_VOTO, PARTIDO, DATA_CADASTRO)
        VALUES 
            (2026, ?, 'AC', ?, 1, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE 
            QTD_VOTOS = VALUES(QTD_VOTOS),
            LOCAL_VOTACAO = VALUES(LOCAL_VOTACAO),
            PARTIDO = VALUES(PARTIDO),
            DATA_UPDATE = NOW()
    ";
    $stmt = $db->prepare($sql);

    foreach ($eleicoes as $eleicaoItem) {
        $idEleicao = (string)($eleicaoItem['idEleicao'] ?? '2026');
        $resultadosVotacao = $eleicaoItem['resultadosVotacao'] ?? [];

        foreach ($resultadosVotacao as $resVoto) {
            $totaisVotosCargo = $resVoto['totaisVotosCargo'] ?? [];

            foreach ($totaisVotosCargo as $cargoItem) {
                $cargoRaw = $cargoItem['codigoCargo'] ?? '';
                $codigoCargo = normalizarCodigoCargo($cargoRaw);

                $votosVotaveis = $cargoItem['votosVotaveis'] ?? [];
                foreach ($votosVotaveis as $voto) {
                    $tipoVoto = $voto['tipoVoto'] ?? 'nominal';
                    if (is_array($tipoVoto)) $tipoVoto = reset($tipoVoto);
                    $qtdVotos = (int)($voto['quantidadeVotos'] ?? 0);

                    if ($qtdVotos < 0) continue;

                    if ($tipoVoto === 'nominal') {
                        $candidato = $voto['identificacaoVotavel']['codigo'] ?? '0';
                        $partido   = $voto['identificacaoVotavel']['partido'] ?? null;
                    } elseif ($tipoVoto === 'legenda') {
                        $candidato = $voto['identificacaoVotavel']['partido'] ?? ($voto['identificacaoVotavel']['codigo'] ?? 'LEGENDA');
                        $partido   = $candidato;
                    } else {
                        // Branco ou Nulo
                        $candidato = strtoupper($tipoVoto);
                        $partido   = null;
                    }

                    try {
                        $stmt->execute([
                            $idEleicao,
                            $municipio,
                            $zona,
                            $secao,
                            $local,
                            $codigoCargo,
                            (string)$candidato,
                            $qtdVotos,
                            $tipoVoto,
                            $partido ? (string)$partido : null
                        ]);
                        $registrosInseridos++;
                    } catch (Exception $e) {
                        // Log silencioso ou debug
                    }
                }
            }
        }
    }

    return $registrosInseridos;
}

// Se executado diretamente via browser ou CLI, reprocessa todos os JSONs da pasta
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    $db = Conexao::getInstance();
    $json_dir = __DIR__ . '/json';
    
    echo "<h1>Leitura em Lote de BUs JSON (Eleições 2026)</h1>";
    if (!is_dir($json_dir)) {
        die("Diretório de JSONs não encontrado: $json_dir");
    }

    $files = glob("{$json_dir}/*.json");
    echo "<p>Total de arquivos JSON encontrados: <strong>" . count($files) . "</strong></p>";

    $totalInseridos = 0;
    foreach ($files as $file) {
        $cnt = processarArquivoBUJson($file, $db);
        $totalInseridos += $cnt;
        echo "Processado: " . basename($file) . " -> <strong>{$cnt}</strong> votos inseridos/atualizados.<br/>";
        flush();
    }

    echo "<h3>Concluído! Total geral de votos processados: {$totalInseridos}</h3>";
}
