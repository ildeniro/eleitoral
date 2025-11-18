<?php
include("template/layout/dashboard/topo.php");
?>

<?php
if (!ver_nivel(1) && !ver_nivel(3)) {
    msg('Você não possui permissão para acessar essa área.');
    url(PORTAL_URL . 'view/admin/dashboard');
}
?>

<link rel="stylesheet" href="<?= PORTAL_URL; ?>template/assets/libs/css/bu.css">

<div class="row">
    <div class="container col-xl-10 col-lg-10">
        <div class="row">
            <div class="container col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12">
                <div class="col-xl-10">
                    <!-- ============================================================== -->
                    <!-- pageheader -->
                    <!-- ============================================================== -->
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="page-header" id="top">
                                <h2 class="pageheader-title">Formulário de Apuração para Prefeito</h2>
                                <div class="page-breadcrumb">
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/admin/dashboard" class="breadcrumb-link">Início</a></li>
                                            <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/apuracao" class="breadcrumb-link">Apuração</a></li>
                                            <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/apuracao/apuracao-paralela" class="breadcrumb-link">Apuração Paralela</a></li>
                                            <li class="breadcrumb-item active" aria-current="page">Cadastro</li>
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
                        <div class="container mt-3 ">
                            <form id="formulario" name="formulario" action="#" method="POST"> 

                                <div class="card-nav-tabs">

                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <div class="row col-xl-12 col-lg-12">
                                            <h5 class="alt-h5-border-blue">DADOS DE LOCALIZAÇÃO</h5>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div id="div_zona" class="form-group">
                                                <label>Zona</label>
                                                <select required="true" id="zona" name="zona" class="form-control select2" style="width: 100%;">
                                                    <option value="">Selecione a Zona</option>
                                                    <?php
                                                    $result8 = $db->prepare("SELECT numero, id 
                                                         FROM sys_zonas 
                                                         WHERE id IN(1,9) 
                                                         GROUP BY numero 
                                                         ORDER BY numero ASC");
                                                    $result8->execute();
                                                    while ($zona = $result8->fetch(PDO::FETCH_ASSOC)) {
                                                        ?>
                                                        <option value='<?= $zona['numero']; ?>'><?= $zona['numero']; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div id="div_secao" class="form-group">
                                                <label>Seção</label>
                                                <select required="true" id="secao" name="secao" class="form-control select2" style="width: 100%;">
                                                    <option value="">Selecione a Seção</option>
                                                    <?php
                                                    if ($pessoa_zona != "") {
                                                        $result8 = $db->prepare("SELECT e.NR_SECAO AS SECAO    
                                                        FROM 2024_secoes e 
                                                        WHERE e.NR_ZONA = ? AND e.TIPO = 'Principal'
                                                        ORDER BY e.NR_SECAO ASC");
                                                        $result8->bindValue(1, $pessoa_zona);
                                                        $result8->execute();
                                                        while ($secao = $result8->fetch(PDO::FETCH_ASSOC)) {
                                                            ?>
                                                            <option value='<?= $secao['SECAO']; ?>'><?= $secao['SECAO']; ?><?= is_numeric(pesquisar("ID", "2024_secoes", 'NR_SECAO_PRINCIPAL', "=", $secao['SECAO'], "")) ? retornar_agregacao($pessoa_zona, $secao['SECAO']) : ""; ?></option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div id="div_local" class="title text-center rs-local-blue"></div>
                                        </div>
                                    </div>   

                                    <!-- ENDEREÇO -->
                                    <div class="row">

                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mt-5">
                                            <div class="row col-xl-12 col-lg-12">
                                                <h5 class="alt-h5 alt-h5-border-green">DADOS DA APURAÇÃO</h5>
                                            </div>
                                        </div>

                                        <div class="card-body">

                                            <?php
                                            $result = $db->prepare("SELECT NR_PARTIDO, CD_CARGO, NR_CANDIDATO, NM_URNA_CANDIDATO   
                                                                     FROM 2024_candidatos 
                                                                     WHERE ANO_ELEICAO = 2024 AND SG_UE = 01392 AND DS_CARGO = 'PREFEITO'
                                                                     ORDER BY NR_CANDIDATO ASC");
                                            $result->execute();
                                            while ($candidatos = $result->fetch(PDO::FETCH_ASSOC)) {
                                                ?>
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <div id="pre_<?= $candidatos['NR_CANDIDATO'] ?>" class="form-group">
                                                            <label for="pre_<?= $candidatos['NR_CANDIDATO'] ?>" class="partido_<?= $candidatos['NR_PARTIDO'] ?>"><strong><?= $candidatos['NR_CANDIDATO'] ?></strong> <?= $candidatos['NM_URNA_CANDIDATO'] ?> </label>
                                                            <input type="text" required="true" class="form-control" id="votos" name="votos[]" data-mask="99999" value=""/>
                                                            <input type="hidden" id="candidato" name="candidato[]" value="<?= $candidatos['NR_CANDIDATO'] ?>"/>
                                                            <input type="hidden" id="partidos" name="partidos[]" value="<?= $candidatos['NR_PARTIDO'] ?>"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>

                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="brancos">Brancos</label>
                                                        <input type="text" required="true" class="form-control" name="brancos" id="brancos" data-mask="99999" value=""/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="nulos">Nulos</label>
                                                        <input type="text" required="true" class="form-control" name="nulos" id="nulos" data-mask="99999" value=""/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- FIM ENDEREÇO -->
                                </div>

                                <div class="text-center">
                                    <button id="botao_confirmar" type="submit" class="btn btn-success btn-large botao-large">CONFIRMAR</button>
                                </div>
                            </form>
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

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/apuracao/bu.js"></script>