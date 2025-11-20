<?php

// importar_dados_abertos.php - VERSÃO FINAL 100% FUNCIONAL

set_time_limit(0);
ini_set('memory_limit', '1G');

$ano = 2022;
$csv_url = 'https://cdn.tse.jus.br/estatistica/sead/odsele/votacao_secao/votacao_secao_2022_AC.csv';
$diretorio = 'sn1/downloads_historico/';
$log_file = $diretorio . 'import_2022_progress.log';
$csv_path = $diretorio . 'votacao_secao_2022_AC.csv';

if (!is_dir($diretorio))
    mkdir($diretorio, 0777, true);

// Baixa CSV
if (!file_exists($csv_path)) {
    echo "Baixando CSV de 2022 (AC)...\n";
    downloadFile($csv_url, $csv_path);
}

// Progresso
$linha_inicio = file_exists($log_file) ? (int) file_get_contents($log_file) : 0;
echo "Retomando da linha: $linha_inicio\n";

$handle = fopen($csv_path, 'r');
if (!$handle)
    die("Erro ao abrir CSV.\n");

// Pula cabeçalho
$header = fgetcsv($handle, 0, ';');
$linha = 1;
$batch = [];
$contador = 0;

while (($row = fgetcsv($handle, 0, ';')) !== false) {
    
    $linha++;
    if ($linha <= $linha_inicio)
        continue;

    // Filtro: Rio Branco = 01392
    if ($row[13] !== '1392')
        continue;

    $ano_eleicao = (int) $row[2];           // ANO_ELEICAO
    $turno = (int) $row[5];                 // NR_TURNO
    $nr_zona = (int) $row[15];              // NR_ZONA
    $nr_secao = (int) $row[16];             // NR_SECAO
    $nr_local_votacao = (int) $row[22];     // NR_LOCAL_VOTACAO
    $sq_candidato = $row[23] ?? null;      // SQ_CANDIDATO
    $nr_votavel = $row[19];                // NR_VOTAVEL
    // cod_cargo = CD_CARGO (índice 17)
    $cod_cargo = $row[17];

    // num_candidato: NULL para brancos, nulos, legenda
    $num_candidato = in_array($nr_votavel, ['95', '96', '97', '98', '99']) ? null : $nr_votavel;

    // partido: NULL para votos especiais
    $partido = null;

    // tipo_voto
    $tipo_voto = match ($nr_votavel) {
        '95', '96' => 'branco',
        '97', '98' => 'nulo',
        '99' => 'legenda',
        default => 'nominal'
    };

    $qtd_votos = (int) $row[21];
    
    if ($qtd_votos <= 0)
        continue;

    $batch[] = [
        $ano_eleicao,
        $turno,
        $nr_zona,
        $nr_secao,
        $nr_local_votacao,
        $sq_candidato,
        $num_candidato,
        $cod_cargo,
        $partido,
        $tipo_voto,
        $qtd_votos,
        date('Y-m-d H:i:s')
    ];
    
    echo "Quantidade: ".count($batch);

    if (count($batch) >= 5000) {

        try {
            inserirBatch($db, $batch);
            $contador += count($batch);
            echo "Inseridas $contador linhas (linha $linha)\n";
        } catch (Exception $e) {
            echo "ERRO no batch (linha $linha): " . $e->getMessage() . "\n";
            break;
        }
        $batch = [];
        file_put_contents($log_file, $linha);
    }
}

if (!empty($batch)) {

    try {
        inserirBatch($db, $batch);
        echo "Último batch inserido.\n";
    } catch (Exception $e) {
        echo "ERRO final: " . $e->getMessage() . "\n";
    }
}

fclose($handle);
file_put_contents($log_file, $linha);
echo "IMPORTAÇÃO CONCLUÍDA! Total processado: $linha linhas.\n";

// === FUNÇÕES ===
function inserirBatch($db, $batch) {

    $sql = "INSERT INTO resultados_eleitorais 
        (ano, turno, nr_zona, nr_secao, nr_local_votacao, sq_candidato, num_candidato, 
         cod_cargo, partido, tipo_voto, qtd_votos, data_importacao)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            nr_zona = VALUES(nr_zona),
            nr_secao = VALUES(nr_secao),
            num_candidato = VALUES(num_candidato),
            qtd_votos = VALUES(qtd_votos),
            tipo_voto = VALUES(tipo_voto),
            data_importacao = VALUES(data_importacao)";

    $stmt = $db->prepare($sql);
    foreach ($batch as $linha) {
        $stmt->execute($linha);
    }
}

function downloadFile($url, $path) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 600);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    $data = curl_exec($ch);
    curl_close($ch);
    if ($data)
        file_put_contents($path, $data);
}
?>