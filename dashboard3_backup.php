<?php
// dashboard3.php → CANDIDATO PRIMEIRO → ANOS QUE ELE DISPUTOU → MAPA COMPLETO

$cpf = $_GET['cpf'] ?? null;
$titulo = $_GET['titulo'] ?? null;
$ano = (int) ($_GET['ano'] ?? 0);
$sq_candidato = $_GET['sq'] ?? null;

$candidato = null;
$anos_disponiveis = [];
$locais = [];
$total_votos = 0;

// 1. BUSCA TODOS OS CANDIDATOS ÚNICOS (por CPF ou Título)
$candidatos = $db->query("
    SELECT DISTINCT nr_cpf_candidato AS cpf, nr_titulo_eleitoral_candidato AS titulo, nm_urna_candidato
    FROM candidatos_detalhados
    GROUP BY sq_candidato, nr_cpf_candidato, nr_titulo_eleitoral_candidato 
    ORDER BY nm_urna_candidato
")->fetchAll(PDO::FETCH_ASSOC);

// 2. SE ESCOLHEU CANDIDATO → BUSCA ANOS QUE ELE DISPUTOU
if ($cpf || $titulo) {
    $where = $cpf ? "nr_cpf_candidato = ?" : "nr_titulo_eleitoral_candidato = ?";
    $param = $cpf ?: $titulo;

    $stmt = $db->prepare("
        SELECT DISTINCT ano_eleicao, sq_candidato, nm_urna_candidato, ds_cargo, sg_partido
        FROM candidatos_detalhados
        WHERE $where
        ORDER BY ano_eleicao DESC
    ");
    $stmt->execute([$param]);
    $anos_disponiveis = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Se já escolheu o ano → carrega dados completos
    if ($ano && count($anos_disponiveis) > 0) {

        $stmt = $db->prepare("
            SELECT * FROM candidatos_detalhados
            WHERE ($where) AND ano_eleicao = ?
            LIMIT 1
        ");
        $stmt->execute([$param, $ano]);
        $candidato = $stmt->fetch(PDO::FETCH_ASSOC);

        $sq_candidato = isset($candidato['sq_candidato']) ? $candidato['sq_candidato'] : '';
        $nr_candidato = isset($candidato['nr_candidato']) ? $candidato['nr_candidato'] : '';
        $cd_cargo = isset($candidato['cd_cargo']) ? $candidato['cd_cargo'] : '';

        if ($ano == 2012 || $ano == 2014) {//Não existia SQ_CANDIDATO
            // Total de votos
            $t = $db->prepare("SELECT COALESCE(SUM(qtd_votos),0) FROM resultados_eleitorais WHERE ano = ? AND cod_cargo = ? AND num_candidato = ?");
            $t->execute([$ano, $cd_cargo, $nr_candidato]);
            $total_votos = $t->fetchColumn();

            // Mapa
            $mapa = $db->prepare("
            SELECT l.nm_local_votacao, l.nm_bairro, COALESCE(l.regional,'Área Rural') regional,
                   l.latitude, l.longitude, COALESCE(SUM(r.qtd_votos),0) votos
            FROM locais_votacao_mestre l
            LEFT JOIN resultados_eleitorais r ON r.nr_local_votacao = l.nr_local_votacao AND r.ano = ? AND r.cod_cargo = ? AND r.num_candidato = ?
            WHERE l.latitude IS NOT NULL AND l.longitude IS NOT NULL
            GROUP BY l.id HAVING votos > 0 ORDER BY votos DESC
        ");
            $mapa->execute([$ano, $cd_cargo, $nr_candidato]);
            $locais = $mapa->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Total de votos
            $t = $db->prepare("SELECT COALESCE(SUM(qtd_votos),0) FROM resultados_eleitorais WHERE ano = ? AND sq_candidato = ?");
            $t->execute([$ano, $sq_candidato]);
            $total_votos = $t->fetchColumn();

            // Mapa
            $mapa = $db->prepare("
            SELECT l.nm_local_votacao, l.nm_bairro, COALESCE(l.regional,'Área Rural') regional,
                   l.latitude, l.longitude, COALESCE(SUM(r.qtd_votos),0) votos
            FROM locais_votacao_mestre l
            LEFT JOIN resultados_eleitorais r ON r.nr_local_votacao = l.nr_local_votacao AND r.ano = ? AND r.sq_candidato = ?
            WHERE l.latitude IS NOT NULL AND l.longitude IS NOT NULL
            GROUP BY l.id HAVING votos > 0 ORDER BY votos DESC
        ");
            $mapa->execute([$ano, $sq_candidato]);
            $locais = $mapa->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Histórico por Candidato • Eleições Acre</title>
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
            }
            .sidebar {
                background:#1e293b;
                padding:30px;
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
            .foto {
                width:140px;
                height:140px;
                object-fit:cover;
                border-radius:50%;
                border:5px solid #0066cc;
            }
            .overlay {
                position:absolute;
                top:50%;
                left:50%;
                transform:translate(-50%,-50%);
                background:rgba(0,0,0,0.95);
                padding:70px;
                border-radius:30px;
                text-align:center;
                z-index:1000;
                border:4px solid #0066cc;
                box-shadow:0 0 60px #0066cc;
            }
        </style>
    </head>
    <body>
        <div class="row g-0">
            <div class="col-md-3 sidebar">
                <h1 class="text-center text-warning fw-bold">Histórico do Candidato(a)</h1>
                <p class="text-center text-info">Escolha o candidato(a) → Veja todos os anos que disputou</p>

                <form method="GET">
                    <div class="mb-4">
                        <label class="form-label text-info fw-bold">1. Escolha o Candidato(a)</label>
                        <select name="cpf" class="form-select" onchange="this.form.submit()">
                            <option value="">→ Digite ou escolha...</option>
                            <?php
                            foreach ($candidatos as $c):
                                $label = $c['nm_urna_candidato'];
                                ?>
                                <option value="<?= $c['cpf'] ?>" <?= $c['cpf'] == $cpf ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if ($cpf || $titulo): ?>
                        <div class="mb-4">
                            <label class="form-label text-info fw-bold">2. Escolha o Ano</label>
                            <select name="ano" class="form-select" onchange="this.form.submit()" required>
                                <option value="">→ Selecione o ano</option>
                                <?php foreach ($anos_disponiveis as $a): ?>
                                    <option value="<?= $a['ano_eleicao'] ?>" <?= $a['ano_eleicao'] == $ano ? 'selected' : '' ?>>
                                        <?= $a['ano_eleicao'] ?> - <?= $a['ds_cargo'] ?> (<?= $a['sg_partido'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </form>

                <?php if ($candidato): ?>


                    <?php
                    if ($ano == 2012 || $ano == 2014) {
                        $fac = "AC";
                    } else {
                        $fac = "FAC";
                    }

                    $foto = "template/assets/images/candidatos/" . $ano . "/$fac" . $sq_candidato . "_div.jpg";

                    if (!file_exists($foto)) {
                        $foto = "template/assets/images/candidatos/" . $ano . "/$fac" . $sq_candidato . "_div.jpeg";

                        if (!file_exists($foto)) {
                            $foto = "template/assets/images/candidatos/sem_foto.jpg";
                        }
                    }
                    ?>

                    <div class="text-center p-4 bg-black bg-opacity-40 rounded-4 mt-4">
                        <img src="<?= $foto ?>" class="foto mb-3">
                        <h2 class="text-warning fw-bold"><?= $candidato['nm_urna_candidato'] ?></h2>
                        <p class="fs-4"><?= $candidato['ds_cargo'] ?> • <?= $ano ?></p>
                        <h1 class="display-3 text-success"><?= number_format($total_votos) ?> votos</h1>
                        <p class="text-info"><strong><?= $candidato['nr_candidato'] ?></strong> • <?= $candidato['sg_partido'] ?></p>
                    </div>
                <?php else: ?>
                    <div class="text-center mt-5">
                        <h3 class="text-info">Escolha um candidato para ver seu histórico</h3>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-md-9 p-0 position-relative">
                <div id="map">
                    <?php if (!$candidato): ?>
<!--                        <div class="overlay">
                            <h1 class="display-1 text-info fw-bold">HISTÓRICO POR CANDIDATO</h1>
                            <p class="fs-3 text-light">Tião Bocalom • Gladson • Jorge Viana • Márcio Bittar</p>
                            <p class="fs-4 text-light mt-4">Todos os anos. Todos os votos. Tudo aqui.</p>
                        </div>-->
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
                                const map = L.map('map').setView([-9.974, -67.807], 12);
                                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                    attribution: '© Ildeniro Lima | Histórico Eleitoral'
                                }).addTo(map);

<?php if ($candidato && $locais): ?>
                                    const dados = <?= json_encode($locais) ?>;
                                    const nome = "<?= addslashes($candidato['nm_urna_candidato']) ?>";

                                    dados.forEach(l => {
                                        const v = parseInt(l.votos);
                                        const raio = Math.max(10, Math.sqrt(v) * 2.6);
                                        L.circleMarker([l.latitude, l.longitude], {
                                            radius: raio,
                                            fillColor: "#0066cc",
                                            color: "#000",
                                            weight: 2,
                                            fillOpacity: 0.85
                                        }).bindPopup(`
                                                        <b>${l.nm_local_votacao}</b><br>
                                                        <small>${l.nm_bairro} • ${l.regional}</small><hr>
                                                        <strong style="color:#0066cc">${nome}</strong><br>
                                                        <strong style="font-size:1.5em">${v.toLocaleString('pt-BR')} votos</strong>
                                                    `).addTo(map);
                                    });
<?php endif; ?>
        </script>
    </body>
</html>