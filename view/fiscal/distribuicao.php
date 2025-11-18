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


                                <div id='div_distribuicao_geral_fiscal' <?= is_numeric($pessoa_tipo_voluntario_guard) && $pessoa_tipo_voluntario_guard == 5 ? "" : "style='display: none;'" ?> class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
                                    <div class="float-xl-left float-none">
                                        <a <?= ver_nivel(1) ? "" : "style='display: none;'"; ?> href="#" id="gerar" onclick="gerar()" class="btn btn-outline-success btn-lg"><i class="fas fa-sitemap mr-1"></i>Gerar Distribuição</a>
                                        <a <?= ver_nivel(1) ? "" : "style='display: none;'"; ?> href="#" id="desfazer" onclick="desfazer()" class="btn btn-outline-danger btn-lg" style="<?= $configuracoes == 0 ? "display: none;" : ""; ?>"><i class="fas fa-trash mr-1"></i>Remover Distribuição</a>
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
                    <!-- Início listagem de alocação dos fiscais -->
                    <div id="div_fiscais_card" <?= is_numeric($pessoa_tipo_voluntario_guard) && $pessoa_tipo_voluntario_guard == 5 ? "" : "style='display: none;'" ?> class="card">
                        <h5 class="card-header  mt-3 px-3">SEÇÕES</h5>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tabela_1" class="table table-sm table-nowrap table-striped table-bordered display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="text-center">ZONA</th>
                                            <th scope="col" class="text-center">SEÇÃO</th>
                                            <th scope="col" class="text-center">REGIONAL</th>
                                            <th scope="col" class="text-center">BAIRRO</th>
                                            <th scope="col" class="text-center">LOCAL</th>
                                            <th scope="col" class="text-center">APTOS</th>
                                            <th class="text-center">FISCAL 1</th>
                                            <th class="text-center">FISCAL 2</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $qtd = 0;
                                        if (
                                                $pessoa_regional != "" && $pessoa_bairro != "" && $pessoa_local != "" && $pessoa_secao != "" &&
                                                $pessoa_regional != 0 && $pessoa_bairro != 0 && $pessoa_local != 0 && $pessoa_secao != 0
                                        ) {
                                            $result88 = $db->prepare("SELECT s.NR_ZONA, lv.ID AS LOCAL_VOTACAO_ID, lv.NM_LOCAL_VOTACAO, lv.REGIONAL_ID, s.NR_SECAO, lv.CD_BAIRRO, SUM(s.QT_ELEITOR_AGREGADO) AS QT_ELEITOR_AGREGADO               
                                                                 FROM 2024_secoes AS s
                                                                 INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID  
                                                                 WHERE lv.REGIONAL_ID = ? AND lv.CD_BAIRRO = ? AND lv.ID = ? AND s.NR_SECAO = ? AND s.TIPO = 'Principal' 
                                                                 GROUP BY s.ID
                                                                 ORDER BY s.NR_SECAO ASC");
                                            $result88->bindValue(1, $pessoa_regional);
                                            $result88->bindValue(2, $pessoa_bairro);
                                            $result88->bindValue(3, $pessoa_local);
                                            $result88->bindValue(4, $pessoa_secao);
                                            $result88->execute();
                                        } else if (
                                                $pessoa_regional != "" && $pessoa_bairro != "" && $pessoa_local == "" && $pessoa_secao != "" &&
                                                $pessoa_regional != 0 && $pessoa_bairro != 0 && $pessoa_local == 0 && $pessoa_secao != 0
                                        ) {
                                            $result88 = $db->prepare("SELECT s.NR_ZONA, lv.ID AS LOCAL_VOTACAO_ID, lv.NM_LOCAL_VOTACAO, lv.REGIONAL_ID, s.NR_SECAO, lv.CD_BAIRRO, SUM(s.QT_ELEITOR_AGREGADO) AS QT_ELEITOR_AGREGADO              
                                                                 FROM 2024_secoes AS s
                                                                 INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID  
                                                                 WHERE lv.REGIONAL_ID = ? AND lv.CD_BAIRRO = ? AND s.NR_SECAO = ? AND s.TIPO = 'Principal'
                                                                 GROUP BY s.ID
                                                                 ORDER BY s.NR_SECAO ASC");
                                            $result88->bindValue(1, $pessoa_regional);
                                            $result88->bindValue(2, $pessoa_bairro);
                                            $result88->bindValue(3, $pessoa_secao);
                                            $result88->execute();
                                        } else if (
                                                $pessoa_regional != "" && $pessoa_bairro != "" && $pessoa_local != "" &&
                                                $pessoa_regional != 0 && $pessoa_bairro != 0 && $pessoa_local != 0
                                        ) {
                                            $result88 = $db->prepare("SELECT s.NR_ZONA, lv.ID AS LOCAL_VOTACAO_ID, lv.NM_LOCAL_VOTACAO, lv.REGIONAL_ID, s.NR_SECAO, lv.CD_BAIRRO, SUM(s.QT_ELEITOR_AGREGADO) AS QT_ELEITOR_AGREGADO              
                                                                 FROM 2024_secoes AS s
                                                                 INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID 
                                                                 WHERE lv.REGIONAL_ID = ? AND lv.CD_BAIRRO = ? AND lv.ID = ? AND s.TIPO = 'Principal'
                                                                 GROUP BY s.ID
                                                                 ORDER BY s.NR_SECAO ASC");
                                            $result88->bindValue(1, $pessoa_regional);
                                            $result88->bindValue(2, $pessoa_bairro);
                                            $result88->bindValue(3, $pessoa_local);
                                            $result88->execute();
                                        } else if ($pessoa_regional != "" && $pessoa_local != "" && $pessoa_regional != 0 && $pessoa_local != 0) {
                                            $result88 = $db->prepare("SELECT s.NR_ZONA, lv.ID AS LOCAL_VOTACAO_ID, lv.NM_LOCAL_VOTACAO, lv.REGIONAL_ID, s.NR_SECAO, lv.CD_BAIRRO, SUM(s.QT_ELEITOR_AGREGADO) AS QT_ELEITOR_AGREGADO        
                                                                 FROM 2024_secoes AS s
                                                                 INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID  
                                                                 WHERE lv.REGIONAL_ID = ? AND lv.ID = ? AND s.TIPO = 'Principal'
                                                                 GROUP BY s.ID
                                                                 ORDER BY s.NR_SECAO ASC");
                                            $result88->bindValue(1, $pessoa_regional);
                                            $result88->bindValue(2, $pessoa_local);
                                            $result88->execute();
                                        } else if ($pessoa_regional != "" && $pessoa_bairro != "" && $pessoa_regional != 0 && $pessoa_bairro != 0) {
                                            $result88 = $db->prepare("SELECT s.NR_ZONA, lv.ID AS LOCAL_VOTACAO_ID, lv.NM_LOCAL_VOTACAO, lv.REGIONAL_ID, s.NR_SECAO, lv.CD_BAIRRO, SUM(s.QT_ELEITOR_AGREGADO) AS QT_ELEITOR_AGREGADO              
                                                                 FROM 2024_secoes AS s
                                                                 INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID  
                                                                 WHERE lv.REGIONAL_ID = ? AND lv.CD_BAIRRO = ? AND s.TIPO = 'Principal'
                                                                 GROUP BY s.ID
                                                                 ORDER BY s.NR_SECAO ASC");
                                            $result88->bindValue(1, $pessoa_regional);
                                            $result88->bindValue(2, $pessoa_bairro);
                                            $result88->execute();
                                        } else if ($pessoa_regional != "" && $pessoa_regional != 0) {
                                            $result88 = $db->prepare("SELECT s.NR_ZONA, lv.ID AS LOCAL_VOTACAO_ID, lv.NM_LOCAL_VOTACAO, lv.REGIONAL_ID, s.NR_SECAO, lv.CD_BAIRRO, SUM(s.QT_ELEITOR_AGREGADO) AS QT_ELEITOR_AGREGADO             
                                                                 FROM 2024_secoes AS s
                                                                 INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID  
                                                                 WHERE lv.REGIONAL_ID = ? AND s.TIPO = 'Principal'
                                                                 GROUP BY s.ID
                                                                 ORDER BY s.NR_SECAO ASC");
                                            $result88->bindValue(1, $pessoa_regional);
                                            $result88->execute();
                                        } else {
                                            $result88 = $db->prepare("SELECT s.NR_ZONA, lv.ID AS LOCAL_VOTACAO_ID, lv.NM_LOCAL_VOTACAO, lv.REGIONAL_ID, s.NR_SECAO, lv.CD_BAIRRO, SUM(s.QT_ELEITOR_AGREGADO) AS QT_ELEITOR_AGREGADO        
                                                                 FROM 2024_secoes AS s
                                                                 INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID  
                                                                 WHERE s.TIPO = 'Principal' AND lv.MUNICIPIO_ID = 94
                                                                 GROUP BY s.ID 
                                                                 ORDER BY s.NR_SECAO ASC");
                                            $result88->execute();
                                        }

                                        while ($secao = $result88->fetch(PDO::FETCH_ASSOC)) {
                                            $result888 = $db->prepare("SELECT v.id, v.nome, v.celular, v.zona, v.secao_numero, v.zona_2, v.secao_numero_2, 
                                                lv.NM_LOCAL_VOTACAO AS local, r.nome AS regional, lv.NM_BAIRRO AS bairro      
                                                                       FROM 2024_voluntarios AS v 
                                                                       INNER JOIN 2024_secoes AS s ON s.NR_ZONA = v.zona AND s.NR_SECAO = v.secao_numero 
                                                                       INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID 
                                                                       INNER JOIN sys_regionais AS r ON r.id = lv.REGIONAL_ID 
                                                                       WHERE v.secao_numero_2 = ? AND v.zona_2 = ? AND v.funcao_id = 5 AND v.status = 1 
                                                                       GROUP BY v.id 
                                                                       ORDER BY v.nome ASC");
                                            $result888->bindValue(1, $secao['NR_SECAO']);
                                            $result888->bindValue(2, $secao['NR_ZONA']);
                                            $result888->execute();
                                            //}

                                            $qtd = $result888->rowCount();

                                            if ($qtd == 0) {
                                                ?>
                                                <tr class="<?= $qtd == 2 ? 'completo' : 'falta'; ?>">
                                                    <td class="col-1 text-center"><?= $secao['NR_ZONA']; ?></td>
                                                    <td class="col-1 text-center"><strong><?= $secao['NR_SECAO']; ?><?= retornar_agregacao($secao['NR_ZONA'], $secao['NR_SECAO']); ?></strong></td>
                                                    <td class="col-1 text-center"><?= pesquisar_tabela("nome", "sys_regionais", "id", "=", $secao['REGIONAL_ID'], ""); ?></td>
                                                    <td class="col-1 text-center"><?= pesquisar_tabela("NM_BAIRRO", "bsc_bairros", "ID", "=", $secao['CD_BAIRRO'], ""); ?></td>
                                                    <td class="text-center"><?= $secao['NM_LOCAL_VOTACAO']; ?></td>
                                                    <td class="col-1 text-center"><?= $secao['QT_ELEITOR_AGREGADO']; ?></td>
                                                    <td class="text-center">
                                                        <a href="" onclick="carregar_dados('<?= $secao['NR_ZONA']; ?>', '<?= $secao['NR_SECAO']; ?>', '<?= $secao['REGIONAL_ID']; ?>', '<?= $secao['LOCAL_VOTACAO_ID']; ?>', '<?= $secao['CD_BAIRRO']; ?>')" data-toggle="modal" data-target="#teste" class="text-success add_fiscal"><i class="mdi mdi-plus-circle" style="font-size: 20px;"></i></a>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="" onclick="carregar_dados('<?= $secao['NR_ZONA']; ?>', '<?= $secao['NR_SECAO']; ?>', '<?= $secao['REGIONAL_ID']; ?>', '<?= $secao['LOCAL_VOTACAO_ID']; ?>', '<?= $secao['CD_BAIRRO']; ?>')" data-toggle="modal" data-target="#teste" class="text-success add_fiscal"><i class="mdi mdi-plus-circle" style="font-size: 20px;"></i></a>
                                                    </td>
                                                </tr>
                                                <?php
                                            } else {
                                                // while ($fiscais = $result888->fetch(PDO::FETCH_ASSOC)) {
                                                ?>
                                                <tr class="<?= $qtd == 2 ? 'completo' : 'falta'; ?>">
                                                    <td class="text-center"><?= $secao['NR_ZONA']; ?></td>
                                                    <td class="text-center"><strong><?= $secao['NR_SECAO']; ?><?= retornar_agregacao($secao['NR_ZONA'], $secao['NR_SECAO']); ?></strong></td>
                                                    <td class="text-center"><?= pesquisar_tabela("nome", "sys_regionais", "id", "=", $secao['REGIONAL_ID'], ""); ?></td>
                                                    <td class="text-center"><?= pesquisar_tabela("NM_BAIRRO", "bsc_bairros", "ID", "=", $secao['CD_BAIRRO'], ""); ?></td>
                                                    <td class="text-center"><?= $secao['NM_LOCAL_VOTACAO']; ?></td>
                                                    <td class="text-center"><?= $secao['QT_ELEITOR_AGREGADO']; ?></td>

                                                    <?php
                                                    if ($qtd < 2) {
                                                        ?>
                                                        <td  class="text-center"><a href="" onclick="carregar_dados('<?= $secao['NR_ZONA']; ?>', '<?= $secao['NR_SECAO']; ?>', '<?= $secao['REGIONAL_ID']; ?>', '<?= $secao['LOCAL_VOTACAO_ID']; ?>', '<?= $secao['CD_BAIRRO']; ?>')" data-toggle="modal" data-target="#teste" class="text-success add_fiscal"><i class="mdi mdi-plus-circle" style="font-size: 20px;"></i></a></td>
                                                        <?php
                                                    }

                                                    while ($fiscais = $result888->fetch(PDO::FETCH_ASSOC)) {
                                                        ?>
                                                        <td class="text-center">
                                                            <span>
                                                                <p class="pading-distribuicao">
                                                                    <b><?= ctexto($fiscais['nome'], "mai"); ?></b>&nbsp;
                                                                    <a id="remover_fiscal" onclick="remover(<?= $fiscais['id']; ?>)" style="cursor: pointer" class="text-danger">
                                                                        <i class="mdi mdi-close-circle" style="font-size: 18px;"></i>
                                                                    </a>
                                                                </p>

                                                                <p class="pading-distribuicao"><?= $fiscais['celular']; ?></p>
                                                                <p class="pading-distribuicao"><b>Zona</b> <?= $fiscais['zona']; ?> - <b>Seção</b> <?= $fiscais['secao_numero']; ?><?= retornar_agregacao($fiscais['zona'], $fiscais['secao_numero']); ?><br/><b>LOC</b> <?= $fiscais['local']; ?><br/><b>BAI</b> <?= $fiscais['bairro']; ?><br/><b>REG</b> <?= $fiscais['regional']; ?></p>
                                                            </span>
                                                        </td>
                                                        <?php
                                                    }
                                                    ?>
                                                </tr>
                                                <?php
                                                // }
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Fim da listagem de alocação dos fiscais -->
                </div>

            </div>
        </div>
        <div style="display: none;" id="div_resultado_estadual"></div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade modal-success" id="teste" role="dialog">
    <div class="modal-dialog" role="document" style="max-width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">ADICIONAR FISCAL</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <form id="fiscal" name="fiscal" action="#" method="POST">

                    <input type="hidden" id="guard_secao" name="guard_secao" value="" />
                    <input type="hidden" id="guard_regional" name="guard_regional" value="" />
                    <input type="hidden" id="guard_local" name="guard_local" value="" />
                    <input type="hidden" id="guard_bairro" name="guard_bairro" value="" />
                    <input type="hidden" id="guard_zona" name="guard_zona" value="" />

                    <div class="row">
                        <div class="col-md-12">
                            <div id="div_pessoas" class="form-group">
                                <label>FISCAL</label>
                                <select id="pessoas" name="pessoas" class="form-control select2" style="width: 100%;">
                                    <option value="">Selecione o Fiscal de Seção</option>
                                    <?php
                                    $qtdd = 1;

                                    if (
                                            $pessoa_local != "" && $pessoa_regional != "" && $pessoa_bairro != "" &&
                                            $pessoa_local != 0 && $pessoa_regional != 0 && $pessoa_bairro != 0
                                    ) {
                                        $result8 = $db->prepare("SELECT b.REGIONAL_NOME, e2j.NM_LOCAL_VOTACAO, p.secao_numero, p.bsc_bairros_id, b.NM_BAIRRO AS bairro, p.zona, p.local_votacao, p.id, p.nome    
                                                                 FROM 2024_voluntarios AS p
                                                                 INNER JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero 
                                                                 INNER JOIN 2024_locais_votacao AS e2j ON e2j.ID = s.LOCAL_VOTACAO_ID 
                                                                 INNER JOIN bsc_bairros AS b ON b.ID = e2j.CD_BAIRRO   
                                                                 WHERE p.deficiencia IN(3, 2) AND e2j.ID = ? AND e2j.REGIONAL_ID = ? AND p.bsc_bairros_id = ? AND p.tipo = 2 AND p.funcao_id = 5 AND p.secao_numero_2 IS NULL AND p.status = 1 AND e2j.MUNICIPIO_ID = 94 OR
                                                                 p.deficiencia IN(3, 2) AND e2j.ID = ? AND e2j.REGIONAL_ID = ? AND p.bsc_bairros_id = ? AND p.tipo = 2 AND p.funcao_id = 5 AND p.secao_numero_2 = ' ' AND p.status = 1 AND e2j.MUNICIPIO_ID = 94      
                                                                 ORDER BY p.data_cadastro ASC");
                                        $result8->bindValue(1, $pessoa_local);
                                        $result8->bindValue(2, $pessoa_regional);
                                        $result8->bindValue(3, $pessoa_bairro);
                                        $result8->bindValue(4, $pessoa_local);
                                        $result8->bindValue(5, $pessoa_regional);
                                        $result8->bindValue(6, $pessoa_bairro);
                                        $result8->execute();
                                    } else if ($pessoa_local != "" && $pessoa_regional != "" && $pessoa_local != 0 && $pessoa_regional != 0) {
                                        $result8 = $db->prepare("SELECT b.REGIONAL_NOME, e2j.NM_LOCAL_VOTACAO, p.secao_numero, p.bsc_bairros_id, b.NM_BAIRRO AS bairro, p.zona, p.local_votacao, p.id, p.nome    
                                                                 FROM 2024_voluntarios AS p
                                                                 INNER JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero 
                                                                 INNER JOIN 2024_locais_votacao AS e2j ON e2j.ID = s.LOCAL_VOTACAO_ID 
                                                                 INNER JOIN bsc_bairros AS b ON b.ID = e2j.CD_BAIRRO   
                                                                 WHERE p.deficiencia IN(3, 2) AND e2j.ID = ? AND e2j.REGIONAL_ID = ? AND p.tipo = 2 AND p.funcao_id = 5 AND p.secao_numero_2 IS NULL AND p.status = 1 AND e2j.MUNICIPIO_ID = 94 OR
                                                                 p.deficiencia IN(3, 2) AND e2j.ID = ? AND e2j.REGIONAL_ID = ? AND p.tipo = 2 AND p.funcao_id = 5 AND p.secao_numero_2 = ' ' AND p.status = 1 AND e2j.MUNICIPIO_ID = 94      
                                                                 ORDER BY p.data_cadastro ASC");
                                        $result8->bindValue(1, $pessoa_local);
                                        $result8->bindValue(2, $pessoa_regional);
                                        $result8->bindValue(3, $pessoa_local);
                                        $result8->bindValue(4, $pessoa_regional);
                                        $result8->execute();
                                    } else if ($pessoa_bairro != "" && $pessoa_regional != "") {
                                        $result8 = $db->prepare("SELECT b.REGIONAL_NOME, e2j.NM_LOCAL_VOTACAO, p.secao_numero, p.bsc_bairros_id, b.NM_BAIRRO AS bairro, p.zona, p.local_votacao, p.id, p.nome    
                                                                 FROM 2024_voluntarios AS p
                                                                 INNER JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero 
                                                                 INNER JOIN 2024_locais_votacao AS e2j ON e2j.ID = s.LOCAL_VOTACAO_ID 
                                                                 INNER JOIN bsc_bairros AS b ON b.ID = e2j.CD_BAIRRO  
                                                                 WHERE p.deficiencia IN(3, 2) AND p.bsc_bairros_id = ? AND e2j.REGIONAL_ID = ? AND p.tipo = 2 AND p.funcao_id = 5 AND p.secao_numero_2 IS NULL AND p.status = 1 AND e2j.MUNICIPIO_ID = 94 OR
                                                                 p.deficiencia IN(3, 2) AND p.bsc_bairros_id = ? AND e2j.REGIONAL_ID = ? AND p.tipo = 2 AND p.funcao_id = 5 AND p.secao_numero_2 = ' ' AND p.status = 1 AND e2j.MUNICIPIO_ID = 94      
                                                                 ORDER BY p.data_cadastro ASC");
                                        $result8->bindValue(1, $pessoa_bairro);
                                        $result8->bindValue(2, $pessoa_regional);
                                        $result8->bindValue(3, $pessoa_bairro);
                                        $result8->bindValue(4, $pessoa_regional);
                                        $result8->execute();
                                    } else if ($pessoa_local == "" && $pessoa_regional != "" && $pessoa_local == 0 && $pessoa_regional != 0) {
                                        $result8 = $db->prepare("SELECT b.REGIONAL_NOME, e2j.NM_LOCAL_VOTACAO, p.secao_numero, p.bsc_bairros_id, b.NM_BAIRRO AS bairro, p.zona, p.local_votacao, p.id, p.nome    
                                                                 FROM 2024_voluntarios AS p
                                                                 INNER JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero 
                                                                 INNER JOIN 2024_locais_votacao AS e2j ON e2j.ID = s.LOCAL_VOTACAO_ID 
                                                                 INNER JOIN bsc_bairros AS b ON b.ID = e2j.CD_BAIRRO   
                                                                 WHERE p.deficiencia IN(3, 2) AND e2j.REGIONAL_ID = ? AND p.tipo = 2 AND p.funcao_id = 5 AND p.secao_numero_2 IS NULL AND p.status = 1 AND e2j.MUNICIPIO_ID = 94 OR
                                                                 p.deficiencia IN(3, 2) AND e2j.REGIONAL_ID = ? AND p.tipo = 2 AND p.funcao_id = 5 AND p.secao_numero_2 = ' ' AND p.status = 1 AND e2j.MUNICIPIO_ID = 94      
                                                                 ORDER BY p.data_cadastro ASC");
                                        $result8->bindValue(1, $pessoa_regional);
                                        $result8->bindValue(2, $pessoa_regional);
                                        $result8->execute();
                                    } else {
                                        $result8 = $db->prepare("SELECT b.REGIONAL_NOME, e2j.NM_LOCAL_VOTACAO, p.secao_numero, p.bsc_bairros_id, b.NM_BAIRRO AS bairro, p.zona, p.local_votacao, p.id, p.nome    
                                                                 FROM 2024_voluntarios AS p
                                                                 INNER JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero 
                                                                 INNER JOIN 2024_locais_votacao AS e2j ON e2j.ID = s.LOCAL_VOTACAO_ID 
                                                                 INNER JOIN bsc_bairros AS b ON b.ID = e2j.CD_BAIRRO  
                                                                 WHERE p.deficiencia IN(3, 2) AND p.tipo = 2 AND p.funcao_id = 5 AND p.secao_numero_2 IS NULL AND p.status = 1 AND e2j.MUNICIPIO_ID = 94 OR
                                                                 p.deficiencia IN(3, 2) AND p.tipo = 2 AND p.funcao_id = 5 AND p.secao_numero_2 = ' ' AND p.status = 1 AND e2j.MUNICIPIO_ID = 94      
                                                                 ORDER BY p.data_cadastro ASC");
                                        $result8->execute();
                                    }

                                    while ($pessoa = $result8->fetch(PDO::FETCH_ASSOC)) {

                                        $indicadores = vf_indicacao($pessoa['id']);
                                        ?>
                                    <option zona="<?= $pessoa['zona']; ?>" secao="<?= $pessoa['secao_numero']; ?>" local="<?= $pessoa['local_votacao']; ?>" value='<?= $pessoa['id']; ?>'><?= $qtdd; ?> - <?= $pessoa['REGIONAL_NOME'] != "" ? "REGIONAL " . $pessoa['REGIONAL_NOME'] : ""; ?> - <?= is_numeric($pessoa['bsc_bairros_id']) ? "BAIRRO " . $pessoa['bairro'] : ""; ?> - <?= $pessoa['NM_LOCAL_VOTACAO'] != "" ? "LDV " . $pessoa['NM_LOCAL_VOTACAO'] : ""; ?> - <?= ctexto($pessoa['nome'], 'mai'); ?> - ZONA <?= $pessoa['zona']; ?> - SEÇÃO <?= $pessoa['secao_numero']; ?></option>
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

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/fiscal/distribuicao.js"></script>