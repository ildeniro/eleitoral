<?php
include("template/layout/dashboard/topo.php");
?>


<div class="container">
    <div class="page-header" id="top">
        <div class="page-breadcrumb">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/admin/dashboard" class="breadcrumb-link-alt">Início</a></li>
                    <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/fiscal/dashboard-fiscalizacao" class="breadcrumb-link-alt">Fiscalização</a></li>
                    <li class="breadcrumb-item active text-brand" aria-current="page">Central do Fiscal</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">

        <div <?= ver_nivel(1) ? "" : "style='display: none;'"; ?> class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card card-figure">
                <figure class="figure">
                    <div class="figure-img">
                        <div class="container pt-5 pb-5">
                            <div class="row">
                                <img class="ml-5" src="<?= PORTAL_URL; ?>template/assets/images/img-svg/businessman-svgrepo-com.svg" alt="">
                                <h3 class="text-muted mt-4 ml-3">Fiscais</h3>
                            </div>
                        </div>
                        <div class="figure-description">
                            <h3 class="figure-title mb-3"> Módulo de Fiscalização </h3>
                            <p class="text-muted mb-0">
                                Este módulo é essencial para garantir uma fiscalização eleitoral eficiente e bem organizada, otimizando a alocação de recursos humanos durante as eleições.
                            </p>
                        </div>
                        <!--                        <div class="figure-tools">
                                                    <a href="#" class="tile tile-circle tile-sm mr-auto"> </a>
                                                    <span class="badge badge-danger">Social</span>
                                                </div>-->
                        <div class="figure-action">
                            <a href="<?= PORTAL_URL; ?>view/fiscal/distribuidos" class="btn btn-block btn-sm btn-personalizado">Entrar</a>
                        </div>
                    </div>
                </figure>
            </div>
        </div>

        <div <?= ver_nivel(1) || ver_nivel(21) ? "" : "style='display: none;'"; ?> class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card card-figure">
                <figure class="figure">
                    <div class="figure-img">
                        <div class="container pt-5 pb-5">
                            <div class="row">
                                <img class="ml-5" src="<?= PORTAL_URL; ?>template/assets/images/img-svg/7x24h-svgrepo-com.svg" alt="">
                                <h3 class="text-muted mt-4 ml-3">Ocorrências</h3>
                            </div>
                        </div>
                        <div class="figure-description">
                            <h3 class="figure-title mb-3"> Módulo Acompanhamento de Ocorrências </h3>
                            <p class="text-muted mb-0">
                                Laboriosam neque officia adipisci quo ut placeat labore? Doloribus, ipsam? Voluptates, minus.
                            </p>
                        </div>
                        <!--                        <div class="figure-tools">
                                                    <a href="#" class="tile tile-circle tile-sm mr-auto"> </a>
                                                    <span class="badge badge-danger">Social</span>
                                                </div>-->
                        <div class="figure-action">
                            <a href="<?= PORTAL_URL; ?>view/fiscal/ocorrencias" class="btn btn-block btn-sm btn-personalizado">Entrar</a>
                        </div>
                    </div>
                </figure>
            </div>
        </div>
    </div>
</div>


<?php
include("template/layout/dashboard/rodape.php");
?>