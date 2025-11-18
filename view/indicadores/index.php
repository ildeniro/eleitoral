<?php
include("template/layout/dashboard/topo.php");

if (!ver_nivel(1) && !ver_nivel(23) && !ver_nivel(6)) {
    msg('Você não possui permissão para acessar essa área.');
    url(PORTAL_URL . 'view/admin/dashboard');
}
?>

<div class="row ">
    <div class="container col-xl-9 col-lg-9">

        <div class="col-xl-10">
            <!-- ============================================================== -->
            <!-- pageheader -->
            <!-- ============================================================== -->
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="page-header" id="top">
                        <h2 class="pageheader-title">Lista de Indicadores de Referência </h2>
                        <div class="page-breadcrumb">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/admin/dashboard" class="breadcrumb-link">Início</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Indicadores de Referência</li>
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
        <!-- ============================================================== -->
        <!-- search bar  -->
        <!-- ============================================================== -->
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-9 col-lg-9 col-md-12 col-sm-12 col-12">
                            <input class="form-control form-control-sm" type="search" id="pesquisar" name="pesquisar" placeholder="Pesquisar" aria-label="Search">
                        </div>
                        <div <?= ver_nivel(2) ? "" : "style='display: none;'"; ?> class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12 ">
                            <div class="float-xl-right float-none mt-xl-0 ">
                                <a href="<?= PORTAL_URL; ?>view/indicadores/cadastro"><button class="btn btn-outline-primary btn-sm" ><i class="fas fa-user-plus mr-1"></i>Adicionar</button></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- end search bar  -->
        <!-- ============================================================== -->
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header" id="headingSeven">
                    <h3 class="mb-0">
                        <button class="btn btn-link" data-toggle="collapse" data-target="#collapseSeven" aria-expanded="true" aria-controls="collapseSeven">
                            <span class="fas fa-angle-down mr-3"></span>Filtros
                        </button>
                    </h3>
                </div>
                <div id="collapseSeven" class="collapse" aria-labelledby="headingSeven" data-parent="#accordion3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-inline ml-4">
                                    <h3 class="font-16  my-1 mr-2">Situação do Indicador: </h3>
                                    <div class="custom-control custom-checkbox my-1 mr-sm-2">
                                        <input type="checkbox" class="custom-control-input" id="customCheck10" name="customCheck10" value="1">
                                        <label class="custom-control-label" for="customCheck10">Ativo</label>
                                    </div>
                                    <div class="custom-control custom-checkbox my-1 mr-sm-2">
                                        <input type="checkbox" class="custom-control-input" id="customCheck11" name="customCheck11" value="1">
                                        <label class="custom-control-label" for="customCheck11">Cancelado</label>
                                    </div>
                                    <a href="#" id="filtrar" class="btn btn-secondary my-1 btn-sm"><i class="fas fa-filter fa-lg"></i>Filtrar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-lg-12 col-md-8 col-sm-12 col-12">
            <!-- ============================================================== -->
            <!-- card influencer one -->
            <!-- ============================================================== -->
            <div id="ajax_pesquisa">
                <?php
                $result = $db->prepare("SELECT *   
                                    FROM 2024_indicacoes c 
                                    WHERE 1
                                    ORDER BY c.status DESC, c.nome ASC");
                $result->execute();
                while ($indicadores = $result->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-xl-9 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="pl-xl-3">
                                        <div class="m-b-0">
                                            <div class="user-avatar-name d-inline-block">
                                                <h2 class="font-24 m-b-10 strong-alt" style="margin-bottom: 0px;"><?= $indicadores['nome']; ?></h2>
                                            </div>
                                        </div>
                                        <div class="user-avatar-address mt-2">
                                            <p class=" text-dark">
                                                <span class="badge <?= $indicadores['status'] == 1 ? "badge-success" : "badge-danger"; ?>"><?= $indicadores['status'] == 1 ? "Ativo" : "Inativo"; ?></span>
                                                <strong class="strong-alt">Função:</strong>
                                                <small class="small-alt"><?= $indicadores['funcao']; ?></small> |
                                                <strong class="strong-alt">Contato: </strong>
                                                <small class="small-alt"><?= $indicadores['telefone']; ?></small> <br>
                                                <strong class="strong-alt">Indicações: </strong>
                                                <small class="small-alt"><?= qtd_indicacao($indicadores['id'], 5); ?></small>
                                                <small class="small-alt">Fiscais de Seção - </small>  
                                                <small class="small-alt"><?= qtd_indicacao($indicadores['id'], 4); ?></small>
                                                <small class="small-alt">Supervisores de Local</small>
                                            </p>
                                          
                                           
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
                                    <div class="float-xl-right float-none mt-xl-0 mt-4">
                                        <a title="Editar Indicador" href="<?= PORTAL_URL; ?>view/indicadores/cadastro/<?= $indicadores['id']; ?>"><button class="btn btn-outline-light btn-sm" ><i class="fas fa-edit mr-1"></i>Editar</button></a>
                                        <button <?= $indicadores['status'] == 0 ? "" : "style='display: none'"; ?> onclick="ativar_indicador(<?= $indicadores['id']; ?>)" title="Ativar Indicadores" class="btn btn-outline-success btn-sm" ><i class="fas fa-check mr-1"></i>Ativar</button>
                                        <button <?= $indicadores['status'] == 1 ? "" : "style='display: none'"; ?> onclick="cancelar_indicador(<?= $indicadores['id']; ?>)" title="Cancelar Indicadores" class="btn btn-outline-danger btn-sm" ><i class="fas fa-power-off mr-1"></i>Cancelar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- end card influencer one -->
        <!-- ============================================================== -->
    </div>
</div>

<?php
include("template/layout/dashboard/rodape.php");
?>

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/indicadores/index.js"></script>