<?php
include("template/layout/dashboard/topo.php");

if (!ver_nivel(1) && !ver_nivel(24)) {
    msg('Você não possui permissão para acessar essa área.');
    url(PORTAL_URL . 'view/admin/dashboard');
}
?>

<div class="row ">
    <div class="container col-xl-9 col-lg-10">

        <div class="col-xl-10">
            <!-- ============================================================== -->
            <!-- pageheader -->
            <!-- ============================================================== -->
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="page-header" id="top">
                        <h2 class="pageheader-title">Lista de Candidatos </h2>
                        <div class="page-breadcrumb">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/admin/dashboard" class="breadcrumb-link">Início</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Candidatos</li>
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
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <input class="form-control form-control-sm" type="search" id="pesquisar" name="pesquisar" placeholder="Pesquisar" aria-label="Search">
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
                                    <h3 class="font-16  my-1 mr-2">Situação do Candidato: </h3>
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
            <!-- <div id="ajax_pesquisa">
                <?php
                $result = $db->prepare("SELECT *   
                                    FROM 2024_candidatos c 
                                    WHERE SG_UE = 01392 
                                    ORDER BY c.STATUS DESC, c.NM_CANDIDATO ASC");
                $result->execute();
                while ($candidato = $result->fetch(PDO::FETCH_ASSOC)) {

                    $foto_caminho = "template/assets/images/candidatos/2024/FAC" . $candidato['SQ_CANDIDATO'];

                    if (is_file($foto_caminho . "_div.jpg")) {
                        $foto_caminho = $foto_caminho . "_div.jpg";
                    } else if (is_file($foto_caminho . "_div.jpeg")) {
                        $foto_caminho = $foto_caminho . "_div.jpeg";
                    }
                ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-xl-9 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="user-avatar float-xl-left pr-4 pl-3 float-none">
                                        <img src="<?= PORTAL_URL . "" . $foto_caminho; ?>" alt="<?= $candidato['NM_URNA_CANDIDATO']; ?>" class="rounded-circle user-avatar-lg">
                                    </div>
                                    <div class="pl-xl-3">
                                        <div class="m-b-0">
                                            <div class="user-avatar-name d-inline-block">
                                                <h2 class="font-24 m-b-10" style="margin-bottom: 0px;"><?= $candidato['NM_CANDIDATO']; ?> - <?= $candidato['NM_URNA_CANDIDATO']; ?></h2>
                                            </div>

                                        </div>
                                        <div class="user-avatar-address mt-2">
                                            <p class=" text-dark">
                                                <span class="badge <?= $candidato['STATUS'] == 1 ? "badge-success" : "badge-danger"; ?>"><?= $candidato['STATUS'] == 1 ? "Ativo" : "Inativo"; ?></span>
                                                <strong style="font-size: 16px; font-weight: 400;">Número:</strong>
                                                <small style="font-size: 16px; font-weight: 700;"><?= $candidato['NR_CANDIDATO']; ?></small> |
                                                <strong style="font-size: 16px; font-weight: 400;">Cargo: </strong>
                                                <small style="font-size: 16px; font-weight: 700;"><?= $candidato['DS_CARGO']; ?></small> |
                                                <strong style="font-size: 16px; font-weight: 400;">Partido: </strong>
                                                <small style="font-size: 16px; font-weight: 700;"><?= $candidato['NM_PARTIDO']; ?></small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div> -->
            <div class="row">
                <div class="col-xl-12 col-lg-8 col-md-8 col-sm-12 col-12">
                    <div id="ajax_pesquisa">
                    <div class="row">
                        <?php
                        $result = $db->prepare("SELECT *   
                                        FROM 2024_candidatos c 
                                        WHERE SG_UE = 01392 
                                        ORDER BY c.STATUS DESC, c.NM_CANDIDATO ASC");
                        $result->execute();
                        while ($candidato = $result->fetch(PDO::FETCH_ASSOC)) {

                            $foto_caminho = "template/assets/images/candidatos/2024/FAC" . $candidato['SQ_CANDIDATO'];

                            if (is_file($foto_caminho . "_div.jpg")) {
                                $foto_caminho = $foto_caminho . "_div.jpg";
                            } else if (is_file($foto_caminho . "_div.jpeg")) {
                                $foto_caminho = $foto_caminho . "_div.jpeg";
                            }
                        ?>

                            <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
                                <div class="product-thumbnail">
                                    <div class="product-img-head">
                                        <div class="product-img">
                                            <img src="<?= PORTAL_URL . "" . $foto_caminho; ?>" alt="<?= $candidato['NM_URNA_CANDIDATO']; ?>" alt="" class="img-fluid">
                                        </div>
                                        <div class="ribbons <?= $candidato['STATUS'] == 1 ? "bg-success" : "bg-danger"; ?>"></div>
                                        <div class="ribbons-text"><?= $candidato['STATUS'] == 1 ? "Ativo" : "Inativo"; ?></div>

                                    </div>
                                    <div class="product-content">
                                        <div class="product-content-head">
                                            <h3 class="product-title"><?= $candidato['NM_CANDIDATO']; ?> - <?= $candidato['NM_URNA_CANDIDATO']; ?></h3>
                                            <div class=" d-inline-block">
                                                <strong style="font-size: 16px; font-weight: 400;">Número:</strong>
                                                <small style="font-size: 16px; font-weight: 700;"><?= $candidato['NR_CANDIDATO']; ?></small> |
                                                <strong style="font-size: 16px; font-weight: 400;">Cargo: </strong>
                                                <small style="font-size: 16px; font-weight: 700;"><?= $candidato['DS_CARGO']; ?></small> |
                                                <strong style="font-size: 16px; font-weight: 400;">Partido: </strong>
                                                <small style="font-size: 16px; font-weight: 700;"><?= $candidato['NM_PARTIDO']; ?></small>
                                            </div>
                                            <!-- <div class="product-price"><?= $candidato['NR_CANDIDATO']; ?></div> -->
                                        </div>
                                        <div class="product-btn">
                                            <a href="#" class="btn  btn-personalizado">Details</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php } ?>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- end card influencer one -->
    <!-- ============================================================== -->
</div>
</div>
</div>

<?php
include("template/layout/dashboard/rodape.php");
?>

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/candidatos/index.js"></script>