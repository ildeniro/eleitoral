<?php
include("template/layout/dashboard/topo.php");

if (!ver_nivel(1)) {
    msg('Você não possui permissão para acessar essa área.');
    url(PORTAL_URL . 'view/admin/dashboard');
}

$id = (!isset($_POST['id']) && isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : 0));
$param = Url::getURL(3);
$param = $param == '' && $id != '' ? $id : $param;

if ($param != null && $param != '' && $param != NULL && $param != 0) {
    $regional_id = $param;
} else {
    msg('Nenhuma regional encontrada para o parâmetro informado');
    url(PORTAL_URL . 'view/apuracao/regional_geral');
}
?>

<div class="row ">
    <div class="container col-xl-10 col-lg-10">

        <div class="col-xl-10">
            <!-- ============================================================== -->
            <!-- pageheader -->
            <!-- ============================================================== -->
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="page-header" id="top">
                        <h2 class="pageheader-title">Resultado por Regional </h2>
                        <div class="page-breadcrumb">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/admin/dashboard" class="breadcrumb-link-alt">Início</a></li>
                                    <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/apuracao" class="breadcrumb-link-alt">Apuração</a></li>
                                    <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/apuracao/resultado-apuracao" class="breadcrumb-link-alt">Resultados</a></li>
                                    <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/apuracao/regional_geral" class="breadcrumb-link-alt">Regionais</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Lista</li>
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
            <div class="col-xl-12 col-lg-12 col-md-8 col-sm-12 col-12">
                <!-- ============================================================== -->
                <!-- card influencer one -->
                <!-- ============================================================== -->
                <div class="table-responsive">

                    <table id="tabela_1" class="table table-sm table-nowrap table-striped table-bordered display" style="width:100%">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center">REGIONAL</th>
                                <th scope="col" class="text-center">ZONA</th>
                                <th scope="col" class="text-center">SEÇÃO</th>
                                <th scope="col" class="text-center">BAIRRO</th>
                                <th scope="col" class="text-center">LOCAL</th>
                                <th scope="col" class="text-center">CANDIDATO</th>
                                <th scope="col" class="text-center">TIPO</th>
                                <th scope="col" class="text-center">VOTOS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $qtd = 0;

                            $result88 = $db->prepare("SELECT sr.nome AS REGIONAL, r.QTD_VOTOS, r.ZONA, r.SECAO, lv.NM_BAIRRO, lv.NM_LOCAL_VOTACAO,
                                                  c.NM_URNA_CANDIDATO, r.TIPO_VOTO  
                                                  FROM 2024_resultados AS r
                                                  INNER JOIN 2024_secoes AS s ON s.NR_ZONA = r.ZONA AND s.NR_SECAO = r.SECAO
                                                  INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID
                                                  INNER JOIN sys_regionais AS sr ON sr.id = lv.REGIONAL_ID 
                                                  INNER JOIN 2024_candidatos AS c ON c.NR_CANDIDATO = r.NUM_CANDIDATO 
                                                  WHERE lv.REGIONAL_ID = ? AND r.COD_CARGO = 'Prefeito' AND r.ANO_ELEICAO = 2024 AND c.CD_CARGO = 11 AND c.ANO_ELEICAO = 2024 AND c.SG_UE = 01392  
                                                  GROUP BY r.ID");
                            $result88->bindValue(1, $regional_id);
                            $result88->execute();

                            while ($regionais = $result88->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                <tr>
                                    <td class="col-1 text-center"><?= $regionais['REGIONAL'] ?></td>
                                    <td class="col-1 text-center"><?= $regionais['ZONA'] ?></td>
                                    <td class="col-1 text-center"><?= $regionais['SECAO'] ?></td>
                                    <td class="col-1 text-center"><?= $regionais['NM_BAIRRO'] ?></td>
                                    <td class="col-1 text-center"><?= $regionais['NM_LOCAL_VOTACAO'] ?></td>
                                    <td class="col-1 text-center"><?= $regionais['NM_URNA_CANDIDATO'] ?></td>
                                    <td class="col-1 text-center"><?= $regionais['TIPO_VOTO'] ?></td>
                                    <td class="col-1 text-center"><?= $regionais['QTD_VOTOS'] ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>

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

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/apuracao/filtro.js"></script>