<?php
include("template/layout/dashboard/topo.php");
?>

<?php
if (!ver_nivel(1) && !ver_nivel(3)) {
    msg('Você não possui permissão para acessar essa área.');
    url(PORTAL_URL . 'view/admin/dashboard');
}

$db = Conexao::getInstance();

// Parâmetros de Filtro
$cargo_filtro = isset($_GET['cargo']) ? strtolower(trim($_GET['cargo'])) : 'governador';
$cargo_filtro = str_replace(['-', '_'], ' ', $cargo_filtro);
$municipio_filtro = isset($_GET['municipio']) ? trim($_GET['municipio']) : 'all'; // 'all' ou código TSE de 5 dígitos (ex: 01392)

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
$sqlMuni = "SELECT DISTINCT id, nome, LPAD(cod_tse, 5, '0') AS cod_tse FROM bsc_municipios WHERE estado_id = 1 ORDER BY nome ASC";
$municipios = $db->query($sqlMuni)->fetchAll(PDO::FETCH_ASSOC);

// Determina Total de Urnas
if ($municipio_filtro === 'all') {
    $nome_localidade = "ESTADO DO ACRE (TODOS OS 22 MUNICÍPIOS)";
    $sqlTotUrnas = "SELECT COUNT(*) FROM locais_votacao_2026";
    $qtd_total_urnas = (int)$db->query($sqlTotUrnas)->fetchColumn();
    if ($qtd_total_urnas === 0) $qtd_total_urnas = 2411;
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

// Urnas Apuradas
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
            $stmtStatsM = $db->prepare("SELECT SECOES_TOTAL as tot, SECOES_APURADAS as apu FROM 2026_apuracao_municipios WHERE COD_MUNICIPIO_TSE = ?");
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

if (!$tem_apuracao_tse) {
    $sqlApuradas = "SELECT COUNT(DISTINCT ZONA, SECAO) FROM 2026_resultados WHERE ANO_ELEICAO = 2026 AND COD_CARGO = ?";
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

// Votos Apurados
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
$soma_votos_validos = 0;
foreach ($votos_apurados_raw as $v) {
    $tipo = strtolower($v['TIPO_VOTO']);
    $cand = (string)$v['NUM_CANDIDATO'];
    $qtd  = (int)$v['total_votos'];
    if ($tipo !== 'branco' && $tipo !== 'nulo' && $cand !== 'BRANCO' && $cand !== 'NULO') {
        $votos_por_candidato[$cand] = ($votos_por_candidato[$cand] ?? 0) + $qtd;
        $soma_votos_validos += $qtd;
    }
}

// Candidatos cadastrados
$sqlCands = "SELECT NR_CANDIDATO, NM_URNA_CANDIDATO, SG_PARTIDO, NM_COLIGACAO, FOTO_URL FROM 2026_candidatos WHERE ANO_ELEICAO = 2026 AND CD_CARGO = ? AND STATUS = 1 ORDER BY NR_CANDIDATO ASC";
$stmtCands = $db->prepare($sqlCands);
$stmtCands->execute([$cargo_info['cd']]);
$candidatos_base = $stmtCands->fetchAll(PDO::FETCH_ASSOC);

$ranking_candidatos = [];
foreach ($candidatos_base as $cb) {
    $num = (string)$cb['NR_CANDIDATO'];
    $vts = $votos_por_candidato[$num] ?? 0;
    $ranking_candidatos[$num] = [
        'numero' => $num,
        'nome' => $cb['NM_URNA_CANDIDATO'],
        'partido' => $cb['SG_PARTIDO'],
        'votos' => $vts,
        'foto' => $cb['FOTO_URL']
    ];
}
foreach ($votos_por_candidato as $num => $vts) {
    if (!isset($ranking_candidatos[$num])) {
        $ranking_candidatos[$num] = [
            'numero' => $num,
            'nome' => "CANDIDATO {$num}",
            'partido' => "PARTIDO",
            'votos' => $vts,
            'foto' => null
        ];
    }
}
uasort($ranking_candidatos, fn($a, $b) => $b['votos'] <=> $a['votos']);
?>

<link rel="stylesheet" href="<?= PORTAL_URL; ?>template/assets/libs/css/apuracao_style.css">
<style>
.filtro-box { background: #f8f9fa; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin-bottom: 20px; }
.candidate-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 10px; background: #fff; }
.candidate-row:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
.bar-progress { height: 12px; border-radius: 6px; background: #e2e8f0; overflow: hidden; margin-top: 6px; }
.bar-fill { height: 100%; background: linear-gradient(90deg, #3b82f6, #06b6d4); border-radius: 6px; }
</style>

<div class="row">
    <div class="container col-xl-10 col-lg-10">
        <div class="row">
            <div class="container col-xl-10 col-lg-10 col-md-12 col-sm-12 col-12">
                <div class="col-xl-10">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="page-header" id="top">
                                <h2 class="pageheader-title">Resultado de Apuração 2026</h2>
                                <div class="page-breadcrumb">
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/admin/dashboard" class="breadcrumb-link">Início</a></li>
                                            <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/apuracao" class="breadcrumb-link">Apuração</a></li>
                                            <li class="breadcrumb-item active" aria-current="page">Resultado Geral 2026</li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <!-- Filtros de Cargo e Município -->
                        <form method="get" class="filtro-box">
                            <div class="row">
                                <div class="col-md-5">
                                    <label><strong>Cargo:</strong></label>
                                    <select name="cargo" class="form-control" onchange="this.form.submit();">
                                        <?php foreach ($cargos_disponiveis as $cKey => $cInfo): ?>
                                            <option value="<?= $cKey; ?>" <?= $cKey === $cargo_filtro ? 'selected' : ''; ?>>
                                                <?= $cInfo['nome']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label><strong>Município (Acre):</strong></label>
                                    <select name="municipio" class="form-control" onchange="this.form.submit();">
                                        <option value="all" <?= $municipio_filtro === 'all' ? 'selected' : ''; ?>>
                                            Todo o Estado do Acre (22 Municípios)
                                        </option>
                                        <?php foreach ($municipios as $m): ?>
                                            <option value="<?= $m['cod_tse']; ?>" <?= $municipio_filtro === $m['cod_tse'] ? 'selected' : ''; ?>>
                                                <?= $m['nome']; ?> (<?= $m['cod_tse']; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2" style="margin-top: 28px;">
                                    <button type="submit" class="btn btn-primary btn-block">Filtrar</button>
                                </div>
                            </div>
                        </form>

                        <div class="content">
                            <div class="row p-2 d-flex">
                                <div class="col-xl-5 col-lg-6 col-md-12">
                                    <h3 class="title">CANDIDATOS A <?= strtoupper($cargo_info['nome']); ?> - <?= $nome_localidade; ?></h3>
                                </div>
                                <div class="col-xl-4 col-lg-6 col-md-12">
                                    <h3 class="title">URNAS APURADAS: <?= number_format($qtd_urnas_apuradas, 0, ',', '.'); ?> de <?= number_format($qtd_total_urnas, 0, ',', '.'); ?> (<?= fdec($porc_urnas_apuradas); ?>%)</h3>
                                </div>
                                <div class="col-xl-3 col-lg-12" style="margin-top: 9px;">
                                    <div class="progress" style="height: 18px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $porc_urnas_apuradas; ?>%" aria-valuenow="<?= $porc_urnas_apuradas; ?>" aria-valuemin="0" aria-valuemax="100">
                                            <?= fdec($porc_urnas_apuradas); ?>%
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div style="margin: 15px 0 25px 0;">
                                <h4>Total de Votos Válidos: <?= number_format($soma_votos_validos, 0, ',', '.'); ?></h4>
                            </div>

                            <div class="candidates-container">
                                <?php
                                $pos = 1;
                                foreach ($ranking_candidatos as $cand):
                                    $vts = (int)$cand['votos'];
                                    $pct = ($soma_votos_validos > 0) ? (($vts / $soma_votos_validos) * 100) : 0;
                                ?>
                                    <div class="candidate-row">
                                        <div style="display:flex; align-items:center; gap:15px; flex:1;">
                                            <div style="font-weight:bold; font-size:18px; width:30px; color:#64748b;"><?= $pos; ?>º</div>
                                            <div style="flex:1;">
                                                <div style="font-size:16px; font-weight:bold;">
                                                    <?= htmlspecialchars($cand['nome']); ?> 
                                                    <span class="badge badge-info" style="font-size:14px;"><?= htmlspecialchars($cand['numero']); ?></span>
                                                    <small style="color:#64748b; font-weight:normal; margin-left:8px;"><?= htmlspecialchars($cand['partido']); ?></small>
                                                </div>
                                                <div class="bar-progress">
                                                    <div class="bar-fill" style="width: <?= number_format($pct, 2, '.', ''); ?>%"></div>
                                                </div>
                                                <small style="color:#64748b; font-weight:600;"><?= number_format($vts, 0, ',', '.'); ?> votos</small>
                                            </div>
                                        </div>
                                        <div style="font-size:24px; font-weight:bold; min-width:85px; text-align:right; font-family:'Oswald', sans-serif;">
                                            <?= fdec($pct); ?>%
                                        </div>
                                    </div>
                                <?php
                                    $pos++;
                                endforeach;
                                ?>
                            </div>

                            <div style="margin-top: 30px; text-align: right;">
                                <a href="<?= PORTAL_URL; ?>2026/resultado-geral-2026.php" class="btn btn-outline-primary" target="_blank">
                                    <i class="fas fa-external-link-alt"></i> Abrir Versão Painel Standalone 2026
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include("template/layout/dashboard/rodape.php");
?>
