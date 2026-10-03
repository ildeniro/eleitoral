<?php
// importar_locais_historicos_TODOS_ANOS_ACRE_COM_NR_SECAO.php → VERSÃO FINAL SUPREMA
set_time_limit(0);
ini_set('memory_limit', '8G');

$diretorio = 'sn1/downloads_historico/';

if (!is_dir($diretorio)) {
    die("<h1 style='color:red'>Pasta não encontrada: $diretorio</h1>");
}

// === TODOS OS ANOS CORRETOS ===
$arquivos = [
    2012 => "votacao_secao_2012_AC.csv",
];

echo "<h1 style='color:#00ff00; background:black; padding:40px; text-align:center; font-size:60px; font-weight:bold;'>
      IMPORTAÇÃO FINAL OFICIAL — LOCAIS DO ACRE
      </h1>";

$total_novos = 0;
$total_atualizados = 0;

foreach ($arquivos as $ano => $arquivo) {
    $caminho = $diretorio . $arquivo;
    if (!file_exists($caminho)) {
        echo "<p style='color:orange; font-size:22px;'>Arquivo não encontrado: $arquivo (pula ano $ano)</p>";
        continue;
    }

    echo "<h2 style='color:#0066ff; font-size:42px;'>Processando ano <strong>$ano</strong> → $arquivo</h2>";
    flush();

    $handle = fopen($caminho, 'r');
    if (!$handle) continue;

    stream_filter_append($handle, 'convert.iconv.ISO-8859-1/UTF-8//IGNORE', STREAM_FILTER_READ);
    fgetcsv($handle, 0, ';'); // pula cabeçalho

    $batch = [];
    $cont_ano_novo = 0;
    $cont_ano_atualizado = 0;

    while (($row = fgetcsv($handle, 0, ';')) !== false) {

        $nr_local     = (int)($row[22] ?? 0);  // NR_LOCAL_VOTACAO
        $nr_zona      = (int)($row[15] ?? 0);  // NR_ZONA
        $cd_mun_tse   = $row[13] ?? '';
        $nm_local     = trim($row[24] ?? 'Local sem nome');
        $endereco     = trim($row[25] ?? '');
        $nm_municipio = trim($row[14] ?? '');

        if ($nr_local < 1000 || empty($cd_mun_tse)) continue;

        // Busca municipio_id
        $stmt = $db->prepare("SELECT id FROM bsc_municipios WHERE cod_tse = ? LIMIT 1");
        $stmt->execute([$cd_mun_tse]);
        $municipio_id = $stmt->fetchColumn();
        if (!$municipio_id) continue;

        // === DADOS COMPLETOS COM NR_SECAO ===
        $batch[] = [
            $nr_local,
            $nr_zona,
            $nm_local,
            $endereco,
            null,                    // cd_bairro
            null,           // nm_bairro
            null, null, null, null,  // cep, telefone, acessibilidade
            null, null,              // latitude, longitude
            'Escola',                // geolocalizacao
            null, null,              // regional_id, regional_real_id
            '0',                     // ajuste
            $municipio_id,
            $ano,                    // ano_primeira_eleicao
            1,                       // ativo
            "$nm_municipio - Zona $nr_zona"
        ];

        if (count($batch) >= 2000) {
            $res = inserirOuAtualizarBatch($db, $batch);
            $cont_ano_novo += $res['novos'];
            $cont_ano_atualizado += $res['atualizados'];
            $total_novos += $res['novos'];
            $total_atualizados += $res['atualizados'];
            $batch = [];
            echo "<h3 style='color:lime; font-size:26px;'>
                  +{$res['novos']} novos locais | +{$res['atualizados']} anos atualizados (ano $ano)
                  </h3>";
            flush();
        }
    }

    if (!empty($batch)) {
        $res = inserirOuAtualizarBatch($db, $batch);
        $cont_ano_novo += $res['novos'];
        $cont_ano_atualizado += $res['atualizados'];
        $total_novos += $res['novos'];
        $total_atualizados += $res['atualizados'];
    }

    fclose($handle);
    echo "<h2 style='color:#00ff00; font-size:48px; background:#000; padding:20px;'>
          ANO $ano → $cont_ano_novo NOVOS + $cont_ano_atualizado ATUALIZADOS
          </h2><hr>";
}

echo "<div style='background:#000; color:#0f0; padding:80px; text-align:center; font-size:60px; margin-top:50px;'>";
echo "<strong>IMPORTAÇÃO CONCLUÍDA COM SUCESSO!</strong><br><br>";
echo "Novos locais inseridos: <strong style='color:#ff0066;'>$total_novos</strong><br>";
echo "Locais com ano corrigido: <strong style='color:yellow;'>$total_atualizados</strong><br><br>";
echo "<a href='dashboard3.php' style='color:white; background:#c00; padding:40px 100px; font-size:50px; text-decoration:none; border-radius:30px; font-weight:bold;'>
      VER A HISTÓRIA ELEITORAL DO ACRE AGORA
      </a>";
echo "</div>";

// === FUNÇÃO PERFEITA ===
function inserirOuAtualizarBatch($db, $batch) {
    $sql = "INSERT INTO locais_votacao_mestre 
        (nr_local_votacao, nr_zona, nm_local_votacao, ds_endereco, cd_bairro, nm_bairro, nr_cep,
         nr_telefone_local, cd_situ_secao_acessibilidade, ds_situ_secao_acessibilidade,
         latitude, longitude, geolocalizacao, regional_id, regional_real_id, ajuste,
         municipio_id, ano_primeira_eleicao, ativo, regional)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) 
        ON DUPLICATE KEY UPDATE
         nr_local_votacao = VALUES(nr_local_votacao),
         ano_primeira_eleicao = VALUES(ano_primeira_eleicao)";

    $stmt = $db->prepare($sql);
    $novos = 0;
    $atualizados = 0;

    foreach ($batch as $dados) {
        $stmt->execute($dados);
        if ($stmt->rowCount() >= 1) {
            $novos += ($stmt->rowCount() == 1 && $db->lastInsertId() > 0) ? 1 : 0;
            $atualizados += ($stmt->rowCount() >= 1 && $db->lastInsertId() == 0) ? 1 : 0;
        }
    }
    return ['novos' => $novos, 'atualizados' => $atualizados];
}
?>