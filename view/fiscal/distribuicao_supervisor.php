<?php
include("template/layout/dashboard/topo.php");

if (!ver_nivel(1) && !ver_nivel(6)) {
    msg('Você não possui permissão para acessar essa área.');
    url(PORTAL_URL . 'view/admin/dashboard');
} else {
    $configuracoes = pesquisar("fiscal", "sys_configuracoes", "id", "=", 1, "");
    $configuracoes_supervisor = pesquisar("supervisor", "sys_configuracoes", "id", "=", 1, "");
}

if (isset($_GET['secao']) && $_GET['secao'] != 0 && is_numeric($_GET['secao'])) {
    $pessoa_secao = $_GET['secao'];
} else {
    $pessoa_secao = "";
}

if (isset($_GET['local']) && $_GET['local'] != 0 && is_numeric($_GET['local'])) {
    $pessoa_local = $_GET['local'];
} else {
    $pessoa_local = "";
}

if (isset($_GET['local2']) && $_GET['local2'] != 0 && is_numeric($_GET['local2'])) {
    $pessoa_local_2 = $_GET['local2'];
} else {
    $pessoa_local_2 = "";
}

if (isset($_GET['regional']) && $_GET['regional'] != 0 && is_numeric($_GET['regional'])) {
    $pessoa_regional = $_GET['regional'];
} else {
    $pessoa_regional = "";
}

if (isset($_GET['regional2']) && $_GET['regional2'] != 0 && is_numeric($_GET['regional2'])) {
    $pessoa_regional_2 = $_GET['regional2'];
} else {
    $pessoa_regional_2 = "";
}

if (isset($_GET['bairro']) && $_GET['bairro'] != 0 && is_numeric($_GET['bairro'])) {
    $pessoa_bairro = $_GET['bairro'];
} else {
    $pessoa_bairro = "";
}

if (isset($_GET['bairro2']) && $_GET['bairro2'] != 0 && is_numeric($_GET['bairro2'])) {
    $pessoa_bairro_2 = $_GET['bairro2'];
} else {
    $pessoa_bairro_2 = "";
}

if (isset($_GET['tipo_voluntario_guard']) && $_GET['tipo_voluntario_guard'] != 0 && is_numeric($_GET['tipo_voluntario_guard'])) {
    $pessoa_tipo_voluntario_guard = $_GET['tipo_voluntario_guard'];
} else {
    $pessoa_tipo_voluntario_guard = "";
}

if (isset($_GET['tipo_eleicao_guard']) && $_GET['tipo_eleicao_guard'] != 0 && is_numeric($_GET['tipo_eleicao_guard'])) {
    $pessoa_tipo_eleicao_guard = $_GET['tipo_eleicao_guard'];
} else {
    $pessoa_tipo_eleicao_guard = "";
}
?>

<?php
include("template/layout/componets/load.php");
?>

<div class="row">
    <div class="container col-xl-10 col-lg-10">
        <div class="col-xl-10">
            <!-- ============================================================== -->
            <!-- pageheader -->
            <!-- ============================================================== -->
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="page-header" id="top">
                        <h2 class="pageheader-title">Distribuição de Fiscais </h2>
                        <div class="page-breadcrumb">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/admin/dashboard" class="breadcrumb-link">Início</a></li>
                                    <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/fiscal/dashboard-fiscalizacao" class="breadcrumb-link">Fiscalização</a></li>
                                    <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/fiscal/dashboard-central-fiscal" class="breadcrumb-link">Central do Fiscal</a></li>
                                    <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/fiscal/distribuidos" class="breadcrumb-link">Fiscais</a></li>
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

        <!-- FILTRO FISCAIS -->
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card">
                    <div class="card-header  mt-3 px-3">
                        <h3>Fazer Distribuição</h3>
                    </div>
                    <div class="card-body pl-4">
                        <form id="form_distribuicao" name="form_distribuicao" action="#" method="POST">
                            <div class="form-row">
                                <div id="div_tipo_voluntario" class="<?= is_numeric($pessoa_tipo_voluntario_guard) && $pessoa_tipo_voluntario_guard == 5 || is_numeric($pessoa_tipo_voluntario_guard) && $pessoa_tipo_voluntario_guard == 4 ? "col-xl-5 col-lg-5" : "col-xl-6 col-lg-6" ?> col-md-12 col-sm-12 col-12 ">
                                    <div class="form-group">
                                        <select id="tipo_voluntario" name="tipo_voluntario" class="form-control select2">
                                            <option value="">Selecione o tipo de voluntário</option>
                                            <?php
                                            $result2 = $db->prepare("SELECT * 
                                                                     FROM sys_funcoes     
                                                                     WHERE id IN(4, 5)   
                                                                     ORDER BY nome");
                                            $result2->execute();
                                            while ($funcoes = $result2->fetch(PDO::FETCH_ASSOC)) {
                                                if ($pessoa_tipo_voluntario_guard == $funcoes['id']) {
                                                    ?>
                                                    <option selected="true" value="<?= $funcoes['id']; ?>"><?= $funcoes['nome']; ?></option>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <option value="<?= $funcoes['id']; ?>"><?= $funcoes['nome']; ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div id="div_tipo_eleicao" class="<?= is_numeric($pessoa_tipo_voluntario_guard) && $pessoa_tipo_voluntario_guard == 5 || is_numeric($pessoa_tipo_voluntario_guard) && $pessoa_tipo_voluntario_guard == 4 ? "col-xl-4 col-lg-4" : "col-xl-6 col-lg-6" ?> col-md-12 col-sm-12 col-12">
                                    <div class="form-group">
                                        <select id="tipo_eleicao" name="tipo_eleicao" class="form-control select2">
                                            <option value="">Selecione o tipo de eleição</option>
                                            <option <?= $pessoa_tipo_eleicao_guard == 1 ? "selected='true'" : ""; ?> value="1">Municipal</option>
                                            <option <?= $pessoa_tipo_eleicao_guard == 2 ? "selected='true'" : ""; ?> value="2">Estadual</option>
                                        </select>
                                    </div>
                                </div>


                                <div id='div_distribuicao_geral_supervisor' <?= is_numeric($pessoa_tipo_voluntario_guard) && $pessoa_tipo_voluntario_guard == 4 ? "" : "style='display: none;'" ?> class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
                                    <div class="float-xl-left float-none">
                                        <a <?= ver_nivel(1) ? "" : "style='display: none;'"; ?> href="#" id="gerar" onclick="gerar_supervisor()" class="btn btn-outline-success btn-lg"><i class="fas fa-sitemap mr-1"></i>Gerar Distribuição</a>
                                        <a <?= ver_nivel(1) ? "" : "style='display: none;'"; ?> href="#" id="desfazer" onclick="desfazer_supervisor()" class="btn btn-outline-danger btn-lg" style="<?= $configuracoes_supervisor == 0 ? "display: none;" : ""; ?>"><i class="fas fa-trash mr-1"></i>Remover Distribuição</a>
                                    </div>
                                </div>


                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- FIM FILTRO FISCAIS -->
        <!-- FILTRO FISCAIS -->

        <div <?= is_numeric($pessoa_tipo_voluntario_guard) ? "" : "style='display: none;'" ?> id="div_resultado_municipal">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <!-- Início listagem de alocação dos coordenadores de local de votação -->
                    <div id="div_supervisores_card" <?= is_numeric($pessoa_tipo_voluntario_guard) && $pessoa_tipo_voluntario_guard == 4 ? "" : "style='display: none;'" ?> class="card">
                        <div class="card-body">
                            <h5 class="card-header  mt-3 px-3">LOCAIS DE VOTAÇÃO</h5>
                            <div class="table-responsive">
                                <table id="tabela_2" class="table table-sm table-nowrap table-striped table-bordered display">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="text-center">REGIONAL</th>
                                            <th scope="col" class="text-center">BAIRRO</th>
                                            <th scope="col" class="text-center">LOCAL</th>
                                            <th scope="col" class="text-center">APTOS</th>
                                            <th class="text-center">SUPERVISORES</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        $qtd = 0;

                                        if (
                                                $pessoa_regional_2 != "" && $pessoa_bairro_2 != "" && $pessoa_local_2 != "" &&
                                                $pessoa_regional_2 != 0 && $pessoa_bairro_2 != 0 && $pessoa_local_2 != 0
                                        ) {
                                            $result88 = $db->prepare("SELECT s.NR_SECAO, lv.REGIONAL_ID, SUM(s.QT_ELEITOR_AGREGADO) AS QT_ELEITOR_AGREGADO, lv.NM_BAIRRO, lv.NM_LOCAL_VOTACAO, s.LOCAL_VOTACAO_ID       
                                                                      FROM 2024_locais_votacao AS lv 
                                                                      INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = lv.ID 
                                                                      WHERE lv.REGIONAL_ID = ? AND lv.CD_BAIRRO = ? AND lv.ID = ?  
                                                                      GROUP BY lv.ID  
                                                                      ORDER BY s.NR_SECAO ASC");
                                            $result88->bindValue(1, $pessoa_regional_2);
                                            $result88->bindValue(2, $pessoa_bairro_2);
                                            $result88->bindValue(3, $pessoa_local_2);
                                            $result88->execute();
                                        } else if (
                                                $pessoa_regional_2 != "" && $pessoa_bairro_2 != "" &&
                                                $pessoa_regional_2 != 0 && $pessoa_bairro_2 != 0
                                        ) {
                                            $result88 = $db->prepare("SELECT s.NR_SECAO, lv.REGIONAL_ID, SUM(s.QT_ELEITOR_AGREGADO) AS QT_ELEITOR_AGREGADO, lv.NM_BAIRRO, lv.NM_LOCAL_VOTACAO, s.LOCAL_VOTACAO_ID      
                                                                      FROM 2024_locais_votacao AS lv 
                                                                      INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = lv.ID 
                                                                      WHERE lv.REGIONAL_ID = ? AND lv.CD_BAIRRO = ?  
                                                                      GROUP BY lv.ID   
                                                                      ORDER BY s.NR_SECAO ASC");
                                            $result88->bindValue(1, $pessoa_regional_2);
                                            $result88->bindValue(2, $pessoa_bairro_2);
                                            $result88->execute();
                                        } else if ($pessoa_regional_2 != "" && $pessoa_regional_2 != 0) {
                                            $result88 = $db->prepare("SELECT s.NR_SECAO, lv.REGIONAL_ID, SUM(s.QT_ELEITOR_AGREGADO) AS QT_ELEITOR_AGREGADO, lv.NM_BAIRRO, lv.NM_LOCAL_VOTACAO, s.LOCAL_VOTACAO_ID       
                                                                      FROM 2024_locais_votacao AS lv 
                                                                      INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = lv.ID 
                                                                      WHERE lv.REGIONAL_ID = ?  
                                                                      GROUP BY lv.ID   
                                                                      ORDER BY s.NR_SECAO ASC");
                                            $result88->bindValue(1, $pessoa_regional_2);
                                            $result88->execute();
                                        } else if ($pessoa_local_2 != "" && $pessoa_local_2 != 0) {
                                            $result88 = $db->prepare("SELECT s.NR_SECAO, lv.REGIONAL_ID, SUM(s.QT_ELEITOR_AGREGADO) AS QT_ELEITOR_AGREGADO, lv.NM_BAIRRO, lv.NM_LOCAL_VOTACAO, s.LOCAL_VOTACAO_ID       
                                                                      FROM 2024_locais_votacao AS lv 
                                                                      INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = lv.ID 
                                                                      WHERE lv.ID = ? 
                                                                      GROUP BY lv.ID   
                                                                      ORDER BY s.NR_SECAO ASC");
                                            $result88->bindValue(1, $pessoa_local_2);
                                            $result88->execute();
                                        } else {
                                            $result88 = $db->prepare("SELECT s.NR_SECAO, lv.REGIONAL_ID, SUM(s.QT_ELEITOR_AGREGADO) AS QT_ELEITOR_AGREGADO, lv.NM_BAIRRO, lv.NM_LOCAL_VOTACAO, s.LOCAL_VOTACAO_ID        
                                                                      FROM 2024_locais_votacao AS lv  
                                                                      INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = lv.ID  
                                                                      WHERE lv.MUNICIPIO_ID = 94 AND s.TIPO = 'Principal'  
                                                                      GROUP BY lv.ID 
                                                                      ORDER BY s.NR_SECAO ASC");
                                            $result88->execute();
                                        }

                                        while ($secao = $result88->fetch(PDO::FETCH_ASSOC)) {

                                            $result888 = $db->prepare("SELECT v.id, v.nome, v.celular          
                                                                       FROM 2024_voluntarios AS v  
                                                                       WHERE v.regional_2 = ? AND v.local_votacao_2 = ? AND v.funcao_id = 4 AND v.status = 1    
                                                                       ORDER BY v.nome ASC");
                                            $result888->bindValue(1, $secao['REGIONAL_ID']);
                                            $result888->bindValue(2, $secao['LOCAL_VOTACAO_ID']);
                                            $result888->execute();

                                            $qtd = $result888->rowCount();
                                            ?>
                                            <tr>
                                                <td class="col-1 text-center"><?= $secao['REGIONAL_ID'] != "" ? pesquisar_tabela("nome", "sys_regionais", "id", "=", $secao['REGIONAL_ID'], "") : ""; ?></td>
                                                <td class="col-1 text-center"><?= $secao['NM_BAIRRO']; ?></td>
                                                <td class="text-center"><?= $secao['NM_LOCAL_VOTACAO']; ?></td>
                                                <td class="col-1 text-center"><?= $secao['QT_ELEITOR_AGREGADO']; ?></td>

                                                <td class=" text-center">

                                                    <a <?= $qtd >= 1 ? "style='display: none;'" : ""; ?> href="" onclick="carregar_dados_supervisor('<?= $secao['NR_SECAO']; ?>', '<?= $secao['REGIONAL_ID']; ?>', '<?= $secao['LOCAL_VOTACAO_ID']; ?>')" data-toggle="modal" data-target="#teste2" class="text-success add_fiscal"><i class="mdi mdi-plus-circle" style="font-size: 20px;"></i></a>

                                                    <?php
                                                    while ($fiscais = $result888->fetch(PDO::FETCH_ASSOC)) {
                                                        ?>
                                                        <span>
                                                            <p class="pading-distribuicao">
                                                                <b><?= $fiscais['nome']; ?></b>
                                                                <a id="remover_fiscal" onclick="remover_supervisor(<?= $fiscais['id']; ?>)" style="cursor: pointer" class="text-danger"><i class="mdi mdi-close-circle" style="font-size: 20px;"></i></a>
                                                            </p>

                                                            <p class="pading-distribuicao">
                                                                <?= $fiscais['celular']; ?>
                                                            </p>
                                                        </span>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Fim da listagem de alocação dos coordenadores de local de votação -->
                </div>

            </div>
        </div>
        <div style="display: none;" id="div_resultado_estadual"></div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade modal-success" id="teste2" role="dialog">
    <div class="modal-dialog" role="document" style="max-width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">ADICIONAR SUPERVISOR</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <form id="supervisor" name="supervisor" action="#" method="POST">

                    <input type="hidden" id="guard_regional_supervisor" name="guard_regional_supervisor" value="" />
                    <input type="hidden" id="guard_secao_supervisor" name="guard_secao" value="" />
                    <input type="hidden" id="guard_local_supervisor" name="guard_local" value="" />

                    <div class="row">
                        <div class="col-md-12">
                            <div id="div_pessoas" class="form-group">
                                <label>SUPERVISOR DE LOCAL</label>
                                <select id="pessoas_supervisor" name="pessoas_supervisor" class="form-control select2" style="width: 100%;">
                                    <option value="">Selecione o Supervisor de Local</option>
                                    <?php
                                    $qtdd = 1;

                                    $result8 = $db->prepare("SELECT b.REGIONAL_NOME, e2j.NM_LOCAL_VOTACAO, p.secao_numero, p.bsc_bairros_id, b.NM_BAIRRO AS bairro, p.zona, p.local_votacao, p.id, p.nome    
                                                                 FROM 2024_voluntarios AS p
                                                                 INNER JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero 
                                                                 INNER JOIN 2024_locais_votacao AS e2j ON e2j.ID = s.LOCAL_VOTACAO_ID 
                                                                 INNER JOIN bsc_bairros AS b ON b.ID = e2j.CD_BAIRRO   
                                                                 WHERE p.deficiencia IN(3, 2) AND p.tipo = 2 AND p.funcao_id = 4 AND p.secao_numero_2 IS NULL AND p.status = 1 AND e2j.MUNICIPIO_ID = 94 OR
                                                                 p.deficiencia IN(3, 2) AND p.tipo = 2 AND p.funcao_id = 4 AND p.secao_numero_2 = ' ' AND p.status = 1 AND e2j.MUNICIPIO_ID = 94      
                                                                 ORDER BY p.data_cadastro ASC");
                                    $result8->execute();

                                    while ($pessoa = $result8->fetch(PDO::FETCH_ASSOC)) {
                                        ?>
                                        <option zona="<?= $pessoa['zona']; ?>" secao="<?= $pessoa['secao_numero']; ?>" local="<?= $pessoa['local_votacao']; ?>" value='<?= $pessoa['id']; ?>'><?= $qtdd; ?> - <?= $pessoa['NM_LOCAL_VOTACAO'] != "" ? "LDV " . $pessoa['NM_LOCAL_VOTACAO'] : ""; ?> - <?= $pessoa['nome']; ?></option>
                                        <?php
                                        $qtdd++;
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-success">CONFIRMAR</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?php
include("template/layout/dashboard/rodape.php");
?>

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/fiscal/distribuicao_supervisor.js"></script>