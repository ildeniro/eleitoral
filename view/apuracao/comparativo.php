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
    <div class="container col-xl-12 col-lg-12">
        <div class="row">
            <div class="container col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="col-xl-10">
                    <!-- ============================================================== -->
                    <!-- pageheader -->
                    <!-- ============================================================== -->
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="page-header" id="top">
                                <h2 class="pageheader-title">Comparativo entre Candidatos</h2>
                                <div class="page-breadcrumb">
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/admin/dashboard" class="breadcrumb-link">Início</a></li>
                                            <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/apuracao" class="breadcrumb-link">Apuração</a></li>
                                            <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/apuracao/resultado-apuracao" class="breadcrumb-link">Resultados</a></li>
                                            <li class="breadcrumb-item active" aria-current="page">Comparativo</li>
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
                        <div class="container" style="max-width: 90%;">

                            <h1 class="title"><img src="<?= PORTAL_URL; ?>assets/img/acre.svg" width="30px" alt="">CANDIDATOS A PREFEITO DO ACRE - RESULTADO DE RIO BRANCO</h1>
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Início do Corpo do Site -->
                                        <div class="content">
                                            <div class="container-fluid">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="card no-margin">
                                                            <div class="compare">
                                                                <div class="row">

                                                                    <?php
                                                                    $foto_caminho = "template/assets/images/candidatos/2024/FAC" . pesquisar("SQ_CANDIDATO", "2024_candidatos", "NR_CANDIDATO", "=", 22, "AND ANO_ELEICAO = 2024 AND SG_UE = 01392 AND DS_CARGO = 'Prefeito'");

                                                                    if (is_file($foto_caminho . "_div.jpg")) {
                                                                        $foto_caminho = $foto_caminho . "_div.jpg";
                                                                    } else if (is_file($foto_caminho . "_div.jpeg")) {
                                                                        $foto_caminho = $foto_caminho . "_div.jpeg";
                                                                    }
                                                                    ?>

                                                                    <div class="col-lg-5">
                                                                        <div class="socorro">
                                                                            <img src="<?= PORTAL_URL . "" . $foto_caminho; ?>" alt="">
                                                                            <div class="name">Tião Bocalom <strong>22</strong></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-2">
                                                                        <div class="versus">
                                                                            <i class="mdi mdi-close menu-icon"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-5">
                                                                        <div class="others">
                                                                            <table>
                                                                                <tbody>

                                                                                    <?php
                                                                                    $votos = 0;
                                                                                    $soma_votos = 0;

                                                                                    $result = $db->prepare("SELECT NR_PARTIDO, CD_CARGO, NR_CANDIDATO, NM_URNA_CANDIDATO   
                                                                                                            FROM 2024_candidatos 
                                                                                                            WHERE ANO_ELEICAO = 2024 AND SG_UE = 01392 AND DS_CARGO = 'Prefeito'");
                                                                                    $result->execute();
                                                                                    while ($candidatos = $result->fetch(PDO::FETCH_ASSOC)) {
                                                                                        $votos = buscar_votos_candidato($candidatos['NR_CANDIDATO'], 1392, 1, 2024);

                                                                                        $cand[$candidatos['NR_CANDIDATO']] = $votos;

                                                                                        $soma_votos += $votos;
                                                                                    }
                                                                                    ?>

                                                                                    <?php
                                                                                    foreach ($cand as $numeroCandidato => $votos) {
                                                                                        if ($votos != "" && is_numeric($votos)) {

                                                                                            $foto_caminho = "template/assets/images/candidatos/2024/FAC" . pesquisar("SQ_CANDIDATO", "2024_candidatos", "NR_CANDIDATO", "=", $numeroCandidato, "AND ANO_ELEICAO = 2024 AND SG_UE = 01392 AND DS_CARGO = 'Prefeito'");

                                                                                            if (is_file($foto_caminho . "_div.jpg")) {
                                                                                                $foto_caminho = $foto_caminho . "_div.jpg";
                                                                                            } else if (is_file($foto_caminho . "_div.jpeg")) {
                                                                                                $foto_caminho = $foto_caminho . "_div.jpeg";
                                                                                            }

                                                                                            $nome = pesquisar("NM_URNA_CANDIDATO", "2024_candidatos", "NR_CANDIDATO", "=", $numeroCandidato, "AND ANO_ELEICAO = 2024 AND SG_UE = 01392 AND DS_CARGO = 'Prefeito'");
                                                                                            ?>

                                                                                            <tr>
                                                                                                <td>
                                                                                                    <div class="form-check title">
                                                                                                        <input checked="checked" id="check_<?= $numeroCandidato; ?>" name="check_<?= $numeroCandidato; ?>" class="form-check-input check_candidatos" type="checkbox" value="<?= $numeroCandidato; ?>">
                                                                                                        <label class="form-check-label">
                                                                                                            <?= $nome; ?>
                                                                                                            <span class="form-check-sign">
                                                                                                                <span class="check"></span>
                                                                                                            </span>
                                                                                                        </label>
                                                                                                    </div>
                                                                                                </td>
                                                                                                <td><strong><?= $numeroCandidato; ?></strong></td>
                                                                                            </tr>

                                                                                            <?php
                                                                                        }
                                                                                    }
                                                                                    ?>
                                                                                </tbody>
                                                                                <tfoot>
                                                                                    <tr>
                                                                                        <th>
                                                                                            <div class="form-check title">
                                                                                                <input id="check_agrupar" name="check_agrupar" class="form-check-input check_candidatos" type="checkbox" value="">
                                                                                                <label class="form-check-label">
                                                                                                    Agrupar 
                                                                                                    <span class="form-check-sign">
                                                                                                        <span class="check"></span>
                                                                                                    </span>
                                                                                                </label>
                                                                                            </div>
                                                                                        </th>
                                                                                        <th></th>
                                                                                    </tr>
                                                                                </tfoot>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card no-margin">
                                                            <?php
                                                            $votos_22 = buscar_votos_candidato(22, 1392, 1, 2024);
                                                            $votos_15 = buscar_votos_candidato(15, 1392, 1, 2024);
                                                            $votos_30 = buscar_votos_candidato(30, 1392, 1, 2024);
                                                            $votos_40 = buscar_votos_candidato(40, 1392, 1, 2024);
                                                            $nulos = buscar_votos_nulos(1392, 2024);
                                                            $brancos = buscar_votos_brancos(1392, 2024);

                                                            $soma_votos = $votos_30 + $votos_40 + $votos_15 + $votos_22 + $nulos + $brancos;
                                                            ?>

                                                            <input type="hidden" id="total" name="total" value="<?= $soma_votos; ?>" />

                                                            <div id="gov_22" class="candidates">
                                                                <div class="image"><img src="<?= PORTAL_URL; ?>template/assets/images/candidatos/2024/FAC10001908807_div.jpg" alt=""></div>
                                                                <div class="name">
                                                                    <span>Tião Bocalom</span> <strong class="onze">22</strong>
                                                                    <div class="grafic">
                                                                        <b id="votos_22" class="votos_candidatos"><?= $votos_22; ?> votos</b>
                                                                        <div class="bar bar-thirteen" style="width: <?= $votos_22 > 0 ? ((100 / $soma_votos) * $votos_22) : 0; ?>%"></div>
                                                                    </div>
                                                                </div>
                                                                <div id="porc_22" class="wishes"><?= $soma_votos > 0 ? fdec(((100 / $soma_votos) * $votos_22)) : "0,0"; ?>%</div>
                                                            </div>

                                                            <div id="div_candidatos">
                                                                <div id="gov_15" class="candidates">
                                                                    <div class="image"><img src="<?= PORTAL_URL; ?>template/assets/images/candidatos/2024/FAC10001984285_div.jpg" alt=""></div>
                                                                    <div class="name">
                                                                        <span>Marcus Alexandre</span> <strong>15</strong>
                                                                        <div class="grafic">
                                                                            <b id="votos_15" class="votos_candidatos"><?= $votos_15; ?> votos</b>
                                                                            <div class="bar bar-others" style="width: <?= $votos_15 > 0 ? ((100 / $soma_votos) * $votos_15) : 0; ?>%"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div id="porc_15" class="wishes"><?= $soma_votos > 0 ? fdec(((100 / $soma_votos) * $votos_15)) : "0,0"; ?>%</div>
                                                                </div>

                                                                <div id="gov_30" class="candidates">
                                                                    <div class="image"><img src="<?= PORTAL_URL; ?>template/assets/images/candidatos/2024/FAC10001913167_div.jpeg" alt=""></div>
                                                                    <div class="name">
                                                                        <span>Jarude</span> <strong>30</strong>
                                                                        <div class="grafic">
                                                                            <b id="votos_30" class="votos_candidatos"><?= $votos_30; ?> votos</b>
                                                                            <div class="bar bar-others" style="width: <?= $votos_30 > 0 ? ((100 / $soma_votos) * $votos_30) : 0; ?>%"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div id="porc_30" class="wishes"><?= $soma_votos > 0 ? fdec(((100 / $soma_votos) * $votos_30)) : "0,0"; ?>%</div>
                                                                </div>

                                                                <div id="gov_40" class="candidates">
                                                                    <div class="image"><img src="<?= PORTAL_URL; ?>template/assets/images/candidatos/2024/FAC10002171655_div.jpg" alt=""></div>
                                                                    <div class="name">
                                                                        <span>Dr. Jenilson</span> <strong>40</strong>
                                                                        <div class="grafic">
                                                                            <b id="votos_40" class="votos_candidatos"><?= $votos_40; ?> votos</b>
                                                                            <div class="bar bar-others" style="width: <?= $votos_40 > 0 ? ((100 / $soma_votos) * $votos_40) : 0; ?>%"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div id="porc_40" class="wishes"><?= $soma_votos > 0 ? fdec(((100 / $soma_votos) * $votos_40)) : "0,0"; ?>%</div>
                                                                </div>

                                                                <div id="gov_brancos" class="candidates">
                                                                    <div class="image"><img src="<?= PORTAL_URL; ?>template/assets/brancos.jpg" alt=""></div>
                                                                    <div class="name">
                                                                        <span>Brancos</span>
                                                                        <div class="grafic">
                                                                            <b id="votos_brancos" class="votos_candidatos"><?= $brancos; ?> votos</b>
                                                                            <div class="bar bar-others" style="width: <?= $soma_votos > 0 ? ((100 / $soma_votos) * $brancos) : 0; ?>%"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div id="porc_brancos" class="wishes"><?= $soma_votos > 0 ? fdec(((100 / $soma_votos) * $brancos)) : "0,0"; ?>%</div>
                                                                </div>
                                                                <div id="gov_nulos" class="candidates">
                                                                    <div class="image"><img src="<?= PORTAL_URL; ?>template/assets/nulos.jpg" alt=""></div>
                                                                    <div class="name">
                                                                        <span>Nulos</span>
                                                                        <div class="grafic">
                                                                            <b id="votos_nulos" class="votos_candidatos"><?= $nulos; ?> votos</b>
                                                                            <div class="bar bar-others" style="width: <?= $soma_votos > 0 ? ((100 / $soma_votos) * $nulos) : 0; ?>%"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div id="porc_nulos" class="wishes"><?= $soma_votos > 0 ? fdec(((100 / $soma_votos) * $nulos)) : "0,0"; ?>%</div>
                                                                </div>
                                                            </div>

                                                            <div id="gov_agrupados" style="display: none" class="candidates">
                                                                <div class="image"><img src="<?= PORTAL_URL; ?>template/assets/user.jpg" alt=""></div>
                                                                <div class="name">
                                                                    <span id="agrupado_numeros"></span>
                                                                    <div class="grafic">
                                                                        <b id="votos_agrupados" class="">0 votos</b>
                                                                        <div class="bar bar-others" style="width: 0%"></div>
                                                                    </div>
                                                                </div>
                                                                <div id="porc_agrupados" class="wishes">0,0%</div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/apuracao/comparativo.js"></script>