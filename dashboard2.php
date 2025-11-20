<?php
// dashboard2.php → VERSÃO INDESTRUTÍVEL + CANDIDATOS REAIS + NOME + PARTIDO + FOTO
$ano1 = (int) ($_GET['ano1'] ?? 2020);
$ano2 = (int) ($_GET['ano2'] ?? 2024);
$sq_candidato = $_GET['sq_candidato'] ?? null;

// Anos disponíveis (a partir de 2012)
$anos = $db->query("SELECT DISTINCT ano FROM resultados_eleitorais WHERE ano >= 2012 ORDER BY ano DESC")->fetchAll(PDO::FETCH_COLUMN);

// Candidatos que disputaram nos dois anos (ou pelo menos em um deles)
$stmt_c = $db->prepare("
    SELECT DISTINCT c.sq_candidato, c.nm_urna_candidato, c.nr_candidato, c.sg_partido, c.ds_cargo, c.ano_eleicao
    FROM candidatos_gerais c
    INNER JOIN resultados_eleitorais r ON c.sq_candidato = r.sq_candidato
    WHERE c.ano_eleicao IN (?, ?)
      AND c.cd_cargo IN (11, 3) -- 11 = Prefeito, 3 = Governador
    ORDER BY c.nm_urna_candidato
");
$stmt_c->execute([$ano1, $ano2]);
$candidatos_disponiveis = $stmt_c->fetchAll(PDO::FETCH_ASSOC);

// Se não tiver sq_candidato selecionado, pega o primeiro da lista
if (!$sq_candidato && !empty($candidatos_disponiveis)) {
    $sq_candidato = $candidatos_disponiveis[0]['sq_candidato'];
}

// Busca informações completas do candidato selecionado
$candidato_info = null;
if ($sq_candidato) {
    $stmt_info = $db->prepare("
        SELECT ano_eleicao, sq_candidato, nm_urna_candidato, nr_candidato, sg_partido, ds_cargo 
        FROM candidatos_gerais 
        WHERE sq_candidato = ? 
        LIMIT 1
    ");
    $stmt_info->execute([$sq_candidato]);
    $candidato_info = $stmt_info->fetch(PDO::FETCH_ASSOC);
}

// === CONSULTA 100% CORRIGIDA (sem erro de referência no HAVING) ===
$sql = "
    SELECT
        l.nr_local_votacao,
        l.nm_local_votacao,
        l.nm_bairro,
        COALESCE(l.regional, 'Área Rural') AS regional,
        COALESCE(m.nome, 'Rio Branco') AS municipio_nome,
        l.latitude,
        l.longitude,
        COALESCE(SUM(CASE WHEN r.ano = ? AND r.sq_candidato = ? THEN r.qtd_votos ELSE 0 END), 0) AS votos_ano1,
        COALESCE(SUM(CASE WHEN r.ano = ? AND r.sq_candidato = ? THEN r.qtd_votos ELSE 0 END), 0) AS votos_ano2
    FROM locais_votacao_mestre l
    LEFT JOIN bsc_municipios m ON l.municipio_id = m.id AND m.estado_id = 1
    LEFT JOIN resultados_eleitorais r 
        ON r.nr_local_votacao = l.nr_local_votacao 
        AND r.ano IN (?, ?)
        AND r.sq_candidato = ?
    WHERE l.municipio_id = 94
      AND l.latitude IS NOT NULL 
      AND l.longitude IS NOT NULL
      AND l.latitude != 0 
      AND l.longitude != 0
    GROUP BY l.id, l.nr_local_votacao, l.nm_local_votacao, l.nm_bairro, l.regional, l.latitude, l.longitude
    HAVING
        COALESCE(SUM(CASE WHEN r.ano = ? AND r.sq_candidato = ? THEN r.qtd_votos ELSE 0 END), 0) > 0
        OR
        COALESCE(SUM(CASE WHEN r.ano = ? AND r.sq_candidato = ? THEN r.qtd_votos ELSE 0 END), 0) > 0
    ORDER BY
        (COALESCE(SUM(CASE WHEN r.ano = ? AND r.sq_candidato = ? THEN r.qtd_votos ELSE 0 END), 0) -
         COALESCE(SUM(CASE WHEN r.ano = ? AND r.sq_candidato = ? THEN r.qtd_votos ELSE 0 END), 0)) DESC
    LIMIT 5000
";

$stmt = $db->prepare($sql);
$stmt->execute([
    $ano1, $sq_candidato, // SELECT votos_ano1
    $ano2, $sq_candidato, // SELECT votos_ano2
    $ano1, $ano2, $sq_candidato, // JOIN anos + sq_candidato
    $ano1, $sq_candidato, // HAVING ano1
    $ano2, $sq_candidato, // HAVING ano2
    $ano2, $sq_candidato, // ORDER BY ano2
    $ano1, $sq_candidato   // ORDER BY ano1
]);
$locais = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total1 = array_sum(array_column($locais, 'votos_ano1'));
$total2 = array_sum(array_column($locais, 'votos_ano2'));
$diff = $total2 - $total1;
$variacao = $total1 > 0 ? round(($total2 / $total1 - 1) * 100, 1) : ($total2 > 0 ? 100 : 0);
?>

<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Comparativo: <?= $candidato_info['nm_urna_candidato'] ?? 'Candidato' ?> (<?= $ano1 ?> → <?= $ano2 ?>)</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <style>
            body {
                margin:0;
                font-family:system-ui;
                background:#0f172a;
                color:white;
            }
            #map {
                height:100vh;
            }
            .sidebar {
                background:#1e293b;
                padding:20px;
                height:100vh;
                overflow-y:auto;
            }
            .card-candidato {
                background:#0f172a;
                border:2px solid #334155;
                border-radius:20px;
                overflow:hidden;
            }
            .foto-candidato {
                width:120px;
                height:120px;
                object-fit:cover;
                border-radius:50%;
                border:4px solid #0066cc;
            }
            .diff-positive {
                color:#22c55e;
            }
            .diff-negative {
                color:#ef4444;
            }
            .form-select {
                background:#334155;
                border:2px solid #475569;
                color:white;
                border-radius:12px;
            }
            .top-bar {
                background:#1e293b;
                padding:15px;
                border-bottom:3px solid #0066cc;
            }
            .variacao {
                font-size:2.5rem;
                font-weight:bold;
            }
        </style>
    </head>
    <body>

        <div class="row g-0">
            <!-- SIDEBAR -->
            <div class="col-lg-4 sidebar">
                <div class="top-bar mb-4">
                    <h2 class="fw-bold text-warning mb-1">Comparativo Eleitoral</h2>
                    <p class="text-info mb-0">Variação de votos por local de votação</p>
                </div>

                <form method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-4">
                            <label class="text-info small fw-bold">Ano Inicial</label>
                            <select name="ano1" class="form-select form-select-sm" onchange="this.form.submit()">
                                <?php foreach ($anos as $a): ?>
                                    <option value="<?= $a ?>" <?= $a == $ano1 ? 'selected' : '' ?>><?= $a ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-4">
                            <label class="text-info small fw-bold">Ano Final</label>
                            <select name="ano2" class="form-select form-select-sm" onchange="this.form.submit()">
                                <?php foreach ($anos as $a): ?>
                                    <option value="<?= $a ?>" <?= $a == $ano2 ? 'selected' : '' ?>><?= $a ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col">
                            <label class="text-info small fw-bold">Candidato</label>
                            <select name="sq_candidato" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">→ Escolha o candidato</option>
                                <?php
                                foreach ($candidatos_disponiveis as $c):
                                    $label = "{$c['nm_urna_candidato']} ({$c['nr_candidato']}) - {$c['ds_cargo']} {$c['sg_partido']}";
                                    ?>
                                    <option value="<?= $c['sq_candidato'] ?>" <?= $c['sq_candidato'] == $sq_candidato ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </form>

                <?php if ($candidato_info && $sq_candidato): ?>
                    <div class="card-candidato text-center p-4 mb-4">

                        <?php
                        $foto_candidato = "template/assets/images/candidatos/" . $candidato_info['ano_eleicao'] . "/FAC" . $candidato_info['sq_candidato'] . "_div.jpg";

                        if (!file_exists($foto_candidato)) {
                            $foto_candidato = "template/assets/images/candidatos/" . $candidato_info['ano_eleicao'] . "/FAC" . $candidato_info['sq_candidato'] . "_div.jpeg";

                            if (!file_exists($foto_candidato)) {
                                $foto_candidato = "template/assets/images/candidatos/sem_foto.jpg";
                            }
                        }
                        ?>

                        <?php if (!empty($foto_candidato)): ?>
                            <img src="<?= htmlspecialchars($foto_candidato) ?>" class="foto-candidato mb-3" alt="<?= $candidato_info['nm_urna_candidato'] ?>">
                        <?php else: ?>

                            <div class="bg-secondary rounded-circle d-inline-block mb-3" style="width:120px;height:120px;"></div>
                        <?php endif; ?>
                        <h3 class="fw-bold text-warning"><?= htmlspecialchars($candidato_info['nm_urna_candidato']) ?></h3>
                        <p class="text-info mb-1">
                            <strong><?= $candidato_info['nr_candidato'] ?></strong> • <?= $candidato_info['sg_partido'] ?>
                        </p>
                        <p class="text-muted"><?= $candidato_info['ds_cargo'] ?> • <?= $ano1 ?> → <?= $ano2 ?></p>

                        <div class="mt-4 p-4 bg-black bg-opacity-30 rounded">
                            <h2 class="variacao <?= $diff >= 0 ? 'diff-positive' : 'diff-negative' ?>">
                                <?= $diff >= 0 ? '+' : '' ?><?= number_format($diff) ?>
                            </h2>
                            <p class="fs-4 mb-0 <?= $variacao >= 0 ? 'text-success' : 'text-danger' ?>">
                                <strong><?= $variacao >= 0 ? '+' : '' ?><?= $variacao ?>%</strong>
                            </p>
                            <small class="text-muted d-block">
                                <?= number_format($total1) ?> → <?= number_format($total2) ?> votos
                            </small>
                        </div>
                    </div>

                    <h5 class="text-info fw-bold mb-3">Maiores variações</h5>
                    <?php
                    foreach (array_slice($locais, 0, 15) as $l):
                        $d = $l['votos_ano2'] - $l['votos_ano1'];
                        $cor = $d >= 0 ? 'border-success' : 'border-danger';
                        $texto_cor = $d >= 0 ? 'text-success' : 'text-danger';
                        ?>
                        <div class="border-start <?= $cor ?> border-5 ps-3 py-2 mb-2 bg-dark bg-opacity-50 rounded">
                            <strong><?= htmlspecialchars($l['nm_local_votacao']) ?></strong><br>
                            <small class="text-muted"><?= $l['nm_bairro'] ?> • <?= $l['regional'] ?></small><br>
                            <span class="small">
                                <?= $ano1 ?>: <?= number_format($l['votos_ano1']) ?> → 
                                <?= $ano2 ?>: <strong class="<?= $texto_cor ?>"><?= number_format($l['votos_ano2']) ?></strong>
                                <span class="<?= $texto_cor ?>">(<?= $d >= 0 ? '+' : '' ?><?= number_format($d) ?>)</span>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center mt-5">
                        <h3 class="text-info">Selecione um candidato acima</h3>
                        <p class="text-muted">Para ver a comparação de votos entre <?= $ano1 ?> e <?= $ano2 ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- MAPA -->
            <div class="col-lg-8 p-0">
                <div id="map"></div>
            </div>
        </div>

        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
                                const map = L.map('map').setView([-9.974, -67.824], 12);
                                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                    attribution: '© Ildeniro Lima | Comparativo Eleitoral'
                                }).addTo(map);

<?php if ($candidato_info && !empty($locais)): ?>
                                    const dados = <?= json_encode($locais) ?>;
                                    const nome = "<?= addslashes($candidato_info['nm_urna_candidato']) ?>";

                                    dados.forEach(l => {
                                        const diff = l.votos_ano2 - l.votos_ano1;
                                        const color = diff >= 200 ? '#166534' :
                                                diff >= 50 ? '#22c55e' :
                                                diff > 0 ? '#86efac' :
                                                diff <= -200 ? '#991b1b' :
                                                diff <= -50 ? '#ef4444' : '#fb923c';
                                        const radius = Math.max(Math.abs(diff) / 10, 20);

                                        L.circleMarker([l.latitude, l.longitude], {
                                            radius, fillColor: color, color: "#000", weight: 1.5, fillOpacity: 0.9
                                        }).bindPopup(`
                                                    <div style="font-family:system-ui; min-width:240px;">
                                                        <b style="font-size:1.1em">${l.nm_local_votacao}</b><br>
                                                        <small>${l.nm_bairro} • ${l.regional}</small><hr>
                                                        <strong>${nome}</strong><br>
                                                        <b><?= $ano1 ?>:</b> ${Number(l.votos_ano1).toLocaleString('pt-BR')} votos<br>
                                                        <b><?= $ano2 ?>:</b> ${Number(l.votos_ano2).toLocaleString('pt-BR')} votos<br>
                                                        <strong style="color:${diff >= 0 ? '#22c55e' : '#ef4444'}; font-size:1.2em">
                                                            ${diff >= 0 ? '+' : ''}${diff.toLocaleString('pt-BR')} votos
                                                        </strong>
                                                    </div>
                                                `).addTo(map);
                                    });
<?php endif; ?>
        </script>
    </body>
</html>