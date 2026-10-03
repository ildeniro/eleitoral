<?php
/**
 * resultado-geral-2026.php
 * Painel de Acompanhamento da Apuração em Tempo Real - Eleições Gerais 2026 (Acre)
 * Preparado para o Dia da Eleição: Suporte aos 22 municípios e todos os 5 cargos
 */

$root_dir = dirname(__DIR__);
require_once $root_dir . '/config/geral.php';
require_once $root_dir . '/config/funcoes.php';
require_once __DIR__ . '/config_apuracao_2026.php';

$db = Conexao::getInstance();
$config = carregar_config_apuracao_2026();

// Parâmetros de Filtro
$cargo_filtro = isset($_GET['cargo']) ? strtolower(trim($_GET['cargo'])) : 'governador';
$cargo_filtro = str_replace(['-', '_'], ' ', $cargo_filtro);
$municipio_filtro = isset($_GET['municipio']) ? trim($_GET['municipio']) : 'all'; // 'all' ou código TSE de 5 dígitos (ex: 01392)
$turno = isset($_GET['turno']) ? (int)$_GET['turno'] : 1;

// Mapeamento de Cargos e Vagas para 2026
$cargos_disponiveis = [
    'governador' => [
        'cd' => 3, 
        'nome' => 'Governador', 
        'vagas' => 1,
        'sistema' => 'majoritario_absoluto',
        'icone' => 'fa-user-tie'
    ],
    'senador' => [
        'cd' => 5, 
        'nome' => 'Senador', 
        'vagas' => 2, // 2 vagas em 2026!
        'sistema' => 'majoritario_simples',
        'icone' => 'fa-landmark'
    ],
    'presidente' => [
        'cd' => 1, 
        'nome' => 'Presidente da República', 
        'vagas' => 1,
        'sistema' => 'majoritario_absoluto',
        'icone' => 'fa-flag'
    ],
    'deputado federal' => [
        'cd' => 6, 
        'nome' => 'Deputado Federal', 
        'vagas' => 8, // 8 vagas no Acre
        'sistema' => 'proporcional',
        'icone' => 'fa-users'
    ],
    'deputado estadual' => [
        'cd' => 7, 
        'nome' => 'Deputado Estadual', 
        'vagas' => 24, // 24 vagas no Acre
        'sistema' => 'proporcional',
        'icone' => 'fa-building-columns'
    ]
];

if (!isset($cargos_disponiveis[$cargo_filtro])) {
    $cargo_filtro = 'governador';
}
$cargo_info = $cargos_disponiveis[$cargo_filtro];

// Busca os 22 municípios do Acre
$sqlMuni = "
    SELECT DISTINCT m.id, m.nome, LPAD(m.cod_tse, 5, '0') AS cod_tse 
    FROM bsc_municipios m
    WHERE m.estado_id = 1 
    ORDER BY m.nome ASC
";
$municipios = $db->query($sqlMuni)->fetchAll(PDO::FETCH_ASSOC);

// Determina o Total de Urnas (Seções)
if ($municipio_filtro === 'all') {
    $nome_localidade = "ESTADO DO ACRE (TODOS OS 22 MUNICÍPIOS)";
    $sqlTotUrnas = "SELECT COUNT(*) FROM locais_votacao_2026";
    $qtd_total_urnas = (int)$db->query($sqlTotUrnas)->fetchColumn();
    if ($qtd_total_urnas === 0) $qtd_total_urnas = 2411; // Padrão oficial Acre 2026
} else {
    $stmtMuniNome = $db->prepare("SELECT nome, id FROM bsc_municipios WHERE LPAD(cod_tse, 5, '0') = ? LIMIT 1");
    $stmtMuniNome->execute([$municipio_filtro]);
    $muni_row = $stmtMuniNome->fetch(PDO::FETCH_ASSOC);
    $nome_localidade = $muni_row ? strtoupper($muni_row['nome']) : "MUNICÍPIO TSE {$municipio_filtro}";
    $muni_id_local = $muni_row ? $muni_row['id'] : 0;

    $stmtTotUrnas = $db->prepare("SELECT COUNT(*) FROM locais_votacao_2026 WHERE municipio_id = ?");
    $stmtTotUrnas->execute([$muni_id_local]);
    $qtd_total_urnas = (int)$stmtTotUrnas->fetchColumn();
    if ($qtd_total_urnas === 0) $qtd_total_urnas = 100;
}

// Determina Quantidade de Urnas Apuradas
$tem_apuracao_tse = false;
$qtd_urnas_apuradas = 0;

try {
    $checkApuracao = (int)$db->query("SELECT COUNT(*) FROM 2026_apuracao_municipios WHERE SECOES_TOTAL > 0")->fetchColumn();
    if ($checkApuracao > 0) {
        $tem_apuracao_tse = true;
        if ($municipio_filtro === 'all') {
            $statsApuracao = $db->query("SELECT SUM(SECOES_TOTAL) as tot, SUM(SECOES_APURADAS) as apu FROM 2026_apuracao_municipios")->fetch(PDO::FETCH_ASSOC);
            $qtd_total_urnas = (int)($statsApuracao['tot'] ?? 2411);
            $qtd_urnas_apuradas = (int)($statsApuracao['apu'] ?? 0);
        } else {
            $stmtStatsM = $db->prepare("SELECT SECOES_TOTAL as tot, SECOES_APURADAS as apu, PORC_APURADA as porc FROM 2026_apuracao_municipios WHERE COD_MUNICIPIO_TSE = ?");
            $stmtStatsM->execute([$municipio_filtro]);
            $statsM = $stmtStatsM->fetch(PDO::FETCH_ASSOC);
            if ($statsM && $statsM['tot'] > 0) {
                $qtd_total_urnas = (int)$statsM['tot'];
                $qtd_urnas_apuradas = (int)$statsM['apu'];
            }
        }
    }
} catch (Exception $e) {
    $tem_apuracao_tse = false;
}

// Fallback para contagem de seções individuais em 2026_resultados se não houver estatística de apuração
if (!$tem_apuracao_tse || $qtd_urnas_apuradas === 0) {
    $sqlApuradas = "
        SELECT COUNT(DISTINCT ZONA, SECAO) 
        FROM 2026_resultados 
        WHERE ANO_ELEICAO = 2026 AND COD_CARGO = ?
    ";
    $paramsApuradas = [$cargo_filtro];

    if ($municipio_filtro !== 'all') {
        $sqlApuradas .= " AND COD_MUNICIPIO_TSE = ?";
        $paramsApuradas[] = $municipio_filtro;
    }
    $stmtApuradas = $db->prepare($sqlApuradas);
    $stmtApuradas->execute($paramsApuradas);
    $qtd_secoes_indiv = (int)$stmtApuradas->fetchColumn();
    if ($qtd_secoes_indiv > 0) {
        $qtd_urnas_apuradas = $qtd_secoes_indiv;
    }
}

$porc_urnas_apuradas = ($qtd_total_urnas > 0) ? min(100, ($qtd_urnas_apuradas / $qtd_total_urnas) * 100) : 0;

// Busca os Votos Apurados na tabela `2026_resultados`
$sqlVotos = "
    SELECT NUM_CANDIDATO, TIPO_VOTO, PARTIDO, SUM(QTD_VOTOS) AS total_votos
    FROM 2026_resultados
    WHERE ANO_ELEICAO = 2026 AND COD_CARGO = ?
";
$paramsVotos = [$cargo_filtro];
if ($municipio_filtro !== 'all') {
    $sqlVotos .= " AND COD_MUNICIPIO_TSE = ?";
    $paramsVotos[] = $municipio_filtro;
}
$sqlVotos .= " GROUP BY NUM_CANDIDATO, TIPO_VOTO";

$stmtVotos = $db->prepare($sqlVotos);
$stmtVotos->execute($paramsVotos);
$votos_apurados_raw = $stmtVotos->fetchAll(PDO::FETCH_ASSOC);

$votos_por_candidato = [];
$votos_brancos = 0;
$votos_nulos = 0;
$soma_votos_validos = 0;

foreach ($votos_apurados_raw as $v) {
    $tipo = strtolower($v['TIPO_VOTO']);
    $cand = (string)$v['NUM_CANDIDATO'];
    $qtd  = (int)$v['total_votos'];

    if ($tipo === 'branco' || $cand === 'BRANCO') {
        $votos_brancos += $qtd;
    } elseif ($tipo === 'nulo' || $cand === 'NULO') {
        $votos_nulos += $qtd;
    } else {
        $votos_por_candidato[$cand] = ($votos_por_candidato[$cand] ?? 0) + $qtd;
        $soma_votos_validos += $qtd;
    }
}

// Busca Metadados dos Candidatos de 2026 cadastrados
$sqlCands = "
    SELECT NR_CANDIDATO, NM_URNA_CANDIDATO, NM_CANDIDATO, SG_PARTIDO, NM_COLIGACAO, FOTO_URL 
    FROM 2026_candidatos 
    WHERE ANO_ELEICAO = 2026 AND CD_CARGO = ? AND STATUS = 1
    ORDER BY NR_CANDIDATO ASC
";
$stmtCands = $db->prepare($sqlCands);
$stmtCands->execute([$cargo_info['cd']]);
$candidatos_base = $stmtCands->fetchAll(PDO::FETCH_ASSOC);

// Monta lista unificada de candidatos com seus votos
$ranking_candidatos = [];
foreach ($candidatos_base as $cb) {
    $num = (string)$cb['NR_CANDIDATO'];
    $vts = $votos_por_candidato[$num] ?? 0;
    $ranking_candidatos[$num] = [
        'numero' => $num,
        'nome' => $cb['NM_URNA_CANDIDATO'] ?: $cb['NM_CANDIDATO'],
        'partido' => $cb['SG_PARTIDO'],
        'coligacao' => $cb['NM_COLIGACAO'],
        'foto' => $cb['FOTO_URL'],
        'votos' => $vts
    ];
}

// Adiciona candidatos que porventura receberam votos no TSE mas não estavam no cadastro prévio
foreach ($votos_por_candidato as $num => $vts) {
    if (!isset($ranking_candidatos[$num])) {
        $ranking_candidatos[$num] = [
            'numero' => $num,
            'nome' => "CANDIDATO {$num}",
            'partido' => "PARTIDO",
            'coligacao' => "",
            'foto' => null,
            'votos' => $vts
        ];
    }
}

// Ordena os candidatos por quantidade de votos decrescente
uasort($ranking_candidatos, function($a, $b) {
    return $b['votos'] <=> $a['votos'];
});

$total_geral_votos = $soma_votos_validos + $votos_brancos + $votos_nulos;
$pct_brancos = ($total_geral_votos > 0) ? round(($votos_brancos / $total_geral_votos) * 100, 2) : 0;
$pct_nulos   = ($total_geral_votos > 0) ? round(($votos_nulos / $total_geral_votos) * 100, 2) : 0;
$pct_validos = ($total_geral_votos > 0) ? round(($soma_votos_validos / $total_geral_votos) * 100, 2) : 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Apuração 2026 - <?= $cargo_info['nome']; ?> (Acre) • Ao Vivo</title>
    <!-- Google Fonts Inter e Oswald -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-color: #0b1120;
            --card-bg: #1e293b;
            --card-border: #334155;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --primary: #38bdf8;
            --primary-gradient: linear-gradient(135deg, #2563eb, #38bdf8);
            --success: #10b981;
            --warning: #f59e0b;
            --accent: #6366f1;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            padding: 24px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Top Bar */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            padding: 16px 24px;
            border-radius: 14px;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            flex-wrap: wrap;
            gap: 16px;
        }

        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .topbar-brand i {
            font-size: 32px;
            color: var(--primary);
        }
        .topbar-brand h1 {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .topbar-brand span {
            font-size: 13px;
            color: var(--text-muted);
            display: block;
        }

        .topbar-clocks {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .clock-pill {
            background: #0f172a;
            border: 1px solid var(--card-border);
            padding: 6px 12px;
            border-radius: 8px;
            text-align: center;
        }
        .clock-pill .lbl {
            font-size: 9px;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 700;
        }
        .clock-pill .val {
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .badge-env {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .badge-env.oficial {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.4);
        }
        .badge-env.simulado {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.4);
        }

        .dot-pulse {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }
        .dot-pulse.green { background: #10b981; }
        .dot-pulse.amber { background: #f59e0b; }

        @keyframes pulse {
            0% { transform: scale(0.9); opacity: 0.7; }
            50% { transform: scale(1.3); opacity: 1; }
            100% { transform: scale(0.9); opacity: 0.7; }
        }

        /* Barra de Controle de Atualização */
        .refresh-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #0f172a;
            border: 1px solid var(--card-border);
            padding: 10px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 13px;
            color: var(--text-muted);
            flex-wrap: wrap;
            gap: 12px;
        }

        /* Filtros */
        .filter-panel {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 18px 24px;
            margin-bottom: 24px;
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 16px;
            align-items: end;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            background: #0f172a;
            border: 1px solid var(--card-border);
            color: #fff;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            outline: none;
            transition: all 0.2s;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.25);
        }

        .btn {
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-primary {
            background: #2563eb;
            color: #fff;
        }
        .btn-primary:hover {
            background: #1d4ed8;
        }
        .btn-outline {
            background: transparent;
            border: 1px solid var(--card-border);
            color: var(--text-main);
        }
        .btn-outline:hover {
            background: #334155;
        }

        /* Status Urnas e Progresso */
        .summary-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        }

        .summary-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .summary-title h2 {
            font-family: 'Oswald', sans-serif;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .summary-title p {
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 500;
            margin-top: 4px;
        }

        .urnas-stats {
            text-align: right;
        }
        .urnas-stats .count {
            font-family: 'JetBrains Mono', monospace;
            font-size: 26px;
            font-weight: 700;
            color: #fff;
        }
        .urnas-stats .percent {
            color: #10b981;
            font-weight: 700;
            font-size: 15px;
        }

        .progress-container {
            width: 100%;
            height: 16px;
            background: #0f172a;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 18px;
            position: relative;
            border: 1px solid var(--card-border);
        }
        .progress-bar-fill {
            height: 100%;
            background: var(--primary-gradient);
            border-radius: 10px;
            transition: width 0.8s ease-in-out;
        }

        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
            border-top: 1px solid var(--card-border);
            padding-top: 16px;
        }
        .stat-item {
            background: #0f172a;
            padding: 12px 16px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.05);
        }
        .stat-item .label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
        }
        .stat-item .val {
            font-family: 'JetBrains Mono', monospace;
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            margin-top: 4px;
        }
        .stat-item .sub {
            font-size: 11px;
            color: var(--primary);
            margin-top: 2px;
        }

        /* Lista de Candidatos */
        .candidates-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .candidate-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 18px 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.2s, border-color 0.2s;
            position: relative;
            overflow: hidden;
        }
        .candidate-card:hover {
            transform: translateY(-2px);
            border-color: #475569;
        }
        .candidate-card.in-zone {
            border-color: rgba(56, 189, 248, 0.4);
            background: linear-gradient(90deg, rgba(30, 41, 59, 1) 0%, rgba(56, 189, 248, 0.06) 100%);
        }
        .candidate-card.leader {
            border-color: rgba(16, 185, 129, 0.5);
            background: linear-gradient(90deg, rgba(30, 41, 59, 1) 0%, rgba(16, 185, 129, 0.08) 100%);
        }

        .candidate-pos {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-muted);
            width: 32px;
            text-align: center;
        }
        .candidate-card.leader .candidate-pos {
            color: #10b981;
        }
        .candidate-card.in-zone .candidate-pos {
            color: var(--primary);
        }

        .candidate-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #0f172a;
            border: 2px solid var(--primary);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .candidate-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .candidate-avatar .initials {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary);
        }

        .candidate-info {
            flex: 1;
            min-width: 0;
        }
        .candidate-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
            flex-wrap: wrap;
        }
        .candidate-name {
            font-size: 17px;
            font-weight: 700;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .candidate-number {
            background: #0f172a;
            border: 1px solid var(--card-border);
            color: var(--primary);
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 6px;
        }
        .candidate-party {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .badge-status-cand {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
            letter-spacing: 0.5px;
        }
        .badge-eleito {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.4);
        }
        .badge-zona {
            background: rgba(56, 189, 248, 0.2);
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.4);
        }

        .candidate-graphic {
            margin-top: 10px;
        }
        .candidate-bar-bg {
            width: 100%;
            height: 8px;
            background: #0f172a;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 4px;
        }
        .candidate-bar-fill {
            height: 100%;
            background: var(--primary-gradient);
            border-radius: 4px;
            transition: width 0.6s ease;
        }
        .candidate-votes-text {
            font-size: 13px;
            font-weight: 600;
            color: #cbd5e1;
        }

        .candidate-percentage {
            font-family: 'Oswald', sans-serif;
            font-size: 32px;
            font-weight: 700;
            color: #fff;
            text-align: right;
            min-width: 95px;
        }
        .candidate-card.leader .candidate-percentage {
            color: #10b981;
        }
        .candidate-card.in-zone .candidate-percentage {
            color: var(--primary);
        }

        .proporcional-notice {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #a5b4fc;
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Rodapé de Ações */
        .footer-tools {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            flex-wrap: wrap;
            gap: 12px;
        }

        @media (max-width: 768px) {
            .filter-panel { grid-template-columns: 1fr; }
            .candidate-card { flex-wrap: wrap; gap: 14px; }
            .candidate-percentage { text-align: left; }
            .topbar { flex-direction: column; align-items: flex-start; }
            .topbar-actions { width: 100%; justify-content: space-between; }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Topbar -->
    <header class="topbar">
        <div class="topbar-brand">
            <i class="fa-solid <?= $cargo_info['icone']; ?>"></i>
            <div>
                <h1>Apuração Eleições 2026 • Acre</h1>
                <span>Totalização em Tempo Real • <?= strtoupper($cargo_info['nome']); ?> (1º Turno)</span>
            </div>
        </div>

        <div class="topbar-clocks">
            <div class="clock-pill">
                <div class="lbl">Acre (Local)</div>
                <div class="val" id="topClockAcre">--:--:--</div>
            </div>
            <div class="clock-pill">
                <div class="lbl">Brasília (DF)</div>
                <div class="val" id="topClockBsb">--:--:--</div>
            </div>
        </div>

        <div class="topbar-actions">
            <div class="badge-env <?= $config['ambiente'] === 'oficial' ? 'oficial' : 'simulado'; ?>" title="<?= $config['descricao_ambiente']; ?>">
                <span class="dot-pulse <?= $config['ambiente'] === 'oficial' ? 'green' : 'amber'; ?>"></span>
                <span><?= $config['ambiente'] === 'oficial' ? 'OFICIAL TSE AO VIVO' : 'MODO SIMULADO'; ?></span>
            </div>
            <button onclick="forcarAtualizacao();" class="btn btn-outline" title="Atualizar agora">
                <i class="fa-solid fa-arrows-rotate"></i> Atualizar
            </button>
        </div>
    </header>

    <!-- Barra de Controle do Auto-Refresh -->
    <div class="refresh-bar">
        <div style="display:flex; align-items:center; gap:8px;">
            <i class="fa-regular fa-clock"></i>
            <span>Última leitura: <strong><?= date('H:i:s'); ?></strong></span>
            <span>• Próxima em: <strong id="refreshTimer" style="color:var(--primary); font-family:'JetBrains Mono', monospace;">30s</strong></span>
        </div>
        <div style="display:flex; align-items:center; gap:10px;">
            <label for="selectIntervalo" style="font-size:12px;">Intervalo de Atualização:</label>
            <select id="selectIntervalo" class="form-control" style="width:auto; padding:4px 10px; font-size:12px;" onchange="alterarIntervalo(this.value);">
                <option value="15">15 segundos</option>
                <option value="30" selected>30 segundos</option>
                <option value="60">60 segundos</option>
                <option value="0">Pausar Atualização</option>
            </select>
            <a href="robo_consumo_continuo_2026.php" class="btn btn-outline" style="padding:4px 10px; font-size:12px;" target="_blank">
                <i class="fa-solid fa-robot"></i> Monitorar Robô
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <form method="get" class="filter-panel" id="formFiltro">
        <div class="form-group">
            <label for="cargo"><i class="fa-solid fa-user-tie"></i> Cargo em Disputa</label>
            <select name="cargo" id="cargo" class="form-control" onchange="document.getElementById('formFiltro').submit();">
                <?php foreach ($cargos_disponiveis as $cKey => $cInfo): ?>
                    <option value="<?= $cKey; ?>" <?= $cKey === $cargo_filtro ? 'selected' : ''; ?>>
                        <?= $cInfo['nome']; ?> <?= $cInfo['vagas'] > 1 ? "({$cInfo['vagas']} vagas)" : ""; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="municipio"><i class="fa-solid fa-location-dot"></i> Município de Apuração</label>
            <select name="municipio" id="municipio" class="form-control" onchange="document.getElementById('formFiltro').submit();">
                <option value="all" <?= $municipio_filtro === 'all' ? 'selected' : ''; ?>>
                    🌐 Todo o Estado do Acre (22 Municípios)
                </option>
                <?php foreach ($municipios as $m): ?>
                    <option value="<?= $m['cod_tse']; ?>" <?= $municipio_filtro === $m['cod_tse'] ? 'selected' : ''; ?>>
                        📍 <?= $m['nome']; ?> (<?= $m['cod_tse']; ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <button type="submit" class="btn btn-primary" style="height: 42px;">
                <i class="fa-solid fa-filter"></i> Filtrar
            </button>
        </div>
    </form>

    <!-- Aviso para Cargos Proporcionais (Deputados) -->
    <?php if ($cargo_info['sistema'] === 'proporcional'): ?>
        <div class="proporcional-notice">
            <i class="fa-solid fa-circle-info" style="font-size:20px;"></i>
            <div>
                <strong>Eleição Proporcional (<?= $cargo_info['vagas']; ?> vagas no Acre):</strong> 
                A ordem abaixo reflete a votação nominal dos candidatos. As vagas definitivas são distribuídas conforme os Quocientes Eleitoral e Partidário (QE e QP) e a cláusula de barreira individual (10% do QE).
            </div>
        </div>
    <?php elseif ($cargo_filtro === 'senador'): ?>
        <div class="proporcional-notice" style="background:rgba(56, 189, 248, 0.1); border-color:rgba(56, 189, 248, 0.3); color:#bae6fd;">
            <i class="fa-solid fa-circle-info" style="font-size:20px;"></i>
            <div>
                <strong>Eleição Majoritária para o Senado (2 Vagas em 2026):</strong> 
                Os <strong>dois candidatos</strong> mais votados no Estado do Acre serão eleitos para mandatos de 8 anos.
            </div>
        </div>
    <?php endif; ?>

    <!-- Card de Status das Urnas -->
    <section class="summary-card">
        <div class="summary-header">
            <div class="summary-title">
                <h2>CANDIDATOS A <?= strtoupper($cargo_info['nome']); ?> - <?= $nome_localidade; ?></h2>
                <p>Eleições Gerais 2026 • 1º Turno • Fonte: Tribunal Superior Eleitoral (TSE)</p>
            </div>
            <div class="urnas-stats">
                <div class="count">
                    <?= number_format($qtd_urnas_apuradas, 0, ',', '.'); ?> / <?= number_format($qtd_total_urnas, 0, ',', '.'); ?>
                </div>
                <div class="percent">
                    <?= number_format($porc_urnas_apuradas, 2, ',', '.'); ?>% das urnas apuradas
                </div>
            </div>
        </div>

        <div class="progress-container">
            <div class="progress-bar-fill" style="width: <?= $porc_urnas_apuradas; ?>%"></div>
        </div>

        <div class="quick-stats">
            <div class="stat-item">
                <div class="label">Votos Válidos (Nominais)</div>
                <div class="val"><?= number_format($soma_votos_validos, 0, ',', '.'); ?></div>
                <div class="sub"><?= number_format($pct_validos, 2, ',', '.'); ?>% do total</div>
            </div>
            <div class="stat-item">
                <div class="label">Votos em Branco</div>
                <div class="val"><?= number_format($votos_brancos, 0, ',', '.'); ?></div>
                <div class="sub"><?= number_format($pct_brancos, 2, ',', '.'); ?>% do total</div>
            </div>
            <div class="stat-item">
                <div class="label">Votos Nulos</div>
                <div class="val"><?= number_format($votos_nulos, 0, ',', '.'); ?></div>
                <div class="sub"><?= number_format($pct_nulos, 2, ',', '.'); ?>% do total</div>
            </div>
            <div class="stat-item">
                <div class="label">Total Geral Computado</div>
                <div class="val"><?= number_format($total_geral_votos, 0, ',', '.'); ?></div>
                <div class="sub">Válidos + Brancos + Nulos</div>
            </div>
        </div>
    </section>

    <!-- Lista de Candidatos -->
    <main class="candidates-list">
        <?php
        $pos = 1;
        if (empty($ranking_candidatos)): ?>
            <div style="text-align: center; padding: 40px; background: var(--card-bg); border-radius: 12px; color: var(--text-muted); border: 1px dashed var(--card-border);">
                <i class="fa-solid fa-inbox" style="font-size: 36px; margin-bottom: 12px; display: block; color: var(--primary);"></i>
                Nenhum voto computado ainda para este cargo nesta localidade.
                <div style="margin-top:10px; font-size:12px;">Certifique-se de que o <a href="robo_consumo_continuo_2026.php" target="_blank" style="color:var(--primary); text-decoration:underline;">Robô de Consumo</a> está ativo.</div>
            </div>
        <?php else:
            foreach ($ranking_candidatos as $cand):
                $votos_cand = (int)$cand['votos'];
                $pct_cand = ($soma_votos_validos > 0) ? (($votos_cand / $soma_votos_validos) * 100) : 0;
                
                // Determina destaque de zona de eleição
                $is_leader = ($pos === 1 && $votos_cand > 0);
                $is_in_zone = false;
                $badge_texto = "";
                $badge_class = "";

                if ($cargo_filtro === 'governador' || $cargo_filtro === 'presidente') {
                    if ($pos === 1 && $votos_cand > 0) {
                        $is_in_zone = true;
                        if ($pct_cand > 50) {
                            $badge_texto = ($porc_urnas_apuradas >= 99) ? "ELEITO EM 1º TURNO" : "1º LUGAR (>50% VÁLIDOS)";
                            $badge_class = "badge-eleito";
                        } else {
                            $badge_texto = "1º LUGAR (DISPUTA 2º TURNO)";
                            $badge_class = "badge-zona";
                        }
                    } elseif ($pos === 2 && $votos_cand > 0) {
                        $badge_texto = "2º LUGAR";
                        $badge_class = "badge-zona";
                    }
                } elseif ($cargo_filtro === 'senador') {
                    if ($pos === 1 && $votos_cand > 0) {
                        $is_in_zone = true;
                        $badge_texto = ($porc_urnas_apuradas >= 99) ? "ELEITO (1ª VAGA)" : "1ª VAGA SENADO";
                        $badge_class = "badge-eleito";
                    } elseif ($pos === 2 && $votos_cand > 0) {
                        $is_in_zone = true;
                        $badge_texto = ($porc_urnas_apuradas >= 99) ? "ELEITO (2ª VAGA)" : "2ª VAGA SENADO";
                        $badge_class = "badge-eleito";
                    }
                } elseif ($cargo_filtro === 'deputado federal') {
                    if ($pos <= 8 && $votos_cand > 0) {
                        $is_in_zone = true;
                        $badge_texto = "ZONA PRELIMINAR ({$pos}º DE 8)";
                        $badge_class = "badge-zona";
                    }
                } elseif ($cargo_filtro === 'deputado estadual') {
                    if ($pos <= 24 && $votos_cand > 0) {
                        $is_in_zone = true;
                        $badge_texto = "ZONA PRELIMINAR ({$pos}º DE 24)";
                        $badge_class = "badge-zona";
                    }
                }
        ?>
            <article class="candidate-card <?= $is_leader ? 'leader' : ($is_in_zone ? 'in-zone' : ''); ?>">
                <div class="candidate-pos"><?= $pos; ?>º</div>

                <div class="candidate-avatar">
                    <?php if (!empty($cand['foto']) && file_exists($root_dir . '/' . $cand['foto'])): ?>
                        <img src="<?= PORTAL_URL . $cand['foto']; ?>" alt="<?= htmlspecialchars($cand['nome']); ?>">
                    <?php else: ?>
                        <div class="initials"><?= substr($cand['nome'], 0, 2); ?></div>
                    <?php endif; ?>
                </div>

                <div class="candidate-info">
                    <div class="candidate-title">
                        <span class="candidate-name"><?= htmlspecialchars($cand['nome']); ?></span>
                        <span class="candidate-number"><?= htmlspecialchars($cand['numero']); ?></span>
                        <?php if (!empty($badge_texto)): ?>
                            <span class="badge-status-cand <?= $badge_class; ?>"><?= $badge_texto; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="candidate-party">
                        <?= htmlspecialchars($cand['partido']); ?> 
                        <?= !empty($cand['coligacao']) ? ' • ' . htmlspecialchars($cand['coligacao']) : ''; ?>
                    </div>

                    <div class="candidate-graphic">
                        <div class="candidate-bar-bg">
                            <div class="candidate-bar-fill" style="width: <?= number_format($pct_cand, 2, '.', ''); ?>%"></div>
                        </div>
                        <div class="candidate-votes-text">
                            <strong><?= number_format($votos_cand, 0, ',', '.'); ?></strong> votos nominais
                        </div>
                    </div>
                </div>

                <div class="candidate-percentage">
                    <?= number_format($pct_cand, 2, ',', '.'); ?>%
                </div>
            </article>
        <?php
                $pos++;
            endforeach;
        endif;
        ?>
    </main>

    <!-- Ferramentas Rápidas no Rodapé -->
    <footer class="footer-tools">
        <div style="font-size: 13px; color: var(--text-muted);">
            InteliVoto 2026 • Apuração Oficial e Totalização em Tempo Real (Estado do Acre)
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="robo_consumo_continuo_2026.php" class="btn btn-outline" style="border-color:#10b981; color:#34d399;" target="_blank" title="Alimentação contínua no banco de dados">
                <i class="fa-solid fa-robot"></i> Robô Automático (Alimentar Banco)
            </a>
            <a href="teste_simulado_tse_aovivo.php" class="btn btn-outline" style="border-color:#38bdf8; color:#38bdf8;" target="_blank" title="Diagnóstico de transmissão da CDN">
                <i class="fa-solid fa-satellite-dish"></i> Teste CDN TSE
            </a>
            <a href="importar_dados_abertos_2026.php" class="btn btn-outline" target="_blank" title="Importar arquivos CSV de dados abertos">
                <i class="fa-solid fa-file-csv"></i> Dados Abertos
            </a>
            <a href="<?= PORTAL_URL; ?>view/admin/dashboard" class="btn btn-primary">
                <i class="fa-solid fa-house"></i> Voltar ao Painel
            </a>
        </div>
    </footer>
</div>

<script>
    // Relógios
    function atualizarRelogiosPainel() {
        const agora = new Date();
        const acreTime = new Date(agora.toLocaleString("en-US", { timeZone: "America/Rio_Branco" }));
        const bsbTime = new Date(agora.toLocaleString("en-US", { timeZone: "America/Sao_Paulo" }));
        
        document.getElementById('topClockAcre').innerText = acreTime.toTimeString().split(' ')[0];
        document.getElementById('topClockBsb').innerText = bsbTime.toTimeString().split(' ')[0];
    }
    setInterval(atualizarRelogiosPainel, 1000);
    atualizarRelogiosPainel();

    // Auto-Refresh Inteligente com preservação de scroll
    let intervalo = parseInt(localStorage.getItem('apuracao_intervalo') || '30');
    let segundosRestantes = intervalo;
    const selectEl = document.getElementById('selectIntervalo');
    if (selectEl) selectEl.value = intervalo;

    function alterarIntervalo(novoVal) {
        intervalo = parseInt(novoVal);
        localStorage.setItem('apuracao_intervalo', intervalo);
        segundosRestantes = intervalo;
        const timerEl = document.getElementById('refreshTimer');
        if (intervalo === 0) {
            timerEl.innerText = 'Pausado';
        } else {
            timerEl.innerText = segundosRestantes + 's';
        }
    }

    function forcarAtualizacao() {
        sessionStorage.setItem('scroll_pos', window.scrollY);
        window.location.reload();
    }

    // Restaura a posição de rolagem após reload
    window.addEventListener('load', () => {
        const pos = sessionStorage.getItem('scroll_pos');
        if (pos) {
            window.scrollTo(0, parseInt(pos));
            sessionStorage.removeItem('scroll_pos');
        }
    });

    setInterval(() => {
        if (intervalo <= 0) return;
        segundosRestantes--;
        const timerEl = document.getElementById('refreshTimer');
        if (timerEl) timerEl.innerText = segundosRestantes + 's';

        if (segundosRestantes <= 0) {
            forcarAtualizacao();
        }
    }, 1000);
</script>

</body>
</html>
