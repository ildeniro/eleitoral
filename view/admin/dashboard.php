<?php
include("template/layout/dashboard/topo.php");
?>


<div class="container col-xl-8 col-lg-8 col-md-8 col-sm-12 col-12">
    <div class="row">

        <div <?= ver_nivel(6) ? "" : "style='display: none;'"; ?> class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card card-figure">
                <figure class="figure">
                    <div class="figure-img">
                        <div class="container pt-5 pb-5">
                            <div class="row">
                                <img class="ml-5" src="<?= PORTAL_URL; ?>template/assets/images/img-svg/clipboard-list-svgrepo-com.svg" alt="">
                                <h3 class="text-muted mt-4 ml-3">Fiscalização</h3>
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
                            <a href="<?= PORTAL_URL; ?>view/fiscal/dashboard-fiscalizacao" class="btn btn-block btn-sm btn-personalizado">Entrar</a>
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

        <div <?= ver_nivel(21) ? "" : "style='display: none;'"; ?> class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card card-figure">
                <figure class="figure">
                    <div class="figure-img">
                        <div class="container pt-5 pb-5">
                            <div class="row">
                                <img class="ml-5" src="<?= PORTAL_URL; ?>template/assets/images/img-svg/stationery-svgrepo-com.svg" alt="">
                                <h3 class="text-muted mt-4 ml-3">Apuração</h3>
                            </div>
                        </div>
                        <div class="figure-description">
                            <h3 class="figure-title mb-3"> Módulo de Apuração </h3>
                            <p class="text-muted mb-0">
                                Laboriosam neque officia adipisci quo ut placeat labore? Doloribus, ipsam? Voluptates, minus.
                            </p>
                        </div>
                        <!--                        <div class="figure-tools">
                                                    <a href="#" class="tile tile-circle tile-sm mr-auto"> </a>
                                                    <span class="badge badge-danger">Social</span>
                                                </div>-->
                        <div class="figure-action">
                            <a href="<?= PORTAL_URL; ?>view/apuracao" class="btn btn-block btn-sm btn-personalizado">Entrar</a>
                        </div>
                    </div>
                </figure>
            </div>
        </div>

        <div <?= ver_nivel(22) ? "" : "style='display: none;'"; ?> class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card card-figure">
                <figure class="figure">
                    <div class="figure-img">
                        <div class="container pt-5 pb-5">
                            <div class="row">
                                <img class="ml-5" src="<?= PORTAL_URL; ?>template/assets/images/img-svg/icon-for-s-that-can-be-used-in-business-part-2-svgrepo-com.svg" alt="">
                                <h3 class="text-muted mt-4 ml-3">Relatórios</h3>
                            </div>
                        </div>
                        <div class="figure-description">
                            <h3 class="figure-title mb-3"> Módulo de Relatórios </h3>
                            <p class="text-muted mb-0">
                                Laboriosam neque officia adipisci quo ut placeat labore? Doloribus, ipsam? Voluptates, minus.
                            </p>
                        </div>
                        <!--                        <div class="figure-tools">
                                                    <a href="#" class="tile tile-circle tile-sm mr-auto"> </a>
                                                    <span class="badge badge-danger">Social</span>
                                                </div>-->
                        <div class="figure-action">
                            <a href="<?= PORTAL_URL; ?>view/relatorios" class="btn btn-block btn-sm btn-personalizado">Entrar</a>
                        </div>
                    </div>
                </figure>
            </div>
        </div>

        <div <?= ver_nivel(2) || ver_nivel(6) ? "" : "style='display: none;'"; ?> class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card card-figure">
                <figure class="figure">
                    <div class="figure-img">
                        <div class="container pt-5 pb-5">
                            <div class="row">
                                <img class="ml-5" src="<?= PORTAL_URL; ?>template/assets/images/img-svg/male-svgrepo-com.svg" alt="">
                                <h3 class="text-muted mt-4 ml-3">Usuários</h3>
                            </div>
                        </div>
                        
                        <div class="figure-description">
                            <h3 class="figure-title mb-3"> Módulo de Usuários </h3>
                            <p class="text-muted mb-0">
                                Módulo para controle de usuários, tais como, cadastros, edições, visualizações e atribuições de permissões de acesso ao sistema.
                            </p>
                        </div>
                        <!--<div class="figure-tools">
                            <a href="#" class="tile tile-circle tile-sm mr-auto"> </a>
                            <span class="badge badge-danger">Social</span>
                        </div>-->
                        <div class="figure-action">
                            <a href="<?= PORTAL_URL; ?>view/usuarios" class="btn btn-block btn-sm btn-personalizado">Entrar</a>
                        </div>
                    </div>
                </figure>
            </div>
        </div>

        <div <?= ver_nivel(23) || ver_nivel(6) ? "" : "style='display: none;'"; ?> class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card card-figure">
                <figure class="figure">
                    <div class="figure-img">
                        <div class="container pt-5 pb-5">
                            <div class="row">
                                <img class="ml-5" src="<?= PORTAL_URL; ?>template/assets/images/img-svg/network-marketing-svgrepo-com.svg" alt="">
                                <h3 class="text-muted mt-4 ml-3">Referências</h3>
                            </div>
                        </div>
                        <div class="figure-description">
                            <h3 class="figure-title mb-3"> Módulo de Referências de Indicação </h3>
                            <p class="text-muted mb-0">
                                Laboriosam neque officia adipisci quo ut placeat labore? Doloribus, ipsam? Voluptates, minus.
                            </p>
                        </div>
                        <!--                        <div class="figure-tools">
                                                    <a href="#" class="tile tile-circle tile-sm mr-auto"> </a>
                                                    <span class="badge badge-danger">Social</span>
                                                </div>-->
                        <div class="figure-action">
                            <a href="<?= PORTAL_URL; ?>view/indicadores" class="btn btn-block btn-sm btn-personalizado">Entrar</a>
                        </div>
                    </div>
                </figure>
            </div>
        </div>

        <div <?= ver_nivel(24) ? "" : "style='display: none;'"; ?> class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card card-figure">
                <figure class="figure">
                    <div class="figure-img">
                        <div class="container pt-5 pb-5">
                            <div class="row">
                                <img class="ml-5" src="<?= PORTAL_URL; ?>template/assets/images/img-svg/person-combination-svgrepo-com.svg" alt="">
                                <h3 class="text-muted mt-4 ml-3">Candidatos</h3>
                            </div>
                        </div>
                        <div class="figure-description">
                            <h3 class="figure-title mb-3"> Módulo de Candidatos </h3>
                            <p class="text-muted mb-0">
                                Laboriosam neque officia adipisci quo ut placeat labore? Doloribus, ipsam? Voluptates, minus.
                            </p>
                        </div>
                        <!--                        <div class="figure-tools">
                                                    <a href="#" class="tile tile-circle tile-sm mr-auto"> </a>
                                                    <span class="badge badge-danger">Social</span>
                                                </div>-->
                        <div class="figure-action">
                            <a href="<?= PORTAL_URL; ?>view/candidatos" class="btn btn-block btn-sm btn-personalizado">Entrar</a>
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