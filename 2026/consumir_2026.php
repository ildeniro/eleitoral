<?php
/**
 * consumir_2026.php
 * Consumo em tempo real de Arquivos de Urna (Ambiente Oficial) - Eleições 2026 (Acre)
 * Suporta os 22 municípios do Acre e suas 2.411 seções
 */

set_time_limit(0);
ini_set('memory_limit', '512M');

// Inclui configurações do sistema
$root_dir = dirname(__DIR__);
require_once $root_dir . '/config/geral.php';
require_once $root_dir . '/config/funcoes.php';
require_once __DIR__ . '/leitura_2026.php';
require_once __DIR__ . '/config_apuracao_2026.php';

$db = Conexao::getInstance();
$config = carregar_config_apuracao_2026();

// Parâmetros de Configuração para o dia da eleição
$eleicao       = isset($_GET['eleicao']) ? $_GET['eleicao'] : "ele2026";
$pleito        = isset($_GET['pleito']) ? $_GET['pleito'] : ($config['pleito'] ?? "3220"); // Código do pleito oficial divulgado pelo TSE
$estado_sigla  = "ac";
$ambiente      = $config['ambiente'] ?? "oficial";
$url_base      = "https://resultados.tse.jus.br";

// Diretórios locais dinâmicos
$download_dir  = __DIR__ . '/downloads';
$json_dir      = __DIR__ . '/json';
$spec_asn1     = $root_dir . '/sn1/python/spec2024/bu.asn1';
$py_script     = $root_dir . '/sn1/json/bu_dump.py';

if (!is_dir($download_dir)) mkdir($download_dir, 0777, true);
if (!is_dir($json_dir)) mkdir($json_dir, 0777, true);

// Filtro opcional por município (via GET ou CLI)
$filtro_municipio_id = isset($_GET['municipio_id']) ? (int)$_GET['municipio_id'] : (isset($argv[1]) ? (int)$argv[1] : null);
$limite_secoes_por_muni = isset($_GET['limite']) ? (int)$_GET['limite'] : (isset($argv[2]) ? (int)$argv[2] : 0);

// Controle de vazão (máximo 100 req/s exigido pelo TSE)
$max_requests_per_second = 50; 
$start_time = microtime(true);
$requests = 0;
$erros_404_consecutivos = 0;

function formatarNumero($num, $digitos = 4) {
    return sprintf("%0{$digitos}d", (int)$num);
}

function formatarPleito($pleito) {
    return str_pad($pleito, 5, '0', STR_PAD_LEFT);
}

// Busca municípios do Acre
$sqlMuni = "SELECT id, nome, cod_tse FROM bsc_municipios WHERE estado_id = 1";
if ($filtro_municipio_id) {
    $sqlMuni .= " AND id = " . (int)$filtro_municipio_id;
}
$sqlMuni .= " ORDER BY nome ASC";
$municipios = $db->query($sqlMuni)->fetchAll(PDO::FETCH_ASSOC);

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Consumo Oficial 2026 - Acre</title>";
echo "<style>body{font-family:monospace;background:#0d1117;color:#c9d1d9;padding:20px;} .ok{color:#3fb950;} .warn{color:#d29922;} .err{color:#f85149;} .info{color:#58a6ff;}</style></head><body>";
echo "<h2>🛰️ Consumo Paralelo de BUs em Tempo Real (Oficial 2026 - Acre)</h2>";
echo "<p class='info'>Ambiente: <strong>$ambiente</strong> | Eleição: <strong>$eleicao</strong> | Pleito: <strong>$pleito</strong> | UF: <strong>AC</strong></p>";
echo "<p>Total de municípios selecionados: <strong>" . count($municipios) . "</strong></p><hr/>";
flush();

$total_secoes_processadas = 0;
$total_bus_baixados = 0;
$total_votos_importados = 0;

foreach ($municipios as $muni) {
    $cd_muni_tse = formatarNumero($muni['cod_tse'], 5);
    $nome_muni   = $muni['nome'];
    $muni_id     = $muni['id'];

    echo "<h3>📍 Município: {$nome_muni} (TSE: {$cd_muni_tse})</h3>";
    flush();

    $sqlSecoes = "SELECT nr_zona, nr_secao, nm_local_votacao FROM locais_votacao_2026 WHERE municipio_id = ? ORDER BY nr_zona, nr_secao ASC";
    if ($limite_secoes_por_muni > 0) {
        $sqlSecoes .= " LIMIT $limite_secoes_por_muni";
    }
    $stmtSec = $db->prepare($sqlSecoes);
    $stmtSec->execute([$muni_id]);
    $secoes = $stmtSec->fetchAll(PDO::FETCH_ASSOC);

    if (empty($secoes)) {
        echo "<p class='warn'>Nenhuma seção encontrada no cadastro para este município.</p>";
        continue;
    }

    echo "<p>Consultando " . count($secoes) . " seções...</p>";

    foreach ($secoes as $s) {
        $zona = formatarNumero($s['nr_zona'], 4);
        $secao = formatarNumero($s['nr_secao'], 4);
        $local = $s['nm_local_votacao'];

        // Controle de vazão
        $requests++;
        $elapsed = microtime(true) - $start_time;
        if ($elapsed < 1 && $requests > $max_requests_per_second) {
            usleep((int)((1 - $elapsed) * 1000000));
            $start_time = microtime(true);
            $requests = 0;
        } elseif ($elapsed >= 1) {
            $start_time = microtime(true);
            $requests = 0;
        }

        $prefixo_pleito = formatarPleito($pleito);
        $json_aux_url = "{$url_base}/{$ambiente}/{$eleicao}/arquivo-urna/{$pleito}/dados/{$estado_sigla}/{$cd_muni_tse}/{$zona}/{$secao}/p{$prefixo_pleito}-{$estado_sigla}-m{$cd_muni_tse}-z{$zona}-s{$secao}-aux.json";

        $ch = curl_init($json_aux_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) EleitoralBot/2026');
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $total_secoes_processadas++;

        if ($http_code == 200 && $response) {
            $erros_404_consecutivos = 0;
            $aux_data = json_decode($response, true);

            if ($aux_data && isset($aux_data['hashes'])) {
                foreach ($aux_data['hashes'] as $hash_info) {
                    $hash = $hash_info['hash'] ?? '';
                    $arqs = $hash_info['arq'] ?? [];

                    foreach ($arqs as $arq) {
                        $nome_arquivo = $arq['nm'] ?? '';
                        if ((strpos($nome_arquivo, '-bu.dat') !== false || substr($nome_arquivo, -3) === '.bu') && $hash) {
                            $bu_path = "{$download_dir}/{$nome_arquivo}";
                            $json_saida = "{$json_dir}/" . preg_replace('/\.(bu|dat)$/i', '', $nome_arquivo) . ".json";

                            if (!file_exists($bu_path)) {
                                $bu_url = "{$url_base}/{$ambiente}/{$eleicao}/arquivo-urna/{$pleito}/dados/{$estado_sigla}/{$cd_muni_tse}/{$zona}/{$secao}/{$hash}/{$nome_arquivo}";
                                
                                $ch2 = curl_init($bu_url);
                                curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch2, CURLOPT_TIMEOUT, 20);
                                curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
                                curl_setopt($ch2, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) EleitoralBot/2026');
                                $bu_content = curl_exec($ch2);
                                $bu_code = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
                                curl_close($ch2);

                                if ($bu_code == 200 && $bu_content) {
                                    file_put_contents($bu_path, $bu_content);
                                    $total_bus_baixados++;
                                    echo "<div class='ok'>✔ [Zona {$zona} | Seção {$secao}] BU Baixado: {$nome_arquivo}</div>";
                                }
                            }

                            if (file_exists($bu_path) && !file_exists($json_saida)) {
                                $cmdPy = "py " . escapeshellarg($py_script) . " -a " . escapeshellarg($spec_asn1) . " -b " . escapeshellarg($bu_path) . " -o " . escapeshellarg($json_saida) . " 2>&1";
                                $outputPy = shell_exec($cmdPy);
                                
                                if (file_exists($json_saida)) {
                                    echo "<div class='info'>⚡ BU decodificado com sucesso em JSON.</div>";
                                    $votos_proc = processarArquivoBUJson($json_saida, $db);
                                    $total_votos_importados += $votos_proc;
                                    echo "<div class='ok'>💾 {$votos_proc} registros de votos gravados na base de 2026.</div>";
                                } else {
                                    echo "<div class='err'>❌ Erro ao decodificar BU: " . htmlspecialchars($outputPy) . "</div>";
                                }
                            } elseif (file_exists($json_saida)) {
                                echo "<div class='info'>ℹ [Zona {$zona} | Seção {$secao}] Já processado anteriormente.</div>";
                            }
                        }
                    }
                }
            }
        } elseif ($http_code == 404) {
            $erros_404_consecutivos++;
            if ($erros_404_consecutivos > 20) {
                usleep(500000);
            }
        } else {
            echo "<div class='warn'>⚠ [Zona {$zona} | Seção {$secao}] Status HTTP: {$http_code}</div>";
        }
        flush();
    }
}

echo "<hr/><h3>✅ Varredura oficial concluída!</h3>";
echo "<p>Total de seções consultadas: <strong>{$total_secoes_processadas}</strong><br/>";
echo "Total de novos BUs baixados: <strong>{$total_bus_baixados}</strong><br/>";
echo "Total de registros de votos inseridos: <strong>{$total_votos_importados}</strong></p>";
echo "<p><a href='resultado-geral-2026.php' style='color:#58a6ff;font-weight:bold;'>👉 Acessar Resultado Geral 2026</a></p>";
echo "</body></html>";
