<?php

// consumir_historico.php - Importa BU de eleições passadas (arquivo TSE)
$uf = 'ac';
$cd_municipio = '01392'; // Rio Branco
$url_base = 'https://resultados.tse.jus.br';
$max_requests_per_second = 100;
$start_time = microtime(true);
$requests = 0;
$diretorio_downloads = 'sn1/downloads_historico/'; // Pasta separada para histórico
// Busca anos passados da tabela bsc_anos_eleicao
$stmt = $db->prepare("
    SELECT ano, cod_eleicao, pleito, turno 
    FROM bsc_anos_eleicao 
    WHERE ano = 2022
    ORDER BY ano DESC, turno ASC
");
$stmt->execute();
$eleicoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($eleicoes as $eleicao) {
    $ano = $eleicao['ano'];
    $pleito = $eleicao['pleito'];
    $turno = $eleicao['turno'];
    $eleicao_nome = 'ele' . $ano; // ex.: ele2022
    $ambiente = 'oficial'; // Arquivo para histórico (não oficial)

    echo "Processando {$ano} - Turno {$turno} (Pleito: {$pleito})\n";

    // Zonas de Rio Branco (1 e 9, como no original)
    $zonas = [1, 9];
    foreach ($zonas as $zona_num) {
        // Busca seções para o ano (use 2024_secoes como proxy, ou crie view genérica)
        $stmt_secoes = $db->prepare("
            SELECT NR_SECAO 
            FROM 2024_secoes 
            WHERE NR_ZONA = ? AND LOCAL_VOTACAO_ID IN (
                SELECT id FROM locais_votacao_mestre WHERE municipio_id = 94
            )
            ORDER BY NR_SECAO ASC
        ");
        $stmt_secoes->execute([$zona_num]);
        $secoes = $stmt_secoes->fetchAll(PDO::FETCH_COLUMN);

        foreach ($secoes as $secao) {
            // Controle de taxa (100 req/s)
            $requests++;
            $elapsed_time = microtime(true) - $start_time;
            if ($elapsed_time < 1 && $requests >= $max_requests_per_second) {
                usleep((1 - $elapsed_time) * 1000000);
                $start_time = microtime(true);
                $requests = 0;
            } elseif ($elapsed_time >= 1) {
                $start_time = microtime(true);
                $requests = 0;
            }

            // URL aux.json (adaptada para arquivo histórico)
            $json_url = $url_base . '/' . $ambiente . '/' . $eleicao_nome . '/arquivo-urna/' . $pleito . '/dados/' . $uf . '/' . $cd_municipio . '/' .
                    formatarNumero($zona_num) . '/' . formatarNumero($secao) . '/p000' . $pleito . '-' . $uf . '-m' . $cd_municipio . '-z' .
                    formatarNumero($zona_num) . '-s' . formatarNumero($secao) . '-aux.json';

            if (url_existe($json_url)) {
                $json_data = file_get_contents($json_url);
                $data = json_decode($json_data, true);
                if (!$data || !isset($data['hashes'])) {
                    echo "JSON inválido ou sem hashes: {$json_url}\n";
                    continue;
                }

                foreach ($data['hashes'] as $hash) {
                    $hash_value = $hash['hash'];
                    $arquivos = [];

                    // DETECÇÃO AUTOMÁTICA DO FORMATO
                    if (isset($hash['arq']) && is_array($hash['arq'])) {
                        // Formato 2024: array de objetos com 'nm'
                        foreach ($hash['arq'] as $arq) {
                            if (isset($arq['nm']) && strpos($arq['nm'], '-bu.dat') !== false) {
                                $arquivos[] = $arq['nm'];
                            }
                        }
                    } elseif (isset($hash['nmarq']) && is_array($hash['nmarq'])) {
                        // Formato 2022: array de strings
                        foreach ($hash['nmarq'] as $nm) {
                            if (strpos($nm, '-bu.dat') !== false || strpos($nm, '.bu') !== false) {
                                $arquivos[] = $nm;
                            }
                        }
                    }

                    // Baixa os BU encontrados
                    foreach ($arquivos as $nome_arquivo) {
                        $caminho_local = $diretorio_downloads . $nome_arquivo;
                        if (file_exists($caminho_local)) {
                            continue; // Já baixado
                        }

                        $link_bu = $url_base . '/' . $ambiente . '/' . $eleicao_nome . '/arquivo-urna/' . $pleito . '/dados/' . $uf . '/' .
                                $cd_municipio . '/' . formatarNumero($zona_num) . '/' . formatarNumero($secao) . '/' .
                                $hash_value . '/' . $nome_arquivo;

                        echo "Baixando: {$nome_arquivo}\n";
                        downloadFile($link_bu, $diretorio_downloads);
                    }
                }
            } else {
                echo "URL inválida para {$ano} Z{$zona_num} S{$secao}: {$json_url}\n";
            }
        }
    }

    // Após download do ano/turno, processa com leitura_historica.php
    echo "Baixados BU para {$ano} Turno {$turno}. Processando...\n";
    exec('php leitura_historica.php ' . $ano . ' ' . $turno . ' ' . $diretorio_downloads);
}

// Funções auxiliares (do original)
function url_existe($url) {
    $headers = @get_headers($url);
    return $headers && strpos($headers[0], '200') !== false;
}

function downloadFile($url, $saveDir) {
    if (!is_dir($saveDir))
        mkdir($saveDir, 0777, true);
    $fileName = basename($url);
    $savePath = $saveDir . $fileName;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 50);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode == 200 && $data !== false) {
        file_put_contents($savePath, $data);
    }
    curl_close($ch);
}

function formatarNumero($numero) {
    return sprintf('%04d', $numero);
}

?>