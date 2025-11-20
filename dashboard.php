<?php
// dashboard.php → VERSÃO FINAL DEFINITIVA E INTELIGENTE
// Eleição Municipal = Só Rio Branco | Eleição Estadual = Todo Acre

$ano = (int) ($_GET['ano'] ?? 0);
$cd_cargo = (int) ($_GET['cargo'] ?? 0);
$sq_candidato = $_GET['sq_candidato'] ?? null;

$candidato = null;
$total_votos = 0;
$regionais = [];
$locais = [];
$abrangencia = 'estadual';

// Anos disponíveis
$anos = $db->query("SELECT DISTINCT ano_eleicao FROM candidatos_gerais ORDER BY ano_eleicao DESC")->fetchAll(PDO::FETCH_COLUMN);

// Determina se é eleição municipal (2024, 2020, 2016, etc.)
$is_municipal = in_array($ano, [2024, 2020, 2016, 2012]);

// === CARGOS DISPONÍVEIS NO ANO SELECIONADO ===
$cargos_disponiveis = [];
if ($ano > 0) {
    $sql = "SELECT DISTINCT cd_cargo, ds_cargo FROM candidatos_gerais WHERE ano_eleicao = ?";
    if ($is_municipal) {
        $sql .= " AND cd_cargo IN (11, 12, 13)"; // 11=Prefeito, 12=Vice, 13=Vereador
    }
    $sql .= " ORDER BY CASE cd_cargo WHEN 11 THEN 1 WHEN 12 THEN 2 WHEN 13 THEN 3 WHEN 3 THEN 4 WHEN 5 THEN 5 ELSE 99 END";

    $stmt = $db->prepare($sql);
    $stmt->execute([$ano]);
    $cargos_disponiveis = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
}

// === CANDIDATOS DO CARGO SELECIONADO ===
$candidatos_lista = [];
if ($ano > 0 && $cd_cargo > 0) {
    $sql = "
        SELECT sq_candidato, nm_urna_candidato, nr_candidato, sg_partido, ds_cargo, cd_cargo
        FROM candidatos_gerais
        WHERE ano_eleicao = ? AND cd_cargo = ?
    ";
    // Se for eleição municipal → filtra só Rio Branco
    if ($is_municipal) {
        $sql .= " AND NM_UE = 'RIO BRANCO'";
    }
    $sql .= " ORDER BY nm_urna_candidato";

    $stmt = $db->prepare($sql);
    $stmt->execute([$ano, $cd_cargo]);
    $candidatos_lista = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// === BUSCA CANDIDATO SELECIONADO + VOTOS ===
if ($ano && $cd_cargo && $sq_candidato) {
    $sql = "
        SELECT nm_urna_candidato, nr_candidato, ds_cargo, sg_partido, cd_cargo 
        FROM candidatos_gerais 
        WHERE sq_candidato = ? AND ano_eleicao = ? AND cd_cargo = ?
    ";
    if ($is_municipal) {
        $sql .= " AND NM_UE = 'RIO BRANCO'";
    }
    $sql .= " LIMIT 1";

    $stmt = $db->prepare($sql);
    $stmt->execute([$sq_candidato, $ano, $cd_cargo]);
    $candidato = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($candidato) {
        $is_prefeito = ($candidato['cd_cargo'] == 11);
        $abrangencia = $is_municipal ? 'municipal' : 'estadual';
        $where_municipio = $is_prefeito ? "AND l.municipio_id = 94" : "";

        // Total de votos
        $sql_total = "SELECT COALESCE(SUM(qtd_votos), 0) FROM resultados_eleitorais WHERE ano = ? AND sq_candidato = ?";
        $t = $db->prepare($sql_total);
        $t->execute([$ano, $sq_candidato]);
        $total_votos = $t->fetchColumn();

        // Regionais (só para Prefeito)
        if ($is_prefeito) {
            $reg = $db->prepare("
                SELECT COALESCE(l.regional, 'Área Rural') AS regional, SUM(r.qtd_votos) AS votos
                FROM locais_votacao_mestre l
                JOIN resultados_eleitorais r ON r.nr_local_votacao = l.nr_local_votacao
                WHERE l.municipio_id = 94 AND r.ano = ? AND r.sq_candidato = ?
                GROUP BY l.regional ORDER BY votos DESC
            ");
            $reg->execute([$ano, $sq_candidato]);
            $regionais = $reg->fetchAll(PDO::FETCH_ASSOC);
        }

        // Mapa
        $sql_mapa = "
            SELECT 
                l.nm_local_votacao, l.nm_bairro, COALESCE(l.regional, 'Área Rural') AS regional,
                COALESCE(m.nome, 'Rio Branco') AS municipio_nome,
                l.latitude, l.longitude, COALESCE(SUM(r.qtd_votos), 0) AS votos
            FROM locais_votacao_mestre l
            LEFT JOIN bsc_municipios m ON l.municipio_id = m.id AND m.estado_id = 1
            LEFT JOIN resultados_eleitorais r ON r.nr_local_votacao = l.nr_local_votacao AND r.ano = ? AND r.sq_candidato = ?
            WHERE l.latitude IS NOT NULL AND l.longitude IS NOT NULL AND l.latitude != 0 AND l.longitude != 0
              $where_municipio
            GROUP BY l.id
            HAVING votos > 0
            ORDER BY votos DESC
            LIMIT 10000
        ";
        $mapa = $db->prepare($sql_mapa);
        $mapa->execute([$ano, $sq_candidato]);
        $locais = $mapa->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $candidato ? $candidato['nm_urna_candidato'] . " • " . $candidato['ds_cargo'] . " " . $ano : "Dashboard Eleitoral • Acre" ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <style>
            body {
                margin:0;
                background:#0f172a;
                color:white;
                font-family:system-ui;
            }
            #map {
                height:100vh;
                position:relative;
            }
            .sidebar {
                background:#1e293b;
                padding:25px;
                height:100vh;
                overflow-y:auto;
            }
            .form-select {
                background:#334155;
                border:2px solid #475569;
                color:white;
                border-radius:12px;
                padding:12px;
                font-size:1.1em;
            }
            .overlay {
                position:absolute;
                top:50%;
                left:50%;
                transform:translate(-50%,-50%);
                background:rgba(0,0,0,0.95);
                padding:60px 80px;
                border-radius:30px;
                text-align:center;
                z-index:1000;
                box-shadow:0 0 50px #0066cc;
                border:3px solid #0066cc;
            }
            .foto {
                width:130px;
                height:130px;
                object-fit:cover;
                border-radius:50%;
                border:4px solid #0066cc;
            }
            .card-candidato {
                background:#1e293b;
                border:2px solid #334155;
                border-radius:20px;
            }
        </style>
    </head>
    <body>
        <div class="row g-0">
            <div class="col-md-3 sidebar">
                <h1 class="text-center text-warning mb-3 fw-bold">Eleições Acre</h1>
                <p class="text-center text-info mb-5">Análise espacial inteligente</p>

                <form method="GET" id="formFiltro">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-info">Ano da Eleição</label>
                        <select name="ano" class="form-select" onchange="this.form.submit()" required>
                            <option value="">→ Escolha o ano</option>
                            <?php foreach ($anos as $a): ?>
                                <option value="<?= $a ?>" <?= $a == $ano ? 'selected' : '' ?>><?= $a ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if ($ano): ?>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-info">Cargo</label>
                            <select name="cargo" class="form-select" onchange="this.form.submit()" required>
                                <option value="">→ Escolha o cargo</option>
                                <?php foreach ($cargos_disponiveis as $id => $nome): ?>
                                    <option value="<?= $id ?>" <?= $id == $cd_cargo ? 'selected' : '' ?>><?= $nome ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <?php if ($ano && $cd_cargo): ?>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-info">Candidato</label>
                            <select name="sq_candidato" class="form-select" onchange="this.form.submit()" required>
                                <option value="">→ Escolha o candidato</option>
                                <?php foreach ($candidatos_lista as $c): ?>
                                    <option value="<?= $c['sq_candidato'] ?>" <?= $c['sq_candidato'] == $sq_candidato ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['nm_urna_candidato']) ?> (<?= $c['nr_candidato'] ?>) - <?= $c['sg_partido'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </form>

                <?php if ($candidato): ?>
                    <?php
                    $pasta = "template/assets/images/candidatos/{$ano}";
                    $foto_base = "{$pasta}/FAC{$sq_candidato}_div";
                    $foto_candidato = "template/assets/images/candidatos/sem_foto.jpg";
                    foreach (['.jpg', '.jpeg', '.png', '.JPG', '.JPEG'] as $ext) {
                        if (file_exists("{$foto_base}{$ext}")) {
                            $foto_candidato = "{$foto_base}{$ext}";
                            break;
                        }
                    }
                    ?>
                    <div class="card-candidato text-center p-4 mt-4">
                        <img src="<?= $foto_candidato ?>" class="foto mb-3" alt="<?= $candidato['nm_urna_candidato'] ?>">
                        <h2 class="fw-bold text-warning"><?= htmlspecialchars($candidato['nm_urna_candidato']) ?></h2>
                        <p class="text-info fs-5"><?= $candidato['ds_cargo'] ?> • <?= $ano ?></p>
                        <h1 class="display-2 text-success fw-bold"><?= number_format($total_votos) ?></h1>
                        <p class="fs-4 text-muted">
                            <b style="color: white;">votos no</b> <strong class="text-info">
                                <?= $abrangencia === 'municipal' ? 'Rio Branco' : 'Estado do Acre' ?>
                            </strong>
                        </p>
                        <p class="text-warning fs-5"><strong><?= $candidato['nr_candidato'] ?></strong> • <?= $candidato['sg_partido'] ?></p>
                    </div>

                    <?php if ($abrangencia === 'municipal' && $candidato['cd_cargo'] == 11): ?>
                        <hr class="my-5 border-secondary">
                        <h4 class="text-info">Votos por Regional (Rio Branco)</h4>
                        <div class="table-responsive">
                            <table class="table table-dark table-striped">
                                <?php
                                foreach ($regionais as $r):
                                    $perc = $total_votos > 0 ? round($r['votos'] * 100 / $total_votos, 1) : 0;
                                    ?>
                                    <tr>
                                        <td><?= $r['regional'] ?></td>
                                        <td class="text-end text-warning"><?= number_format($r['votos']) ?></td>
                                        <td class="text-end text-success"><strong><?= $perc ?>%</strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center mt-5">
                        <h3 class="text-info">Selecione ano → cargo → candidato</h3>
                        <p class="text-muted">Sistema inteligente: só mostra candidatos da cidade/estado correto</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-md-9 p-0">
                <div id="map">
                    <?php if (!$candidato): ?>
                        <div class="overlay">
                            <h1 class="text-info fw-bold">DASHBOARD ELEITORAL</h1>
                            <h2 class="text-light">Estado do Acre</h2>
                            <p class="fs-4 text-light mt-4">Ano → Cargo → Candidato</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
                                const map = L.map('map').setView([-9.974, -67.807], <?= $abrangencia === 'municipal' ? '12' : '12' ?>);
                                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                    attribution: '© Ildeniro Lima | Eleições Acre'
                                }).addTo(map);

<?php if ($candidato && !empty($locais)): ?>
                                    const dados = <?= json_encode($locais) ?>;
                                    const nome = "<?= addslashes($candidato['nm_urna_candidato']) ?>";

                                    function cor(v) {
                                        return v > 5000 ? '#800026' :
                                                v > 3000 ? '#BD0026' :
                                                v > 1500 ? '#E31A1C' :
                                                v > 800 ? '#FC4E2A' :
                                                v > 400 ? '#FD8D3C' :
                                                v > 200 ? '#FEB24C' :
                                                v > 50 ? '#FED976' : '#FFEDA0';
                                    }

                                    dados.forEach(l => {
                                        const v = parseInt(l.votos);
                                        const raio = Math.max(8, Math.sqrt(v) * <?= $abrangencia === 'estadual' ? '1.0' : '1.0' ?>);

                                        L.circleMarker([l.latitude, l.longitude], {
                                            radius: raio,
                                            fillColor: cor(v),
                                            color: "#000",
                                            weight: 2,
                                            fillOpacity: 0.9
                                        }).bindPopup(`
                                <b>${l.nm_local_votacao}</b><br>
                                <small>${l.municipio_nome} • ${l.regional}</small><hr>
                                <strong style="color:#0066cc">${nome}</strong><br>
                                <strong style="font-size:1.4em">${v.toLocaleString('pt-BR')} votos</strong>
                            `).addTo(map);
                                    });
<?php endif; ?>
        </script>
    </body>
</html>