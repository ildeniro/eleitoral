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
                        <h2 class="pageheader-title">Distribuição de Volutários </h2>
                        <div class="page-breadcrumb">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/admin/dashboard" class="breadcrumb-link">Início</a></li>
                                    <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/fiscal/index" class="breadcrumb-link">Fiscalização</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Distribuição</li>
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
                                        <a <?= ver_nivel(1) ? "" : "style='display: none;'"; ?> href="#" id="gerar" onclick="gerar()" class="btn btn-outline-success btn-lg" style="<?= $configuracoes == 1 ? "display: none;" : ""; ?>"><i class="fas fa-sitemap mr-1"></i>Distribuir</a>
                                        <a <?= ver_nivel(1) ? "" : "style='display: none;'"; ?> href="#" id="desfazer" onclick="desfazer()" class="btn btn-outline-danger btn-lg" style="<?= $configuracoes == 0 ? "display: none;" : ""; ?>"><i class="fas fa-trash mr-1"></i>Remover Distribuição</a>
                                    </div>
                                </div>

                                <div id='div_distribuicao_geral_supervisor' <?= is_numeric($pessoa_tipo_voluntario_guard) && $pessoa_tipo_voluntario_guard == 4 ? "" : "style='display: none;'" ?> class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
                                    <div class="float-xl-left float-none">
                                        <a <?= ver_nivel(1) ? "" : "style='display: none;'"; ?> href="#" id="gerar" onclick="gerar_supervisor()" class="btn btn-outline-success btn-lg" style="<?= $configuracoes_supervisor == 1 ? "display: none;" : ""; ?>"><i class="fas fa-sitemap mr-1"></i>Distribuir</a>
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
                    <div class="card px-3">
                        <div class="card-header" id="headingSeven1">
                            <h3 class="mb-0">
                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapseSeven1" aria-expanded="true" aria-controls="collapseSeven1">
                                    <span class="fas fa-angle-down mr-3"></span>FILTROS
                                </button>
                            </h3>
                        </div>
                        <div id="collapseSeven1" class="collapse" aria-labelledby="headingSeven1" data-parent="#accordion3">
                            <div class="card-body">
                                <div class="row">
                                    <div id="div_fiscais" <?= is_numeric($pessoa_tipo_voluntario_guard) && $pessoa_tipo_voluntario_guard == 5 ? "" : "style='display: none;'" ?> class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <div class="card">
                                            <!--                        <h5 class="card-header mt-3 px-3">FISCAIS</h5>-->
                                            <div class="card-body">
                                                <form id="filtros" name="filtros" action="#" method="GET">

                                                    <input type="hidden" id="tipo_voluntario_guard" name="tipo_voluntario_guard" value="<?= $pessoa_tipo_voluntario_guard; ?>" />
                                                    <input type="hidden" id="tipo_eleicao_guard" name="tipo_eleicao_guard" value="<?= $pessoa_tipo_eleicao_guard; ?>" />

                                                    <div class="form-row">
                                                        <div class="col-xl-2 col-lg-2 col-md-12 col-sm-12 col-12 ">
                                                            <label for="validationCustomUsername">REGIONAL</label>
                                                            <div class="input-group">
                                                                <select class="form-control" id="regional" name="regional">
                                                                    <option value="">Todas as regionais</option>
                                                                    <?php
                                                                    $result8 = $db->prepare("SELECT nome, id 
                                                                         FROM sys_regionais
                                                                         WHERE 1 
                                                                         ORDER BY nome ASC");
                                                                    $result8->execute();
                                                                    while ($regional = $result8->fetch(PDO::FETCH_ASSOC)) {
                                                                        if ($pessoa_regional == $regional['id']) {
                                                                    ?>
                                                                            <option selected="true" value='<?= $regional['id']; ?>'><?= $regional['nome']; ?></option>
                                                                        <?php
                                                                        } else {
                                                                        ?>
                                                                            <option value='<?= $regional['id']; ?>'><?= $regional['nome']; ?></option>
                                                                    <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12 ">
                                                            <label for="validationCustomUsername">BAIRRO</label>
                                                            <div class="input-group">
                                                                <select class="form-control" id="bairro" name="bairro">
                                                                    <option value="">Todos os bairros</option>
                                                                    <?php
                                                                    $result8 = $db->prepare("SELECT ID, NM_BAIRRO 
                                                                         FROM bsc_bairros 
                                                                         WHERE 1 
                                                                         GROUP BY NM_BAIRRO 
                                                                         ORDER BY NM_BAIRRO ASC");
                                                                    $result8->execute();
                                                                    while ($bairro = $result8->fetch(PDO::FETCH_ASSOC)) {
                                                                        if ($pessoa_bairro == $bairro['ID']) {
                                                                    ?>
                                                                            <option selected="true" value='<?= $bairro['ID']; ?>'><?= $bairro['NM_BAIRRO']; ?></option>
                                                                            <?php
                                                                        }
                                                                    }

                                                                    if ($pessoa_regional != "" && $pessoa_regional != 0) {
                                                                        $result82 = $db->prepare("SELECT ID, NM_BAIRRO  
                                                                              FROM bsc_bairros 
                                                                              WHERE REGIONAL_ID = ?
                                                                              GROUP BY NM_BAIRRO 
                                                                              ORDER BY NM_BAIRRO ASC");
                                                                        $result82->bindValue(1, $pessoa_regional);
                                                                        $result82->execute();
                                                                        while ($bairro2 = $result82->fetch(PDO::FETCH_ASSOC)) {
                                                                            if ($pessoa_bairro != $bairro2['ID']) {
                                                                            ?>
                                                                                <option value='<?= $bairro2['ID']; ?>'><?= $bairro2['NM_BAIRRO']; ?></option>
                                                                    <?php
                                                                            }
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12 ">
                                                            <label for="validationCustomUsername">LOCAL</label>
                                                            <div class="input-group">
                                                                <select class="form-control" id="local" name="local">
                                                                    <option value="">Todos os locais</option>
                                                                    <?php
                                                                    $result81 = $db->prepare("SELECT lv.ID, lv.NM_LOCAL_VOTACAO 
                                                                          FROM 2024_locais_votacao AS lv 
                                                                          INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = lv.ID 
                                                                          WHERE s.TIPO = 'Principal' 
                                                                          GROUP BY lv.NM_LOCAL_VOTACAO  
                                                                          ORDER BY lv.NM_LOCAL_VOTACAO  ASC");
                                                                    $result81->execute();
                                                                    while ($local = $result81->fetch(PDO::FETCH_ASSOC)) {
                                                                        if ($pessoa_local == $local['ID']) {
                                                                    ?>
                                                                            <option selected="true" value='<?= $local['ID']; ?>'><?= $local['NM_LOCAL_VOTACAO']; ?></option>
                                                                            <?php
                                                                        }
                                                                    }

                                                                    if ($pessoa_regional != "" && $pessoa_bairro != "" && $pessoa_regional != 0 && $pessoa_bairro != 0) {
                                                                        $result822 = $db->prepare("SELECT lv.ID, lv.NM_LOCAL_VOTACAO   
                                                                               FROM 2024_locais_votacao AS lv 
                                                                               INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = lv.ID 
                                                                               WHERE lv.REGIONAL_ID = ? AND lv.CD_BAIRRO = ? AND s.TIPO = 'Principal'  
                                                                               GROUP BY lv.NM_LOCAL_VOTACAO  
                                                                               ORDER BY lv.NM_LOCAL_VOTACAO ASC");
                                                                        $result822->bindValue(1, $pessoa_regional);
                                                                        $result822->bindValue(2, $pessoa_bairro);
                                                                        $result822->execute();
                                                                        while ($local2 = $result822->fetch(PDO::FETCH_ASSOC)) {
                                                                            if ($pessoa_local != $local2['ID']) {
                                                                            ?>
                                                                                <option value='<?= $local2['ID']; ?>'><?= $local2['NM_LOCAL_VOTACAO']; ?></option>
                                                                            <?php
                                                                            }
                                                                        }
                                                                    } else if ($pessoa_regional != "" && $pessoa_regional != 0) {
                                                                        $result822 = $db->prepare("SELECT lv.ID, lv.NM_LOCAL_VOTACAO 
                                                                               FROM 2024_locais_votacao AS lv 
                                                                               INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = lv.ID 
                                                                               WHERE lv.REGIONAL_ID = ? AND s.TIPO = 'Principal' 
                                                                               GROUP BY lv.NM_LOCAL_VOTACAO  
                                                                               ORDER BY lv.NM_LOCAL_VOTACAO  ASC");
                                                                        $result822->bindValue(1, $pessoa_regional);
                                                                        $result822->execute();
                                                                        while ($local3 = $result822->fetch(PDO::FETCH_ASSOC)) {
                                                                            if ($pessoa_local != $local3['ID']) {
                                                                            ?>
                                                                                <option value='<?= $local3['ID']; ?>'><?= $local3['NM_LOCAL_VOTACAO']; ?></option>
                                                                    <?php
                                                                            }
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-2 col-lg-2 col-md-12 col-sm-12 col-12 ">
                                                            <label for="validationCustomUsername">SEÇÃO</label>
                                                            <div class="input-group">
                                                                <select class="form-control" id="secao" name="secao">
                                                                    <option value="">Todas as seções</option>
                                                                    <?php
                                                                    if ($pessoa_secao != "" && $pessoa_secao != 0) {
                                                                        $result83 = $db->prepare("SELECT NR_SECAO, ID 
                                                                              FROM 2024_secoes 
                                                                              WHERE NR_SECAO = ? AND TIPO = 'Principal'   
                                                                              GROUP BY NR_SECAO 
                                                                              ORDER BY NR_SECAO ASC");
                                                                        $result83->bindValue(1, $pessoa_secao);
                                                                        $result83->execute();
                                                                        while ($secao = $result83->fetch(PDO::FETCH_ASSOC)) {
                                                                            if ($pessoa_secao == $secao['NR_SECAO']) {
                                                                    ?>
                                                                                <option selected="true" value='<?= $secao['NR_SECAO']; ?>'><?= $secao['NR_SECAO']; ?></option>
                                                                            <?php
                                                                            }
                                                                        }

                                                                        $result83 = $db->prepare("SELECT NR_SECAO, ID 
                                                                              FROM 2024_secoes 
                                                                              WHERE LOCAL_VOTACAO_ID = ? AND NR_SECAO <> ? AND TIPO = 'Principal'   
                                                                              GROUP BY NR_SECAO 
                                                                              ORDER BY NR_SECAO ASC");
                                                                        $result83->bindValue(1, $pessoa_local);
                                                                        $result83->bindValue(2, $pessoa_secao);
                                                                        $result83->execute();
                                                                        while ($secao = $result83->fetch(PDO::FETCH_ASSOC)) {
                                                                            ?>
                                                                            <option value='<?= $secao['NR_SECAO']; ?>'><?= $secao['NR_SECAO']; ?></option>
                                                                            <?php
                                                                        }
                                                                    } else if ($pessoa_local != "" && $pessoa_local != 0) {
                                                                        $result83 = $db->prepare("SELECT NR_SECAO, ID 
                                                                              FROM 2024_secoes 
                                                                              WHERE LOCAL_VOTACAO_ID = ? AND TIPO = 'Principal'   
                                                                              GROUP BY NR_SECAO 
                                                                              ORDER BY NR_SECAO ASC");
                                                                        $result83->bindValue(1, $pessoa_local);
                                                                        $result83->execute();
                                                                        while ($secao = $result83->fetch(PDO::FETCH_ASSOC)) {
                                                                            if ($pessoa_secao == $secao['NR_SECAO']) {
                                                                            ?>
                                                                                <option selected="true" value='<?= $secao['NR_SECAO']; ?>'><?= $secao['NR_SECAO']; ?></option>
                                                                            <?php
                                                                            } else {
                                                                            ?>
                                                                                <option value='<?= $secao['NR_SECAO']; ?>'><?= $secao['NR_SECAO']; ?></option>
                                                                            <?php
                                                                            }
                                                                        }
                                                                    } else if ($pessoa_bairro != "" && $pessoa_bairro != 0) {
                                                                        $result83 = $db->prepare("SELECT s.NR_SECAO, s.ID 
                                                                              FROM 2024_secoes AS s
                                                                              INNER JOIN 2024_locais_votacao AS v ON s.LOCAL_VOTACAO_ID = v.ID 
                                                                              WHERE v.CD_BAIRRO = ? AND s.TIPO = 'Principal'   
                                                                              GROUP BY s.NR_SECAO 
                                                                              ORDER BY s.NR_SECAO ASC");
                                                                        $result83->bindValue(1, $pessoa_bairro);
                                                                        $result83->execute();
                                                                        while ($secao = $result83->fetch(PDO::FETCH_ASSOC)) {
                                                                            if ($pessoa_secao == $secao['NR_SECAO']) {
                                                                            ?>
                                                                                <option selected="true" value='<?= $secao['NR_SECAO']; ?>'><?= $secao['NR_SECAO']; ?></option>
                                                                            <?php
                                                                            } else {
                                                                            ?>
                                                                                <option value='<?= $secao['NR_SECAO']; ?>'><?= $secao['NR_SECAO']; ?></option>
                                                                    <?php
                                                                            }
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- FIM FILTRO FISCAIS -->

                                <!-- FILTROS COORDENADOR DE LOCA DE VOTAÇÃO -->
                                <div id="div_supervisores" <?= is_numeric($pessoa_tipo_voluntario_guard) && $pessoa_tipo_voluntario_guard == 4 ? "" : "style='display: none;'" ?> class="row">
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <div class="card">
                                            <!--                        <h5 class="card-header mt-3 px-3">SUPERVISORES</h5>-->
                                            <div class="card-body">
                                                <form id="filtros2" name="filtros2" action="#" method="GET">

                                                    <input type="hidden" id="tipo_voluntario_guard" name="tipo_voluntario_guard" value="<?= $pessoa_tipo_voluntario_guard; ?>" />
                                                    <input type="hidden" id="tipo_eleicao_guard" name="tipo_eleicao_guard" value="<?= $pessoa_tipo_eleicao_guard; ?>" />

                                                    <div class="form-row">
                                                        <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12 ">
                                                            <label for="validationCustomUsername">REGIONAL</label>
                                                            <div class="input-group">
                                                                <select class="form-control" id="regional2" name="regional2">
                                                                    <option value="">Todas as regionais</option>
                                                                    <?php
                                                                    $result82 = $db->prepare("SELECT nome, id 
                                                                         FROM sys_regionais
                                                                         WHERE 1 
                                                                         ORDER BY nome ASC");
                                                                    $result82->execute();
                                                                    while ($regional2 = $result82->fetch(PDO::FETCH_ASSOC)) {
                                                                        if ($pessoa_regional_2 == $regional2['id']) {
                                                                    ?>
                                                                            <option selected="true" value='<?= $regional2['id']; ?>'><?= $regional2['nome']; ?></option>
                                                                        <?php
                                                                        } else {
                                                                        ?>
                                                                            <option value='<?= $regional2['id']; ?>'><?= $regional2['nome']; ?></option>
                                                                    <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12 ">
                                                            <label for="validationCustomUsername">BAIRRO</label>
                                                            <div class="input-group">
                                                                <select class="form-control" id="bairro2" name="bairro2">
                                                                    <option value="">Todos os bairros</option>
                                                                    <?php
                                                                    $result82 = $db->prepare("SELECT ID, NM_BAIRRO 
                                                                         FROM bsc_bairros 
                                                                         WHERE 1 
                                                                         GROUP BY NM_BAIRRO 
                                                                         ORDER BY NM_BAIRRO ASC");
                                                                    $result82->execute();
                                                                    while ($bairro = $result82->fetch(PDO::FETCH_ASSOC)) {
                                                                        if ($pessoa_bairro_2 == $bairro['ID']) {
                                                                    ?>
                                                                            <option selected="true" value='<?= $bairro['ID']; ?>'><?= $bairro['NM_BAIRRO']; ?></option>
                                                                            <?php
                                                                        }
                                                                    }

                                                                    if ($pessoa_regional_2 != "" && $pessoa_regional_2 != 0) {
                                                                        $result822 = $db->prepare("SELECT ID, NM_BAIRRO  
                                                                              FROM bsc_bairros 
                                                                              WHERE REGIONAL_ID = ?
                                                                              GROUP BY NM_BAIRRO 
                                                                              ORDER BY NM_BAIRRO ASC");
                                                                        $result822->bindValue(1, $pessoa_regional_2);
                                                                        $result822->execute();
                                                                        while ($bairro2 = $result822->fetch(PDO::FETCH_ASSOC)) {
                                                                            if ($pessoa_bairro_2 != $bairro2['ID']) {
                                                                            ?>
                                                                                <option value='<?= $bairro2['ID']; ?>'><?= $bairro2['NM_BAIRRO']; ?></option>
                                                                    <?php
                                                                            }
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12 ">
                                                            <label for="validationCustomUsername">LOCAL</label>
                                                            <div class="input-group">
                                                                <select class="form-control2" id="local2" name="local2">
                                                                    <option value="">Todos os locais</option>
                                                                    <?php
                                                                    $result812 = $db->prepare("SELECT lv.ID, lv.NM_LOCAL_VOTACAO 
                                                                           FROM 2024_locais_votacao AS lv 
                                                                           INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = lv.ID 
                                                                           WHERE s.TIPO = 'Principal'  
                                                                           GROUP BY lv.NM_LOCAL_VOTACAO 
                                                                           ORDER BY lv.NM_LOCAL_VOTACAO ASC");
                                                                    $result812->execute();
                                                                    while ($local2 = $result812->fetch(PDO::FETCH_ASSOC)) {
                                                                        if ($pessoa_local_2 == $local2['ID']) {
                                                                    ?>
                                                                            <option selected="true" value='<?= $local2['ID']; ?>'><?= $local2['NM_LOCAL_VOTACAO']; ?></option>
                                                                            <?php
                                                                        }
                                                                    }

                                                                    if ($pessoa_regional_2 != "" && $pessoa_bairro_2 != "" && $pessoa_regional_2 != 0 && $pessoa_bairro_2 != 0) {
                                                                        $result8223 = $db->prepare("SELECT lv.ID, lv.NM_LOCAL_VOTACAO   
                                                                                FROM 2024_locais_votacao AS lv 
                                                                                INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = lv.ID 
                                                                                WHERE lv.REGIONAL_ID = ? AND lv.CD_BAIRRO = ? AND s.TIPO = 'Principal'  
                                                                                GROUP BY lv.NM_LOCAL_VOTACAO 
                                                                                ORDER BY lv.NM_LOCAL_VOTACAO ASC");
                                                                        $result8223->bindValue(1, $pessoa_regional_2);
                                                                        $result8223->bindValue(2, $pessoa_bairro_2);
                                                                        $result8223->execute();
                                                                        while ($local2 = $result8223->fetch(PDO::FETCH_ASSOC)) {
                                                                            if ($pessoa_local_2 != $local2['ID']) {
                                                                            ?>
                                                                                <option value='<?= $local2['ID']; ?>'><?= $local2['NM_LOCAL_VOTACAO']; ?></option>
                                                                            <?php
                                                                            }
                                                                        }
                                                                    } else if ($pessoa_regional_2 != "" && $pessoa_regional_2 != 0) {
                                                                        $result8222 = $db->prepare("SELECT lv.ID, lv.NM_LOCAL_VOTACAO 
                                                                                FROM 2024_locais_votacao AS lv 
                                                                                INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = lv.ID
                                                                                WHERE lv.REGIONAL_ID = ? AND s.TIPO = 'Principal' 
                                                                                GROUP BY lv.NM_LOCAL_VOTACAO 
                                                                                ORDER BY lv.NM_LOCAL_VOTACAO ASC");
                                                                        $result8222->bindValue(1, $pessoa_regional_2);
                                                                        $result8222->execute();
                                                                        while ($local32 = $result8222->fetch(PDO::FETCH_ASSOC)) {
                                                                            if ($pessoa_local_2 != $local32['ID']) {
                                                                            ?>
                                                                                <option value='<?= $local32['ID']; ?>'><?= $local32['NM_LOCAL_VOTACAO']; ?></option>
                                                                            <?php
                                                                            }
                                                                        }
                                                                    } else {
                                                                        $result8222 = $db->prepare("SELECT lv.ID, lv.NM_LOCAL_VOTACAO  
                                                                                FROM 2024_locais_votacao AS lv 
                                                                                INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = lv.ID
                                                                                WHERE s.TIPO = 'Principal'  
                                                                                GROUP BY lv.NM_LOCAL_VOTACAO 
                                                                                ORDER BY lv.NM_LOCAL_VOTACAO ASC");
                                                                        $result8222->execute();
                                                                        while ($local32 = $result8222->fetch(PDO::FETCH_ASSOC)) {
                                                                            if ($pessoa_local_2 != $local32['ID']) {
                                                                            ?>
                                                                                <option value='<?= $local32['ID']; ?>'><?= $local32['NM_LOCAL_VOTACAO']; ?></option>
                                                                    <?php
                                                                            }
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- FIM FILTRO COORDENADOR DE LOCA DE VOTAÇÃO -->

                                <!-- FILTROS ADVOGADOS -->
                                <div id="div_advogados" style="display: none;" class="row">
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <div class="card">
                                            <h5 class="card-header mt-3 px-3">ADVOGADOS</h5>
                                            <div class="card-body">
                                                <form action="">
                                                    <div class="form-row">
                                                        <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12 ">
                                                            <label for="validationCustomUsername">REGIONAL</label>
                                                            <div class="input-group">
                                                                <select class="form-control" id="input-select">
                                                                    <option>Todas as regionais</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- FIM FILTRO ADVOGADOS -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div id="div_fiscais_card" <?= is_numeric($pessoa_tipo_voluntario_guard) && $pessoa_tipo_voluntario_guard == 5 ? "" : "style='display: none;'" ?> class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card px-3">
                        <div class="card-header" id="headingSeven">
                            <h3 class="mb-0">
                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapseSeven" aria-expanded="true" aria-controls="collapseSeven">
                                    <span class="fas fa-angle-down mr-3"></span>REGIONAL - SEÇÕES
                                </button>
                            </h3>
                        </div>
                        <div id="collapseSeven" class="collapse" aria-labelledby="headingSeven" data-parent="#accordion3">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="card-body">
                                    <div class="accrodion-regular">
                                        <div id="accordion4">
                                            <div class="row">
                                                <?php
                                                if ($pessoa_regional != "" && $pessoa_regional != 0) {
                                                    $resultx = $db->prepare("SELECT lv.REGIONAL_ID, s.NR_SECAO     
                                                                     FROM 2024_locais_votacao AS lv 
                                                                     INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = lv.ID 
                                                                     WHERE lv.REGIONAL_ID = ? AND s.TIPO = 'Principal'  
                                                                     GROUP BY lv.REGIONAL_ID 
                                                                     ORDER BY s.NR_SECAO ASC");
                                                    $resultx->bindValue(1, $pessoa_regional);
                                                    $resultx->execute();
                                                } else {
                                                    $resultx = $db->prepare("SELECT lv.REGIONAL_ID, s.NR_SECAO     
                                                                     FROM 2024_locais_votacao AS lv 
                                                                     INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = lv.ID 
                                                                     WHERE lv.MUNICIPIO_ID = 94 AND s.TIPO = 'Principal'  
                                                                     GROUP BY lv.REGIONAL_ID 
                                                                     ORDER BY s.NR_SECAO ASC");
                                                    $resultx->execute();
                                                }

                                                while ($regionais = $resultx->fetch(PDO::FETCH_ASSOC)) {
                                                ?>

                                                    <div class="card col-xl-4 col-lg-4">
                                                        <div class="card-header bg-success" id="headingTen">
                                                            <h5 class="mb-0">
                                                                <button class="btn btn-link text-white" data-toggle="collapse" data-target="#collapseTen<?= $regionais['REGIONAL_ID']; ?>" aria-expanded="true" aria-controls="collapseTen<?= $regionais['REGIONAL_ID']; ?>">
                                                                    <span class="fas fa-angle-down mr-3"></span><?= pesquisar_tabela("nome", "sys_regionais", "id", "=", $regionais['REGIONAL_ID'], ""); ?>
                                                                </button>
                                                            </h5>
                                                        </div>
                                                        <div id="collapseTen<?= $regionais['REGIONAL_ID']; ?>" class="collapse" aria-labelledby="headingTen" data-parent="#accordion4">
                                                            <div class="card-body">
                                                                <?php
                                                                if (
                                                                    $regionais['REGIONAL_ID'] != "" && $pessoa_local != "" && $pessoa_bairro != "" && $pessoa_secao != "" &&
                                                                    $pessoa_local != 0 && $pessoa_bairro != 0 && $pessoa_secao != 0
                                                                ) {
                                                                    $result88888 = $db->prepare("SELECT s.NR_SECAO     
                                                                                         FROM 2024_secoes AS s 
                                                                                         INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID 
                                                                                         WHERE lv.REGIONAL_ID = ? AND s.LOCAL_VOTACAO_ID = ? AND lv.CD_BAIRRO = ? AND s.NR_SECAO = ? AND s.TIPO = 'Principal' 
                                                                                         ORDER BY s.NR_SECAO ASC");
                                                                    $result88888->bindValue(1, $regionais['REGIONAL_ID']);
                                                                    $result88888->bindValue(2, $pessoa_local);
                                                                    $result88888->bindValue(3, $pessoa_bairro);
                                                                    $result88888->bindValue(4, $pessoa_secao);
                                                                    $result88888->execute();
                                                                } else if (
                                                                    $regionais['REGIONAL_ID'] != "" && $pessoa_local == "" && $pessoa_bairro != "" && $pessoa_secao != "" &&
                                                                    $pessoa_local == 0 && $pessoa_bairro != 0 && $pessoa_secao != 0
                                                                ) {
                                                                    $result88888 = $db->prepare("SELECT s.NR_SECAO     
                                                                                         FROM 2024_secoes AS s 
                                                                                         INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID 
                                                                                         WHERE lv.REGIONAL_ID = ? AND lv.CD_BAIRRO = ? AND s.NR_SECAO = ? AND s.TIPO = 'Principal' 
                                                                                         ORDER BY s.NR_SECAO ASC");
                                                                    $result88888->bindValue(1, $regionais['REGIONAL_ID']);
                                                                    $result88888->bindValue(2, $pessoa_bairro);
                                                                    $result88888->bindValue(3, $pessoa_secao);
                                                                    $result88888->execute();
                                                                } else if (
                                                                    $regionais['REGIONAL_ID'] != "" && $pessoa_local != "" && $pessoa_bairro != "" &&
                                                                    $pessoa_local != 0 && $pessoa_bairro != 0
                                                                ) {
                                                                    $result88888 = $db->prepare("SELECT s.NR_SECAO     
                                                                                         FROM 2024_secoes AS s 
                                                                                         INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID 
                                                                                         WHERE lv.REGIONAL_ID = ? AND s.LOCAL_VOTACAO_ID = ? AND lv.CD_BAIRRO = ? AND s.TIPO = 'Principal' 
                                                                                         ORDER BY s.NR_SECAO ASC");
                                                                    $result88888->bindValue(1, $regionais['REGIONAL_ID']);
                                                                    $result88888->bindValue(2, $pessoa_local);
                                                                    $result88888->bindValue(3, $pessoa_bairro);
                                                                    $result88888->execute();
                                                                } else if ($regionais['REGIONAL_ID'] != "" && $pessoa_local != "" && $pessoa_local != 0) {
                                                                    $result88888 = $db->prepare("SELECT s.NR_SECAO     
                                                                                         FROM 2024_secoes AS s 
                                                                                         INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID 
                                                                                         WHERE lv.REGIONAL_ID = ? AND s.LOCAL_VOTACAO_ID = ? AND s.TIPO = 'Principal' 
                                                                                         ORDER BY s.NR_SECAO ASC");
                                                                    $result88888->bindValue(1, $regionais['REGIONAL_ID']);
                                                                    $result88888->bindValue(2, $pessoa_local);
                                                                    $result88888->execute();
                                                                } else if ($regionais['REGIONAL_ID'] != "" && $pessoa_bairro != "" && $pessoa_bairro != 0) {
                                                                    $result88888 = $db->prepare("SELECT s.NR_SECAO     
                                                                                         FROM 2024_secoes AS s 
                                                                                         INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID 
                                                                                         WHERE lv.REGIONAL_ID = ? AND lv.CD_BAIRRO = ? AND s.TIPO = 'Principal' 
                                                                                         ORDER BY s.NR_SECAO ASC");
                                                                    $result88888->bindValue(1, $regionais['REGIONAL_ID']);
                                                                    $result88888->bindValue(2, $pessoa_bairro);
                                                                    $result88888->execute();
                                                                } else if ($regionais['REGIONAL_ID'] != "") {
                                                                    $result88888 = $db->prepare("SELECT s.NR_SECAO     
                                                                                         FROM 2024_secoes AS s 
                                                                                         INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID 
                                                                                         WHERE lv.REGIONAL_ID = ? AND s.TIPO = 'Principal' 
                                                                                         ORDER BY s.NR_SECAO ASC");
                                                                    $result88888->bindValue(1, $regionais['REGIONAL_ID']);
                                                                    $result88888->execute();
                                                                } else {
                                                                    $result88888 = $db->prepare("SELECT NR_SECAO     
                                                                                         FROM 2024_secoes 
                                                                                         WHERE TIPO = 'Principal' 
                                                                                         ORDER BY NR_SECAO ASC");
                                                                    $result88888->execute();
                                                                }

                                                                while ($secao88888 = $result88888->fetch(PDO::FETCH_ASSOC)) {

                                                                    if ($regionais['REGIONAL_ID'] != "" && $pessoa_local != "" && $pessoa_local != 0) {
                                                                        $result99 = $db->prepare("SELECT id, nome   
                                                                                                      FROM 2024_voluntarios 
                                                                                                      WHERE regional_2 = ? AND local_votacao_2 = ? AND secao_numero_2 = ? AND funcao_id = 5 AND status = 1   
                                                                                                      ORDER BY nome ASC");
                                                                        $result99->bindValue(1, $regionais['REGIONAL_ID']);
                                                                        $result99->bindValue(2, $pessoa_local);
                                                                        $result99->bindValue(3, $secao88888['NR_SECAO']);
                                                                        $result99->execute();
                                                                    } else if ($regionais['REGIONAL_ID'] != "") {
                                                                        $result99 = $db->prepare("SELECT id, nome   
                                                                                                     FROM 2024_voluntarios 
                                                                                                     WHERE regional_2 = ? AND secao_numero_2 = ? AND funcao_id = 5 AND status = 1   
                                                                                                     ORDER BY nome ASC");
                                                                        $result99->bindValue(1, $regionais['REGIONAL_ID']);
                                                                        $result99->bindValue(2, $secao88888['NR_SECAO']);
                                                                        $result99->execute();
                                                                    } else {
                                                                        $result99 = $db->prepare("SELECT id, nome   
                                                                                                      FROM 2024_voluntarios 
                                                                                                      WHERE secao_numero_2 = ? AND funcao_id = 5 AND status = 1  
                                                                                                      ORDER BY nome ASC");
                                                                        $result99->bindValue(1, $secao88888['NR_SECAO']);
                                                                        $result99->execute();
                                                                    }

                                                                    if ($result99->rowCount() == 0) {
                                                                ?>
                                                                        <span class="badge badge-danger"><?= $secao88888['NR_SECAO']; ?></span>
                                                                    <?php
                                                                    } else if ($result99->rowCount() == 1) {
                                                                    ?>
                                                                        <span class="badge badge-info"><?= $secao88888['NR_SECAO']; ?></span>
                                                                    <?php
                                                                    } else if ($result99->rowCount() >= 2) {
                                                                    ?>
                                                                        <span class="badge badge-success"><?= $secao88888['NR_SECAO']; ?></span>
                                                                <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                <?php
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
                <div id="div_supervisor_regionais" <?= is_numeric($pessoa_tipo_voluntario_guard) && $pessoa_tipo_voluntario_guard == 4 ? "" : "style='display: none;'" ?> class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card px-3">
                        <div class="card-header" id="headingSeven">
                            <h3 class="mb-0">
                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapseSeven2" aria-expanded="true" aria-controls="collapseSeven2">
                                    <span class="fas fa-angle-down mr-3"></span>REGIONAIS
                                </button>
                            </h3>
                        </div>
                        <div id="collapseSeven2" class="collapse" aria-labelledby="headingSeven" data-parent="#accordion3">
                            <div class="card-body">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <ul class="list-group">
                                            <div class="row">

                                                <?php
                                                if ($pessoa_regional_2 != "" && $pessoa_regional_2 != 0) {
                                                    $resultx = $db->prepare("SELECT e.REGIONAL_ID, r.nome AS REGIONAL, s.NR_SECAO    
                                                                 FROM 2024_locais_votacao e
                                                                 INNER JOIN sys_regionais AS r ON r.id = e.REGIONAL_ID 
                                                                 INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = e.ID   
                                                                 WHERE e.REGIONAL_ID = ? AND s.TIPO = 'Principal' 
                                                                 GROUP BY e.REGIONAL_ID 
                                                                 ORDER BY r.nome ASC");
                                                    $resultx->bindValue(1, $pessoa_regional_2);
                                                    $resultx->execute();
                                                } else {
                                                    $resultx = $db->prepare("SELECT e.REGIONAL_ID, r.nome AS REGIONAL, s.NR_SECAO   
                                                                 FROM 2024_locais_votacao e 
                                                                 INNER JOIN sys_regionais AS r ON r.id = e.REGIONAL_ID 
                                                                 INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = e.ID 
                                                                 WHERE s.TIPO = 'Principal' AND e.MUNICIPIO_ID = 94   
                                                                 GROUP BY e.REGIONAL_ID 
                                                                 ORDER BY r.nome ASC");
                                                    $resultx->execute();
                                                }

                                                while ($regionais = $resultx->fetch(PDO::FETCH_ASSOC)) {

                                                    $result99 = $db->prepare("SELECT id, nome   
                                                                 FROM 2024_voluntarios 
                                                                 WHERE regional_2 = ? AND funcao_id = 4 AND status = 1   
                                                                 ORDER BY nome ASC");
                                                    $result99->bindValue(1, $regionais['REGIONAL_ID']);
                                                    $result99->execute();

                                                    if ($result99->rowCount() < 1) {
                                                ?>
                                                        <li class="list-group-item list-group-item-danger col-xl-3 col-lg-3"><?= $regionais['REGIONAL']; ?></li>
                                                    <?php
                                                    } else {
                                                    ?>
                                                        <li class="list-group-item list-group-item-success col-xl-3 col-lg-3"><?= $regionais['REGIONAL']; ?></li>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </ul>
                                    </div>


                                    <div id="div_advogado_locais" style="display: none;" class="card">
                                        <div class="card-header mt-3 px-3">
                                            <h5>LOCAIS</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="card">

                                                <div class="card-body">
                                                    <ul class="list-group">
                                                        <li class="list-group-item list-group-item-success">ACREPREVIDENCIA</li>
                                                        <li class="list-group-item list-group-item-danger">ASSEMBLEIA LEGISLATIVA DO ACRE</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                                            <th class="text-center">FISCAIS</th>
                                            <th class="text-center">QTD</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $qtd = 0;
                                        if (
                                            $pessoa_regional != "" && $pessoa_bairro != "" && $pessoa_local != "" && $pessoa_secao != "" &&
                                            $pessoa_regional != 0 && $pessoa_bairro != 0 && $pessoa_local != 0 && $pessoa_secao != 0
                                        ) {
                                            $result88 = $db->prepare("SELECT s.NR_ZONA, lv.ID AS LOCAL_VOTACAO_ID, lv.NM_LOCAL_VOTACAO, lv.REGIONAL_ID, s.NR_SECAO, lv.CD_BAIRRO, s.QT_ELEITOR_AGREGADO              
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
                                            $result88 = $db->prepare("SELECT s.NR_ZONA, lv.ID AS LOCAL_VOTACAO_ID, lv.NM_LOCAL_VOTACAO, lv.REGIONAL_ID, s.NR_SECAO, lv.CD_BAIRRO, s.QT_ELEITOR_AGREGADO              
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
                                            $result88 = $db->prepare("SELECT s.NR_ZONA, lv.ID AS LOCAL_VOTACAO_ID, lv.NM_LOCAL_VOTACAO, lv.REGIONAL_ID, s.NR_SECAO, lv.CD_BAIRRO, s.QT_ELEITOR_AGREGADO             
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
                                            $result88 = $db->prepare("SELECT s.NR_ZONA, lv.ID AS LOCAL_VOTACAO_ID, lv.NM_LOCAL_VOTACAO, lv.REGIONAL_ID, s.NR_SECAO, lv.CD_BAIRRO, s.QT_ELEITOR_AGREGADO       
                                                                 FROM 2024_secoes AS s
                                                                 INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID  
                                                                 WHERE lv.REGIONAL_ID = ? AND lv.ID = ? AND s.TIPO = 'Principal'
                                                                 GROUP BY s.ID
                                                                 ORDER BY s.NR_SECAO ASC");
                                            $result88->bindValue(1, $pessoa_regional);
                                            $result88->bindValue(2, $pessoa_local);
                                            $result88->execute();
                                        } else if ($pessoa_regional != "" && $pessoa_bairro != "" && $pessoa_regional != 0 && $pessoa_bairro != 0) {
                                            $result88 = $db->prepare("SELECT s.NR_ZONA, lv.ID AS LOCAL_VOTACAO_ID, lv.NM_LOCAL_VOTACAO, lv.REGIONAL_ID, s.NR_SECAO, lv.CD_BAIRRO, s.QT_ELEITOR_AGREGADO             
                                                                 FROM 2024_secoes AS s
                                                                 INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID  
                                                                 WHERE lv.REGIONAL_ID = ? AND lv.CD_BAIRRO = ? AND s.TIPO = 'Principal'
                                                                 GROUP BY s.ID
                                                                 ORDER BY s.NR_SECAO ASC");
                                            $result88->bindValue(1, $pessoa_regional);
                                            $result88->bindValue(2, $pessoa_bairro);
                                            $result88->execute();
                                        } else if ($pessoa_regional != "" && $pessoa_regional != 0) {
                                            $result88 = $db->prepare("SELECT s.NR_ZONA, lv.ID AS LOCAL_VOTACAO_ID, lv.NM_LOCAL_VOTACAO, lv.REGIONAL_ID, s.NR_SECAO, lv.CD_BAIRRO, s.QT_ELEITOR_AGREGADO            
                                                                 FROM 2024_secoes AS s
                                                                 INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID  
                                                                 WHERE lv.REGIONAL_ID = ? AND s.TIPO = 'Principal'
                                                                 GROUP BY s.ID
                                                                 ORDER BY s.NR_SECAO ASC");
                                            $result88->bindValue(1, $pessoa_regional);
                                            $result88->execute();
                                        } else {
                                            $result88 = $db->prepare("SELECT s.NR_ZONA, lv.ID AS LOCAL_VOTACAO_ID, lv.NM_LOCAL_VOTACAO, lv.REGIONAL_ID, s.NR_SECAO, lv.CD_BAIRRO, s.QT_ELEITOR_AGREGADO       
                                                                 FROM 2024_secoes AS s
                                                                 INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID  
                                                                 WHERE s.TIPO = 'Principal' AND lv.MUNICIPIO_ID = 94
                                                                 GROUP BY s.ID 
                                                                 ORDER BY s.NR_SECAO ASC");
                                            $result88->execute();
                                        }

                                        while ($secao = $result88->fetch(PDO::FETCH_ASSOC)) {

                                            //                                            if ($secao['REGIONAL_ID'] != "" && $secao['LOCAL_VOTACAO_ID'] != "") {
                                            //                                                $result888 = $db->prepare("SELECT id, nome, celular   
                                            //                                                                 FROM 2024_voluntarios 
                                            //                                                                 WHERE regional_2 = ? AND local_votacao_2 = ? AND secao_numero_2 = ? AND zona_2 = ? AND funcao_id = 5 AND status = 1   
                                            //                                                                 ORDER BY nome ASC");
                                            //                                                $result888->bindValue(1, $secao['REGIONAL_ID']);
                                            //                                                $result888->bindValue(2, $secao['LOCAL_VOTACAO_ID']);
                                            //                                                $result888->bindValue(3, $secao['NR_SECAO']);
                                            //                                                $result888->bindValue(4, $secao['NR_ZONA']);
                                            //                                                $result888->execute();
                                            //                                            } else if ($secao['REGIONAL_ID'] != "") {
                                            //                                                $result888 = $db->prepare("SELECT id, nome, celular   
                                            //                                                                 FROM 2024_voluntarios 
                                            //                                                                 WHERE regional_2 = ? AND secao_numero_2 = ? AND zona_2 = ? AND funcao_id = 5 AND status = 1   
                                            //                                                                 ORDER BY nome ASC");
                                            //                                                $result888->bindValue(1, $secao['REGIONAL_ID']);
                                            //                                                $result888->bindValue(2, $secao['NR_SECAO']);
                                            //                                                $result888->bindValue(3, $secao['NR_ZONA']);
                                            //                                                $result888->execute();
                                            //                                            } else {
                                            $result888 = $db->prepare("SELECT id, nome, celular, zona_2, secao_numero_2     
                                                                 FROM 2024_voluntarios  
                                                                 WHERE secao_numero_2 = ? AND zona_2 = ? AND funcao_id = 5 AND status = 1   
                                                                 ORDER BY nome ASC");
                                            $result888->bindValue(1, $secao['NR_SECAO']);
                                            $result888->bindValue(2, $secao['NR_ZONA']);
                                            $result888->execute();
                                            //}

                                            $qtd = $result888->rowCount();

                                            if ($qtd == 0) {
                                        ?>
                                                <tr class="<?= $qtd == 2 ? 'completo' : 'falta'; ?>">
                                                    <td class="col-1 text-center"><?= $secao['NR_ZONA']; ?></td>
                                                    <td class="col-1 text-center"><strong><?= $secao['NR_SECAO']; ?></strong></td>
                                                    <td class="col-1 text-center"><?= pesquisar_tabela("nome", "sys_regionais", "id", "=", $secao['REGIONAL_ID'], ""); ?></td>
                                                    <td class="col-1 text-center"><?= pesquisar_tabela("NM_BAIRRO", "bsc_bairros", "ID", "=", $secao['CD_BAIRRO'], ""); ?></td>
                                                    <td class="text-center"><?= $secao['NM_LOCAL_VOTACAO']; ?></td>
                                                    <td class="col-1 text-center"><?= $secao['QT_ELEITOR_AGREGADO']; ?></td>
                                                    <td class="text-center">
                                                        <a href="" onclick="carregar_dados('<?= $secao['NR_ZONA']; ?>', '<?= $secao['NR_SECAO']; ?>', '<?= $secao['REGIONAL_ID']; ?>', '<?= $secao['LOCAL_VOTACAO_ID']; ?>', '<?= $secao['CD_BAIRRO']; ?>')" data-toggle="modal" data-target="#teste" class="text-success add_fiscal"><i class="mdi mdi-plus-circle" style="font-size: 20px;"></i></a>
                                                    </td>
                                                    <td class="col-1 text-center">
                                                        <div class="qtd sim">
                                                            <?= $qtd; ?> | 2
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php
                                            } else {
                                                // while ($fiscais = $result888->fetch(PDO::FETCH_ASSOC)) {
                                            ?>
                                                <tr class="<?= $qtd == 2 ? 'completo' : 'falta'; ?>">
                                                    <td class="text-center"><?= $secao['NR_ZONA']; ?></td>
                                                    <td class="text-center"><strong><?= $secao['NR_SECAO']; ?></strong></td>
                                                    <td class="text-center"><?= pesquisar_tabela("nome", "sys_regionais", "id", "=", $secao['REGIONAL_ID'], ""); ?></td>
                                                    <td class="text-center"><?= pesquisar_tabela("NM_BAIRRO", "bsc_bairros", "ID", "=", $secao['CD_BAIRRO'], ""); ?></td>
                                                    <td class="text-center"><?= $secao['NM_LOCAL_VOTACAO']; ?></td>
                                                    <td class="text-center"><?= $secao['QT_ELEITOR_AGREGADO']; ?></td>
                                                    <td class="text-center">
                                                        <?php
                                                        if ($qtd < 2) {
                                                        ?>
                                                            <a href="" onclick="carregar_dados('<?= $secao['NR_ZONA']; ?>', '<?= $secao['NR_SECAO']; ?>', '<?= $secao['REGIONAL_ID']; ?>', '<?= $secao['LOCAL_VOTACAO_ID']; ?>', '<?= $secao['CD_BAIRRO']; ?>')" data-toggle="modal" data-target="#teste" class="text-success add_fiscal"><i class="mdi mdi-plus-circle" style="font-size: 20px;"></i></a>
                                                        <?php
                                                        } ?>
                                                        <div class="dropdown dropleft">
                                                            <a class="dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                visualizar
                                                            </a>
                                                            <div class="dropdown-menu p-2" aria-labelledby="dropdownMenuButton" style="width: 700px;">
                                                                <?php
                                                                while ($fiscais = $result888->fetch(PDO::FETCH_ASSOC)) {
                                                                ?>
                                                                    <span><b><?= ctexto($fiscais['nome'], "mai"); ?></b> | <?= $fiscais['celular']; ?> - <b>Zona</b> <?= $fiscais['zona_2']; ?> - <b>Seção</b> <?= $fiscais['secao_numero_2']; ?></span> | <a id="remover_fiscal" onclick="remover(<?= $fiscais['id']; ?>)" style="cursor: pointer" class="text-danger"><i class="mdi mdi-close-circle" style="font-size: 20px;"></i></a> </br>
                                                                <?php
                                                                }
                                                                ?>

                                                            </div>
                                                        </div>

                                                    </td>
                                                    <td class="text-center">
                                                        <div class="qtd sim">
                                                            <?= $qtd; ?> | 2
                                                        </div>
                                                    </td>
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
                                            <th class="text-center">QTD</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        $qtd = 0;

                                        if (
                                            $pessoa_regional_2 != "" && $pessoa_bairro_2 != "" && $pessoa_local_2 != "" &&
                                            $pessoa_regional_2 != 0 && $pessoa_bairro_2 != 0 && $pessoa_local_2 != 0
                                        ) {
                                            $result88 = $db->prepare("SELECT s.NR_SECAO, lv.REGIONAL_ID, s.QT_ELEITOR_AGREGADO, lv.NM_BAIRRO, lv.NM_LOCAL_VOTACAO, s.LOCAL_VOTACAO_ID       
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
                                            $result88 = $db->prepare("SELECT s.NR_SECAO, lv.REGIONAL_ID, s.QT_ELEITOR_AGREGADO, lv.NM_BAIRRO, lv.NM_LOCAL_VOTACAO, s.LOCAL_VOTACAO_ID      
                                                                      FROM 2024_locais_votacao AS lv 
                                                                      INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = lv.ID 
                                                                      WHERE lv.REGIONAL_ID = ? AND lv.CD_BAIRRO = ?  
                                                                      GROUP BY lv.ID   
                                                                      ORDER BY s.NR_SECAO ASC");
                                            $result88->bindValue(1, $pessoa_regional_2);
                                            $result88->bindValue(2, $pessoa_bairro_2);
                                            $result88->execute();
                                        } else if ($pessoa_regional_2 != "" && $pessoa_regional_2 != 0) {
                                            $result88 = $db->prepare("SELECT s.NR_SECAO, lv.REGIONAL_ID, s.QT_ELEITOR_AGREGADO, lv.NM_BAIRRO, lv.NM_LOCAL_VOTACAO, s.LOCAL_VOTACAO_ID       
                                                                      FROM 2024_locais_votacao AS lv 
                                                                      INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = lv.ID 
                                                                      WHERE lv.REGIONAL_ID = ?  
                                                                      GROUP BY lv.ID   
                                                                      ORDER BY s.NR_SECAO ASC");
                                            $result88->bindValue(1, $pessoa_regional_2);
                                            $result88->execute();
                                        } else if ($pessoa_local_2 != "" && $pessoa_local_2 != 0) {
                                            $result88 = $db->prepare("SELECT s.NR_SECAO, lv.REGIONAL_ID, s.QT_ELEITOR_AGREGADO, lv.NM_BAIRRO, lv.NM_LOCAL_VOTACAO, s.LOCAL_VOTACAO_ID       
                                                                      FROM 2024_locais_votacao AS lv 
                                                                      INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = lv.ID 
                                                                      WHERE lv.ID = ? 
                                                                      GROUP BY lv.ID   
                                                                      ORDER BY s.NR_SECAO ASC");
                                            $result88->bindValue(1, $pessoa_local_2);
                                            $result88->execute();
                                        } else {
                                            $result88 = $db->prepare("SELECT s.NR_SECAO, lv.REGIONAL_ID, s.QT_ELEITOR_AGREGADO, lv.NM_BAIRRO, lv.NM_LOCAL_VOTACAO, s.LOCAL_VOTACAO_ID        
                                                                      FROM 2024_locais_votacao AS lv  
                                                                      INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = lv.ID  
                                                                      WHERE lv.MUNICIPIO_ID = 94 AND s.TIPO = 'Principal'  
                                                                      GROUP BY lv.ID 
                                                                      ORDER BY s.NR_SECAO ASC");
                                            $result88->execute();
                                        }

                                        while ($secao = $result88->fetch(PDO::FETCH_ASSOC)) {

                                            $result888 = $db->prepare("SELECT id, nome, celular, zona_2, secao_numero_2, local_votacao_2   
                                                                       FROM 2024_voluntarios  
                                                                       WHERE regional_2 = ? AND local_votacao_2 = ? AND funcao_id = 4 AND status = 1    
                                                                       ORDER BY nome ASC");
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
                                                    <a href="" onclick="carregar_dados_supervisor('<?= $secao['NR_SECAO']; ?>', '<?= $secao['REGIONAL_ID']; ?>', '<?= $secao['NM_LOCAL_VOTACAO']; ?>')" data-toggle="modal" data-target="#teste2" class="text-success add_fiscal"><i class="mdi mdi-plus-circle" style="font-size: 20px;"></i></a>
                                                    <?php
                                                    while ($fiscais = $result888->fetch(PDO::FETCH_ASSOC)) {
                                                    ?>
                                                        <span><b><?= $fiscais['nome']; ?></b> <a id="remover_fiscal" onclick="remover_supervisor(<?= $fiscais['id']; ?>)" style="cursor: pointer" class="text-danger"><i class="mdi mdi-close-circle" style="font-size: 20px;"></i></a><br /><?= $fiscais['celular']; ?> - <b>Local: </b> <?= is_numeric($fiscais['local_votacao_2']) ? pesquisar("NM_LOCAL_VOTACAO", "2024_locais_votacao", "ID", "=", $fiscais['local_votacao_2'], "") : ""; ?>
                                                        <?php
                                                    }
                                                        ?>
                                                </td>

                                                <td class="col-1 text-center">1|1</td>
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

                    <!-- Início listagem de alocação dos Advogados -->
                    <div id="div_advogados_card" style="display: none;" class="card">
                        <div class="card-body">
                            <h5 class="card-header mt-3 px-3">ADVOGADOS</h5>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">REGIONAL</th>
                                        <th scope="col">APTOS</th>
                                        <th class="col">ADVOGADOS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Cadeia Velha</td>
                                        <td>250</td>
                                        <td>GABRIELA CARVALHO RIBEIRO - (68) 9 8408-3286 </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                    <!-- Fim da listagem de alocação dos Advogados -->
                </div>

            </div>
        </div>
        <div style="display: none;" id="div_resultado_estadual"></div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade modal-success" id="teste" role="dialog">
    <div class="modal-dialog" role="document">
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
                                    <option value="">Selecione o Fiscal</option>
                                    <?php
                                    $qtdd = 1;

                                    if (
                                        $pessoa_local != "" && $pessoa_regional != "" && $pessoa_bairro != "" &&
                                        $pessoa_local != 0 && $pessoa_regional != 0 && $pessoa_bairro != 0
                                    ) {
                                        $result8 = $db->prepare("SELECT p.secao_numero, p.bsc_bairros_id, b.NM_BAIRRO AS bairro, p.zona, p.local_votacao, p.id, p.nome    
                                                                 FROM 2024_voluntarios AS p
                                                                 INNER JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero 
                                                                 INNER JOIN 2024_locais_votacao AS e2j ON e2j.ID = s.LOCAL_VOTACAO_ID 
                                                                 INNER JOIN bsc_bairros AS b ON b.ID = p.bsc_bairros_id  
                                                                 WHERE e2j.ID = ? AND e2j.REGIONAL_ID = ? AND p.bsc_bairros_id = ? AND p.tipo = 2 AND p.funcao_id = 5 AND p.secao_numero_2 IS NULL AND p.status = 1 AND e2j.MUNICIPIO_ID = 94 OR
                                                                 e2j.ID = ? AND e2j.REGIONAL_ID = ? AND p.bsc_bairros_id = ? AND p.tipo = 2 AND p.funcao_id = 5 AND p.secao_numero_2 = ' ' AND p.status = 1 AND e2j.MUNICIPIO_ID = 94      
                                                                 ORDER BY p.data_cadastro ASC");
                                        $result8->bindValue(1, $pessoa_local);
                                        $result8->bindValue(2, $pessoa_regional);
                                        $result8->bindValue(3, $pessoa_bairro);
                                        $result8->bindValue(4, $pessoa_local);
                                        $result8->bindValue(5, $pessoa_regional);
                                        $result8->bindValue(6, $pessoa_bairro);
                                        $result8->execute();
                                    } else if ($pessoa_local != "" && $pessoa_regional != "" && $pessoa_local != 0 && $pessoa_regional != 0) {
                                        $result8 = $db->prepare("SELECT p.secao_numero, p.bsc_bairros_id, b.NM_BAIRRO AS bairro, p.zona, p.local_votacao, p.id, p.nome    
                                                                 FROM 2024_voluntarios AS p
                                                                 INNER JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero 
                                                                 INNER JOIN 2024_locais_votacao AS e2j ON e2j.ID = s.LOCAL_VOTACAO_ID 
                                                                 INNER JOIN bsc_bairros AS b ON b.ID = p.bsc_bairros_id  
                                                                 WHERE e2j.ID = ? AND e2j.REGIONAL_ID = ? AND p.tipo = 2 AND p.funcao_id = 5 AND p.secao_numero_2 IS NULL AND p.status = 1 AND e2j.MUNICIPIO_ID = 94 OR
                                                                 e2j.ID = ? AND e2j.REGIONAL_ID = ? AND p.tipo = 2 AND p.funcao_id = 5 AND p.secao_numero_2 = ' ' AND p.status = 1 AND e2j.MUNICIPIO_ID = 94      
                                                                 ORDER BY p.data_cadastro ASC");
                                        $result8->bindValue(1, $pessoa_local);
                                        $result8->bindValue(2, $pessoa_regional);
                                        $result8->bindValue(3, $pessoa_local);
                                        $result8->bindValue(4, $pessoa_regional);
                                        $result8->execute();
                                    } else if ($pessoa_bairro != "" && $pessoa_regional != "") {
                                        $result8 = $db->prepare("SELECT p.secao_numero, p.bsc_bairros_id, b.NM_BAIRRO AS bairro, p.zona, p.local_votacao, p.id, p.nome    
                                                                 FROM 2024_voluntarios AS p
                                                                 INNER JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero 
                                                                 INNER JOIN 2024_locais_votacao AS e2j ON e2j.ID = s.LOCAL_VOTACAO_ID 
                                                                 INNER JOIN bsc_bairros AS b ON b.ID = p.bsc_bairros_id  
                                                                 WHERE p.bsc_bairros_id = ? AND e2j.REGIONAL_ID = ? AND p.tipo = 2 AND p.funcao_id = 5 AND p.secao_numero_2 IS NULL AND p.status = 1 AND e2j.MUNICIPIO_ID = 94 OR
                                                                 p.bsc_bairros_id = ? AND e2j.REGIONAL_ID = ? AND p.tipo = 2 AND p.funcao_id = 5 AND p.secao_numero_2 = ' ' AND p.status = 1 AND e2j.MUNICIPIO_ID = 94      
                                                                 ORDER BY p.data_cadastro ASC");
                                        $result8->bindValue(1, $pessoa_bairro);
                                        $result8->bindValue(2, $pessoa_regional);
                                        $result8->bindValue(3, $pessoa_bairro);
                                        $result8->bindValue(4, $pessoa_regional);
                                        $result8->execute();
                                    } else if ($pessoa_local == "" && $pessoa_regional != "" && $pessoa_local == 0 && $pessoa_regional != 0) {
                                        $result8 = $db->prepare("SELECT p.secao_numero, p.bsc_bairros_id, b.NM_BAIRRO AS bairro, p.zona, p.local_votacao, p.id, p.nome    
                                                                 FROM 2024_voluntarios AS p
                                                                 INNER JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero 
                                                                 INNER JOIN 2024_locais_votacao AS e2j ON e2j.ID = s.LOCAL_VOTACAO_ID 
                                                                 INNER JOIN bsc_bairros AS b ON b.ID = p.bsc_bairros_id  
                                                                 WHERE e2j.REGIONAL_ID = ? AND p.tipo = 2 AND p.funcao_id = 5 AND p.secao_numero_2 IS NULL AND p.status = 1 AND e2j.MUNICIPIO_ID = 94 OR
                                                                 e2j.REGIONAL_ID = ? AND p.tipo = 2 AND p.funcao_id = 5 AND p.secao_numero_2 = ' ' AND p.status = 1 AND e2j.MUNICIPIO_ID = 94      
                                                                 ORDER BY p.data_cadastro ASC");
                                        $result8->bindValue(1, $pessoa_regional);
                                        $result8->bindValue(2, $pessoa_regional);
                                        $result8->execute();
                                    } else {
                                        $result8 = $db->prepare("SELECT p.secao_numero, p.bsc_bairros_id, b.NM_BAIRRO AS bairro, p.zona, p.local_votacao, p.id, p.nome    
                                                                 FROM 2024_voluntarios AS p
                                                                 INNER JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero 
                                                                 INNER JOIN 2024_locais_votacao AS e2j ON e2j.ID = s.LOCAL_VOTACAO_ID 
                                                                 INNER JOIN bsc_bairros AS b ON b.ID = p.bsc_bairros_id  
                                                                 WHERE p.tipo = 2 AND p.funcao_id = 5 AND p.secao_numero_2 IS NULL AND p.status = 1 AND e2j.MUNICIPIO_ID = 94 OR
                                                                 p.tipo = 2 AND p.funcao_id = 5 AND p.secao_numero_2 = ' ' AND p.status = 1 AND e2j.MUNICIPIO_ID = 94      
                                                                 ORDER BY p.data_cadastro ASC");
                                        $result8->execute();
                                    }

                                    while ($pessoa = $result8->fetch(PDO::FETCH_ASSOC)) {

                                        $indicadores = vf_indicacao($pessoa['id']);
                                    ?>
                                        <option zona="<?= $pessoa['zona']; ?>" secao="<?= $pessoa['secao_numero']; ?>" local="<?= $pessoa['local_votacao']; ?>" value='<?= $pessoa['id']; ?>'><?= $qtdd; ?> - <?= $indicadores != "" ? "Indicado por " . $indicadores : ""; ?> - <?= $pessoa['nome']; ?><?= is_numeric($pessoa['bsc_bairros_id']) ? "- BAIRRO " . $pessoa['bairro'] : ""; ?> - ZONA <?= $pessoa['zona']; ?> - SEÇÃO <?= $pessoa['secao_numero']; ?></option>
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

<!-- Modal -->
<div class="modal fade modal-success" id="teste2" role="dialog">
    <div class="modal-dialog" role="document">
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
                                    <option value="">Selecione o Supervisor</option>
                                    <?php
                                    $qtdd = 1;

                                    if (
                                        $pessoa_local != "" && $pessoa_regional != "" && $pessoa_bairro != "" &&
                                        $pessoa_local != 0 && $pessoa_regional != 0 && $pessoa_bairro != 0
                                    ) {
                                        $result8 = $db->prepare("SELECT p.secao_numero, p.bsc_bairros_id, b.NM_BAIRRO AS bairro, p.zona, p.local_votacao, p.id, p.nome    
                                                                 FROM 2024_voluntarios AS p
                                                                 INNER JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero 
                                                                 INNER JOIN 2024_locais_votacao AS e2j ON e2j.ID = s.LOCAL_VOTACAO_ID 
                                                                 INNER JOIN bsc_bairros AS b ON b.ID = p.bsc_bairros_id  
                                                                 WHERE e2j.ID = ? AND e2j.REGIONAL_ID = ? AND p.bsc_bairros_id = ? AND p.tipo = 2 AND p.funcao_id = 4 AND p.secao_numero_2 IS NULL AND p.status = 1 AND e2j.MUNICIPIO_ID = 94 OR
                                                                 e2j.ID = ? AND e2j.REGIONAL_ID = ? AND p.bsc_bairros_id = ? AND p.tipo = 2 AND p.funcao_id = 4 AND p.secao_numero_2 = ' ' AND p.status = 1 AND e2j.MUNICIPIO_ID = 94      
                                                                 ORDER BY p.data_cadastro ASC");
                                        $result8->bindValue(1, $pessoa_local);
                                        $result8->bindValue(2, $pessoa_regional);
                                        $result8->bindValue(3, $pessoa_bairro);
                                        $result8->bindValue(4, $pessoa_local);
                                        $result8->bindValue(5, $pessoa_regional);
                                        $result8->bindValue(6, $pessoa_bairro);
                                        $result8->execute();
                                    } else if ($pessoa_local != "" && $pessoa_regional != "" && $pessoa_local != 0 && $pessoa_regional != 0) {
                                        $result8 = $db->prepare("SELECT p.secao_numero, p.bsc_bairros_id, b.NM_BAIRRO AS bairro, p.zona, p.local_votacao, p.id, p.nome    
                                                                 FROM 2024_voluntarios AS p
                                                                 INNER JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero 
                                                                 INNER JOIN 2024_locais_votacao AS e2j ON e2j.ID = s.LOCAL_VOTACAO_ID 
                                                                 INNER JOIN bsc_bairros AS b ON b.ID = p.bsc_bairros_id  
                                                                 WHERE e2j.ID = ? AND e2j.REGIONAL_ID = ? AND p.tipo = 2 AND p.funcao_id = 4 AND p.secao_numero_2 IS NULL AND p.status = 1 AND e2j.MUNICIPIO_ID = 94 OR
                                                                 e2j.ID = ? AND e2j.REGIONAL_ID = ? AND p.tipo = 2 AND p.funcao_id = 4 AND p.secao_numero_2 = ' ' AND p.status = 1 AND e2j.MUNICIPIO_ID = 94      
                                                                 ORDER BY p.data_cadastro ASC");
                                        $result8->bindValue(1, $pessoa_local);
                                        $result8->bindValue(2, $pessoa_regional);
                                        $result8->bindValue(3, $pessoa_local);
                                        $result8->bindValue(4, $pessoa_regional);
                                        $result8->execute();
                                    } else if ($pessoa_bairro != "" && $pessoa_regional != "") {
                                        $result8 = $db->prepare("SELECT p.secao_numero, p.bsc_bairros_id, b.NM_BAIRRO AS bairro, p.zona, p.local_votacao, p.id, p.nome    
                                                                 FROM 2024_voluntarios AS p
                                                                 INNER JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero 
                                                                 INNER JOIN 2024_locais_votacao AS e2j ON e2j.ID = s.LOCAL_VOTACAO_ID 
                                                                 INNER JOIN bsc_bairros AS b ON b.ID = p.bsc_bairros_id  
                                                                 WHERE p.bsc_bairros_id = ? AND e2j.REGIONAL_ID = ? AND p.tipo = 2 AND p.funcao_id = 4 AND p.secao_numero_2 IS NULL AND p.status = 1 AND e2j.MUNICIPIO_ID = 94 OR
                                                                 p.bsc_bairros_id = ? AND e2j.REGIONAL_ID = ? AND p.tipo = 2 AND p.funcao_id = 4 AND p.secao_numero_2 = ' ' AND p.status = 1 AND e2j.MUNICIPIO_ID = 94      
                                                                 ORDER BY p.data_cadastro ASC");
                                        $result8->bindValue(1, $pessoa_bairro);
                                        $result8->bindValue(2, $pessoa_regional);
                                        $result8->bindValue(3, $pessoa_bairro);
                                        $result8->bindValue(4, $pessoa_regional);
                                        $result8->execute();
                                    } else if ($pessoa_local == "" && $pessoa_regional != "" && $pessoa_local == 0 && $pessoa_regional != 0) {
                                        $result8 = $db->prepare("SELECT p.secao_numero, p.bsc_bairros_id, b.NM_BAIRRO AS bairro, p.zona, p.local_votacao, p.id, p.nome    
                                                                 FROM 2024_voluntarios AS p
                                                                 INNER JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero 
                                                                 INNER JOIN 2024_locais_votacao AS e2j ON e2j.ID = s.LOCAL_VOTACAO_ID 
                                                                 INNER JOIN bsc_bairros AS b ON b.ID = p.bsc_bairros_id  
                                                                 WHERE e2j.REGIONAL_ID = ? AND p.tipo = 2 AND p.funcao_id = 4 AND p.secao_numero_2 IS NULL AND p.status = 1 AND e2j.MUNICIPIO_ID = 94 OR
                                                                 e2j.REGIONAL_ID = ? AND p.tipo = 2 AND p.funcao_id = 4 AND p.secao_numero_2 = ' ' AND p.status = 1 AND e2j.MUNICIPIO_ID = 94      
                                                                 ORDER BY p.data_cadastro ASC");
                                        $result8->bindValue(1, $pessoa_regional);
                                        $result8->bindValue(2, $pessoa_regional);
                                        $result8->execute();
                                    } else {
                                        $result8 = $db->prepare("SELECT p.secao_numero, p.bsc_bairros_id, b.NM_BAIRRO AS bairro, p.zona, p.local_votacao, p.id, p.nome    
                                                                 FROM 2024_voluntarios AS p
                                                                 INNER JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero 
                                                                 INNER JOIN 2024_locais_votacao AS e2j ON e2j.ID = s.LOCAL_VOTACAO_ID 
                                                                 INNER JOIN bsc_bairros AS b ON b.ID = p.bsc_bairros_id  
                                                                 WHERE p.tipo = 2 AND p.funcao_id = 4 AND p.secao_numero_2 IS NULL AND p.status = 1 AND e2j.MUNICIPIO_ID = 94 OR
                                                                 p.tipo = 2 AND p.funcao_id = 4 AND p.secao_numero_2 = ' ' AND p.status = 1 AND e2j.MUNICIPIO_ID = 94      
                                                                 ORDER BY p.data_cadastro ASC");
                                        $result8->execute();
                                    }

                                    while ($pessoa = $result8->fetch(PDO::FETCH_ASSOC)) {

                                        $indicadores = vf_indicacao($pessoa['id']);
                                    ?>
                                        <option zona="<?= $pessoa['zona']; ?>" secao="<?= $pessoa['secao_numero']; ?>" local="<?= $pessoa['local_votacao']; ?>" value='<?= $pessoa['id']; ?>'><?= $qtdd; ?> - <?= $indicadores != "" ? "Indicado por " . $indicadores : ""; ?> - <?= $pessoa['nome']; ?><?= is_numeric($pessoa['bsc_bairros_id']) ? "- BAIRRO " . $pessoa['bairro'] : ""; ?> - ZONA <?= $pessoa['zona']; ?> - SEÇÃO <?= $pessoa['secao_numero']; ?></option>
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