<?php
include("template/layout/dashboard/topo.php");

if (!ver_nivel(1) && !ver_nivel(3)) {
    msg('Você não possui permissão para acessar essa área.');
    url(PORTAL_URL . 'view/admin/dashboard');
}
?>

<div class="row ">
    <div class="container col-xl-9 col-lg-10">

        <!-- ============================================================== -->
        <!-- widgets   -->
        <!-- ============================================================== -->
        <div class="row">
            <div class="col-xl-10">
                <!-- ============================================================== -->
                <!-- pageheader -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="page-header" id="top">
                            <h2 class="pageheader-title">Eleições 2024</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/admin/dashboard" class="breadcrumb-link">Início</a></li>
                                        <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/apuracao" class="breadcrumb-link">Apuração</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
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
        </div>
        <div class="row">
            <!-- ============================================================== -->
            <!-- four widgets   -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- total views   -->
            <!-- ============================================================== -->
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-inline-block">
                            <h5 class="text-muted">Eleitores Aptos</h5>
                            <h2 class="mb-0"> <?= aptos_municipio(94); ?></h2>
                        </div>
                        <div class="float-right icon-circle-medium  icon-box-lg  bg-info-light mt-1">
                            <i class="fa fa-eye fa-fw fa-sm text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- end total views   -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- total followers   -->
            <!-- ============================================================== -->
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-inline-block">
                            <h5 class="text-muted">Candidatos a prefeito</h5>
                            <h2 class="mb-0"> <?= qtd_candidatos(2024, "PREFEITO", 1392); ?></h2>
                        </div>
                        <div class="float-right icon-circle-medium  icon-box-lg  bg-primary-light mt-1">
                            <i class="fa fa-user fa-fw fa-sm text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- end total followers   -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- total followers   -->
            <!-- ============================================================== -->
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-inline-block">
                            <h5 class="text-muted">Candidatos a veriador</h5>
                            <h2 class="mb-0"> <?= qtd_candidatos(2024, "VEREADOR", 1392); ?></h2>
                        </div>
                        <div class="float-right icon-circle-medium  icon-box-lg  bg-primary-light mt-1">
                            <i class="fa fa-user fa-fw fa-sm text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- end total followers   -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- partnerships   -->
            <!-- ============================================================== -->
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-inline-block">
                            <h5 class="text-muted">Quantidade de Urnas</h5>
                            <h2 class="mb-0"><?= urnas_tipo(1392); ?></h2>
                        </div>
                        <div class="float-right icon-circle-medium  icon-box-lg  bg-secondary-light mt-1">
                            <i class="fa fa-handshake fa-fw fa-sm text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- end partnerships   -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- total earned   -->
            <!-- ============================================================== -->
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-inline-block">
                            <h5 class="text-muted">Seções</h5>
                            <h2 class="mb-0"> <?= secoes_tipo(1392); ?></h2>
                        </div>
                        <div class="float-right icon-circle-medium  icon-box-lg  bg-brand-light mt-1">
                            <i class="fa fa-money-bill-alt fa-fw fa-sm text-brand"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- end total earned   -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- end widgets   -->
        <!-- ============================================================== -->
        <div class="row mt-3">

            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card px-3">
                    <div class="card-body ">
                        <div class="row">
                            <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12">
                                <input class="form-control form-control-sm" type="search" id="pesquisar" name="pesquisar" placeholder="Pesquisar" aria-label="Search">
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                <div class="float-xl-right float-none mt-xl-0 mt-4">
                                    <a href="<?= PORTAL_URL; ?>view/apuracao/bu" class="btn btn-primary btn-sm"><i class="fas fa-user-plus mr-1"></i>Apuração Manual</a>
                                    <a href="<?= PORTAL_URL; ?>view/apuracao/resultado-geral" id="gerar" class="btn btn-success btn-sm"><i class="fas fa-sitemap mr-1"></i>Resultado Geral</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                <!-- ============================================================== -->
                <!-- social source  -->
                <!-- ============================================================== -->
                <div class="card">
                    <h5 class="card-header p-3"> Total de Candidatos por Partido</h5>
                    <div class="card-body p-0">
                        <ul id="ajax_pesquisa" class="social-sales list-group list-group-flush">
                            <?php
                            $result = $db->prepare("SELECT c.ANO_ELEICAO, c.ID_PARTIDO, c.SG_PARTIDO  
                                                    FROM 2024_candidatos AS c 
                                                    WHERE c.ANO_ELEICAO = 2024
                                                    GROUP BY c.SG_PARTIDO");
                            $result->execute();
                            while ($partidos = $result->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                <li class="list-group-item social-sales-content">
<!--                                    <span class="social-sales-icon-circle facebook-bgcolor mr-2">
                                        <i class="fab fa-facebook-f"></i>
                                    </span>-->
                                    <span class="social-sales-name"><?= $partidos['SG_PARTIDO']; ?></span>
                                    <span class="social-sales-count text-dark"><?= qtd_candidatos_partido($partidos['ANO_ELEICAO'], $partidos['ID_PARTIDO'], 1392); ?> Candidatos</span>
                                </li>

                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                    <!--<div class="card-footer text-center">
                        <a href="#" class="btn-primary-link">View Details</a>
                    </div>-->
                </div>
                <!-- ============================================================== -->
                <!-- end social source  -->
                <!-- ============================================================== -->
            </div>

            <!-- ============================================================== -->
            <!-- gross profit  -->
            <!-- ============================================================== -->

            <?php
            $quantidade_urnas = urnas_tipo(1392);
            $urnas_apuradas = urnas_apuradas_municipio(1392);

            // Calcular a porcentagem apurada (sem o símbolo de %)
            $porc_apurada = ($urnas_apuradas / $quantidade_urnas) * 100;

            // Calcular a porcentagem que falta apurar
            $porc_falta_apurar = 100 - $porc_apurada;

            // Formatar os valores para ter uma casa decimal e adicionar o símbolo de porcentagem
            $porc_apurada_formatada = number_format($porc_apurada, 1, ',', '.') . '%';
            $porc_falta_apurar_formatada = number_format($porc_falta_apurar, 1, ',', '.') . '%';
            ?>

            <input type="hidden" id='apuradas' name='apuradas' value="<?= $porc_apurada_formatada; ?>" />
            <input type="hidden" id='nao_apuradas' name='nao_apuradas' value="<?= $porc_falta_apurar_formatada; ?>" />

            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="card ">
                    <h5 class="card-header p-3">% Apuração total das urnas</h5>
                    <div class="card-body">
                        <div id="morris_gross" style="height: 374px;"></div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- end gross profit  -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- ap and ar balance  -->
        <!-- ============================================================== -->
        <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card p-2" style="display: none;">
                <h5 class="card-header">AP and AR Balance
                </h5>
                <div class="card-body">
                    <canvas id="chartjs_balance_bar"></canvas>
                </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- end ap and ar balance  -->
        <!-- ============================================================== -->
    </div>
</div>

<?php
include("template/layout/dashboard/rodape.php");
?>

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/candidatos/dashboard.js"></script>