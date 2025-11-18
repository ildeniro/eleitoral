<?php
include("template/layout/dashboard/topo.php");
?>


<div class="container">
    <div class="page-header" id="top">
        <div class="page-breadcrumb">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/admin/dashboard" class="breadcrumb-link-alt">Início</a></li>
                    <li class="breadcrumb-item active text-brand" aria-current="page">Fiscalização</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">

        <div <?= ver_nivel(1) || ver_nivel(6) ? "" : "style='display: none;'"; ?> class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card card-figure">
                <figure class="figure">
                    <div class="figure-img">
                        <div class="container pt-5 pb-5">
                            <div class="row">
                                <img class="ml-5" src="<?= PORTAL_URL; ?>template/assets/images/img-svg/conference-call-svgrepo-com.svg" alt="">
                                <h3 class="text-muted mt-4 ml-3">Voluntários</h3>
                            </div>
                        </div>
                        <div class="figure-description">
                            <h3 class="figure-title mb-3"> Módulo de Voluntários </h3>
                            <p class="text-muted mb-0">
                                Este módulo é essencial para garantir uma fiscalização eleitoral eficiente e bem organizada, otimizando a alocação de recursos humanos durante as eleições.
                            </p>
                        </div>
                        <!--                        <div class="figure-tools">
                                                    <a href="#" class="tile tile-circle tile-sm mr-auto"> </a>
                                                    <span class="badge badge-danger">Social</span>
                                                </div>-->
                        <div class="figure-action">
                            <a href="<?= PORTAL_URL; ?>view/fiscal" class="btn btn-block btn-sm btn-personalizado">Entrar</a>
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
                                <img class="ml-5" src="<?= PORTAL_URL; ?>template/assets/images/img-svg/collaboration-svgrepo-com.svg" alt="">
                                <h3 class="text-muted mt-4 ml-3">Central do Fiscal</h3>
                            </div>
                        </div>
                        <div class="figure-description">
                            <h3 class="figure-title mb-3"> Módulo de Central do Fiscal </h3>
                            <p class="text-muted mb-0">
                                Esse modulo onde fica concentrado o gerenciamento dos fiscais.
                            </p>
                        </div>
                        <!--                        <div class="figure-tools">
                                                    <a href="#" class="tile tile-circle tile-sm mr-auto"> </a>
                                                    <span class="badge badge-danger">Social</span>
                                                </div>-->
                        <div class="figure-action">
                            <a href="<?= PORTAL_URL; ?>view/fiscal/dashboard-central-fiscal" class="btn btn-block btn-sm btn-personalizado">Entrar</a>
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