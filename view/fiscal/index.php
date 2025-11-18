<?php
include("template/layout/dashboard/topo.php");

if (!ver_nivel(1) && !ver_nivel(6)) {
    msg('Você não possui permissão para acessar essa área.');
    url(PORTAL_URL . 'view/admin/dashboard');
}
?>

<?php
if (isset($_POST['local']) && $_POST['local'] != "" && $_POST['local'] != "0") {
    $pessoa_local = $_POST['local'];
} else {
    $pessoa_local = "";
}

if (isset($_POST['secao']) && $_POST['secao'] != "" && $_POST['secao'] != "0") {
    $pessoa_secao = $_POST['secao'];
} else {
    $pessoa_secao = "";
}

if (isset($_POST['zona']) && $_POST['zona'] != "0" && is_numeric($_POST['zona'])) {
    $pessoa_zona = $_POST['zona'];
} else {
    $pessoa_zona = "";
}

if (isset($_POST['situacao']) && $_POST['situacao'] != "") {
    $pessoa_situacao = $_POST['situacao'];
} else {
    $pessoa_situacao = "";
}

if (isset($_POST['pessoa']) && $_POST['pessoa'] != "0" && is_numeric($_POST['pessoa'])) {
    $pessoa_id = $_POST['pessoa'];
} else {
    $pessoa_id = "";
}

if (isset($_POST['funcao']) && $_POST['funcao'] != "0" && is_numeric($_POST['funcao'])) {
    $pessoa_funcao = $_POST['funcao'];
} else {
    $pessoa_funcao = "";
}

if (isset($_POST['indicacao']) && $_POST['indicacao'] != "") {
    $pessoa_indicacao = $_POST['indicacao'];
} else {
    $pessoa_indicacao = "";
}

if (isset($_POST['regional']) && $_POST['regional'] != "") {
    $pessoa_regional = $_POST['regional'];
} else {
    $pessoa_regional = "";
}

if (isset($_POST['tipo']) && $_POST['tipo'] != "") {
    $pessoa_tipo = $_POST['tipo'];
} else {
    $pessoa_tipo = "";
}

if (isset($_POST['treinamento']) && $_POST['treinamento'] != "" && is_numeric($_POST['treinamento'])) {
    $pessoa_treinamento = $_POST['treinamento'];
} else {
    $pessoa_treinamento = "";
}

$contador = 1;
$contador2 = 1;
$condicao = "";

if ($pessoa_regional != "") {
    $condicao .= "AND lv.REGIONAL_ID = ? ";
}

if ($pessoa_id != "") {
    $condicao .= "AND p.id = ? ";
}

if ($pessoa_funcao != "") {
    $condicao .= "AND p.funcao_id = ? ";
}

if ($pessoa_zona != "") {
    $condicao .= "AND p.zona = ? ";
}

if ($pessoa_secao != "") {
    $condicao .= "AND p.secao_numero = ? ";
}

if ($pessoa_situacao != "" && $pessoa_situacao != 10) {
    $condicao .= "AND p.status = ? ";
} else if ($pessoa_situacao == 10) {
    $condicao .= "AND p.secao_numero_2 IS NOT NULL AND p.bairro_2 IS NOT NULL AND regional_2 IS NOT NULL ";
}

if ($pessoa_local != "") {
    $condicao .= "AND s.LOCAL_VOTACAO_ID = ? ";
}

if ($pessoa_indicacao != "") {
    $condicao .= "AND i.indicacao_id = ? ";
}

if ($pessoa_tipo != "") {
    $condicao .= "AND p.tipo = ? ";
}

if ($pessoa_treinamento != "" && $pessoa_treinamento != 2) {
    $condicao .= "AND p.treinamento = ? ";
} else if ($pessoa_treinamento != "" && $pessoa_treinamento == 2) {
    $condicao .= "AND p.treinamento IS NULL";
}

// Número de registros por página
$limite = 100;
// Pega a página atual via GET, se não houver será a página 1
$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
// Calcula o OFFSET (deslocamento) para a consulta SQL
$offset = ($pagina - 1) * $limite;

$sqlTotal = $db->prepare("SELECT p.id   
                FROM 2024_voluntarios p
                INNER JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero
                INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID 
                LEFT JOIN 2024_voluntarios_indicacoes AS i ON i.voluntario_id = p.id
                WHERE 1 $condicao 
                GROUP BY p.id");

if ($pessoa_regional != "") {
    $sqlTotal->bindValue($contador2, $pessoa_regional);
    $contador2++;
}

if ($pessoa_id != "") {
    $sqlTotal->bindValue($contador2, $pessoa_id);
    $contador2++;
}

if ($pessoa_funcao != "") {
    $sqlTotal->bindValue($contador2, $pessoa_funcao);
    $contador2++;
}

if ($pessoa_zona != "") {
    $sqlTotal->bindValue($contador2, $pessoa_zona);
    $contador2++;
}

if ($pessoa_secao != "") {
    $sqlTotal->bindValue($contador2, $pessoa_secao);
    $contador2++;
}

if ($pessoa_situacao != "" && $pessoa_situacao != 10) {
    $sqlTotal->bindValue($contador2, $pessoa_situacao);
    $contador2++;
}

if ($pessoa_local != "") {
    $sqlTotal->bindValue($contador2, $pessoa_local);
    $contador2++;
}

if ($pessoa_indicacao != "") {
    $sqlTotal->bindValue($contador2, $pessoa_indicacao);
    $contador2++;
}

if ($pessoa_tipo != "") {
    $sqlTotal->bindValue($contador2, $pessoa_tipo);
    $contador2++;
}

if ($pessoa_treinamento != "" && $pessoa_treinamento != 2) {
    $sqlTotal->bindValue($contador2, $pessoa_treinamento);
    $contador2++;
}

$sqlTotal->execute();
$totalRegistros = $sqlTotal->rowCount();

// Calcula o total de páginas
$totalPaginas = ceil($totalRegistros / $limite);
?>

<div class="row">
    <div class="container col-xl-9 col-lg-9 col-md-10">
        <div class="col-xl-10">
            <!-- ============================================================== -->
            <!-- pageheader -->
            <!-- ============================================================== -->
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="row col-sm-12 col-xl-12 col-lg-12">
                        <h2 class="alt-h5-border-brand">Lista de Voluntários</h2>
                    </div>

                    <div class="page-header" id="top">
                        <div class="page-breadcrumb">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/admin/dashboard" class="breadcrumb-link-alt">Início</a></li>
                                    <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/fiscal/dashboard-fiscalizacao" class="breadcrumb-link-alt">Fiscalização</a></li>
                                    <li class="breadcrumb-item active text-brand" aria-current="page">Voluntários</li>
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
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
            <div class="row row-cols-auto">

                <div class="col-12 col-xs-4 col-sm-6 col-lg-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="container col-xs-2">
                                <div class="d-inline-block">
                                    <h5 class="text-muted">Volutários</h5>
                                    <h2 class="mb-0"><?= qtd_voluntarios($pessoa_treinamento, 2024, $pessoa_regional, $pessoa_funcao, $pessoa_zona, $pessoa_secao, $pessoa_situacao, $pessoa_local, $pessoa_indicacao); ?></h2>
                                </div>
                                <div class="float-right icon-circle-small  icon-box-sm  bg-primary-light mt-1">
                                    <i class="fa fa-users fa-fw fa-sm text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xs-4 col-sm-6 col-lg-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="container col-xs-2">
                                <div class="d-inline-block">
                                    <h5 class="text-muted">Supervisores</h5>
                                    <h2 class="mb-0"><?= qtd_funcao($pessoa_treinamento, 2024, 4, $pessoa_regional, $pessoa_funcao, $pessoa_zona, $pessoa_secao, $pessoa_situacao, $pessoa_local, $pessoa_indicacao); ?></h2>
                                </div>
                                <div class="float-right icon-circle-small  icon-box-sm  bg-success-light mt-1">
                                    <i class="fa fa-user-secret fa-fw fa-sm text-success"></i>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-12 col-xs-4 col-sm-6 col-lg-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="container col-xs-2">
                                <div class="d-inline-block">
                                    <h5 class="text-muted">Fiscais</h5>
                                    <h2 class="mb-0"><?= qtd_funcao($pessoa_treinamento, 2024, 5, $pessoa_regional, $pessoa_funcao, $pessoa_zona, $pessoa_secao, $pessoa_situacao, $pessoa_local, $pessoa_indicacao); ?></h2>
                                </div>
                                <div class="float-right icon-circle-small  icon-box-sm  bg-success-light mt-1">
                                    <i class="fa fa-address-card fa-fw fa-sm text-success"></i>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div <?= is_numeric($pessoa_situacao) ? ($pessoa_situacao == 1 ? "" : "style='display:none;'") : "" ?> class="col-12 col-xs-4 col-sm-6 col-lg-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="container col-xs-2">

                                <div class="d-inline-block">
                                    <h5 class="text-muted">Ativo</h5>
                                    <h2 class="mb-0"><?= qtd_status($pessoa_treinamento, 2024, $pessoa_regional, 1, $pessoa_funcao, $pessoa_zona, $pessoa_secao, $pessoa_local, $pessoa_indicacao); ?></h2>
                                </div>
                                <div class="float-right icon-circle-small  icon-box-sm  bg-success-light mt-1">
                                    <i class="fa fa-user fa-fw fa-sm text-success"></i>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div <?= is_numeric($pessoa_situacao) ? ($pessoa_situacao == 2 ? "" : "style='display:none;'") : "" ?> class="col-12 col-xs-4 col-sm-6 col-lg-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="container col-xs-2">

                                <div class="d-inline-block">
                                    <h5 class="text-muted">Desistência</h5>
                                    <h2 class="mb-0"><?= qtd_status($pessoa_treinamento, 2024, $pessoa_regional, 2, $pessoa_funcao, $pessoa_zona, $pessoa_secao, $pessoa_local, $pessoa_indicacao); ?></h2>
                                </div>
                                <div class="float-right icon-circle-small  icon-box-sm  bg-danger-light mt-1">
                                    <i class="fa fa-user-times fa-fw fa-sm text-danger"></i>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div <?= is_numeric($pessoa_situacao) ? ($pessoa_situacao == 3 ? "" : "style='display:none;'") : "" ?> class="col-12 col-xs-4 col-sm-6 col-lg-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="container col-xs-2">

                                <div class="d-inline-block">
                                    <h5 class="text-muted">Saúde</h5>
                                    <h2 class="mb-0"><?= qtd_status($pessoa_treinamento, 2024, $pessoa_regional, 3, $pessoa_funcao, $pessoa_zona, $pessoa_secao, $pessoa_local, $pessoa_indicacao); ?></h2>
                                </div>
                                <div class="float-right icon-circle-small  icon-box-sm  bg-brand-light mt-1">
                                    <i class="fa fas fa-frown fa-fw fa-sm text-warning"></i>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div <?= is_numeric($pessoa_situacao) ? ($pessoa_situacao == 4 ? "" : "style='display:none;'") : "" ?> class="col-12 col-xs-4 col-sm-6 col-lg-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="container col-xs-2">

                                <div class="d-inline-block">
                                    <h5 class="text-muted">TRE</h5>
                                    <h2 class="mb-0"><?= qtd_status($pessoa_treinamento, 2024, $pessoa_regional, 4, $pessoa_funcao, $pessoa_zona, $pessoa_secao, $pessoa_local, $pessoa_indicacao); ?></h2>
                                </div>
                                <div class="float-right icon-circle-small  icon-box-sm  bg-info-light mt-1">
                                    <i class="fa fa-balance-scale fa-fw fa-sm text-info"></i>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div <?= is_numeric($pessoa_situacao) ? ($pessoa_situacao == 0 ? "" : "style='display:none;'") : "" ?> class="col-12 col-xs-4 col-sm-6 col-lg-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="container col-xs-2">

                                <div class="d-inline-block">
                                    <h5 class="text-muted">Outros</h5>
                                    <h2 class="mb-0"><?= qtd_status($pessoa_treinamento, 2024, $pessoa_regional, 0, $pessoa_funcao, $pessoa_zona, $pessoa_secao, $pessoa_local, $pessoa_indicacao); ?></h2>
                                </div>
                                <div class="float-right icon-circle-small  icon-box-sm  bg-primary-light mt-1">
                                    <i class="fa  fa-asterisk fa-fw fa-sm text-primary"></i>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card px-3 bg-brand">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12">
                            <input class="form-control form-control-sm" type="search" id="pesquisar" name="pesquisar" placeholder="Pesquisar" aria-label="Search">
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <div class="float-xl-right float-none mt-xl-0 mt-4">
                                <a <?= ver_nivel(1) || ver_nivel(6) ? "" : "style='display: none;'"; ?> href="<?= PORTAL_URL; ?>view/fiscal/cadastro" class="btn btn-primary btn-sm"><i class="fas fa-user-plus mr-1"></i>Adicionar</a>
                                <!--<a style="<?= ver_nivel(1) ? "" : "display: none;"; ?>" href="<?= PORTAL_URL; ?>view/fiscal/distribuidos" id="gerar" class="btn btn-warning btn-sm"><i class="fas fa-sitemap mr-1"></i>Distribuição</a>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card px-3">
                <div class="card-header" id="headingSeven">
                    <h3 class="mb-0">
                        <button class="btn btn-link text-brand" data-toggle="collapse" data-target="#collapseSeven" aria-expanded="true" aria-controls="collapseSeven">
                            <span class="fas fa-angle-down mr-3"></span>Filtros
                        </button>
                    </h3>
                </div>
                <div id="collapseSeven" class="collapse" aria-labelledby="headingSeven" data-parent="#accordion3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <form id="filtros" name="filtros" action="#" method="POST">
                                    <div class="row">
                                        <div class="col-md-4 col-xl-4">
                                            <div id="div_regional" class="form-group">
                                                <label for="regional">Regional</label></label>
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
                                        <div class="col-md-4 col-xl-4">
                                            <div id="div_funcao" class="form-group">
                                                <label for="funcao">Função</label></label>
                                                <select id="funcao" name="funcao" class="form-control select2" style="width: 100%;">
                                                    <option value="">Selecione a Função</option>
                                                    <?php
                                                    $result8 = $db->prepare("SELECT nome, id 
                                                                                 FROM sys_funcoes
                                                                                 WHERE id IN(5, 4) 
                                                                                 ORDER BY nome ASC");
                                                    $result8->execute();
                                                    while ($funcoes = $result8->fetch(PDO::FETCH_ASSOC)) {
                                                        if ($pessoa_funcao == $funcoes['id']) {
                                                            ?>
                                                            <option selected="true" value='<?= $funcoes['id']; ?>'><?= $funcoes['nome']; ?></option>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <option value='<?= $funcoes['id']; ?>'><?= $funcoes['nome']; ?></option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-xl-4">
                                            <div id="div_treinamento" class="form-group">
                                                <label for="treinamento">Treinamento</label></label>
                                                <select id="treinamento" name="treinamento" class="form-control select2" style="width: 100%;">
                                                    <option value="">Selecione a opção</option>
                                                    <option <?= $pessoa_treinamento == 1 ? "selected='true'" : ""; ?> value="1">Fez o treinamento</option>
                                                    <option <?= $pessoa_treinamento == 0 ? "selected='true'" : ""; ?> value="0">Não fez o treinamento</option>
                                                    <option <?= $pessoa_treinamento == 2 ? "selected='true'" : ""; ?> value="2">Não registrado </option>
                                                </select>
                                            </div>
                                        </div>
                                        <!--<div class="col-md-2">
                                            <div id="div_endereco_fiscal" class="form-group">
                                                <label for="endereco_fiscal">Tipo <b class="error">*</b></label></label>
                                                <select id="tipo" name="tipo" class="form-control select2" style="width: 100%;">
                                                    <option value="">Selecione o Tipo</option>
                                                    <option <?= $pessoa_tipo == 1 ? "selected='true'" : ""; ?> value="1">Com ajuda de custo</option>
                                                    <option <?= $pessoa_tipo == 2 ? "selected='true'" : ""; ?> value="2">Sem ajuda de custo</option>
                                                </select>
                                            </div>
                                        </div>-->
                                        <div class="col-md-4 col-xl-4">
                                            <div id="div_zona" class="form-group">
                                                <label for="zona">Zona</label></label>
                                                <select id="zona" name="zona" class="form-control select2" style="width: 100%;">
                                                    <option value="">Selecione a Zona</option>
                                                    <?php
                                                    if ($pessoa_regional != "") {
                                                        $result8 = $db->prepare("SELECT z.numero AS ZONA  
                                                                     FROM sys_zonas z  
                                                                     INNER JOIN 2024_secoes AS s ON s.NR_ZONA = z.numero 
                                                                     INNER JOIN 2024_locais_votacao AS v ON v.ID = s.LOCAL_VOTACAO_ID  
                                                                     WHERE v.REGIONAL_ID = ? 
                                                                     GROUP BY z.numero 
                                                                     ORDER BY z.numero ASC");
                                                        $result8->bindValue(1, $pessoa_regional);
                                                        $result8->execute();
                                                    } else {
                                                        $result8 = $db->prepare("SELECT z.numero AS ZONA  
                                                                     FROM sys_zonas z 
                                                                     WHERE 1 
                                                                     GROUP BY z.numero 
                                                                     ORDER BY z.numero ASC");
                                                        $result8->execute();
                                                    }
                                                    while ($zona = $result8->fetch(PDO::FETCH_ASSOC)) {
                                                        if ($pessoa_zona == $zona['ZONA']) {
                                                            ?>
                                                            <option selected="true" value='<?= $zona['ZONA']; ?>'><?= $zona['ZONA']; ?></option>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <option value='<?= $zona['ZONA']; ?>'><?= $zona['ZONA']; ?></option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-xl-4">
                                            <div id="div_secao" class="form-group">
                                                <label for="secao">Seção</label></label>
                                                <select id="secao" name="secao" class="form-control select2" style="width: 100%;">
                                                    <option value="">Selecione a Seção</option>
                                                    <?php
                                                    if ($pessoa_zona != "" && $pessoa_regional != "") {
                                                        $result8 = $db->prepare("SELECT e.NR_SECAO   
                                                                         FROM 2024_secoes e
                                                                         INNER JOIN 2024_locais_votacao AS v ON v.ID = e.LOCAL_VOTACAO_ID 
                                                                         WHERE e.NR_ZONA = ? AND v.REGIONAL_ID = ? 
                                                                         ORDER BY e.NR_SECAO ASC");
                                                        $result8->bindValue(1, $pessoa_zona);
                                                        $result8->bindValue(2, $pessoa_regional);
                                                        $result8->execute();
                                                    } else if ($pessoa_zona != "") {
                                                        $result8 = $db->prepare("SELECT e.NR_SECAO   
                                                                         FROM 2024_secoes e 
                                                                         WHERE e.NR_ZONA = ? 
                                                                         ORDER BY e.NR_SECAO ASC");
                                                        $result8->bindValue(1, $pessoa_zona);
                                                        $result8->execute();
                                                    }

                                                    while ($secao = $result8->fetch(PDO::FETCH_ASSOC)) {
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
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-xl-4">
                                            <div id="div_zona" class="form-group">
                                                <label for="situacao">Status</label></label>
                                                <select id="situacao" name="situacao" class="form-control select2" style="width: 100%;">
                                                    <option value="">Selecione o Status</option>
                                                    <option <?= $pessoa_situacao == 1 ? "selected='true'" : ""; ?> value="1">Ativo</option>
                                                    <option <?= $pessoa_situacao == 2 ? "selected='true'" : ""; ?> value="2">Desistência</option>
                                                    <option <?= $pessoa_situacao == 3 ? "selected='true'" : ""; ?> value="3">Saúde</option>
                                                    <option <?= $pessoa_situacao == 4 ? "selected='true'" : ""; ?> value="4">Requisitado pelo TRE</option>
                                                    <option <?= $pessoa_situacao == 10 ? "selected='true'" : ""; ?> value="10">Distribuidos</option>
                                                    <option <?= $pessoa_situacao == 5 ? "selected='true'" : ""; ?> value="5">Não Distribuidos</option>
                                                    <option <?= $pessoa_situacao == 0 ? "selected='true'" : ""; ?> value="0">Outros</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 col-xl-6">
                                            <div id="div_local" class="form-group">
                                                <label for="local">Local de Votação</label></label>
                                                <select id="local" name="local" class="form-control select2" style="width: 100%;">
                                                    <option value="">Selecione o Local de Votação</option>
                                                    <?php
                                                    if ($pessoa_zona != "" && $pessoa_regional != "" && $pessoa_secao != "") {
                                                        $result8 = $db->prepare("SELECT e.ID, e.NM_LOCAL_VOTACAO   
                                                                         FROM 2024_locais_votacao e 
                                                                         INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = e.ID 
                                                                         WHERE e.NR_ZONA = ? AND e.REGIONAL_ID = ? AND s.NR_SECAO = ?   
                                                                         GROUP BY e.NM_LOCAL_VOTACAO
                                                                         ORDER BY e.NM_LOCAL_VOTACAO ASC");
                                                        $result8->bindValue(1, $pessoa_zona);
                                                        $result8->bindValue(2, $pessoa_regional);
                                                        $result8->bindValue(3, $pessoa_secao);
                                                        $result8->execute();
                                                    } else if ($pessoa_zona != "" && $pessoa_regional != "") {
                                                        $result8 = $db->prepare("SELECT e.ID, e.NM_LOCAL_VOTACAO  
                                                                         FROM 2024_locais_votacao e 
                                                                         WHERE e.NR_ZONA = ? AND e.REGIONAL_ID = ?  
                                                                         GROUP BY e.NM_LOCAL_VOTACAO
                                                                         ORDER BY e.NM_LOCAL_VOTACAO ASC");
                                                        $result8->bindValue(1, $pessoa_zona);
                                                        $result8->bindValue(2, $pessoa_regional);
                                                        $result8->execute();
                                                    } else if ($pessoa_zona != "") {
                                                        $result8 = $db->prepare("SELECT e.ID, e.NM_LOCAL_VOTACAO   
                                                                         FROM 2024_locais_votacao e 
                                                                         WHERE e.NR_ZONA = ?  
                                                                         GROUP BY e.NM_LOCAL_VOTACAO
                                                                         ORDER BY e.NM_LOCAL_VOTACAO ASC");
                                                        $result8->bindValue(1, $pessoa_zona);
                                                        $result8->execute();
                                                    } else if ($pessoa_regional != "") {
                                                        $result8 = $db->prepare("SELECT e.ID, e.NM_LOCAL_VOTACAO   
                                                                         FROM 2024_locais_votacao e 
                                                                         WHERE e.REGIONAL_ID = ?  
                                                                         GROUP BY e.NM_LOCAL_VOTACAO
                                                                         ORDER BY e.NM_LOCAL_VOTACAO ASC");
                                                        $result8->bindValue(1, $pessoa_regional);
                                                        $result8->execute();
                                                    } else if ($pessoa_secao != "") {
                                                        $result8 = $db->prepare("SELECT e.ID, e.NM_LOCAL_VOTACAO   
                                                                         FROM 2024_locais_votacao e 
                                                                         WHERE e.NR_SECAO = ? 
                                                                         GROUP BY e.NM_LOCAL_VOTACAO
                                                                         ORDER BY e.NM_LOCAL_VOTACAO ASC");
                                                        $result8->bindValue(1, $pessoa_secao);
                                                        $result8->execute();
                                                    } else {
                                                        $result8 = $db->prepare("SELECT e.ID, e.NM_LOCAL_VOTACAO   
                                                                         FROM 2024_locais_votacao e 
                                                                         WHERE 1  
                                                                         GROUP BY e.NM_LOCAL_VOTACAO
                                                                         ORDER BY e.NM_LOCAL_VOTACAO ASC");
                                                        $result8->execute();
                                                    }

                                                    while ($local = $result8->fetch(PDO::FETCH_ASSOC)) {
                                                        if ($pessoa_local == $local['ID']) {
                                                            ?>
                                                            <option selected="true" value='<?= $local['ID']; ?>'><?= $local['NM_LOCAL_VOTACAO']; ?></option>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <option value='<?= $local['ID']; ?>'><?= $local['NM_LOCAL_VOTACAO']; ?></option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xl-6">
                                            <div id="div_indicacao" class="form-group">
                                                <label for="indicacao">Indicação</label></label>
                                                <select id="indicacao" name="indicacao" class="form-control select2" style="width: 100%;">
                                                    <option value="">Selecione a Indicação</option>
                                                    <?php
                                                    $result8 = $db->prepare("SELECT i.id, i.nome     
                                                                         FROM 2024_indicacoes i 
                                                                         INNER JOIN 2024_voluntarios_indicacoes AS vi ON vi.indicacao_id = i.id
                                                                         WHERE 1 
                                                                         GROUP BY i.id 
                                                                         ORDER BY i.nome ASC");
                                                    $result8->execute();
                                                    while ($indicacao = $result8->fetch(PDO::FETCH_ASSOC)) {
                                                        if ($pessoa_indicacao == $indicacao['id']) {
                                                            ?>
                                                            <option selected="true" value='<?= $indicacao['id']; ?>'><?= $indicacao['nome']; ?></option>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <option value='<?= $indicacao['id']; ?>'><?= $indicacao['nome']; ?></option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <a href="#" id="filtrar" class="btn btn-primary btn-lg btn-block "><i class="fas fa-filter fa-lg"></i>Filtrar</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- end search bar  -->
        <!-- ============================================================== -->

        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mt-4">
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <?php
                    // Botão "Anterior"
                    if ($pagina > 1) {
                        $paginaAnterior = $pagina - 1;
                        echo "<li class='page-item'><a class='page-link' href='?pagina=$paginaAnterior'>Anterior</a></li>";
                    }

                    // Links de paginação
                    for ($i = 1; $i <= $totalPaginas; $i++) {
                        if ($i == $pagina) {
                            echo "<li class='page-item'><a class='page-link' href='#' style='z-index: 2; color: #fff; text-decoration: none; background-color: #5969ff; border-color: #5969ff;'>$i</a></li> "; // Página atual em negrito
                        } else {
                            echo "<li class='page-item'><a class='page-link' href='?pagina=$i'>$i</a></li>";
                        }
                    }

                    // Botão "Próximo"
                    if ($pagina < $totalPaginas) {
                        $proximaPagina = $pagina + 1;
                        echo "<li class='page-item'><a class='page-link' href='?pagina=$proximaPagina'>Próximo</a></li>";
                    }
                    ?>
                </ul>
            </nav>
        </div>


        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div id="ajax_pesquisa">
                <?php
                $result8 = $db->prepare("SELECT p.resp_cancelamento, p.motivo, p.data_cancelamento, p.endereco, p.nm_mae, p.bsc_bairros_id, p.numero, p.nascimento, p.id, p.nome, p.cpf, p.funcao_id, p.celular, p.zona, p.secao_numero, p.local_votacao, p.cidade, p.tipo, p.usuario_id,      
                                     p.zona_2, p.secao_numero_2, p.bairro_2, p.regional_2, p.municipio_2, p.local_votacao_2, p.status  
                                     FROM 2024_voluntarios p  
                                     INNER JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero
                                     INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID 
                                     LEFT JOIN 2024_voluntarios_indicacoes AS i ON i.voluntario_id = p.id 
                                     WHERE 1 $condicao 
                                     GROUP BY p.id 
                                     ORDER BY p.nome ASC
                                     LIMIT $limite OFFSET $offset");

                if ($pessoa_regional != "") {
                    $result8->bindValue($contador, $pessoa_regional);
                    $contador++;
                }

                if ($pessoa_id != "") {
                    $result8->bindValue($contador, $pessoa_id);
                    $contador++;
                }

                if ($pessoa_funcao != "") {
                    $result8->bindValue($contador, $pessoa_funcao);
                    $contador++;
                }

                if ($pessoa_zona != "") {
                    $result8->bindValue($contador, $pessoa_zona);
                    $contador++;
                }

                if ($pessoa_secao != "") {
                    $result8->bindValue($contador, $pessoa_secao);
                    $contador++;
                }

                if ($pessoa_situacao != "" && $pessoa_situacao != 10) {
                    $result8->bindValue($contador, $pessoa_situacao);
                    $contador2++;
                }

                if ($pessoa_local != "") {
                    $result8->bindValue($contador, $pessoa_local);
                    $contador++;
                }

                if ($pessoa_indicacao != "") {
                    $result8->bindValue($contador, $pessoa_indicacao);
                    $contador++;
                }

                if ($pessoa_tipo != "") {
                    $result8->bindValue($contador, $pessoa_tipo);
                    $contador++;
                }

                if ($pessoa_treinamento != "" && $pessoa_treinamento != 2) {
                    $result8->bindValue($contador, $pessoa_treinamento);
                    $contador++;
                }

                $result8->execute();
                while ($pessoas = $result8->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <!-- ============================================================== -->
                    <!-- card influencer one -->
                    <!-- ============================================================== -->
                    <div class="card px-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-xl-8 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="pl-xl-2 pb-1">
                                        <div class="user-avatar-name d-inline-block">
                                            <h2 class="font-24 m-b-10 strong-alt" style="margin-bottom: 0px;"><?= ctexto($pessoas['nome'], "mai"); ?></h2>
                                        </div>
                                        
                                        <?php
                                        if ($pessoas['secao_numero_2'] != NULL && $pessoas['bairro_2'] != NULL && $pessoas['regional_2'] != NULL) {
                                            ?>
                                            <b style='color: green; margin-left: 10px; font-size: 15px;'>Distribuição Realizada</b>
                                            <?php
                                        }
                                        ?>

                                        <div class="user-avatar-address mt-2">
                                            <p class=" text-dark" style="margin-bottom: -5px;">
                                                <small class="small-alt"><span class="badge <?= status_voluntario($pessoas['status']); ?>"><?= status_voluntario_nome($pessoas['status']); ?></span></small> |
                                                <strong class="strong-alt">CPF: </strong>
                                                <small class="small-alt"><?= $pessoas['cpf']; ?></small> |
                                                <strong class="strong-alt">Função: </strong>
                                                <small class="small-alt"><?= pesquisar("nome", "sys_funcoes", "id", "=", $pessoas["funcao_id"], ""); ?></small> |
                                                <strong class="strong-alt">Zona:</strong>
                                                <small class="small-alt"><?= $pessoas['zona']; ?></small> |
                                                <strong class="strong-alt">Seção: </strong>
                                                <small class="small-alt"><?= $pessoas['secao_numero']; ?><?= retornar_agregacao($pessoas['zona'], $pessoas['secao_numero']); ?></small> |
                                                <strong class="strong-alt">Local: </strong>
                                                <small class="small-alt"><?= wordwrap($pessoas['local_votacao'], 50, "<br/>"); ?></small> |
                                                <strong class="strong-alt">Município: </strong>
                                                <small class="small-alt"><?= pesquisar("nome", "bsc_municipios", "id", "=", $pessoas['cidade'], ""); ?></small>
                                            </p>

                                            <div class="row">
                                                <button onclick="mais_detalhes(<?= $pessoas['id']; ?>)" class="btn btn-link text-brand" data-toggle="collapse" data-target="#collapseSeven_<?= $pessoas['id']; ?>" aria-expanded="true" aria-controls="collapseSeven_<?= $pessoas['id']; ?>">
                                                    <span class="fas fa-angle-down mr-3 text-brand"></span><span id="mais_detalhes_<?= $pessoas['id']; ?>">Mais detalhes</span>
                                                </button>
                                                <div id="collapseSeven_<?= $pessoas['id']; ?>" class="collapse" aria-labelledby="headingSeven" data-parent="#accordion4">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                                                <div class="form-inline ml-4">
                                                                    <p class="text-dark mt-1" style="margin-bottom: -5px;">
                                                                        <strong class="strong-alt">Cadastrado por: </strong>
                                                                        <small class="small-alt"><?= pesquisar("nome", "seg_usuarios", "id", "=", $pessoas['usuario_id'], ""); ?></small> |
                                                                        <strong class="strong-alt">Data de Nascimento: </strong>
                                                                        <small class="small-alt"><?= obterDataBRTimestamp($pessoas["nascimento"]); ?></small> |
                                                                        <strong class="strong-alt">Nome da Mãe: </strong>
                                                                        <small class="small-alt"><?= $pessoas["nm_mae"]; ?></small> |
                                                                        <strong class="strong-alt">Celular: </strong>
                                                                        <small class="small-alt"><?= $pessoas['celular']; ?></small> |<br />
                                                                        <strong class="strong-alt">Endereço: </strong>
                                                                        <small class="small-alt"><?= $pessoas['endereco']; ?></small> |
                                                                        <strong class="strong-alt">Número: </strong>
                                                                        <small class="small-alt"><?= $pessoas['numero']; ?></small> |
                                                                        <strong class="strong-alt">Bairro: </strong>
                                                                        <small class="small-alt"><?= pesquisar("NM_BAIRRO", "bsc_bairros", "ID", "=", $pessoas['bsc_bairros_id'], ""); ?></small>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="cancelamento" <?= $pessoas['status'] != 1 ? "" : "style='display: none;'"; ?> class="card-body">
                                                        <div class="row">
                                                            <p>
                                                            <div class="col-md-12 detalhes-log">
                                                                Desativado por: <b><?= is_numeric($pessoas['resp_cancelamento']) ? pesquisar("nome", "seg_usuarios", "id", "=", $pessoas['resp_cancelamento'], "") : ""; ?></b> em <b><?= obterDataBRTimestamp($pessoas['data_cancelamento']); ?></b> às <b><?= obterHoraTimestamp($pessoas['data_cancelamento']); ?></b>
                                                                - Motivo: <b><?= $pessoas['motivo']; ?></b>
                                                            </div>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="float-xl-right float-none mt-xl-0 mt-4">
                                        <a href="<?= PORTAL_URL; ?>view/fiscal/cadastro/<?= $pessoas['id']; ?>" class="btn btn-outline-light btn-sm"><i class="fas fa-edit fa-lg mr-1"></i>Editar</a>
                                        <?php
                                        if ($pessoas['secao_numero_2'] == NULL && $pessoas['bairro_2'] == NULL && $pessoas['regional_2'] == NULL) {
                                            ?>
                                            <button <?= $pessoas['status'] != 1 ? "style='cursor: pointer;'" : "style='display: none;'"; ?> onclick="ativar_voluntario(<?= $pessoas['id']; ?>)" title="Ativar Voluntário" id="ativar" rel="<?= $pessoas['id']; ?>" class="btn btn-outline-success btn-sm"><i class="fas fa-check mr-1"></i> Ativar</button>
                                            <button <?= $pessoas['status'] == 1 ? "style='cursor: pointer;'" : "style='display: none;'"; ?> onclick="desativar_voluntario(<?= $pessoas['id']; ?>)" title="Desativar Voluntário" id="desativar" rel="<?= $pessoas['id']; ?>" class="btn btn-outline-danger btn-sm"><i class="fas fa-power-off mr-1"></i>Desativar</button>
                                            <?php
                                        } //else if (ver_nivel(1)) {
                                        // echo "<br/><b style='color: green;'>Distribuição Realizada</b>";
                                        // }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <?php
                    // Botão "Anterior"
                    if ($pagina > 1) {
                        $paginaAnterior = $pagina - 1;
                        echo "<li class='page-item'><a class='page-link' href='?pagina=$paginaAnterior'>Anterior</a></li>";
                    }

                    // Links de paginação
                    for ($i = 1; $i <= $totalPaginas; $i++) {
                        if ($i == $pagina) {
                            echo "<li class='page-item'><a class='page-link' href='#' style='z-index: 2; color: #fff; text-decoration: none; background-color: #5969ff; border-color: #5969ff;'>$i</a></li> "; // Página atual em negrito
                        } else {
                            echo "<li class='page-item'><a class='page-link' href='?pagina=$i'>$i</a></li>";
                        }
                    }

                    // Botão "Próximo"
                    if ($pagina < $totalPaginas) {
                        $proximaPagina = $pagina + 1;
                        echo "<li class='page-item'><a class='page-link' href='?pagina=$proximaPagina'>Próximo</a></li>";
                    }
                    ?>
                </ul>
            </nav>
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

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/fiscal/index.js"></script>