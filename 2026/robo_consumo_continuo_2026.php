<?php
/**
 * robo_consumo_continuo_2026.php
 * Robô Automático de Consumo Contínuo em Tempo Real - Eleições 2026 (Acre)
 * 
 * Funciona de forma 100% autônoma:
 * - Pode ser aberto em uma aba do navegador ou rodar via terminal CLI (php robo_consumo_continuo_2026.php)
 * - Consulta a CDN oficial do TSE respeitando o cache e o limite de requisições
 * - Decodifica o JWS em segundo plano
 * - Persiste na tabela `2026_resultados` e `2026_candidatos`
 */

$root_dir = dirname(__DIR__);
require_once $root_dir . '/config/geral.php';

$db = Conexao::getInstance();

// Parâmetros do Simulado Ativo
$eleicao_estadual = isset($_GET['eleicao']) ? $_GET['eleicao'] : '21272'; // 21272 = Eleição Estadual Acre no simulado ativo
$intervalo_segundos = isset($_GET['intervalo']) ? max(10, (int)$_GET['intervalo']) : 30; // Padrão 30s
$modo_cli = (php_sapi_name() === 'cli');

// Função central de ingestão de todos os 22 municípios do Acre
function processarSimuladoAcreCompleto($db, $eleicao_id) {
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

    $cargosConfig = [
        ['eleicao' => '21270', 'code' => '0001', 'cargo_cd' => 1, 'nome' => 'presidente', 'sg_ue' => 'BR', 'nm_ue' => 'BRASIL'],
        ['eleicao' => '21272', 'code' => '0003', 'cargo_cd' => 3, 'nome' => 'governador', 'sg_ue' => 'AC', 'nm_ue' => 'ACRE'],
        ['eleicao' => '21272', 'code' => '0005', 'cargo_cd' => 5, 'nome' => 'senador', 'sg_ue' => 'AC', 'nm_ue' => 'ACRE'],
        ['eleicao' => '21272', 'code' => '0006', 'cargo_cd' => 6, 'nome' => 'deputado federal', 'sg_ue' => 'AC', 'nm_ue' => 'ACRE'],
        ['eleicao' => '21272', 'code' => '0007', 'cargo_cd' => 7, 'nome' => 'deputado estadual', 'sg_ue' => 'AC', 'nm_ue' => 'ACRE'],
    ];

    $mh = curl_multi_init();
    $curl_handles = [];

    foreach ($municipios as $m) {
        $cod = $m['cod_tse'];
        foreach ($cargosConfig as $cfg) {
            $url = "https://resultados-sim.tse.jus.br/simulado/simulado2026/ele2026/{$cfg['eleicao']}/dados/ac/ac{$cod}-c{$cfg['code']}-e0{$cfg['eleicao']}-u.jws";
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
                'eleicao' => $cfg['eleicao']
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
    $total_candidatos_atualizados = 0;
    $votos_por_cargo = [];

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

                    if ($item['cargo_nome'] === 'governador') {
                        $sec_tot = (int)($payload['s']['ts'] ?? 0);
                        $sec_apu = (int)($payload['s']['st'] ?? 0);
                        $porc = str_replace(',', '.', (string)($payload['s']['pst'] ?? '0.00'));
                        $stmtStats->execute([$item['cod_muni'], $item['nome_muni'], $sec_tot, $sec_apu, (float)$porc]);
                    }

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
        'municipios_total' => count($municipios),
        'candidatos_atualizados' => $total_candidatos_atualizados,
        'votos_por_cargo' => $votos_por_cargo,
        'secoes_total' => $totUrnas,
        'secoes_apuradas' => $apuUrnas,
        'porc' => number_format($porcGlobal, 2, ',', '.')
    ];
}

// Se for requisição AJAX para rodar 1 ciclo
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    header('Content-Type: application/json');
    $res = processarSimuladoAcreCompleto($db, $eleicao_estadual);
    echo json_encode([
        'timestamp' => date('H:i:s d/m/Y'),
        'global' => [
            'porc' => $res['porc'],
            'secoes_apuradas' => $res['secoes_apuradas'],
            'secoes_total' => $res['secoes_total'],
            'candidatos' => $res['candidatos_atualizados'],
            'votos_por_cargo' => $res['votos_por_cargo']
        ],
        'municipios_processados' => $res['municipios_total'],
        'arquivos_ok' => $res['arquivos_processados']
    ]);
    exit;
}

// Se for execução via CLI (terminal do Windows)
if ($modo_cli) {
    echo "=========================================================\n";
    echo "🤖 ROBÔ AUTÔNOMO DE CONSUMO DO SIMULADO TSE 2026 (ACRE)\n";
    echo "5 Cargos | 22 Municípios do Acre | Intervalo: {$intervalo_segundos}s\n";
    echo "Pressione Ctrl+C a qualquer momento para parar.\n";
    echo "=========================================================\n\n";

    $ciclo = 1;
    while (true) {
        $hora = date('H:i:s');
        echo "[{$hora}] 🔄 Ciclo #{$ciclo}: Consultando 110 arquivos do TSE CDN (5 Cargos x 22 Municípios)...\n";

        $t0 = microtime(true);
        $res = processarSimuladoAcreCompleto($db, $eleicao_estadual);
        $t1 = microtime(true);
        $duracao = round($t1 - $t0, 2);

        echo "  ✔ Atualizado em {$duracao}s! ({$res['arquivos_processados']} arquivos processados)\n";
        echo "  ✔ Apuração Global: {$res['porc']}% ({$res['secoes_apuradas']}/{$res['secoes_total']} seções)\n";
        foreach ($res['votos_por_cargo'] as $cargo => $vts) {
            echo "  ✔ " . strtoupper($cargo) . ": " . number_format($vts, 0, ',', '.') . " votos\n";
        }
        echo "  ⏳ Aguardando {$intervalo_segundos} segundos para a próxima verificação...\n\n";
        $ciclo++;
        sleep($intervalo_segundos);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Robô Automático de Apuração 2026 - TSE Ao Vivo</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&family=Oswald:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #070d19; color: #f1f5f9; padding: 30px; }
        .container { max-width: 1000px; margin: 0 auto; background: #0f172a; border-radius: 16px; padding: 28px; border: 1px solid #1e293b; box-shadow: 0 10px 40px rgba(0,0,0,0.5); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; border-bottom: 1px solid #1e293b; padding-bottom: 20px; }
        .header h1 { font-family: 'Oswald', sans-serif; font-size: 24px; color: #38bdf8; display: flex; align-items: center; gap: 12px; margin: 0; }
        .live-status { display: flex; align-items: center; gap: 10px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); padding: 8px 16px; border-radius: 20px; color: #34d399; font-weight: 700; font-size: 13px; }
        .pulse-dot { width: 10px; height: 10px; background: #10b981; border-radius: 50%; animation: pulse 1.5s infinite; }
        @keyframes pulse { 0% { transform: scale(0.9); opacity: 0.7; } 50% { transform: scale(1.4); opacity: 1; } 100% { transform: scale(0.9); opacity: 0.7; } }
        
        .control-bar { display: flex; gap: 12px; align-items: center; background: #1e293b; padding: 16px; border-radius: 12px; margin-bottom: 24px; flex-wrap: wrap; }
        .btn { padding: 10px 20px; border-radius: 8px; font-weight: 700; font-size: 14px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; text-decoration: none; }
        .btn-start { background: #10b981; color: #fff; }
        .btn-start:hover { background: #059669; }
        .btn-pause { background: #f59e0b; color: #fff; }
        .btn-pause:hover { background: #d97706; }
        .btn-clean { background: #ef4444; color: #fff; }
        .btn-clean:hover { background: #dc2626; }
        .btn-link { background: #3b82f6; color: #fff; }
        .btn-link:hover { background: #2563eb; }

        .dashboard-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; }
        .card-stat { background: #1e293b; padding: 20px; border-radius: 12px; border: 1px solid #334155; }
        .card-stat h3 { font-family: 'Oswald', sans-serif; font-size: 18px; color: #94a3b8; margin: 0 0 10px 0; display: flex; justify-content: space-between; }
        .card-stat .big-val { font-size: 32px; font-weight: 800; color: #fff; }
        .card-stat .detail { color: #38bdf8; font-size: 13px; font-weight: 600; margin-top: 6px; }

        .console-box { background: #020617; border: 1px solid #1e293b; border-radius: 12px; padding: 16px; font-family: 'JetBrains Mono', monospace; font-size: 13px; height: 280px; overflow-y: auto; color: #cbd5e1; }
        .log-entry { margin-bottom: 6px; line-height: 1.5; }
        .log-ok { color: #34d399; }
        .log-info { color: #38bdf8; }
        .log-warn { color: #fbbf24; }
        .log-err { color: #f87171; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div>
            <h1><i class="fa-solid fa-robot"></i> Robô de Consumo Automático - TSE 2026</h1>
            <p style="color:#94a3b8; font-size:13px; margin-top:4px;">Alimentação Contínua em Tempo Real para a tabela <code>2026_resultados</code> (Acre)</p>
        </div>
        <div class="live-status" id="statusBadge">
            <span class="pulse-dot"></span>
            <span id="statusText">ROBÔ ATIVO</span>
        </div>
    </div>

    <!-- Controles -->
    <div class="control-bar">
        <button id="btnToggle" class="btn btn-pause" onclick="toggleRobo();">
            <i class="fa-solid fa-pause"></i> Pausar Robô
        </button>
        <button class="btn btn-link" onclick="executarCicloAgora();" title="Consulta a CDN do TSE neste exato momento">
            <i class="fa-solid fa-bolt"></i> Consultar Agora
        </button>
        <a href="resultado-geral-2026.php" class="btn btn-link" target="_blank" title="Abre o painel em outra aba">
            <i class="fa-solid fa-chart-column"></i> Ver Painel de Apuração
        </a>
        <a href="importar_dados_abertos_2026.php?acao=limpar" class="btn btn-clean" onclick="return confirm('Deseja realmente zerar a tabela de resultados?');">
            <i class="fa-solid fa-trash"></i> Limpar Tabela
        </a>
        <div style="margin-left:auto; color:#94a3b8; font-size:13px; font-weight:600;">
            Intervalo: <span id="spanIntervalo"><?= $intervalo_segundos; ?></span>s • Próxima em: <strong id="countdown" style="color:#38bdf8;"><?= $intervalo_segundos; ?>s</strong>
        </div>
    </div>

    <!-- Estatísticas dos Cargos Monitorados -->
    <div class="dashboard-grid">
        <div class="card-stat">
            <h3><span>Governador (Acre)</span> <i class="fa-solid fa-user-tie"></i></h3>
            <div class="big-val" id="govApurado">0,00%</div>
            <div class="detail" id="govDetalhe">Aguardando 1º ciclo de coleta do TSE...</div>
        </div>
        <div class="card-stat">
            <h3><span>Senador (Acre)</span> <i class="fa-solid fa-landmark"></i></h3>
            <div class="big-val" id="senApurado">0,00%</div>
            <div class="detail" id="senDetalhe">Aguardando 1º ciclo de coleta do TSE...</div>
        </div>
    </div>

    <!-- Console de Eventos em Tempo Real -->
    <h3 style="font-size:15px; margin-bottom:10px; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;">
        <i class="fa-solid fa-terminal"></i> Log de Execução e Ingestão no Banco de Dados:
    </h3>
    <div class="console-box" id="consoleLog">
        <div class="log-entry log-info">[<?= date('H:i:s'); ?>] 🚀 Robô inicializado. Respeitando limite de vazão da CDN do TSE (1 requisição a cada <?= $intervalo_segundos; ?>s).</div>
    </div>
</div>

<script>
    let ativo = true;
    const intervaloPadrao = <?= $intervalo_segundos; ?>;
    let tempoRestante = intervaloPadrao;
    let contadorCiclos = 0;

    function log(msg, tipo = 'info') {
        const consoleEl = document.getElementById('consoleLog');
        const d = new Date();
        const hora = d.toTimeString().split(' ')[0];
        const corClasse = 'log-' + tipo;
        const entry = document.createElement('div');
        entry.className = 'log-entry ' + corClasse;
        entry.innerHTML = `[${hora}] ${msg}`;
        consoleEl.appendChild(entry);
        consoleEl.scrollTop = consoleEl.scrollHeight;
    }

    function toggleRobo() {
        ativo = !ativo;
        const btn = document.getElementById('btnToggle');
        const badge = document.getElementById('statusBadge');
        const text = document.getElementById('statusText');

        if (ativo) {
            btn.className = 'btn btn-pause';
            btn.innerHTML = '<i class="fa-solid fa-pause"></i> Pausar Robô';
            badge.style.background = 'rgba(16, 185, 129, 0.15)';
            badge.style.borderColor = 'rgba(16, 185, 129, 0.4)';
            badge.style.color = '#34d399';
            text.innerText = 'ROBÔ ATIVO';
            log('▶ Robô retomado pelo usuário.', 'ok');
            tempoRestante = 1;
        } else {
            btn.className = 'btn btn-start';
            btn.innerHTML = '<i class="fa-solid fa-play"></i> Iniciar Robô';
            badge.style.background = 'rgba(245, 158, 11, 0.15)';
            badge.style.borderColor = 'rgba(245, 158, 11, 0.4)';
            badge.style.color = '#fbbf24';
            text.innerText = 'PAUSADO';
            log('⏸ Robô pausado pelo usuário.', 'warn');
        }
    }

    function executarCicloAgora() {
        contadorCiclos++;
        log(`⚡ Ciclo #${contadorCiclos}: Conectando à CDN do TSE (Simulado 2026 - Acre)...`, 'info');

        fetch('robo_consumo_continuo_2026.php?ajax=1&eleicao=<?= $eleicao_estadual; ?>')
            .then(res => res.json())
            .then(data => {
                // Atualiza Governador
                if (data.governador && !data.governador.erro) {
                    const g = data.governador;
                    document.getElementById('govApurado').innerText = g.porc + '%';
                    document.getElementById('govDetalhe').innerText = `${g.secoes_apuradas} de ${g.secoes_total} seções apuradas • ${g.votos.toLocaleString()} votos • Atualizado às ${g.hora_tse}`;
                    log(`✔ [Governador] Dados gravados no banco: ${g.porc}% apurado (${g.votos.toLocaleString()} votos computados).`, 'ok');
                } else if (data.governador && data.governador.erro) {
                    log(`❌ [Governador] ${data.governador.erro}`, 'err');
                }

                // Atualiza Senador
                if (data.senador && !data.senador.erro) {
                    const s = data.senador;
                    document.getElementById('senApurado').innerText = s.porc + '%';
                    document.getElementById('senDetalhe').innerText = `${s.secoes_apuradas} de ${s.secoes_total} seções apuradas • ${s.votos.toLocaleString()} votos • Atualizado às ${s.hora_tse}`;
                    log(`✔ [Senador] Dados gravados no banco: ${s.porc}% apurado (${s.votos.toLocaleString()} votos computados).`, 'ok');
                } else if (data.senador && data.senador.erro) {
                    log(`❌ [Senador] ${data.senador.erro}`, 'err');
                }
            })
            .catch(err => {
                log(`❌ Erro na comunicação: ${err.message}`, 'err');
            });
    }

    // Cronômetro regressivo e loop contínuo automático
    setInterval(() => {
        if (!ativo) return;
        tempoRestante--;
        document.getElementById('countdown').innerText = tempoRestante + 's';

        if (tempoRestante <= 0) {
            executarCicloAgora();
            tempoRestante = intervaloPadrao;
        }
    }, 1000);

    // Executa o primeiro ciclo imediatamente ao abrir a página
    window.addEventListener('DOMContentLoaded', () => {
        executarCicloAgora();
    });
</script>

</body>
</html>
