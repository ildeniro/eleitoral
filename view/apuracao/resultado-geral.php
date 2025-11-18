<?php
include("template/layout/dashboard/topo.php");
?>

<?php
if (!ver_nivel(1) && !ver_nivel(3)) {
    msg('Você não possui permissão para acessar essa área.');
    url(PORTAL_URL . 'view/admin/dashboard');
}
?>

<link rel="stylesheet" href="<?= PORTAL_URL; ?>template/assets/libs/css/apuracao_style.css">

<div class="row">
    <div class="container col-xl-10 col-lg-10">
        <div class="row">
            <div class="container col-xl-10 col-lg-10 col-md-12 col-sm-12 col-12">
                <div class="col-xl-10">
                    <!-- ============================================================== -->
                    <!-- pageheader -->
                    <!-- ============================================================== -->
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="page-header" id="top">
                                <h2 class="pageheader-title">Resultado de Apuração</h2>
                                <div class="page-breadcrumb">
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/admin/dashboard" class="breadcrumb-link">Início</a></li>
                                            <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/apuracao" class="breadcrumb-link">Apuração</a></li>
                                            <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/apuracao/resultado-apuracao" class="breadcrumb-link">Resultados</a></li>
                                            <li class="breadcrumb-item active" aria-current="page">Resultado Geral</li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ============================================================== -->
                    <!-- end pageheader  -->
                    <!-- ============================================================== -->
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="container">
                            <!-- Início do Corpo do Site -->
                            <div class="content">
                                <?php
                                $qtd_urnas_apuradas = urnas_apuradas_municipio(1392);
                                $qtd_total_urnas = urnas_tipo(1392);
                                $porc_urnas_apuradas = ($qtd_urnas_apuradas / $qtd_total_urnas) * 100;
                                ?>

                                <div class="row p-2 d-flex">
                                    <div class="col-xl-4 col-lg-6 col-md-12 col-sm-12 col-12">
                                        <h1 class="title">CANDIDATOS A PREFEITO DO ACRE - RESULTADO DE RIO BRANCO</h1>
                                    </div>
                                    <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12 col-12">
                                        <h1 class="title">TOTAL DE URNAS APURADAS: <?= $qtd_urnas_apuradas; ?>(<?= fdec($porc_urnas_apuradas); ?>%)</h1>
                                    </div>
                                    <div class="col-xl-3 col-lg-9 col-md-9 col-sm-8 col-7" style="margin-top: 9px;">
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: <?= $porc_urnas_apuradas; ?>%" aria-valuenow="<?= $porc_urnas_apuradas; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                    <div class="col-xl-2 col-lg-3 col-md-3 col-sm-4 col-5" style="margin-top: 5px;">
                                        <div style="width: 77%;" class="wishes2"><?= str_replace(",00", "", fdec($qtd_total_urnas)); ?>(100%)
                                    </div>
                                </div>

                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-md-12">

                                            <div class="no-margin">
                                                <?php
                                                $votos = 0;
                                                $soma_votos = 0;

                                                $result = $db->prepare("SELECT NR_PARTIDO, CD_CARGO, NR_CANDIDATO, NM_URNA_CANDIDATO   
                                                                     FROM 2024_candidatos 
                                                                     WHERE ANO_ELEICAO = 2024 AND SG_UE = 01392 AND DS_CARGO = 'Prefeito'
                                                                     ORDER BY NM_URNA_CANDIDATO DESC");
                                                $result->execute();
                                                while ($candidatos = $result->fetch(PDO::FETCH_ASSOC)) {
                                                    $votos = buscar_votos_candidato($candidatos['NR_CANDIDATO'], 1392, 1, 2024);

                                                    $cand[$candidatos['NR_CANDIDATO']] = $votos;

                                                    $soma_votos += $votos;
                                                }
                                                ?>

                                                <input type="hidden" id="total" name="total" value="<?= $soma_votos; ?>" />

                                                <div>
                                                    <h4>Total de Votos: <?= str_replace(",00", "", fdec($soma_votos)); ?></h4>
                                                </div>

                                                <?php
                                                foreach ($cand as $numeroCandidato => $votos) {
                                                    if ($votos != "" && is_numeric($votos)) {

                                                        $foto_caminho = "template/assets/images/candidatos/2024/FAC" . pesquisar("SQ_CANDIDATO", "2024_candidatos", "NR_CANDIDATO", "=", $numeroCandidato, "AND ANO_ELEICAO = 2024 AND SG_UE = 01392 AND DS_CARGO = 'Prefeito'");

                                                        if (is_file($foto_caminho . "_div.jpg")) {
                                                            $foto_caminho = $foto_caminho . "_div.jpg";
                                                        } else if (is_file($foto_caminho . "_div.jpeg")) {
                                                            $foto_caminho = $foto_caminho . "_div.jpeg";
                                                        }
                                                ?>

                                                        <div id="gov_13" class="candidates">
                                                            <div class="image"><img src="<?= PORTAL_URL . "" . $foto_caminho; ?>" alt=""></div>
                                                            <div class="name">
                                                                <span><?= pesquisar("NM_URNA_CANDIDATO", "2024_candidatos", "NR_CANDIDATO", "=", $numeroCandidato, "AND ANO_ELEICAO = 2024 AND SG_UE = 01392 AND DS_CARGO = 'Prefeito'") ?></span> <strong class="onze"><?= $numeroCandidato; ?></strong>
                                                                <div class="grafic">
                                                                    <b class="votos_candidatos"><?= number_format($votos, 0, ',', '.'); ?> votos</b>
                                                                    <div class="bar bar-thirteen" style="width: <?= $votos > 0 ? ((100 / $soma_votos) * $votos) : 0; ?>%"></div>
                                                                </div>
                                                            </div>
                                                            <div class="wishes"><?= $votos > 0 ? fdec(((100 / $soma_votos) * $votos)) : "0,0"; ?>%</div>
                                                        </div>

                                                <?php
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fim do Corpo do Site -->

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

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/apuracao/resultado-geral.js"></script>