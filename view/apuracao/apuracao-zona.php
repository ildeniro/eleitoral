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
                                            <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/apuracao/apuracao-paralela" class="breadcrumb-link">Apuração Paralela</a></li>
                                            <li class="breadcrumb-item active" aria-current="page">Apuração Por Zona</li>
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
                    <h5 class="card-header p-3">ZONA 01</h5>
                    <div class="card-body">
                        <div class="container">
                            <div class="d-flex flex-wrap bd-highlight mb-3">
                                <?php
                                $secoes = $db->prepare("SELECT *     
                                        FROM 2024_secoes e 
                                        WHERE e.NR_ZONA = '1' AND e.TIPO = 'Principal' 
                                        ORDER BY e.NR_SECAO ASC");
                                $secoes->execute();

                                while ($row = $secoes->fetch(PDO::FETCH_ASSOC)) {
                                    if (vf_secaoo_apuracao_2024('1', $row['NR_SECAO'])) {
                                ?>
                                        <div class="p-2 bd-highlight border bg-success text-light text-center" style="font-size: 18px; width: 60px;"><?= $row['NR_SECAO'] ?></div>
                                    <?php
                                    } else {
                                    ?>
                                        <div class="p-2 bd-highlight border bg-danger text-light text-center"  style="font-size: 18px; width: 60px;"><?= $row['NR_SECAO'] ?></div>
                                <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-3">
                    <h5 class="card-header p-3">ZONA 09</h5>
                    <div class="card-body">
                        <div class="container">
                            <div class="d-flex flex-wrap bd-highlight mb-3">
                                <?php
                                $secoes = $db->prepare("SELECT *     
                                        FROM 2024_secoes e 
                                        WHERE e.NR_ZONA = '9' AND e.TIPO = 'Principal' 
                                        ORDER BY e.NR_SECAO ASC");
                                $secoes->execute();

                                while ($row = $secoes->fetch(PDO::FETCH_ASSOC)) {
                                    if (vf_secaoo_apuracao_2024('9', $row['NR_SECAO'])) {
                                ?>
                                        <div class="p-2 bd-highlight border bg-success text-light text-center"  style="font-size: 18px; width: 60px;"><?= $row['NR_SECAO'] ?></div>
                                    <?php
                                    } else {
                                    ?>
                                        <div class="p-2 bd-highlight border bg-danger text-light text-center"  style="font-size: 18px; width: 60px;"><?= $row['NR_SECAO'] ?></div>
                                <?php
                                    }
                                }
                                ?>
                            </div>
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