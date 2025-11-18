<?php
include("template/layout/dashboard/topo.php");
?>


<div class="container">
    <div class="page-header" id="top">
        <div class="page-breadcrumb">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/admin/dashboard" class="breadcrumb-link-alt">Início</a></li>
                    <li class="breadcrumb-item active text-brand" aria-current="page">Apuração</li>
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
                                <img class="ml-5" src="<?= PORTAL_URL; ?>template/assets/images/img-svg/disk1-svgrepo-com.svg" alt="">
                                <h3 class="text-muted mt-4 ml-3">Apuração Paralela</h3>
                            </div>
                        </div>
                        <div class="figure-description">
                            <!-- <h3 class="figure-title mb-3"> Módulo de Visualização do Resultado Geral </h3>
                            <p class="text-muted mb-0">
                                Esse modulo mostra de forma geral o resultado da apuração.
                            </p> -->
                        </div>
                        <!--                        <div class="figure-tools">
                                                    <a href="#" class="tile tile-circle tile-sm mr-auto"> </a>
                                                    <span class="badge badge-danger">Social</span>
                                                </div>-->
                        <div class="figure-action">
                            <a href="<?= PORTAL_URL; ?>view/apuracao/apuracao-paralela" class="btn btn-block btn-sm btn-personalizado">Entrar</a>
                        </div>
                    </div>
                </figure>
            </div>
        </div>

        <div <?= ver_nivel(1) ? "" : "style='display: none;'"; ?> class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card card-figure">
                <figure class="figure">
                    <div class="figure-img">
                        <div class="container pt-5 pb-5">
                            <div class="row">
                                <img class="ml-5" src="<?= PORTAL_URL; ?>template/assets/images/img-svg/seo-web-business-svgrepo-com.svg" alt="">
                                <h3 class="text-muted mt-4 ml-3">Resultados</h3>
                            </div>
                        </div>
                        <div class="figure-description">
                            <h3 class="figure-title mb-3"> Módulo de Resultado da Apuração </h3>
                            <p class="text-muted mb-0">
                                Esse modulo mostra os resultados parcial ou final da apuração das eleições.
                            </p>
                        </div>
                        
                        <div class="figure-action">
                            <a href="<?= PORTAL_URL; ?>view/apuracao/resultado-apuracao" class="btn btn-block btn-sm btn-personalizado">Entrar</a>
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