<?php
include("template/layout/dashboard/topo.php");

if (!ver_nivel(1) && !ver_nivel(6)) {
    msg('Você não possui permissão para acessar essa área.');
    url(PORTAL_URL . 'view/admin/dashboard');
}
?>

<?php
if (isset($_POST['local']) && $_POST['local'] != "" && is_numeric($_POST['local'])) {
    $pessoa_local = $_POST['local'];
} else {
    $pessoa_local = "";
}

if (isset($_POST['bairro']) && $_POST['bairro'] != "" && is_numeric($_POST['bairro'])) {
    $pessoa_bairro = $_POST['bairro'];
} else {
    $pessoa_bairro = "";
}

if (isset($_POST['status']) && $_POST['status'] != "" && is_numeric($_POST['status'])) {
    $pessoa_status = $_POST['status'];
} else {
    $pessoa_status = "";
}

if (isset($_POST['situacao']) && $_POST['situacao'] != "" && is_numeric($_POST['situacao'])) {
    $pessoa_situacao = $_POST['situacao'];
} else {
    $pessoa_situacao = "";
}

if (isset($_POST['demandante']) && $_POST['demandante'] != "") {
    $pessoa_demandante = $_POST['demandante'];
} else {
    $pessoa_demandante = "";
}

if (isset($_POST['regional']) && $_POST['regional'] != "" && is_numeric($_POST['regional'])) {
    $pessoa_regional = $_POST['regional'];
} else {
    $pessoa_regional = "";
}

$contador = 1;
$contador2 = 1;
$condicao = "";

if ($pessoa_regional != "") {
    $condicao .= "AND lv.REGIONAL_ID = ? ";
}

if ($pessoa_bairro != "") {
    $condicao .= "AND vo.bairro_id = ? ";
}

if ($pessoa_situacao != "") {
    $condicao .= "AND vo.situacao = ? ";
}

if ($pessoa_demandante != "") {
    $condicao .= "AND p.nome = ? ";
}

if ($pessoa_local != "") {
    $condicao .= "AND vo.local_votacao_id = ? ";
}

// Número de registros por página
$limite = 100;
// Pega a página atual via GET, se não houver será a página 1
$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
// Calcula o OFFSET (deslocamento) para a consulta SQL
$offset = ($pagina - 1) * $limite;

$sqlTotal = $db->prepare("SELECT vo.bairro_id, vo.local_votacao_id, vo.responsavel_id, vo.outros, vo.outros_contato, vo.ocorrencia, vo.data_cadastro, vo.id AS ocorrencia_id, vo.situacao, 
                                         p.endereco, p.nm_mae, p.bsc_bairros_id, p.numero, p.nascimento, 
                                         p.id, p.nome, p.cpf, p.funcao_id, p.celular, p.zona, p.secao_numero, 
                                         p.local_votacao, p.cidade, p.tipo, p.usuario_id,      
                                         p.zona_2, p.secao_numero_2, p.bairro_2, p.regional_2, p.municipio_2, 
                                         p.local_votacao_2, p.status  
                                         FROM 2024_voluntarios_ocorrencias AS vo 
                                         LEFT JOIN 2024_voluntarios AS p ON p.id = vo.voluntario_id 
                                         LEFT JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero
                                         INNER JOIN 2024_locais_votacao AS lv ON lv.ID = vo.local_votacao_id  
                                         LEFT JOIN 2024_voluntarios_indicacoes AS i ON i.voluntario_id = p.id 
                                         WHERE vo.status = 1 $condicao 
                                          AND (p.secao_numero_2 IS NOT NULL AND p.bairro_2 IS NOT NULL AND p.regional_2 IS NOT NULL OR p.id IS NULL)
                                         GROUP BY vo.id  
                                         ORDER BY p.nome ASC");

if ($pessoa_regional != "") {
    $sqlTotal->bindValue($contador, $pessoa_regional);
    $contador++;
}

if ($pessoa_bairro != "") {
    $sqlTotal->bindValue($contador, $pessoa_bairro);
    $contador++;
}

if ($pessoa_situacao != "") {
    $sqlTotal->bindValue($contador, $pessoa_situacao);
    $contador++;
}

if ($pessoa_demandante != "") {
    $sqlTotal->bindValue($contador, $pessoa_demandante);
    $contador++;
}

if ($pessoa_local != "") {
    $sqlTotal->bindValue($contador, $pessoa_local);
    $contador++;
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
                    <div class="page-header" id="top">
                        <div class="row col-sm-12 col-xl-12 col-lg-12">
                            <h2 class="alt-h5-border-yello">Lista de Ocorrências</h2>
                        </div>
                        <div class="page-breadcrumb">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/admin/dashboard" class="breadcrumb-link-alt-amarelo">Início</a></li>
                                    <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/fiscal/dashboard-fiscalizacao" class="breadcrumb-link-alt-amarelo">Fiscalização</a></li>
                                    <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/fiscal/dashboard-central-fiscal" class="breadcrumb-link-alt-amarelo">Central do Fiscal</a></li>
                                    <li class="breadcrumb-item active text-amarelo" aria-current="page">Ocorrências</li>
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

                <div class="col-12 col-xs-4 col-sm-6 col-lg-6 col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="container col-xs-2">
                                <div class="d-inline-block">
                                    <h5 class="text-muted">Total de ocorrências</h5>
                                    <h2 class="mb-0"><?= $totalRegistros; ?></h2>
                                </div>
                                <div class="float-right icon-circle-small  icon-box-sm  bg-primary-light mt-1">
                                    <i class="fa fa-users fa-fw fa-sm text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xs-4 col-sm-6 col-lg-6 col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="container col-xs-2">
                                <div class="d-inline-block">
                                    <h5 class="text-muted">Solucionadas</h5>
                                    <h2 class="mb-0"><?= total_ocorrencias(1); ?></h2>
                                </div>
                                <div class="float-right icon-circle-small  icon-box-sm  bg-success-light mt-1">
                                    <i class="fa fa-user-secret fa-fw fa-sm text-success"></i>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-12 col-xs-4 col-sm-6 col-lg-6 col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="container col-xs-2">
                                <div class="d-inline-block">
                                    <h5 class="text-muted">Não Solucionadas</h5>
                                    <h2 class="mb-0"><?= total_ocorrencias(0); ?></h2>
                                </div>
                                <div class="float-right icon-circle-small  icon-box-sm  bg-success-light mt-1">
                                    <i class="fa fa-address-card fa-fw fa-sm text-success"></i>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-12 col-xs-4 col-sm-6 col-lg-6 col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="container col-xs-2">
                                <div class="d-inline-block">
                                    <h5 class="text-muted">Encaminhado</h5>
                                    <h2 class="mb-0"><?= encaminhamento_qtd(1); ?></h2>
                                </div>
                                <div class="float-right icon-circle-small  icon-box-sm  bg-success-light mt-1">
                                    <i class="fa fa-address-card fa-fw fa-sm text-success"></i>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-12 col-xs-4 col-sm-6 col-lg-6 col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="container col-xs-2">
                                <div class="d-inline-block">
                                    <h5 class="text-muted">Sem Encaminhamento</h5>
                                    <h2 class="mb-0"><?= encaminhamento_qtd(0); ?></h2>
                                </div>
                                <div class="float-right icon-circle-small  icon-box-sm  bg-success-light mt-1">
                                    <i class="fa fa-address-card fa-fw fa-sm text-success"></i>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card px-3 bg-warning">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12">
                            <input class="form-control form-control-sm" type="search" id="pesquisar" name="pesquisar" placeholder="Pesquisar" aria-label="Search">
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <div class="float-xl-right float-none mt-xl-0 mt-4">
                                <a href="<?= PORTAL_URL; ?>view/fiscal/cadastro-ocorrencias" id="gerar" class="btn btn-success btn-sm"><i class="fas fa-plus mr-1"></i>Adicionar Ocorrência</a>
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
                        <button class="btn btn-link btn-link-brand" data-toggle="collapse" data-target="#collapseSeven" aria-expanded="true" aria-controls="collapseSeven">
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
                                        <div class="col-md-12 col-xl-6">
                                            <div id="div_regional" class="form-group">
                                                <label for="regional">Regional</label></label>
                                                <select class="form-control" id="regional" name="regional">
                                                    <option value="">Todas as regionais</option>
                                                    <?php
                                                    $result8 = $db->prepare("SELECT r.nome, r.id 
                                                                             FROM sys_regionais AS r
                                                                             INNER JOIN 2024_locais_votacao AS lv ON lv.REGIONAL_ID = r.id 
                                                                             INNER JOIN 2024_voluntarios_ocorrencias AS vo ON vo.local_votacao_id = lv.ID
                                                                             WHERE 1 
                                                                             GROUP BY r.id 
                                                                             ORDER BY r.nome ASC");
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
                                        <div class="col-md-12 col-xl-6">
                                            <div id="div_local" class="form-group">
                                                <label for="local">Local de Votação</label></label>
                                                <select id="local" name="local" class="form-control select2" style="width: 100%;">
                                                    <option value="">Selecione o Local de Votação</option>
                                                    <?php
                                                    $result811 = $db->prepare("SELECT lv.ID, lv.NM_LOCAL_VOTACAO  
                                                                               FROM 2024_locais_votacao lv 
                                                                               INNER JOIN 2024_voluntarios_ocorrencias AS vo ON vo.local_votacao_id = lv.ID 
                                                                               WHERE 1   
                                                                               ORDER BY lv.NM_LOCAL_VOTACAO ASC");
                                                    $result811->execute();
                                                    while ($local = $result811->fetch(PDO::FETCH_ASSOC)) {
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
                                            <div id="div_bairro" class="form-group">
                                                <label for="bairro">Bairro</label></label>
                                                <select id="bairro" name="bairro" class="form-control select2" style="width: 100%;">
                                                    <option value="">Selecione o Bairro</option>
                                                    <?php
                                                    $result82 = $db->prepare("SELECT b.NM_BAIRRO AS BAIRRO, b.ID 
                                                                              FROM bsc_bairros AS b 
                                                                              INNER JOIN 2024_voluntarios_ocorrencias AS vo ON vo.bairro_id = b.ID
                                                                              WHERE 1  
                                                                              ORDER BY b.NM_BAIRRO ASC");
                                                    $result82->execute();
                                                    while ($bairros = $result82->fetch(PDO::FETCH_ASSOC)) {
                                                        if ($pessoa_bairro == $bairros['ID']) {
                                                    ?>
                                                            <option selected="true" value='<?= $bairros['ID']; ?>'><?= $bairros['BAIRRO']; ?></option>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <option value='<?= $bairros['ID']; ?>'><?= $bairros['BAIRRO']; ?></option>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-xl-6">
                                            <div id="div_demandante" class="form-group">
                                                <label for="demandante">Demandante</label></label>
                                                <select id="demandante" name="demandante" class="form-control select2" style="width: 100%;">
                                                    <option value="">Selecione o Demandante</option>
                                                    <?php
                                                    $result823 = $db->prepare("SELECT v.nome AS voluntario   
                                                                               FROM 2024_voluntarios_ocorrencias AS vo 
                                                                               INNER JOIN 2024_voluntarios AS v ON v.id = vo.voluntario_id 
                                                                               WHERE vo.outros IS NULL   
                                                                               ORDER BY v.nome ASC");
                                                    $result823->execute();
                                                    while ($demandantes = $result823->fetch(PDO::FETCH_ASSOC)) {
                                                        if ($pessoa_demandante == $demandantes['voluntario']) {
                                                    ?>
                                                            <option selected="true" value='<?= $demandantes['voluntario']; ?>'><?= ctexto($demandantes['voluntario'], "mai"); ?></option>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <option value='<?= $demandantes['voluntario']; ?>'><?= ctexto($demandantes['voluntario'], "mai"); ?></option>
                                                        <?php
                                                        }
                                                    }

                                                    $result8234 = $db->prepare("SELECT vo.outros AS voluntario, vo.outros_contato  
                                                                                FROM 2024_voluntarios_ocorrencias AS vo 
                                                                                WHERE vo.outros IS NOT NULL   
                                                                                ORDER BY vo.outros ASC");
                                                    $result8234->execute();
                                                    while ($demandantes_outros = $result8234->fetch(PDO::FETCH_ASSOC)) {
                                                        if ($pessoa_demandante == $demandantes_outros['voluntario']) {
                                                        ?>
                                                            <option selected="true" value='<?= $demandantes_outros['voluntario']; ?>'><?= ctexto($demandantes_outros['voluntario'], "mai"); ?></option>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <option value='<?= $demandantes_outros['voluntario']; ?>'><?= ctexto($demandantes_outros['voluntario'], "mai"); ?></option>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl-6">
                                            <div id="div_situacao" class="form-group">
                                                <label for="situacao">Situação</label></label>
                                                <select id="situacao" name="situacao" class="form-control select2" style="width: 100%;">
                                                    <option value="">Selecione a Situação</option>
                                                    <option <?= $pessoa_situacao == 1 ? "selected='true'" : ""; ?> value="1">Solucionado</option>
                                                    <option <?= $pessoa_situacao == 0 ? "selected='true'" : ""; ?> value="0">Não Solucionado</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl-6">
                                            <div id="div_status" class="form-group">
                                                <label for="status">Status</label></label>
                                                <select id="status" name="status" class="form-control select2" style="width: 100%;">
                                                    <option value="">Selecione o Status</option>
                                                    <option <?= $pessoa_status == 1 ? "selected='true'" : ""; ?> value="1">Encaminhado</option>
                                                    <option <?= $pessoa_status == 0 ? "selected='true'" : ""; ?> value="0">Sem Encaminhamento</option>
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
                        echo "<li class='page-item'><a class='page-link page-link-alt' href='?pagina=$paginaAnterior'>Anterior</a></li>";
                    }

                    // Links de paginação
                    for ($i = 1; $i <= $totalPaginas; $i++) {
                        if ($i == $pagina) {
                            echo "<li class='page-item'><a class='page-link page-link-alt' href='#' style='z-index: 2; color: #fff; text-decoration: none; background-color: #ffc108; border-color: #ffc108;'>$i</a></li> "; // Página atual em negrito
                        } else {
                            echo "<li class='page-item'><a class='page-link page-link-alt' href='?pagina=$i'>$i</a></li>";
                        }
                    }

                    // Botão "Próximo"
                    if ($pagina < $totalPaginas) {
                        $proximaPagina = $pagina + 1;
                        echo "<li class='page-item'><a class='page-link page-link-alt' href='?pagina=$proximaPagina'>Próximo</a></li>";
                    }
                    ?>
                </ul>
            </nav>
        </div>

        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div id="ajax_pesquisa">
                <?php
                $result83 = $db->prepare("SELECT vo.bairro_id, vo.local_votacao_id, vo.responsavel_id, vo.outros, vo.outros_contato, vo.ocorrencia, vo.data_cadastro, vo.id AS ocorrencia_id, vo.situacao, 
                                         p.endereco, p.nm_mae, p.bsc_bairros_id, p.numero, p.nascimento, 
                                         p.id, p.nome, p.cpf, p.funcao_id, p.celular, p.zona, p.secao_numero, 
                                         p.local_votacao, p.cidade, p.tipo, p.usuario_id,      
                                         p.zona_2, p.secao_numero_2, p.bairro_2, p.regional_2, p.municipio_2, 
                                         p.local_votacao_2, p.status  
                                         FROM 2024_voluntarios_ocorrencias AS vo 
                                         LEFT JOIN 2024_voluntarios AS p ON p.id = vo.voluntario_id 
                                         LEFT JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero
                                         INNER JOIN 2024_locais_votacao AS lv ON lv.ID = vo.local_votacao_id 
                                         LEFT JOIN 2024_voluntarios_indicacoes AS i ON i.voluntario_id = p.id 
                                         WHERE vo.status = 1 $condicao 
                                         AND (p.secao_numero_2 IS NOT NULL AND p.bairro_2 IS NOT NULL AND p.regional_2 IS NOT NULL OR p.id IS NULL)
                                         GROUP BY vo.id  
                                         ORDER BY p.nome ASC
                                         LIMIT $limite OFFSET $offset");

                if ($pessoa_regional != "") {
                    $result83->bindValue($contador2, $pessoa_regional);
                    $contador2++;
                }

                if ($pessoa_bairro != "") {
                    $result83->bindValue($contador2, $pessoa_bairro);
                    $contador2++;
                }

                if ($pessoa_situacao != "") {
                    $result83->bindValue($contador2, $pessoa_situacao);
                    $contador2++;
                }

                if ($pessoa_demandante != "") {
                    $result83->bindValue($contador2, $pessoa_demandante);
                    $contador2++;
                }

                if ($pessoa_local != "") {
                    $result83->bindValue($contador2, $pessoa_local);
                    $contador2++;
                }

                $result83->execute();
                while ($pessoas = $result83->fetch(PDO::FETCH_ASSOC)) {

                    $result335 = $db->prepare("SELECT ve.id, ve.descricao, u.nome AS responsavel, ve.data_update       
                                               FROM 2024_voluntarios_encaminhamentos AS ve
                                               INNER JOIN seg_usuarios AS u ON u.id = ve.responsavel_id 
                                               WHERE ve.ocorrencia_id = ? AND ve.status = 1");
                    $result335->bindValue(1, $pessoas["ocorrencia_id"]);
                    $result335->execute();

                    $qtd_encaminhamentos = $result335->rowCount();

                    if ($pessoa_status == 1 && $qtd_encaminhamentos >= 1 || $pessoa_status == 0 && $qtd_encaminhamentos == 0 || $pessoa_status == "") {

                        $bairro = is_numeric($pessoas['bairro_id']) ? pesquisar("NM_BAIRRO", "bsc_bairros", "ID", "=", $pessoas['bairro_id'], "") : "";
                        $regional = is_numeric($pessoas['bairro_id']) ? pesquisar("REGIONAL_NOME", "bsc_bairros", "ID", "=", $pessoas['bairro_id'], "") : "";
                ?>
                        <!-- ============================================================== -->
                        <!-- card influencer one -->
                        <!-- ============================================================== -->
                        <div class="card px-3">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12">
                                        <div class="pl-xl-2 ">
                                            <div class="m-b-0">
                                                <div class="user-avatar-name d-inline-block">
                                                    <h2 class="font-20 strong-alt" style="margin-bottom: 0px;"><strong class="strong-alt">Demandante: </strong><?= $pessoas['nome'] != "" && $pessoas['nome'] != null ? ctexto($pessoas['nome'], "mai") : ctexto($pessoas['outros'], "mai"); ?></h2>
                                                </div>
                                            </div>
                                            <div <?= $pessoas['outros_contato'] != "" && $pessoas['outros_contato'] != null ? "" : "style='display: none;'"; ?> class="user-avatar-address mt-1">
                                                <p class=" text-dark">
                                                    <strong class="strong-alt">Contato:</strong>
                                                    <small class="small-alt"><?= $pessoas['outros_contato']; ?></small> |
                                                    <strong class="strong-alt">Data/Hora: </strong>
                                                    <small class="small-alt"><?= obterDataBRTimestamp($pessoas['data_cadastro']) . " - " . obterHoraTimestamp($pessoas['data_cadastro']); ?> - <?= is_numeric($pessoas['responsavel_id']) ? ctexto(pesquisar("nome", "seg_usuarios", "id", "=", $pessoas['responsavel_id'], ""), "pri") : ""; ?></small>
                                                </p>
                                            </div>
                                            <div class="user-avatar-address mt-1">

                                                <p class="text-dark mt-1">
                                                    <span class="badge <?= $pessoas['situacao'] == 1 ? "bg-solucionado" : "badge-danger"; ?>"><?= $pessoas['situacao'] == 1 ? "Solucionado" : "Não solucionado"; ?></span>
                                                    <span class="badge <?= $qtd_encaminhamentos >= 1 ? "badge-success" : "badge-grey"; ?>"><?= $qtd_encaminhamentos >= 1 ? "Encaminhado" : "Sem Encaminhamento"; ?></span>
                                                </p>
                                            </div>

                                            <div class="user-avatar-address mt-3">
                                                <p class="text-dark p-alt">
                                                    <strong class="strong-alt">Ocorrência: </strong>
                                                    <small class="small-alt"><?= resume($pessoas['ocorrencia'], "200"); ?></small>
                                                </p>
                                                <div class="row">
                                                    <button onclick="mais_detalhes_ocorrencia(<?= $pessoas['ocorrencia_id']; ?>)" class="btn btn-link btn-link-brand" data-toggle="collapse" data-target="#collapseSeven_<?= $pessoas['ocorrencia_id']; ?>" aria-expanded="true" aria-controls="collapseSeven_<?= $pessoas['ocorrencia_id']; ?>">
                                                        <span class="fas fa-angle-down mr-3"></span><span id="mais_detalhes_<?= $pessoas['ocorrencia_id']; ?>">Mais detalhes</span>
                                                    </button>
                                                </div>

                                                <div id="collapseSeven_<?= $pessoas['ocorrencia_id']; ?>" class="collapse row" aria-labelledby="headingSeven" data-parent="#accordion4">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                                                <div class="form-inline ml-4">
                                                                    <strong class="strong-alt">Local de Votação: </strong>
                                                                    <small class="small-alt">&nbsp;&nbsp;<?= is_numeric($pessoas['local_votacao_id']) ? pesquisar("NM_LOCAL_VOTACAO", "2024_locais_votacao", "ID", "=", $pessoas['local_votacao_id'], "") : ""; ?></small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                                                <div class="form-inline ml-4">
                                                                    <strong class="strong-alt">Bairro: </strong>
                                                                    <small class="small-alt">&nbsp;&nbsp;<?= $bairro; ?></small>&nbsp;&nbsp;&nbsp;&nbsp;

                                                                    <strong class="strong-alt">Regional: </strong>
                                                                    <small class="small-alt">&nbsp;&nbsp;<?= $regional; ?></small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row mt-2">
                                                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                                                <div class="form-inline ml-4">
                                                                    <p class="text-dark mt-1">
                                                                        <strong class="strong-alt"><b>Encaminhamento(s):</b> <button onclick="add_encaminhamento(<?= $pessoas['ocorrencia_id']; ?>)" id="add_encaminhamento" name="add_encaminhamento" class="btn btn-success btn-xs">Novo</button></strong>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div id="cancelamento" class="card-body scroll-distribuidos border mt-2 row">
                                                            <?php
                                                            while ($encaminhamentos = $result335->fetch(PDO::FETCH_ASSOC)) {
                                                            ?>
                                                                <div class=" row mb-2">
                                                                    <div class="col-md-12 detalhes-log ">
                                                                        <p class=" text-dark p-alt">
                                                                            <strong class="strong-alt">Usuário:</strong>
                                                                            <small class="small-alt">
                                                                                <b><?= $encaminhamentos['responsavel']; ?></b> em <b><?= obterDataBRTimestamp($encaminhamentos['data_update']); ?></b> às
                                                                                <b><?= obterHoraTimestamp($encaminhamentos['data_update']); ?></b>
                                                                            </small>
                                                                        </p>
                                                                        <p class=" text-dark p-alt">
                                                                            <strong class="strong-alt"> Descrição: </strong>
                                                                            <small class="small-alt"><b><?= $encaminhamentos['descricao']; ?></b></small>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12">
                                        <div class="float-xl-right float-none mt-xl-0 mt-4">
                                            <a title="Editar Usuário" href="<?= PORTAL_URL; ?>view/fiscal/cadastro-ocorrencias/<?= $pessoas['ocorrencia_id']; ?>"><button class="btn btn-outline-light btn-sm"><i class="fas fa-edit mr-1"></i>Editar</button></a>
                                            <a href="#" onclick="remover(<?= $pessoas['ocorrencia_id']; ?>)" class="btn btn-outline-danger btn-sm"><i class="fas fa-trash mr-1"></i>Remover</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php }
                } ?>
            </div>
        </div>

        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <?php
                    // Botão "Anterior"
                    if ($pagina > 1) {
                        $paginaAnterior = $pagina - 1;
                        echo "<li class='page-item'><a class='page-link page-link-alt' href='?pagina=$paginaAnterior'>Anterior</a></li>";
                    }

                    // Links de paginação
                    for ($i = 1; $i <= $totalPaginas; $i++) {
                        if ($i == $pagina) {
                            echo "<li class='page-item'><a class='page-link page-link-alt' href='#' style='z-index: 2; color: #fff; text-decoration: none; background-color: #ffc108; border-color: #ffc108;'>$i</a></li> "; // Página atual em negrito
                        } else {
                            echo "<li class='page-item'><a class='page-link page-link-alt' href='?pagina=$i'>$i</a></li>";
                        }
                    }

                    // Botão "Próximo"
                    if ($pagina < $totalPaginas) {
                        $proximaPagina = $pagina + 1;
                        echo "<li class='page-item'><a class='page-link page-link-alt' href='?pagina=$proximaPagina'>Próximo</a></li>";
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

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/fiscal/ocorrencias.js"></script>