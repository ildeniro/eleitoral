<?php
/**
 * robo_consumo_continuo_2026.php
 * Robô Automático de Consumo Contínuo em Tempo Real - Eleições 2026 (Acre)
 * 
 * Funciona de forma 100% autônoma:
 * - Painel de Controle para o Dia da Eleição (Produção Oficial ou Simulado)
 * - Pode ser aberto em uma aba do navegador ou rodar via terminal CLI (php robo_consumo_continuo_2026.php)
 * - Consulta a CDN oficial do TSE respeitando a vazão e cache
 * - Decodifica envelopes JWS em segundo plano
 * - Persiste na tabela `2026_resultados`, `2026_candidatos` e `2026_apuracao_municipios`
 */

$root_dir = dirname(__DIR__);
require_once $root_dir . '/config/geral.php';
require_once __DIR__ . '/config_apuracao_2026.php';

$db = Conexao::getInstance();
$config = carregar_config_apuracao_2026();

// Parâmetros de execução
$modo_cli = (php_sapi_name() === 'cli');
$intervalo_segundos = isset($_GET['intervalo']) ? max(10, (int)$_GET['intervalo']) : (int)$config['intervalo_segundos'];

// ==============================================================================
// ENDPOINT: TESTE DE CONECTIVIDADE COM O TSE (PING / DIAGNÓSTICO)
// ==============================================================================
if (isset($_GET['action']) && $_GET['action'] === 'testar_conexao') {
    header('Content-Type: application/json');
    $amb = $_GET['ambiente'] ?? $config['ambiente'];
    $ef = $_GET['eleicao_fed'] ?? $config['eleicao_federal'];
    $ee = $_GET['eleicao_est'] ?? $config['eleicao_estadual'];

    // Testa Governador em Rio Branco (01392)
    $url_gov = obter_url_tse_jws('01392', '0003', $ee, $amb);
    // Testa Presidente em Rio Branco (01392)
    $url_pres = obter_url_tse_jws('01392', '0001', $ef, $amb);

    $t0 = microtime(true);
    $ch = curl_init($url_gov);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 6);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) EleitoralBot/2026');
    $resp = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $tempo = round((microtime(true) - $t0) * 1000);
    curl_close($ch);

    $status_ok = ($http_code == 200 && strlen($resp) > 50);
    $mensagem = "";
    if ($http_code == 200) {
        $mensagem = "Conexão estabelecida com sucesso! Arquivos do TSE disponíveis para leitura.";
    } elseif ($http_code == 404) {
        $mensagem = "O servidor do TSE respondeu com HTTP 404 (Não Encontrado). Verifique o código da eleição ou aguarde a abertura dos dados pelo TSE.";
    } else {
        $mensagem = "Falha ao contatar TSE. Código HTTP retornado: " . ($http_code ?: 'Sem resposta/Timeout');
    }

    echo json_encode([
        'sucesso' => $status_ok,
        'http_code' => $http_code,
        'tempo_ms' => $tempo,
        'url_testada' => $url_gov,
        'ambiente' => $amb,
        'eleicao_estadual' => $ee,
        'eleicao_federal' => $ef,
        'mensagem' => $mensagem
    ]);
    exit;
}

// ==============================================================================
// ENDPOINT: SALVAR CONFIGURAÇÕES DA ELEIÇÃO
// ==============================================================================
if (isset($_POST['action']) && $_POST['action'] === 'salvar_config') {
    header('Content-Type: application/json');
    $novo_ambiente = trim($_POST['ambiente'] ?? 'simulado');
    $nova_fed = trim($_POST['eleicao_federal'] ?? $config['eleicao_federal']);
    $nova_est = trim($_POST['eleicao_estadual'] ?? $config['eleicao_estadual']);
    $novo_int = max(10, (int)($_POST['intervalo_segundos'] ?? 30));

    $salvo = salvar_config_apuracao_2026([
        'ambiente' => $novo_ambiente,
        'eleicao_federal' => $nova_fed,
        'eleicao_estadual' => $nova_est,
        'intervalo_segundos' => $novo_int
    ]);

    echo json_encode(['sucesso' => true, 'config' => $salvo]);
    exit;
}

// ==============================================================================
// ENDPOINT: ZERAR BASE DE DADOS (PREPARAÇÃO PARA O INÍCIO DA ELEIÇÃO)
// ==============================================================================
if (isset($_POST['action']) && $_POST['action'] === 'zerar_banco') {
    header('Content-Type: application/json');
    try {
        zerar_base_apuracao_2026($db);
        echo json_encode(['sucesso' => true, 'mensagem' => 'Base de dados 2026 zerada com sucesso! Pronta para a eleição.']);
    } catch (Exception $e) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao limpar banco: ' . $e->getMessage()]);
    }
    exit;
}

// ==============================================================================
// FUNÇÃO CENTRAL: INGESTÃO DOS 22 MUNICÍPIOS DO ACRE VIA CURL_MULTI
// ==============================================================================
function processarConsumoAcreCompleto($db, $config) {
    // 1. Garante a tabela de estatísticas de apuração dos municípios
    $db->exec("
        CREATE TABLE IF NOT EXISTS `2026_apuracao_municipios` (
            `COD_MUNICIPIO_TSE` varchar(10) NOT NULL,
            `NOME_MUNICIPIO` varchar(100) NOT NULL,
            `SECOES_TOTAL` int(11) NOT NULL DEFAULT 0,
            `SECOES_APURADAS` int(11) NOT NULL DEFAULT 0,
            `PORC_APURADA` decimal(5,2) NOT NULL DEFAULT 0.00,
            `DATA_ATUALIZACAO` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`COD_MUNICIPIO_TSE`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

    $sqlMuni = "SELECT id, nome, LPAD(cod_tse, 5, '0') as cod_tse FROM bsc_municipios WHERE estado_id = 1 ORDER BY nome ASC";
    $municipios = $db->query($sqlMuni)->fetchAll(PDO::FETCH_ASSOC);

    $eleicaoFed = $config['eleicao_federal'];
    $eleicaoEst = $config['eleicao_estadual'];
    $ambiente = $config['ambiente'];

    // 5 cargos a monitorar
    $cargosConfig = [
        ['eleicao' => $eleicaoFed, 'code' => '0001', 'cargo_cd' => 1, 'nome' => 'presidente', 'sg_ue' => 'BR', 'nm_ue' => 'BRASIL'],
        ['eleicao' => $eleicaoEst, 'code' => '0003', 'cargo_cd' => 3, 'nome' => 'governador', 'sg_ue' => 'AC', 'nm_ue' => 'ACRE'],
        ['eleicao' => $eleicaoEst, 'code' => '0005', 'cargo_cd' => 5, 'nome' => 'senador', 'sg_ue' => 'AC', 'nm_ue' => 'ACRE'],
        ['eleicao' => $eleicaoEst, 'code' => '0006', 'cargo_cd' => 6, 'nome' => 'deputado federal', 'sg_ue' => 'AC', 'nm_ue' => 'ACRE'],
        ['eleicao' => $eleicaoEst, 'code' => '0007', 'cargo_cd' => 7, 'nome' => 'deputado estadual', 'sg_ue' => 'AC', 'nm_ue' => 'ACRE'],
    ];

    $mh = curl_multi_init();
    $curl_handles = [];

    foreach ($municipios as $m) {
        $cod = $m['cod_tse'];
        foreach ($cargosConfig as $cfg) {
            $url = obter_url_tse_jws($cod, $cfg['code'], $cfg['eleicao'], $ambiente);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 8);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) EleitoralRobo/2026');
            curl_multi_add_handle($mh, $ch);
            $curl_handles[] = [
                'handle' => $ch,
                'cod_muni' => $cod,
                'nome_muni' => $m['nome'],
                'cargo_nome' => $cfg['nome'],
                'cargo_code' => $cfg['cargo_cd'],
                'sg_ue' => $cfg['sg_ue'],
                'nm_ue' => $cfg['nm_ue'],
                'eleicao' => $cfg['eleicao'],
                'url' => $url
            ];
        }
    }

    $running = null;
    do {
        curl_multi_exec($mh, $running);
        curl_multi_select($mh);
    } while ($running > 0);

    $stmtResult = $db->prepare("
        INSERT INTO 2026_resultados 
            (ANO_ELEICAO, COD_ELEICAO, TURNO, UF, COD_MUNICIPIO_TSE, ZONA, SECAO, LOCAL_VOTACAO, COD_CARGO, NUM_CANDIDATO, QTD_VOTOS, TIPO_VOTO, PARTIDO, DATA_CADASTRO)
        VALUES 
            (2026, ?, 1, 'AC', ?, 1, 9999, 'TOTALIZADO MUNICIPIO TSE', ?, ?, ?, 'nominal', ?, NOW())
        ON DUPLICATE KEY UPDATE 
            QTD_VOTOS = VALUES(QTD_VOTOS),
            DATA_UPDATE = NOW()
    ");

    $stmtVotoEspecial = $db->prepare("
        INSERT INTO 2026_resultados 
            (ANO_ELEICAO, COD_ELEICAO, TURNO, UF, COD_MUNICIPIO_TSE, ZONA, SECAO, LOCAL_VOTACAO, COD_CARGO, NUM_CANDIDATO, QTD_VOTOS, TIPO_VOTO, PARTIDO, DATA_CADASTRO)
        VALUES 
            (2026, ?, 1, 'AC', ?, 1, 9999, 'TOTALIZADO MUNICIPIO TSE', ?, ?, ?, ?, NULL, NOW())
        ON DUPLICATE KEY UPDATE 
            QTD_VOTOS = VALUES(QTD_VOTOS),
            DATA_UPDATE = NOW()
    ");

    $stmtStats = $db->prepare("
        INSERT INTO 2026_apuracao_municipios 
            (COD_MUNICIPIO_TSE, NOME_MUNICIPIO, SECOES_TOTAL, SECOES_APURADAS, PORC_APURADA, DATA_ATUALIZACAO)
        VALUES 
            (?, ?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE 
            SECOES_TOTAL = VALUES(SECOES_TOTAL),
            SECOES_APURADAS = VALUES(SECOES_APURADAS),
            PORC_APURADA = VALUES(PORC_APURADA),
            DATA_ATUALIZACAO = NOW()
    ");

    $stmtCand = $db->prepare("
        INSERT INTO 2026_candidatos 
            (ANO_ELEICAO, NR_TURNO, SG_UF, SG_UE, NM_UE, CD_CARGO, DS_CARGO, NR_CANDIDATO, NM_CANDIDATO, NM_URNA_CANDIDATO, SG_PARTIDO, NM_PARTIDO, STATUS)
        VALUES 
            (2026, 1, 'AC', ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
        ON DUPLICATE KEY UPDATE 
            NM_URNA_CANDIDATO = VALUES(NM_URNA_CANDIDATO),
            SG_PARTIDO = VALUES(SG_PARTIDO),
            NM_PARTIDO = VALUES(NM_PARTIDO)
    ");

    $total_arquivos_ok = 0;
    $total_arquivos_falha = 0;
    $total_candidatos_atualizados = 0;
    $votos_por_cargo = [
        'presidente' => 0,
        'governador' => 0,
        'senador' => 0,
        'deputado federal' => 0,
        'deputado estadual' => 0
    ];

    $db->beginTransaction();

    foreach ($curl_handles as $item) {
        $content = curl_multi_getcontent($item['handle']);
        $http = curl_getinfo($item['handle'], CURLINFO_HTTP_CODE);
        curl_multi_remove_handle($mh, $item['handle']);
        curl_close($item['handle']);

        if ($http == 200 && $content) {
            $parts = explode('.', $content);
            if (count($parts) >= 2) {
                $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
                if ($payload) {
                    $total_arquivos_ok++;

                    // Estatísticas de seções a partir do arquivo de Governador (ou geral do município)
                    if ($item['cargo_nome'] === 'governador') {
                        $sec_tot = (int)($payload['s']['ts'] ?? 0);
                        $sec_apu = (int)($payload['s']['st'] ?? 0);
                        $porc = str_replace(',', '.', (string)($payload['s']['pst'] ?? '0.00'));
                        $stmtStats->execute([$item['cod_muni'], $item['nome_muni'], $sec_tot, $sec_apu, (float)$porc]);
                    }

                    // Votos dos candidatos
                    if (isset($payload['carg'][0]['agr'])) {
                        foreach ($payload['carg'][0]['agr'] as $agr) {
                            foreach ($agr['par'] as $par) {
                                $sg = $par['sg'] ?? 'PARTIDO';
                                $nm_partido = $par['nm'] ?? $sg;
                                foreach ($par['cand'] as $cand) {
                                    $num = (string)($cand['n'] ?? '0');
                                    $votos = (int)($cand['vap'] ?? 0);
                                    $nm_urna = (string)($cand['nmu'] ?? $cand['nm'] ?? "CANDIDATO {$num}");
                                    $nm_cand = (string)($cand['nm'] ?? $nm_urna);

                                    $stmtCand->execute([
                                        $item['sg_ue'],
                                        $item['nm_ue'],
                                        $item['cargo_code'],
                                        strtoupper($item['cargo_nome']),
                                        (int)$num,
                                        $nm_cand,
                                        $nm_urna,
                                        $sg,
                                        $nm_partido
                                    ]);

                                    $stmtResult->execute([
                                        $item['eleicao'],
                                        $item['cod_muni'],
                                        $item['cargo_nome'],
                                        $num,
                                        $votos,
                                        $sg
                                    ]);

                                    $total_candidatos_atualizados++;
                                    $votos_por_cargo[$item['cargo_nome']] = ($votos_por_cargo[$item['cargo_nome']] ?? 0) + $votos;
                                }
                            }
                        }
                    }

                    // Votos Brancos e Nulos
                    $vb = (int)($payload['v']['vb'] ?? 0);
                    $vn = (int)($payload['v']['tvn'] ?? 0);
                    if ($vb > 0) {
                        $stmtVotoEspecial->execute([$item['eleicao'], $item['cod_muni'], $item['cargo_nome'], 'BRANCO', $vb, 'branco']);
                    }
                    if ($vn > 0) {
                        $stmtVotoEspecial->execute([$item['eleicao'], $item['cod_muni'], $item['cargo_nome'], 'NULO', $vn, 'nulo']);
                    }
                }
            }
        } else {
            $total_arquivos_falha++;
        }
    }
    curl_multi_close($mh);
    $db->commit();

    $statsTotal = $db->query("SELECT SUM(SECOES_TOTAL) as tot, SUM(SECOES_APURADAS) as apu FROM 2026_apuracao_municipios")->fetch(PDO::FETCH_ASSOC);
    $totUrnas = (int)($statsTotal['tot'] ?? 2411);
    $apuUrnas = (int)($statsTotal['apu'] ?? 0);
    $porcGlobal = $totUrnas > 0 ? round(($apuUrnas / $totUrnas) * 100, 2) : 0;

    return [
        'sucesso' => true,
        'arquivos_processados' => $total_arquivos_ok,
        'arquivos_falha' => $total_arquivos_falha,
        'municipios_total' => count($municipios),
        'candidatos_atualizados' => $total_candidatos_atualizados,
        'votos_por_cargo' => $votos_por_cargo,
        'secoes_total' => $totUrnas,
        'secoes_apuradas' => $apuUrnas,
        'porc' => number_format($porcGlobal, 2, ',', '.'),
        'porc_num' => $porcGlobal,
        'ambiente' => $ambiente,
        'eleicao_federal' => $eleicaoFed,
        'eleicao_estadual' => $eleicaoEst
    ];
}

// ==============================================================================
// REQUISIÇÃO AJAX DO ROBÔ (CICLO PERIÓDICO)
// ==============================================================================
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    header('Content-Type: application/json');
    $config_recarregada = carregar_config_apuracao_2026();
    $t_start = microtime(true);
    $res = processarConsumoAcreCompleto($db, $config_recarregada);
    $t_end = microtime(true);
    $duracao = round($t_end - $t_start, 2);

    echo json_encode([
        'timestamp' => date('H:i:s'),
        'duracao_segundos' => $duracao,
        'ambiente' => $config_recarregada['ambiente'],
        'eleicao_federal' => $config_recarregada['eleicao_federal'],
        'eleicao_estadual' => $config_recarregada['eleicao_estadual'],
        'global' => [
            'porc' => $res['porc'],
            'porc_num' => $res['porc_num'],
            'secoes_apuradas' => $res['secoes_apuradas'],
            'secoes_total' => $res['secoes_total'],
            'candidatos' => $res['candidatos_atualizados'],
            'votos_por_cargo' => $res['votos_por_cargo']
        ],
        'municipios_processados' => $res['municipios_total'],
        'arquivos_ok' => $res['arquivos_processados'],
        'arquivos_falha' => $res['arquivos_falha']
    ]);
    exit;
}

// ==============================================================================
// MODO CLI (EXECUÇÃO VIA TERMINAL)
// ==============================================================================
if ($modo_cli) {
    echo "=========================================================\n";
    echo "🤖 ROBÔ AUTÔNOMO DE CONSUMO - ELEIÇÕES 2026 (ACRE)\n";
    echo "Ambiente: " . strtoupper($config['ambiente']) . " (" . $config['descricao_ambiente'] . ")\n";
    echo "Eleição Federal: {$config['eleicao_federal']} | Eleição Estadual: {$config['eleicao_estadual']}\n";
    echo "Intervalo de Consulta: {$intervalo_segundos}s\n";
    echo "Pressione Ctrl+C a qualquer momento para pausar.\n";
    echo "=========================================================\n\n";

    $ciclo = 1;
    while (true) {
        $hora = date('H:i:s');
        echo "[{$hora}] 🔄 Ciclo #{$ciclo}: Consultando 110 arquivos do TSE...\n";

        $t0 = microtime(true);
        $res = processarConsumoAcreCompleto($db, $config);
        $dur = round(microtime(true) - $t0, 2);

        echo "  ✔ Sucesso em {$dur}s! ({$res['arquivos_processados']} arquivos lidos, {$res['arquivos_falha']} indisponíveis)\n";
        echo "  ✔ Apuração Geral Acre: {$res['porc']}% ({$res['secoes_apuradas']}/{$res['secoes_total']} seções)\n";
        foreach ($res['votos_por_cargo'] as $cargo => $vts) {
            echo "    • " . str_pad(ucfirst($cargo), 18) . ": " . number_format($vts, 0, ',', '.') . " votos\n";
        }
        echo "  ⏳ Aguardando {$intervalo_segundos} segundos para o próximo ciclo...\n\n";
        $ciclo++;
        sleep($intervalo_segundos);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Robô de Apuração 2026 - Central TSE Ao Vivo</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&family=Oswald:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-body: #070d19;
            --bg-card: #0f172a;
            --bg-input: #1e293b;
            --border: #1e293b;
            --border-hover: #334155;
            --primary: #38bdf8;
            --accent: #3b82f6;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-body); color: var(--text-main); padding: 24px; }
        .container { max-width: 1100px; margin: 0 auto; background: var(--bg-card); border-radius: 16px; padding: 28px; border: 1px solid var(--border); box-shadow: 0 10px 40px rgba(0,0,0,0.5); }
        
        /* Cabeçalho */
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 20px; flex-wrap: wrap; gap: 16px; }
        .header-title h1 { font-family: 'Oswald', sans-serif; font-size: 26px; color: var(--primary); display: flex; align-items: center; gap: 12px; margin: 0; }
        .header-title p { color: var(--text-muted); font-size: 13px; margin-top: 4px; }
        
        .header-clocks { display: flex; gap: 16px; align-items: center; }
        .clock-badge { background: #020617; border: 1px solid var(--border); padding: 6px 14px; border-radius: 8px; text-align: center; }
        .clock-badge .label { font-size: 10px; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; }
        .clock-badge .time { font-family: 'JetBrains Mono', monospace; font-size: 16px; font-weight: 700; color: #fff; }

        /* Barra de Alerta do Ambiente (Oficial vs Simulado) */
        .env-banner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 600;
            flex-wrap: wrap;
            gap: 12px;
        }
        .env-banner.simulado {
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.4);
            color: #fbbf24;
        }
        .env-banner.oficial {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #34d399;
        }
        .pulse-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; animation: pulse 1.5s infinite; }
        .pulse-dot.green { background: #10b981; }
        .pulse-dot.amber { background: #f59e0b; }
        @keyframes pulse { 0% { transform: scale(0.9); opacity: 0.7; } 50% { transform: scale(1.4); opacity: 1; } 100% { transform: scale(0.9); opacity: 0.7; } }

        /* Controles Principais */
        .control-bar { display: flex; gap: 10px; align-items: center; background: #1e293b; padding: 14px 18px; border-radius: 12px; margin-bottom: 24px; flex-wrap: wrap; }
        .btn { padding: 9px 18px; border-radius: 8px; font-weight: 700; font-size: 13px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; text-decoration: none; }
        .btn-start { background: var(--success); color: #fff; }
        .btn-start:hover { background: #059669; }
        .btn-pause { background: var(--warning); color: #fff; }
        .btn-pause:hover { background: #d97706; }
        .btn-clean { background: var(--danger); color: #fff; }
        .btn-clean:hover { background: #dc2626; }
        .btn-link { background: var(--accent); color: #fff; }
        .btn-link:hover { background: #2563eb; }
        .btn-outline { background: transparent; border: 1px solid var(--border-hover); color: var(--text-main); }
        .btn-outline:hover { background: #334155; }

        /* Barra de Progresso Global do Acre */
        .progress-box { background: #020617; border: 1px solid var(--border); border-radius: 12px; padding: 18px; margin-bottom: 24px; }
        .progress-header { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 8px; }
        .progress-header h3 { font-size: 14px; text-transform: uppercase; color: var(--text-muted); font-weight: 700; }
        .progress-header .pct { font-family: 'Oswald', sans-serif; font-size: 24px; color: var(--primary); }
        .progress-bar-bg { width: 100%; height: 12px; background: #1e293b; border-radius: 6px; overflow: hidden; margin-bottom: 8px; }
        .progress-bar-fill { height: 100%; background: linear-gradient(90deg, #3b82f6, #38bdf8); width: 0%; transition: width 0.6s ease; }
        .progress-info { display: flex; justify-content: space-between; font-size: 12px; color: var(--text-muted); }

        /* Grid dos 5 Cargos */
        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 14px; margin-bottom: 24px; }
        .card-stat { background: #1e293b; padding: 16px; border-radius: 10px; border: 1px solid #334155; position: relative; }
        .card-stat h4 { font-size: 12px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px; display: flex; justify-content: space-between; }
        .card-stat .votos-count { font-family: 'JetBrains Mono', monospace; font-size: 20px; font-weight: 700; color: #fff; }
        .card-stat .cargo-detalhe { font-size: 11px; color: var(--primary); margin-top: 4px; }

        /* Console de Logs */
        .console-box { background: #020617; border: 1px solid var(--border); border-radius: 12px; padding: 16px; font-family: 'JetBrains Mono', monospace; font-size: 12.5px; height: 260px; overflow-y: auto; color: #cbd5e1; }
        .log-entry { margin-bottom: 5px; line-height: 1.5; }
        .log-ok { color: #34d399; }
        .log-info { color: #38bdf8; }
        .log-warn { color: #fbbf24; }
        .log-err { color: #f87171; }

        /* Modal de Configuração */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px); }
        .modal-content { background: #0f172a; border: 1px solid var(--border-hover); border-radius: 14px; max-width: 520px; width: 90%; padding: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.6); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid var(--border); padding-bottom: 12px; }
        .modal-header h3 { font-family: 'Oswald', sans-serif; font-size: 20px; color: var(--primary); margin: 0; }
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-size: 12px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px; }
        .form-control { width: 100%; background: #1e293b; border: 1px solid #334155; border-radius: 8px; color: #fff; padding: 10px; font-size: 14px; outline: none; }
        .form-control:focus { border-color: var(--primary); }
    </style>
</head>
<body>

<div class="container">
    <!-- Cabeçalho -->
    <div class="header">
        <div class="header-title">
            <h1><i class="fa-solid fa-robot"></i> Central de Consumo TSE • Eleições 2026</h1>
            <p>Ingestão Contínua e Automática para os 22 Municípios do Acre (5 Cargos em Disputa)</p>
        </div>
        <div class="header-clocks">
            <div class="clock-badge">
                <div class="label">Rio Branco (Acre)</div>
                <div class="time" id="clockAcre">--:--:--</div>
            </div>
            <div class="clock-badge">
                <div class="label">Brasília (DF)</div>
                <div class="time" id="clockBrasilia">--:--:--</div>
            </div>
        </div>
    </div>

    <!-- Banner do Ambiente Ativo -->
    <div class="env-banner <?= $config['ambiente'] === 'oficial' ? 'oficial' : 'simulado'; ?>" id="envBanner">
        <div style="display:flex; align-items:center; gap:10px;">
            <span class="pulse-dot <?= $config['ambiente'] === 'oficial' ? 'green' : 'amber'; ?>"></span>
            <span>
                <strong>Ambiente Ativo:</strong> 
                <span id="envDescricao"><?= $config['descricao_ambiente']; ?></span>
                (Federal: <code id="lblFed"><?= $config['eleicao_federal']; ?></code> | Estadual AC: <code id="lblEst"><?= $config['eleicao_estadual']; ?></code>)
            </span>
        </div>
        <div style="display:flex; gap:8px;">
            <button class="btn btn-outline" style="padding:6px 12px; font-size:12px;" onclick="testarConexaoTSE();">
                <i class="fa-solid fa-satellite-dish"></i> Testar Conexão
            </button>
            <button class="btn btn-outline" style="padding:6px 12px; font-size:12px;" onclick="abrirModalConfig();">
                <i class="fa-solid fa-gear"></i> Alterar Configuração
            </button>
        </div>
    </div>

    <!-- Barra de Controle -->
    <div class="control-bar">
        <button id="btnToggle" class="btn btn-pause" onclick="toggleRobo();">
            <i class="fa-solid fa-pause"></i> Pausar Robô
        </button>
        <button class="btn btn-link" onclick="executarCicloAgora();" title="Consulta a CDN do TSE neste exato momento">
            <i class="fa-solid fa-bolt"></i> Consultar Agora
        </button>
        <a href="resultado-geral-2026.php" class="btn btn-link" target="_blank" title="Abre o painel em nova aba">
            <i class="fa-solid fa-chart-pie"></i> Ver Painel de Apuração
        </a>
        <button class="btn btn-clean" onclick="zerarBancoParaEleicao();" title="Zera a tabela de resultados para início oficial da eleição">
            <i class="fa-solid fa-trash-can"></i> Zerar Banco p/ Eleição
        </button>
        <div style="margin-left:auto; color:var(--text-muted); font-size:13px; font-weight:600;">
            Intervalo: <span id="spanIntervalo"><?= $intervalo_segundos; ?></span>s • Próxima em: <strong id="countdown" style="color:var(--primary);"><?= $intervalo_segundos; ?>s</strong>
        </div>
    </div>

    <!-- Barra de Progresso Global do Acre -->
    <div class="progress-box">
        <div class="progress-header">
            <h3><i class="fa-solid fa-chart-column"></i> Totalização Geral do Estado do Acre</h3>
            <div class="pct" id="globalPorc">0,00%</div>
        </div>
        <div class="progress-bar-bg">
            <div class="progress-bar-fill" id="globalBar" style="width: 0%;"></div>
        </div>
        <div class="progress-info">
            <span id="globalSecoes">0 de 2.411 seções apuradas</span>
            <span id="globalCandStats">0 candidatos computados</span>
            <span id="globalTempo">Último ciclo: --s</span>
        </div>
    </div>

    <!-- Cards dos 5 Cargos em Disputa -->
    <div class="dashboard-grid">
        <div class="card-stat">
            <h4><span>Governador</span> <i class="fa-solid fa-user-tie"></i></h4>
            <div class="votos-count" id="votosGov">0</div>
            <div class="cargo-detalhe" id="detalheGov">Aguardando dados...</div>
        </div>
        <div class="card-stat">
            <h4><span>Senador (2 vagas)</span> <i class="fa-solid fa-landmark"></i></h4>
            <div class="votos-count" id="votosSen">0</div>
            <div class="cargo-detalhe" id="detalheSen">Aguardando dados...</div>
        </div>
        <div class="card-stat">
            <h4><span>Presidente</span> <i class="fa-solid fa-flag"></i></h4>
            <div class="votos-count" id="votosPres">0</div>
            <div class="cargo-detalhe" id="detalhePres">Aguardando dados...</div>
        </div>
        <div class="card-stat">
            <h4><span>Dep. Federal (8)</span> <i class="fa-solid fa-users"></i></h4>
            <div class="votos-count" id="votosDepFed">0</div>
            <div class="cargo-detalhe" id="detalheDepFed">Aguardando dados...</div>
        </div>
        <div class="card-stat">
            <h4><span>Dep. Estadual (24)</span> <i class="fa-solid fa-building-columns"></i></h4>
            <div class="votos-count" id="votosDepEst">0</div>
            <div class="cargo-detalhe" id="detalheDepEst">Aguardando dados...</div>
        </div>
    </div>

    <!-- Console de Eventos em Tempo Real -->
    <h3 style="font-size:14px; margin-bottom:8px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">
        <i class="fa-solid fa-terminal"></i> Terminal de Monitoramento em Tempo Real:
    </h3>
    <div class="console-box" id="consoleLog">
        <div class="log-entry log-info">[<?= date('H:i:s'); ?>] 🚀 Robô inicializado. Preparado para consumo simultâneo dos 22 municípios do Acre.</div>
    </div>
</div>

<!-- Modal de Configuração -->
<div class="modal-overlay" id="modalConfig">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fa-solid fa-sliders"></i> Configuração da Apuração 2026</h3>
            <button onclick="fecharModalConfig();" style="background:none; border:none; color:#fff; font-size:18px; cursor:pointer;"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="formConfig" onsubmit="salvarConfig(event);">
            <div class="form-group">
                <label for="cfgAmbiente">Ambiente de Transmissão:</label>
                <select id="cfgAmbiente" name="ambiente" class="form-control">
                    <option value="simulado" <?= $config['ambiente'] === 'simulado' ? 'selected' : ''; ?>>🟡 Simulado de Testes (resultados-sim.tse.jus.br)</option>
                    <option value="oficial" <?= $config['ambiente'] === 'oficial' ? 'selected' : ''; ?>>🟢 Transmissão Oficial TSE (resultados.tse.jus.br)</option>
                </select>
            </div>
            <div class="form-group">
                <label for="cfgFed">Código Eleição Federal (Presidente):</label>
                <input type="text" id="cfgFed" name="eleicao_federal" class="form-control" value="<?= htmlspecialchars($config['eleicao_federal']); ?>" required>
                <small style="color:var(--text-muted); font-size:11px;">Simulado: 21270 | Oficial: código divulgado pelo TSE no dia.</small>
            </div>
            <div class="form-group">
                <label for="cfgEst">Código Eleição Estadual Acre (Gov, Sen, Deps):</label>
                <input type="text" id="cfgEst" name="eleicao_estadual" class="form-control" value="<?= htmlspecialchars($config['eleicao_estadual']); ?>" required>
                <small style="color:var(--text-muted); font-size:11px;">Simulado: 21272 | Oficial: código divulgado pelo TSE no dia.</small>
            </div>
            <div class="form-group">
                <label for="cfgInt">Intervalo entre Consultas (segundos):</label>
                <select id="cfgInt" name="intervalo_segundos" class="form-control">
                    <option value="15" <?= $intervalo_segundos == 15 ? 'selected' : ''; ?>>15 segundos (Mais Rápido)</option>
                    <option value="30" <?= $intervalo_segundos == 30 ? 'selected' : ''; ?>>30 segundos (Recomendado)</option>
                    <option value="60" <?= $intervalo_segundos == 60 ? 'selected' : ''; ?>>60 segundos</option>
                </select>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" class="btn btn-outline" onclick="fecharModalConfig();">Cancelar</button>
                <button type="submit" class="btn btn-start"><i class="fa-solid fa-floppy-disk"></i> Salvar e Aplicar</button>
            </div>
        </form>
    </div>
</div>

<script>
    let ativo = true;
    let intervaloPadrao = <?= $intervalo_segundos; ?>;
    let tempoRestante = intervaloPadrao;
    let contadorCiclos = 0;
    let emExecucao = false;

    // Relógios
    function atualizarRelogios() {
        const agora = new Date();
        const acreTime = new Date(agora.toLocaleString("en-US", { timeZone: "America/Rio_Branco" }));
        const bsbTime = new Date(agora.toLocaleString("en-US", { timeZone: "America/Sao_Paulo" }));
        
        document.getElementById('clockAcre').innerText = acreTime.toTimeString().split(' ')[0];
        document.getElementById('clockBrasilia').innerText = bsbTime.toTimeString().split(' ')[0];
    }
    setInterval(atualizarRelogios, 1000);
    atualizarRelogios();

    function log(msg, tipo = 'info') {
        const consoleEl = document.getElementById('consoleLog');
        const d = new Date();
        const hora = d.toTimeString().split(' ')[0];
        const entry = document.createElement('div');
        entry.className = 'log-entry log-' + tipo;
        entry.innerHTML = `[${hora}] ${msg}`;
        consoleEl.appendChild(entry);
        consoleEl.scrollTop = consoleEl.scrollHeight;
    }

    function toggleRobo() {
        ativo = !ativo;
        const btn = document.getElementById('btnToggle');
        if (ativo) {
            btn.className = 'btn btn-pause';
            btn.innerHTML = '<i class="fa-solid fa-pause"></i> Pausar Robô';
            log('▶ Robô ativado pelo operador.', 'ok');
            tempoRestante = 1;
        } else {
            btn.className = 'btn btn-start';
            btn.innerHTML = '<i class="fa-solid fa-play"></i> Iniciar Robô';
            log('⏸ Robô pausado pelo operador.', 'warn');
        }
    }

    function executarCicloAgora() {
        if (emExecucao) return;
        emExecucao = true;
        contadorCiclos++;
        log(`⚡ Ciclo #${contadorCiclos}: Conectando aos 110 endpoints do TSE...`, 'info');

        fetch('robo_consumo_continuo_2026.php?ajax=1')
            .then(res => res.json())
            .then(data => {
                emExecucao = false;
                if (data.global) {
                    const g = data.global;
                    // Progresso Geral
                    document.getElementById('globalPorc').innerText = g.porc + '%';
                    document.getElementById('globalBar').style.width = g.porc_num + '%';
                    document.getElementById('globalSecoes').innerText = `${g.secoes_apuradas.toLocaleString()} de ${g.secoes_total.toLocaleString()} seções apuradas`;
                    document.getElementById('globalCandStats').innerText = `${g.candidatos.toLocaleString()} atualizações de votos`;
                    document.getElementById('globalTempo').innerText = `Último ciclo: ${data.duracao_segundos}s (${data.arquivos_ok} arqs)`;

                    // Votos por cargo
                    if (g.votos_por_cargo) {
                        const v = g.votos_por_cargo;
                        document.getElementById('votosGov').innerText = (v['governador'] || 0).toLocaleString();
                        document.getElementById('votosSen').innerText = (v['senador'] || 0).toLocaleString();
                        document.getElementById('votosPres').innerText = (v['presidente'] || 0).toLocaleString();
                        document.getElementById('votosDepFed').innerText = (v['deputado federal'] || 0).toLocaleString();
                        document.getElementById('votosDepEst').innerText = (v['deputado estadual'] || 0).toLocaleString();

                        document.getElementById('detalheGov').innerText = `${g.porc}% apurado`;
                        document.getElementById('detalheSen').innerText = `${g.porc}% apurado`;
                        document.getElementById('detalhePres').innerText = `${g.porc}% apurado`;
                        document.getElementById('detalheDepFed').innerText = `${g.porc}% apurado`;
                        document.getElementById('detalheDepEst').innerText = `${g.porc}% apurado`;
                    }

                    log(`✔ Ciclo #${contadorCiclos} concluído em ${data.duracao_segundos}s. ${g.porc}% das urnas apuradas no Acre.`, 'ok');
                }
            })
            .catch(err => {
                emExecucao = false;
                log(`❌ Erro no ciclo #${contadorCiclos}: ${err.message}`, 'err');
            });
    }

    // Modal Configuração
    function abrirModalConfig() {
        document.getElementById('modalConfig').style.display = 'flex';
    }
    function fecharModalConfig() {
        document.getElementById('modalConfig').style.display = 'none';
    }

    function salvarConfig(e) {
        e.preventDefault();
        const formData = new FormData(document.getElementById('formConfig'));
        formData.append('action', 'salvar_config');

        fetch('robo_consumo_continuo_2026.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.sucesso) {
                fecharModalConfig();
                intervaloPadrao = parseInt(data.config.intervalo_segundos);
                document.getElementById('spanIntervalo').innerText = intervaloPadrao;
                document.getElementById('lblFed').innerText = data.config.eleicao_federal;
                document.getElementById('lblEst').innerText = data.config.eleicao_estadual;
                document.getElementById('envDescricao').innerText = data.config.descricao_ambiente;

                const banner = document.getElementById('envBanner');
                if (data.config.ambiente === 'oficial') {
                    banner.className = 'env-banner oficial';
                } else {
                    banner.className = 'env-banner simulado';
                }

                log(`⚙️ Configurações salvas: Ambiente ${data.config.ambiente.toUpperCase()}, Fed: ${data.config.eleicao_federal}, Est: ${data.config.eleicao_estadual}, Intervalo: ${intervaloPadrao}s`, 'ok');
                tempoRestante = 2; // Dispara próximo ciclo logo
            }
        })
        .catch(err => alert('Erro ao salvar configuração: ' + err.message));
    }

    // Testar Conexão com TSE
    function testarConexaoTSE() {
        log('🔍 Testando conectividade com o TSE...', 'info');
        fetch('robo_consumo_continuo_2026.php?action=testar_conexao')
            .then(res => res.json())
            .then(data => {
                if (data.sucesso) {
                    log(`✅ [TSE CONEXÃO OK] HTTP 200 recebido em ${data.tempo_ms}ms! Dados disponíveis na CDN.`, 'ok');
                    alert(`✅ Conexão TSE Ativa!\n\nStatus HTTP: ${data.http_code}\nLatência: ${data.tempo_ms}ms\nAmbiente: ${data.ambiente}\n\n${data.mensagem}`);
                } else {
                    log(`⚠️ [TSE AVISO] HTTP ${data.http_code} em ${data.tempo_ms}ms. ${data.mensagem}`, 'warn');
                    alert(`⚠️ Atenção na Conexão TSE:\n\nStatus HTTP: ${data.http_code}\nLatência: ${data.tempo_ms}ms\n\n${data.mensagem}`);
                }
            })
            .catch(err => {
                log(`❌ Erro de rede ao testar TSE: ${err.message}`, 'err');
                alert('Erro de conexão: ' + err.message);
            });
    }

    // Zerar Banco para Domingo
    function zerarBancoParaEleicao() {
        const conf = confirm(
            "⚠️ ATENÇÃO - PREPARAÇÃO PARA O DIA DA ELEIÇÃO:\n\n" +
            "Deseja realmente ZERAR todos os dados das tabelas de apuração 2026?\n" +
            "Isso deixará o banco com 0 votos para começar a eleição oficial no domingo.\n\n" +
            "Confirmar limpeza?"
        );
        if (!conf) return;

        const formData = new FormData();
        formData.append('action', 'zerar_banco');

        fetch('robo_consumo_continuo_2026.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.sucesso) {
                log('🧹 ' + data.mensagem, 'ok');
                alert('Banco zerado com sucesso! Pronto para a eleição oficial.');
                executarCicloAgora();
            } else {
                alert('Erro: ' + data.mensagem);
            }
        })
        .catch(err => alert('Erro: ' + err.message));
    }

    // Cronômetro regressivo
    setInterval(() => {
        if (!ativo) return;
        tempoRestante--;
        document.getElementById('countdown').innerText = tempoRestante + 's';

        if (tempoRestante <= 0) {
            executarCicloAgora();
            tempoRestante = intervaloPadrao;
        }
    }, 1000);

    // Primeiro ciclo automático ao abrir
    window.addEventListener('DOMContentLoaded', () => {
        executarCicloAgora();
    });
</script>

</body>
</html>
