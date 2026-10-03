<?php
/**
 * teste_simulado_tse_aovivo.php
 * Conector e Testador ao Vivo do Simulado Oficial do TSE (Eleições 2026 - Acre)
 * Captura dados da CDN do TSE (JWS/JSON) transmitidos agora pelo Tribunal
 */

$root_dir = dirname(__DIR__);
require_once $root_dir . '/config/geral.php';

$db = Conexao::getInstance();

// Parâmetros identificados na transmissão ao vivo do TSE
$eleicao_id  = isset($_GET['eleicao']) ? $_GET['eleicao'] : '21272'; // 21272 = Eleição Estadual Acre 2026 Simulado
$cargo_code  = isset($_GET['cargo']) ? sprintf("%04d", (int)$_GET['cargo']) : '0003';       
// Presidente (0001) é Eleição Federal 21270; os demais cargos estaduais são 21272
$eleicao_id  = ($cargo_code === '0001') ? '21270' : (isset($_GET['eleicao']) ? $_GET['eleicao'] : '21272');
$uf          = 'ac';

$cargos_map = [
    '0001' => 'Presidente da República',
    '0003' => 'Governador',
    '0005' => 'Senador',
    '0006' => 'Deputado Federal',
    '0007' => 'Deputado Estadual'
];

// URL oficial da CDN do TSE Simulado para o Acre
$url_tse = "https://resultados-sim.tse.jus.br/simulado/simulado2026/ele2026/{$eleicao_id}/dados/{$uf}/{$uf}-c{$cargo_code}-e0{$eleicao_id}-u.jws";

// Ação de ingestão para o banco local
$gravar_no_banco = isset($_POST['gravar']) || isset($_GET['gravar']);

$ch = curl_init($url_tse);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) EleitoralBot/2026');
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$payload_data = null;
$erro = null;

if ($http_code == 200 && $response) {
    // Decodifica JWS (formato Header.Payload.Signature)
    $parts = explode('.', $response);
    if (count($parts) >= 2) {
        $payload_b64 = strtr($parts[1], '-_', '+/');
        $json_str = base64_decode($payload_b64);
        $payload_data = json_decode($json_str, true);
    } else {
        $payload_data = json_decode($response, true);
    }
} else {
    $erro = "Status HTTP: {$http_code}. Não foi possível conectar ao endpoint: {$url_tse}";
}

// Se solicitada a gravação no banco de 2026
$votos_gravados = 0;
$municipios_gravados = 0;
if ($gravar_no_banco) {
    // Garante a tabela de estatísticas dos municípios
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
        $nome = $m['nome'];

        foreach ($cargosConfig as $cfg) {
            $url = "https://resultados-sim.tse.jus.br/simulado/simulado2026/ele2026/{$cfg['eleicao']}/dados/ac/ac{$cod}-c{$cfg['code']}-e0{$cfg['eleicao']}-u.jws";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 8);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
            curl_multi_add_handle($mh, $ch);
            $curl_handles[] = [
                'handle' => $ch,
                'cod_muni' => $cod,
                'nome_muni' => $nome,
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

    $stmt = $db->prepare("
        INSERT INTO 2026_resultados 
            (ANO_ELEICAO, COD_ELEICAO, TURNO, UF, COD_MUNICIPIO_TSE, ZONA, SECAO, LOCAL_VOTACAO, COD_CARGO, NUM_CANDIDATO, QTD_VOTOS, TIPO_VOTO, PARTIDO, DATA_CADASTRO)
        VALUES 
            (2026, ?, 1, 'AC', ?, 1, 9999, 'TOTALIZADO MUNICIPIO TSE', ?, ?, ?, 'nominal', ?, NOW())
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

    $db->beginTransaction();
    $muni_contados = [];

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
                    if ($item['cargo_nome'] === 'governador') {
                        $sec_tot = (int)($payload['s']['ts'] ?? 0);
                        $sec_apu = (int)($payload['s']['st'] ?? 0);
                        $porc = str_replace(',', '.', (string)($payload['s']['pst'] ?? '0.00'));
                        $stmtStats->execute([$item['cod_muni'], $item['nome_muni'], $sec_tot, $sec_apu, (float)$porc]);
                        $muni_contados[$item['cod_muni']] = true;
                    }

                    if (isset($payload['carg'][0]['agr'])) {
                        foreach ($payload['carg'][0]['agr'] as $colig) {
                            foreach ($colig['par'] as $part) {
                                $sg_partido = $part['sg'] ?? 'PARTIDO';
                                $nm_partido = $part['nm'] ?? $sg_partido;
                                foreach ($part['cand'] as $cand) {
                                    $num = $cand['n'] ?? '0';
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
                                        $sg_partido,
                                        $nm_partido
                                    ]);

                                    $stmt->execute([
                                        $item['eleicao'],
                                        $item['cod_muni'],
                                        $item['cargo_nome'],
                                        (string)$num,
                                        $votos,
                                        $sg_partido
                                    ]);
                                    $votos_gravados++;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    curl_multi_close($mh);
    $db->commit();
    $municipios_gravados = count($muni_contados);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Teste ao Vivo - Simulado TSE 2026 (Acre)</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Oswald:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #0b1120; color: #f8fafc; padding: 25px; }
        .container { max-width: 1000px; margin: 0 auto; background: #1e293b; border-radius: 14px; padding: 25px; border: 1px solid #334155; }
        h1 { font-family: 'Oswald', sans-serif; color: #38bdf8; display: flex; align-items: center; gap: 12px; margin-top: 0; }
        .live-tag { background: #10b981; color: #fff; font-size: 12px; padding: 4px 10px; border-radius: 20px; font-weight: bold; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin: 20px 0; }
        .stat-card { background: #0f172a; padding: 14px 18px; border-radius: 8px; border: 1px solid #334155; }
        .stat-card small { color: #94a3b8; font-size: 11px; text-transform: uppercase; font-weight: 600; }
        .stat-card p { font-size: 22px; font-weight: 800; color: #fff; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px 14px; text-align: left; border-bottom: 1px solid #334155; font-size: 14px; }
        th { background: #0f172a; color: #38bdf8; font-family: 'Oswald', sans-serif; }
        tr:hover { background: rgba(255,255,255,0.02); }
        .btn { padding: 10px 18px; border-radius: 8px; font-weight: bold; text-decoration: none; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; font-size: 14px; }
        .btn-green { background: #10b981; color: #fff; }
        .btn-green:hover { background: #059669; }
        .btn-blue { background: #3b82f6; color: #fff; }
        .btn-blue:hover { background: #2563eb; }
        .progress-bar-bg { width: 100%; height: 14px; background: #0f172a; border-radius: 7px; overflow: hidden; margin: 15px 0; border: 1px solid #334155; }
        .progress-bar-fill { height: 100%; background: linear-gradient(90deg, #10b981, #38bdf8); }
        .alert-success { background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; color: #a7f3d0; padding: 12px 16px; border-radius: 8px; margin-bottom: 15px; }
    </style>
</head>
<body>
<div class="container">
    <h1>
        <i class="fa-solid fa-satellite-dish"></i> 
        Simulado Oficial TSE 2026 - Conexão ao Vivo
        <span class="live-tag">ON-AIR AGORA</span>
    </h1>
    <p style="color:#94a3b8;">
        Transmissão direta da CDN do TSE (<code>resultados-sim.tse.jus.br</code>) coletada em tempo real para o <strong>Estado do Acre</strong>.
    </p>

    <!-- Seletor de Cargo -->
    <div style="display:flex; gap:10px; margin: 18px 0; flex-wrap:wrap;">
        <?php foreach ($cargos_map as $cId => $cLabel): ?>
            <a href="?cargo=<?= $cId; ?>" class="btn" style="border:1px solid #334155; color:#fff; <?= $cargo_code == $cId ? 'background:#0284c7; font-weight:800;' : 'background:#0f172a;'; ?>">
                <?= $cLabel; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if ($votos_gravados > 0): ?>
        <div class="alert-success">
            ✔ <strong><?= $votos_gravados; ?></strong> registros de candidatos gravados com sucesso distribuídos por todos os <strong><?= $municipios_gravados; ?></strong> municípios do Acre!
        </div>
    <?php endif; ?>

    <?php if ($payload_data): ?>
        <div class="stats-grid">
            <div class="stat-card">
                <small>Eleição Simulado</small>
                <p><?= htmlspecialchars($payload_data['ele'] ?? '21272'); ?></p>
            </div>
            <div class="stat-card">
                <small>Geração TSE</small>
                <p><?= htmlspecialchars($payload_data['hg'] ?? ''); ?> <span style="font-size:13px;color:#94a3b8;"><?= htmlspecialchars($payload_data['dg'] ?? ''); ?></span></p>
            </div>
            <div class="stat-card">
                <small>Urnas Apuradas (Simulado)</small>
                <p><?= htmlspecialchars($payload_data['s']['pst'] ?? '0'); ?>%</p>
            </div>
            <div class="stat-card">
                <small>Votos Válidos Apurados</small>
                <p><?= number_format((int)($payload_data['v']['vv'] ?? 0), 0, ',', '.'); ?></p>
            </div>
        </div>

        <div class="progress-bar-bg">
            <div class="progress-bar-fill" style="width: <?= str_replace(',', '.', $payload_data['s']['pst'] ?? '0'); ?>%"></div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:20px;">
            <h3>📊 Candidatos Transmitidos no Simulado TSE (<?= $cargos_map[$cargo_code] ?? 'Cargo'; ?> - AC):</h3>
            <form method="post" style="display:inline;">
                <input type="hidden" name="gravar" value="1">
                <button type="submit" class="btn btn-green">
                    <i class="fa-solid fa-cloud-arrow-down"></i> Gravar Todos os 5 Cargos na Base Local
                </button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Nome do Candidato</th>
                    <th>Partido</th>
                    <th>Votos Apurados</th>
                    <th>% Votos</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($payload_data['carg'][0]['agr'])) {
                    foreach ($payload_data['carg'][0]['agr'] as $colig) {
                        foreach ($colig['par'] as $part) {
                            foreach ($part['cand'] as $cand) {
                                ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($cand['n']); ?></strong></td>
                                    <td><?= htmlspecialchars($cand['nmu']); ?></td>
                                    <td><?= htmlspecialchars($part['sg']); ?></td>
                                    <td><strong><?= number_format((int)$cand['vap'], 0, ',', '.'); ?></strong></td>
                                    <td style="color:#38bdf8; font-weight:bold;"><?= htmlspecialchars($cand['pvap']); ?>%</td>
                                </tr>
                                <?php
                            }
                        }
                    }
                }
                ?>
            </tbody>
        </table>

        <div style="margin-top: 25px; display:flex; gap:12px;">
            <a href="resultado-geral-2026.php" class="btn btn-blue">
                <i class="fa-solid fa-chart-column"></i> Ver no Painel de Apuração 2026
            </a>
            <button onclick="window.location.reload();" class="btn btn-blue" style="background:#475569;">
                <i class="fa-solid fa-rotate"></i> Atualizar Conexão TSE
            </button>
        </div>

    <?php elseif ($erro): ?>
        <div style="background:rgba(239,68,68,0.2); border:1px solid #ef4444; color:#fca5a5; padding:15px; border-radius:8px;">
            <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($erro); ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
