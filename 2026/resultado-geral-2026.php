<?php
/**
 * resultado-geral-2026.php
 * Painel de Acompanhamento da Apuração em Tempo Real - Eleições Gerais 2026 (Acre)
 * Suporte a todos os 22 municípios e cargos de Governador, Senador, etc.
 */

$root_dir = dirname(__DIR__);
require_once $root_dir . '/config/geral.php';
require_once $root_dir . '/config/funcoes.php';

$db = Conexao::getInstance();

// Parâmetros de Filtro
$cargo_filtro = isset($_GET['cargo']) ? strtolower(trim($_GET['cargo'])) : 'governador';
$cargo_filtro = str_replace(['-', '_'], ' ', $cargo_filtro);
$municipio_filtro = isset($_GET['municipio']) ? trim($_GET['municipio']) : 'all'; // 'all' ou código TSE de 5 dígitos (ex: 01392)
$turno = isset($_GET['turno']) ? (int)$_GET['turno'] : 1;

// Mapeamento de Cargos para 2026
$cargos_disponiveis = [
    'presidente' => ['cd' => 1, 'nome' => 'Presidente da República'],
    'governador' => ['cd' => 3, 'nome' => 'Governador'],
    'senador' => ['cd' => 5, 'nome' => 'Senador'],
    'deputado federal' => ['cd' => 6, 'nome' => 'Deputado Federal'],
    'deputado estadual' => ['cd' => 7, 'nome' => 'Deputado Estadual']
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
    if ($qtd_total_urnas === 0) $qtd_total_urnas = 2411; // Padrão Acre 2026
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

// Fallback para contagem de seções individuais em 2026_resultados se não houver dados consolidados do TSE
if (!$tem_apuracao_tse) {
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
    $qtd_urnas_apuradas = (int)$stmtApuradas->fetchColumn();
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
        'nome' => $cb['NM_URNA_CANDIDATO'],
        'partido' => $cb['SG_PARTIDO'],
        'coligacao' => $cb['NM_COLIGACAO'],
        'foto' => $cb['FOTO_URL'],
        'votos' => $vts
    ];
}

// Adiciona candidatos que porventura receberam votos no BU mas não estavam no seed inicial
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
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Apuração 2026 - Resultado Geral Acre</title>
    <!-- Google Fonts Inter e Oswald -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-color: #0b1120;
            --card-bg: #1e293b;
            --card-border: #334155;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --primary: #3b82f6;
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
            margin-bottom: 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .topbar-brand i {
            font-size: 28px;
            color: #38bdf8;
        }
        .topbar-brand h1 {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .topbar-brand span {
            font-size: 13px;
            color: var(--text-muted);
            display: block;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .badge-live {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.4);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(0.9); opacity: 0.7; }
            50% { transform: scale(1.3); opacity: 1; }
            100% { transform: scale(0.9); opacity: 0.7; }
        }

        /* Filtros */
        .filter-panel {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 20px;
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
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
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
            background: var(--primary);
            color: #fff;
        }
        .btn-primary:hover {
            background: #2563eb;
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
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #38bdf8;
        }
        .summary-title p {
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 500;
        }

        .urnas-stats {
            text-align: right;
        }
        .urnas-stats .count {
            font-size: 24px;
            font-weight: 800;
            color: #fff;
        }
        .urnas-stats .percent {
            color: #10b981;
            font-weight: 700;
        }

        .progress-container {
            width: 100%;
            height: 16px;
            background: #0f172a;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 16px;
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
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            margin-top: 4px;
        }

        /* Lista de Candidatos (Estilo Resultado-Geral) */
        .candidates-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
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
        .candidate-card.leader {
            border-color: rgba(56, 189, 248, 0.5);
            background: linear-gradient(90deg, rgba(30, 41, 59, 1) 0%, rgba(56, 189, 248, 0.08) 100%);
        }

        .candidate-pos {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-muted);
            width: 28px;
            text-align: center;
        }
        .candidate-card.leader .candidate-pos {
            color: #38bdf8;
        }

        .candidate-avatar {
            width: 68px;
            height: 68px;
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
            color: #38bdf8;
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
            color: #38bdf8;
            font-family: 'Oswald', sans-serif;
            font-size: 15px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 6px;
        }
        .candidate-party {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .candidate-graphic {
            margin-top: 10px;
        }
        .candidate-bar-bg {
            width: 100%;
            height: 10px;
            background: #0f172a;
            border-radius: 5px;
            overflow: hidden;
            margin-bottom: 4px;
        }
        .candidate-bar-fill {
            height: 100%;
            background: var(--primary-gradient);
            border-radius: 5px;
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
            color: #38bdf8;
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
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Topbar -->
    <header class="topbar">
        <div class="topbar-brand">
            <i class="fa-solid fa-square-poll-vertical"></i>
            <div>
                <h1>Apuração Eleições 2026</h1>
                <span>Totalização Paralela em Tempo Real • Estado do Acre</span>
            </div>
        </div>
        <div class="topbar-actions">
            <div class="badge-live">
                <span class="dot"></span>
                <span>AO VIVO</span>
            </div>
            <button onclick="window.location.reload();" class="btn btn-outline" title="Atualizar agora">
                <i class="fa-solid fa-arrows-rotate"></i> Atualizar
            </button>
        </div>
    </header>

    <!-- Filtros -->
    <form method="get" class="filter-panel" id="formFiltro">
        <div class="form-group">
            <label for="cargo"><i class="fa-solid fa-user-tie"></i> Cargo em Disputa</label>
            <select name="cargo" id="cargo" class="form-control" onchange="document.getElementById('formFiltro').submit();">
                <?php foreach ($cargos_disponiveis as $cKey => $cInfo): ?>
                    <option value="<?= $cKey; ?>" <?= $cKey === $cargo_filtro ? 'selected' : ''; ?>>
                        <?= $cInfo['nome']; ?>
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

    <!-- Card de Status das Urnas -->
    <section class="summary-card">
        <div class="summary-header">
            <div class="summary-title">
                <h2>CANDIDATOS A <?= strtoupper($cargo_info['nome']); ?> - <?= $nome_localidade; ?></h2>
                <p>Eleição Geral 2026 • 1º Turno</p>
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
            </div>
            <div class="stat-item">
                <div class="label">Votos em Branco</div>
                <div class="val"><?= number_format($votos_brancos, 0, ',', '.'); ?></div>
            </div>
            <div class="stat-item">
                <div class="label">Votos Nulos</div>
                <div class="val"><?= number_format($votos_nulos, 0, ',', '.'); ?></div>
            </div>
            <div class="stat-item">
                <div class="label">Total Geral Apurado</div>
                <div class="val"><?= number_format($total_geral_votos, 0, ',', '.'); ?></div>
            </div>
        </div>
    </section>

    <!-- Lista de Candidatos -->
    <main class="candidates-list">
        <?php
        $pos = 1;
        if (empty($ranking_candidatos)): ?>
            <div style="text-align: center; padding: 40px; background: var(--card-bg); border-radius: 12px; color: var(--text-muted);">
                <i class="fa-solid fa-inbox" style="font-size: 36px; margin-bottom: 12px; display: block;"></i>
                Nenhum voto ou candidato registrado ainda para este cargo nesta localidade.
            </div>
        <?php else:
            foreach ($ranking_candidatos as $cand):
                $votos_cand = (int)$cand['votos'];
                $pct_cand = ($soma_votos_validos > 0) ? (($votos_cand / $soma_votos_validos) * 100) : 0;
                $is_leader = ($pos === 1 && $votos_cand > 0);
        ?>
            <article class="candidate-card <?= $is_leader ? 'leader' : ''; ?>">
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
                        <?php if ($is_leader): ?>
                            <span style="background: rgba(56, 189, 248, 0.2); color:#38bdf8; font-size:11px; font-weight:700; padding:2px 8px; border-radius:4px;">1º LUGAR</span>
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
                            <strong><?= number_format($votos_cand, 0, ',', '.'); ?></strong> votos
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
            InteliVoto 2026 • Apuração Oficial e Paralela dos 22 Municípios do Acre
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="robo_consumo_continuo_2026.php" class="btn btn-outline" style="border-color:#10b981; color:#34d399;" target="_blank" title="Alimentação 100% contínua e automática no banco de dados">
                <i class="fa-solid fa-robot"></i> Robô Automático (Alimentar Banco)
            </a>
            <a href="teste_simulado_tse_aovivo.php" class="btn btn-outline" style="border-color:#38bdf8; color:#38bdf8;" target="_blank" title="Ver dados ao vivo da transmissão do TSE agora">
                <i class="fa-solid fa-satellite-dish"></i> Simulado TSE ao Vivo
            </a>
            <a href="consumir_simulado_2026.php" class="btn btn-outline" target="_blank" title="Executar rotina do simulador seção a seção">
                <i class="fa-solid fa-terminal"></i> Consumo Seção a Seção
            </a>
            <a href="importar_dados_abertos_2026.php" class="btn btn-outline" target="_blank" title="Importar arquivo CSV">
                <i class="fa-solid fa-file-csv"></i> Importar / Simular Dados
            </a>
            <a href="importar_dados_abertos_2026.php?acao=limpar" class="btn btn-outline" style="border-color:#ef4444; color:#f87171;" onclick="return confirm('Deseja realmente limpar a tabela de resultados 2026?');" title="Zera a tabela de resultados">
                <i class="fa-solid fa-trash-can"></i> Limpar Resultados
            </a>
            <a href="<?= PORTAL_URL; ?>view/admin/dashboard" class="btn btn-primary">
                <i class="fa-solid fa-house"></i> Voltar ao Painel
            </a>
        </div>
    </footer>
</div>

<script>
    // Auto-refresh a cada 30 segundos se a página estiver aberta em dia de eleição
    setTimeout(function() {
        console.log("Recarregando apuração 2026...");
        window.location.reload();
    }, 30000);
</script>

</body>
</html>
