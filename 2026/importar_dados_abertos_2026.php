<?php
/**
 * importar_dados_abertos_2026.php
 * Importação em lote dos Dados Abertos do TSE (CSV) para as Eleições 2026
 * Filtrado exclusivamente para os 22 municípios do Estado do Acre
 */

set_time_limit(0);
ini_set('memory_limit', '1G');

$root_dir = dirname(__DIR__);
require_once $root_dir . '/config/geral.php';

$db = Conexao::getInstance();

$ano = 2026;
$diretorio = __DIR__ . '/downloads_historico/';
if (!is_dir($diretorio)) {
    mkdir($diretorio, 0777, true);
}

$csv_filename = 'votacao_secao_2026_AC.csv';
$csv_path = $diretorio . $csv_filename;
$log_file = $diretorio . 'import_2026_progress.log';
$csv_url_default = "https://cdn.tse.jus.br/estatistica/sead/odsele/votacao_secao/votacao_secao_2026_AC.csv";

// Lista com os 22 códigos de município do Acre no TSE (normalizados sem e com zeros à esquerda)
$codigos_acre = [
    '1120' => 'Acrelândia',
    '1570' => 'Assis Brasil',
    '1058' => 'Brasiléia',
    '1007' => 'Bujari',
    '1015' => 'Capixaba',
    '1074' => 'Cruzeiro do Sul',
    '1112' => 'Epitaciolândia',
    '1139' => 'Feijó',
    '1104' => 'Jordão',
    '1090' => 'Mâncio Lima',
    '1554' => 'Manoel Urbano',
    '1040' => 'Marechal Thaumaturgo',
    '1511' => 'Plácido de Castro',
    '1023' => 'Porto Acre',
    '1066' => 'Porto Walter',
    '1392' => 'Rio Branco',
    '1082' => 'Rodrigues Alves',
    '1031' => 'Santa Rosa do Purus',
    '1457' => 'Sena Madureira',
    '1538' => 'Senador Guiomard',
    '1473' => 'Tarauacá',
    '1490' => 'Xapuri'
];

function normalizarCodigoMunicipio($cod) {
    return ltrim(trim((string)$cod), '0');
}

function normalizarNomeCargo($cd_cargo) {
    $cargos = [
        '1' => 'presidente',
        '2' => 'vice-presidente',
        '3' => 'governador',
        '4' => 'vice-governador',
        '5' => 'senador',
        '6' => 'deputado federal',
        '7' => 'deputado estadual',
        '8' => 'deputado distrital',
        '11' => 'prefeito',
        '12' => 'vice-prefeito',
        '13' => 'vereador'
    ];
    return $cargos[(string)$cd_cargo] ?? (string)$cd_cargo;
}

function downloadArquivo($url, $path) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 900);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) DadosAbertos2026');
    $data = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200 && $data) {
        file_put_contents($path, $data);
        return true;
    }
    return false;
}

function inserirLoteResultados($db, $batch) {
    $sql = "
        INSERT INTO 2026_resultados 
            (ANO_ELEICAO, TURNO, UF, COD_MUNICIPIO_TSE, ZONA, SECAO, LOCAL_VOTACAO, COD_CARGO, NUM_CANDIDATO, QTD_VOTOS, TIPO_VOTO, PARTIDO, DATA_CADASTRO)
        VALUES 
            (?, ?, 'AC', ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE 
            QTD_VOTOS = VALUES(QTD_VOTOS),
            LOCAL_VOTACAO = VALUES(LOCAL_VOTACAO),
            PARTIDO = VALUES(PARTIDO),
            DATA_UPDATE = NOW()
    ";
    $stmt = $db->prepare($sql);
    foreach ($batch as $linha) {
        $stmt->execute($linha);
    }
}

// Ação de download sob demanda ou limpeza
$acao = $_POST['acao'] ?? ($_GET['acao'] ?? '');

if ($acao === 'limpar') {
    $db->exec("TRUNCATE TABLE 2026_resultados");
    $msg_sucesso = "Tabela 2026_resultados foi esvaziada com sucesso! O painel está zerado.";
}

if ($acao === 'gerar_simulado') {
    require_once __DIR__ . '/gerar_simulado_2026.php';
    $msg_sucesso = "Arquivo simulado gerado com sucesso em 2026/downloads_historico/votacao_secao_2026_AC.csv!";
}

// Contagem atual na tabela
$total_atual_resultados = (int)$db->query("SELECT COUNT(*) FROM 2026_resultados WHERE ANO_ELEICAO = 2026")->fetchColumn();
$total_secoes_apuradas = (int)$db->query("SELECT COUNT(DISTINCT ZONA, SECAO) FROM 2026_resultados WHERE ANO_ELEICAO = 2026")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Importar Dados Abertos 2026 - Acre</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0f172a; color: #f8fafc; padding: 30px; }
        .container { max-width: 950px; margin: 0 auto; background: #1e293b; border-radius: 12px; padding: 25px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        h1 { color: #38bdf8; margin-top: 0; }
        .badge { background: #0284c7; color: white; padding: 4px 10px; border-radius: 6px; font-size: 13px; }
        .btn { background: #2563eb; color: white; border: none; padding: 10px 20px; font-weight: bold; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; transition: background 0.2s; }
        .btn:hover { background: #1d4ed8; }
        .btn-success { background: #16a34a; }
        .btn-success:hover { background: #15803d; }
        .btn-danger { background: #dc2626; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-purple { background: #7c3aed; }
        .btn-purple:hover { background: #6d28d9; }
        .btn-outline { background: transparent; border: 1px solid #475569; color: #e2e8f0; }
        .btn-outline:hover { background: #334155; }
        pre { background: #090d16; border: 1px solid #334155; padding: 15px; border-radius: 8px; overflow-x: auto; color: #a5f3fc; font-family: monospace; }
        .grid-munis { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 8px; margin: 15px 0; font-size: 12px; }
        .muni-item { background: #334155; padding: 6px 10px; border-radius: 4px; }
        .progress-box { margin-top: 20px; }
        .alert-box { background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; color: #a7f3d0; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; }
        .stats-banner { display: flex; gap: 20px; background: #0f172a; padding: 15px; border-radius: 8px; border: 1px solid #334155; margin-bottom: 20px; }
        .stats-item h4 { font-size: 11px; text-transform: uppercase; color: #94a3b8; margin: 0; }
        .stats-item p { font-size: 20px; font-weight: bold; color: #38bdf8; margin: 4px 0 0 0; }
        .actions-bar { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 15px; }
    </style>
</head>
<body>
<div class="container">
    <h1>📥 Importador e Simulador de Dados Abertos 2026</h1>
    <p>Esta rotina gerencia a importação de dados por seção para os <strong>22 municípios do Estado do Acre</strong> na tabela <code>2026_resultados</code>.</p>
    
    <?php if (isset($msg_sucesso)): ?>
        <div class="alert-box">✔ <?= htmlspecialchars($msg_sucesso); ?></div>
    <?php endif; ?>

    <div class="stats-banner">
        <div class="stats-item">
            <h4>Registros na Base 2026</h4>
            <p><?= number_format($total_atual_resultados, 0, ',', '.'); ?></p>
        </div>
        <div class="stats-item">
            <h4>Seções com Apuração</h4>
            <p><?= number_format($total_secoes_apuradas, 0, ',', '.'); ?> de 2.411</p>
        </div>
        <div class="stats-item">
            <h4>Arquivo Local</h4>
            <p style="font-size:14px;color:#a5f3fc;font-family:monospace;"><?= file_exists($csv_path) ? 'votacao_secao_2026_AC.csv (' . number_format(filesize($csv_path)/1024, 1) . ' KB)' : 'Nenhum' ?></p>
        </div>
    </div>

    <!-- Barra de Ações Rápidas de Simulação -->
    <div style="background:#090d16; border:1px solid #334155; border-radius:8px; padding:15px; margin-bottom:20px;">
        <h3 style="color:#e2e8f0; font-size:15px; margin-top:0;">⚡ Ações Rápidas de Teste e Simulação:</h3>
        <div class="actions-bar">
            <!-- Gerar Simulado -->
            <form method="post" style="display:inline;">
                <input type="hidden" name="acao" value="gerar_simulado">
                <button type="submit" class="btn btn-purple" title="Gera um CSV realista com seções de todo o Acre">
                    🧪 1. Gerar/Restaurar CSV Simulado
                </button>
            </form>

            <!-- Executar Importação do Simulado -->
            <form method="post" style="display:inline;">
                <input type="hidden" name="acao" value="processar">
                <button type="submit" class="btn btn-success" title="Lê o CSV local e popula a base 2026">
                    ▶ 2. Importar CSV para a Base
                </button>
            </form>

            <!-- Ver Apuração -->
            <a href="resultado-geral-2026.php" class="btn btn-primary" target="_blank">
                📊 3. Abrir Painel de Apuração
            </a>

            <!-- Teste ao Vivo TSE -->
            <a href="teste_simulado_tse_aovivo.php" class="btn btn-purple" style="background:#0284c7;" target="_blank">
                🛰️ 4. Conectar Simulado TSE ao Vivo
            </a>

            <!-- Limpar Tabela -->
            <form method="post" style="display:inline;" onsubmit="return confirm('Deseja realmente limpar todos os resultados de 2026?');">
                <input type="hidden" name="acao" value="limpar">
                <button type="submit" class="btn btn-danger" title="Zera a tabela 2026_resultados">
                    🗑️ Limpar Tabela (Zerar)
                </button>
            </form>
        </div>
    </div>

    <h3>Municípios Monitorados (22 municípios do Acre):</h3>
    <div class="grid-munis">
        <?php foreach ($codigos_acre as $cod => $nome): ?>
            <div class="muni-item">📍 <strong><?= $nome; ?></strong> (<?= sprintf('%05d', $cod); ?>)</div>
        <?php endforeach; ?>
    </div>

    <form method="post" enctype="multipart/form-data" style="margin-top: 20px; border-top: 1px solid #334155; padding-top: 20px;">
        <p><strong>Upload Personalizado:</strong> Enviar outro arquivo CSV de votação</p>
        <input type="file" name="csv_upload" accept=".csv" style="margin-bottom: 15px; display:block;">

        <p><strong>Download Remoto Oficial:</strong> Link do Portal de Dados Abertos</p>
        <input type="text" name="csv_url" value="<?= htmlspecialchars($csv_url_default); ?>" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #475569; background: #0f172a; color: #fff; margin-bottom: 15px;">

        <input type="hidden" name="acao" value="processar">
        <button type="submit" class="btn btn-success">▶ Processar CSV Indicado</button>
    </form>

<?php
if ($acao === 'processar') {
    echo "<div class='progress-box'><h3>Processando importação...</h3><pre>";

    // Se houve upload
    if (isset($_FILES['csv_upload']) && $_FILES['csv_upload']['error'] === UPLOAD_ERR_OK) {
        move_uploaded_file($_FILES['csv_upload']['tmp_name'], $csv_path);
        echo "Arquivo enviado por upload salvo com sucesso.\n";
    }

    // Se o arquivo não existir localmente, tenta baixar
    if (!file_exists($csv_path)) {
        $url_alvo = !empty($_POST['csv_url']) ? trim($_POST['csv_url']) : $csv_url_default;
        echo "Arquivo local não encontrado. Tentando baixar de: {$url_alvo} ...\n";
        flush();
        $ok = downloadArquivo($url_alvo, $csv_path);
        if (!$ok) {
            echo "Aviso: Não foi possível baixar diretamente (o arquivo do TSE pode ainda não estar publicado ou exigir autenticação/captcha).\n";
            echo "Dica: Você pode baixar o CSV ou ZIP de votacao_secao_2026_AC no portal dadosabertos.tse.jus.br e colocar em:\n";
            echo "{$csv_path}\n";
            echo "</pre></div>";
            exit;
        }
    }

    $handle = fopen($csv_path, 'r');
    if (!$handle) {
        die("Erro ao abrir o arquivo CSV em: $csv_path\n</pre></div>");
    }

    // Tenta detectar delimitador (; ou ,)
    $primeiraLinha = fgets($handle);
    $delimitador = (strpos($primeiraLinha, ';') !== false) ? ';' : ',';
    rewind($handle);

    // Lê cabeçalho
    $header = fgetcsv($handle, 0, $delimitador);
    if (!$header) {
        die("Erro: Cabeçalho vazio.\n</pre></div>");
    }

    // Identifica índices das colunas
    $mapHeader = [];
    foreach ($header as $idx => $colName) {
        $mapHeader[strtoupper(trim(str_replace('"', '', $colName)))] = $idx;
    }

    $idx_ano       = $mapHeader['ANO_ELEICAO'] ?? 2;
    $idx_turno     = $mapHeader['NR_TURNO'] ?? 5;
    $idx_uf        = $mapHeader['SG_UF'] ?? 10;
    $idx_muni      = $mapHeader['CD_MUNICIPIO'] ?? 13;
    $idx_zona      = $mapHeader['NR_ZONA'] ?? 15;
    $idx_secao     = $mapHeader['NR_SECAO'] ?? 16;
    $idx_cargo     = $mapHeader['CD_CARGO'] ?? 17;
    $idx_votavel   = $mapHeader['NR_VOTAVEL'] ?? 19;
    $idx_votos     = $mapHeader['QT_VOTOS'] ?? 21;
    $idx_local     = $mapHeader['NR_LOCAL_VOTACAO'] ?? 22;
    $idx_sq_cand   = $mapHeader['SQ_CANDIDATO'] ?? 23;

    $linha = 1;
    $inseridos = 0;
    $ignorados_fora_acre = 0;
    $batch = [];

    while (($row = fgetcsv($handle, 0, $delimitador)) !== false) {
        $linha++;

        $codMuniRaw = $row[$idx_muni] ?? '';
        $codNormalizado = normalizarCodigoMunicipio($codMuniRaw);

        // Filtro restrito aos 22 municípios do Acre
        if (!isset($codigos_acre[$codNormalizado])) {
            $ignorados_fora_acre++;
            continue;
        }

        $ano_eleicao   = (int)($row[$idx_ano] ?? 2026);
        $turno         = (int)($row[$idx_turno] ?? 1);
        $cd_muni_tse   = sprintf('%05d', (int)$codNormalizado);
        $nr_zona       = (int)($row[$idx_zona] ?? 0);
        $nr_secao      = (int)($row[$idx_secao] ?? 0);
        $cd_cargo_raw  = $row[$idx_cargo] ?? '';
        $cod_cargo     = normalizarNomeCargo($cd_cargo_raw);
        $nr_votavel    = trim((string)($row[$idx_votavel] ?? ''));
        $qtd_votos     = (int)($row[$idx_votos] ?? 0);
        $local_votacao = trim((string)($row[$idx_local] ?? ''));

        if ($qtd_votos <= 0) continue;

        // Trata tipo de voto
        $tipo_voto = match ($nr_votavel) {
            '95', '96' => 'branco',
            '97', '98' => 'nulo',
            '99' => 'legenda',
            default => 'nominal'
        };

        $num_candidato = in_array($nr_votavel, ['95', '96', '97', '98', '99']) ? strtoupper($tipo_voto) : $nr_votavel;
        $partido = null;

        $batch[] = [
            $ano_eleicao,
            $turno,
            $cd_muni_tse,
            $nr_zona,
            $nr_secao,
            $local_votacao,
            $cod_cargo,
            $num_candidato,
            $qtd_votos,
            $tipo_voto,
            $partido
        ];

        if (count($batch) >= 2000) {
            inserirLoteResultados($db, $batch);
            $inseridos += count($batch);
            echo "Linha {$linha}: {$inseridos} registros inseridos para o Acre...\n";
            flush();
            $batch = [];
        }
    }

    if (!empty($batch)) {
        inserirLoteResultados($db, $batch);
        $inseridos += count($batch);
    }

    fclose($handle);

    echo "\n============================================\n";
    echo "🎉 IMPORTAÇÃO 2026 CONCLUÍDA COM SUCESSO!\n";
    echo "Total de linhas lidas no CSV: {$linha}\n";
    echo "Total de registros inseridos para o Acre: {$inseridos}\n";
    echo "============================================\n";
    echo "</pre>";
    echo "<p><a href='resultado-geral-2026.php' class='btn btn-success'>👉 Abrir Apuração Geral 2026</a></p></div>";
}
?>
</div>
</body>
</html>
