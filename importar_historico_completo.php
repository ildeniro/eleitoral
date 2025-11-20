<?php
// importar_historico_completo.php - VERSÃO FINAL: TODO O ACRE (22 municípios)
set_time_limit(0);
ini_set('memory_limit', '4G');
ini_set('output_buffering', 'Off');
ini_set('zlib.output_compression', false);

$diretorio = 'sn1/downloads_historico/';
if (!is_dir($diretorio)) {
    die("<h1 style='color:red'>Pasta não encontrada: $diretorio</h1>");
}

$arquivos_esperados = [
    2024 => "votacao_secao_2024_AC.csv",
    2022 => "votacao_secao_2022_AC.csv",
    2020 => "votacao_secao_2020_AC.csv",
    2018 => "votacao_secao_2018_AC.csv",
    2016 => "votacao_secao_2016_AC.csv",
    2014 => "votacao_secao_2014_AC.csv",
    2012 => "votacao_secao_2012_AC.csv",
    2008 => "votacao_secao_2008_AC.csv",
];

echo "<h1 style='color:#0066cc; font-family: Arial;'>IMPORTADOR HISTÓRICO ELEITORAL - ESTADO DO ACRE (22 municípios)</h1>";
echo "<p><strong>Importando TODOS os municípios do Acre</strong> | Arquivos em: <code>$diretorio</code></p><hr>";

// === PROCESSAMENTO DOS ARQUIVOS ===
foreach ($arquivos_esperados as $ano => $nome_arquivo) {
    $csv_path = $diretorio . $nome_arquivo;
    $log_file = $diretorio . "import_{$ano}_progress.log";

    if (!file_exists($csv_path)) {
        echo "<h3 style='color:orange'>Aguardando → $nome_arquivo (não encontrado)</h3>";
        continue;
    }

    echo "<h2 style='color:blue'>Processando: <strong>$ano</strong> → $nome_arquivo</h2>";

    $linha_inicio = file_exists($log_file) ? (int)file_get_contents($log_file) : 0;
    $total_linhas_arquivo = contarLinhas($csv_path);
    echo "Total de linhas no arquivo: <strong>" . number_format($total_linhas_arquivo) . "</strong><br>";
    echo "Retomando da linha: <strong>$linha_inicio</strong><br><br>";

    $handle = fopen($csv_path, 'r');
    if (!$handle) {
        echo "<span style='color:red'>Erro ao abrir arquivo!</span><hr>";
        continue;
    }

    fgetcsv($handle, 0, ';'); // pula cabeçalho
    for ($i = 1; $i < $linha_inicio; $i++) {
        fgetcsv($handle, 0, ';');
    }

    $linha = $linha_inicio;
    $batch = [];
    $contador = 0;
    $ultimo_log = time();

    while (($row = fgetcsv($handle, 0, ';')) !== false) {
        $linha++;

        // Progresso a cada 10.000 linhas
        if ($linha % 10000 == 0) {
            $percent = round(($linha / $total_linhas_arquivo) * 100, 2);
            echo "Processando... $linha / $total_linhas_arquivo linhas ($percent%)<br>";
            flush();
            if (time() - $ultimo_log > 30) {
                file_put_contents($log_file, $linha);
                $ultimo_log = time();
            }
        }

        // REMOVIDO O FILTRO DE MUNICÍPIO → IMPORTA TODO O ACRE!
        $qtd_votos = (int)($row[21] ?? 0);
        if ($qtd_votos <= 0) continue;

        $nr_votavel = $row[19] ?? '';
        $num_candidato = in_array($nr_votavel, ['95','96','97','98','99']) ? null : $nr_votavel;

        $tipo_voto = match ($nr_votavel) {
            '95','96' => 'branco',
            '97','98' => 'nulo',
            '99'     => 'legenda',
            default  => 'nominal'
        };

        $batch[] = [
            (int)$row[2],     // ano
            (int)$row[5],     // turno
            (int)$row[15],    // zona
            (int)$row[16],    // secao
            (int)$row[22],    // local_votacao
            $row[23] ?? null, // sq_candidato
            $num_candidato,
            $row[17],         // cod_cargo
            null,             // partido (pode preencher depois se quiser)
            $tipo_voto,
            $qtd_votos,
            date('Y-m-d H:i:s')
        ];

        if (count($batch) >= 5000) {
            inserirBatch($db, $batch);
            $contador += count($batch);
            $batch = [];
            file_put_contents($log_file, $linha);
        }
    }

    // Último batch
    if (!empty($batch)) {
        inserirBatch($db, $batch);
        $contador += count($batch);
    }

    fclose($handle);
    file_put_contents($log_file, $linha);

    echo "<h3 style='color:green; font-size:22px;'>ANO $ano IMPORTADO 100%</h3>";
    echo "<p><strong>Registros inseridos neste arquivo:</strong> " . number_format($contador) . "</p>";
    echo "<p><strong>Linhas processadas:</strong> " . number_format($linha) . "</p><hr>";
}

echo "<h1 style='color:green; text-align:center; font-size:32px;'>IMPORTAÇÃO DO ACRE COMPLETA!</h1>";
echo "<div style='text-align:center; margin:50px;'>";
echo "<a href='dashboard_dinamico.php' style='font-size:26px; padding:20px 60px; background:#0066cc; color:white; text-decoration:none; border-radius:15px; font-weight:bold;'>";
echo "ABRIR DASHBOARD DINÂMICO";
echo "</a>";
echo "</div>";

// === FUNÇÕES ===
function contarLinhas($arquivo) {
    $handle = fopen($arquivo, 'r');
    $count = 0;
    while (fgets($handle)) $count++;
    fclose($handle);
    return $count - 1; // subtrai cabeçalho
}

function inserirBatch($db, $batch) {
    $sql = "INSERT INTO resultados_eleitorais
        (ano, turno, nr_zona, nr_secao, nr_local_votacao, sq_candidato, num_candidato,
         cod_cargo, partido, tipo_voto, qtd_votos, data_importacao)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            ano = VALUES(ano),
            turno = VALUES(turno),
            nr_zona = VALUES(nr_zona),
            nr_secao = VALUES(nr_secao),
            nr_local_votacao = VALUES(nr_local_votacao),
            cod_cargo = VALUES(cod_cargo),
            num_candidato = VALUES(num_candidato),
            tipo_voto = VALUES(tipo_voto)";

    $stmt = $db->prepare($sql);
    foreach ($batch as $linha) {
        $stmt->execute($linha);
    }
}
?>