<?php
include("template/layout/dashboard/topo.php");
?>

<?php
$id = (!isset($_POST['id']) && isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : 0));
$param = Url::getURL(3);
$param = $param == '' && $id != '' ? $id : $param;

if (!ver_nivel(1) && !ver_nivel(21)) {
    msg('Você não possui permissão para acessar essa área.');
    url(PORTAL_URL . 'view/admin/dashboard');
}

if ($param != null && $param != '' && $param != NULL && $param != 0) {
    $id = $param;

    $result = $db->prepare("SELECT vo.outros, vo.outros_contato, vo.id, vo.voluntario_id, vo.ocorrencia, vo.local_votacao_id, vo.bairro_id, vo.situacao       
                            FROM 2024_voluntarios_ocorrencias vo  
                            WHERE vo.id = ?");
    $result->bindValue(1, $id);
    $result->execute();
    $dados_usuario = $result->fetch(PDO::FETCH_ASSOC);

    $usuario_id = $dados_usuario['id'];
    $usuario_demandante = $dados_usuario['voluntario_id'];
    $usuario_ocorrencia = $dados_usuario['ocorrencia'];
    $usuario_local = $dados_usuario['local_votacao_id'];
    $usuario_bairro = $dados_usuario['bairro_id'];
    $usuario_situacao = $dados_usuario['situacao'];
    $usuario_outros = $dados_usuario['outros'];
    $usuario_outros_contato = $dados_usuario['outros_contato'];
} else {
    $usuario_id = "";
    $usuario_demandante = "";
    $usuario_ocorrencia = "";
    $usuario_local = "";
    $usuario_bairro = "";
    $usuario_situacao = 0;
    $usuario_outros = "";
    $usuario_outros_contato = "";
}
?>

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
                                <h2 class="pageheader-title">Cadastro </h2>
                                <div class="page-breadcrumb">
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/admin/dashboard" class="breadcrumb-link-alt-amarelo">Início</a></li>
                                            <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/fiscal/dashboard-fiscalizacao" class="breadcrumb-link-alt-amarelo">Fiscalização</a></li>
                                            <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/fiscal/dashboard-central-fiscal" class="breadcrumb-link-alt-amarelo">Central do Fiscal</a></li>
                                            <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/fiscal/ocorrencias" class="breadcrumb-link-alt-amarelo">Ocorrências</a></li>
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
                            <form id="form_usuario" name="form_usuario" action="#" method="POST">
                                <input type="hidden" id="id" name="id" value="<?= $usuario_id ?>" />

                                <div class="form-row">
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <hr width="100%" class="mt-4">
                                        <div class="row col-sm-12 col-xl-12 col-lg-12">
                                            <h5 class="alt-h5-border-red">OCORRÊNCIA</h5>
                                        </div>
                                    </div>

                                    <div id="div_demandante" <?= $usuario_outros == "" || $usuario_outros == null ? "" : "style='display: none'"; ?> class="col-xl-9 col-lg-6 col-md-12 col-sm-12 col-12 mb-2">
                                        <div id="div_demandantes" class="form-group">
                                            <label for="login">DEMANDANTE <b class="error">*</b></label>
                                            <select id="demandante" name="demandante" class="form-control form-control-lg select2" style="width: 100%;">
                                                <option value="">Escolha o demandante</option>
                                                <?php
                                                $result85 = $db->prepare("SELECT v.id, v.nome, f.nome AS funcao   
                                                             FROM 2024_voluntarios AS v 
                                                             INNER JOIN sys_funcoes AS f ON f.id = v.funcao_id 
                                                             WHERE v.status = 1 AND v.secao_numero_2 IS NOT NULL AND v.bairro_2 IS NOT NULL AND v.regional_2 IS NOT NULL  
                                                             ORDER BY v.nome ASC");
                                                $result85->execute();
                                                while ($demandante = $result85->fetch(PDO::FETCH_ASSOC)) {
                                                    if ($usuario_demandante == $demandante['id']) {
                                                        ?>
                                                        <option selected="true" value='<?= $demandante['id']; ?>'><?= ctexto($demandante['nome'], "pri"); ?> - <?= $demandante['funcao']; ?></option>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <option value='<?= $demandante['id']; ?>'><?= ctexto($demandante['nome'], "pri"); ?> - <?= $demandante['funcao']; ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div <?= $usuario_outros != "" && $usuario_outros != null ? "" : "style='display: none'"; ?> id="div_outros" class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-2"> 
                                        <div id="div_outros_demandante" class="form-group">
                                            <label for="outros">NOME DO DEMANDANTE <b class="error">*</b></label>
                                            <input maxlength="280" class="form-control" type="text" placeholder="Informe o nome do Demandante" style="height: 45px;" id="outros" name="outros" value="<?= $usuario_outros; ?>"/>
                                        </div>
                                    </div>

                                    <div <?= $usuario_outros != "" && $usuario_outros != null ? "" : "style='display: none'"; ?> id="div_outros_contato" class="col-xl-3 col-lg-6 col-md-12 col-sm-12 col-12 mb-2"> 
                                        <div id="div_outros_contatos" class="form-group">
                                            <label for="outros_contato">CONTATO <b class="error">*</b></label>
                                            <input class="form-control" type="text" data-mask="(99)99999-9999" placeholder="Informe o Contato" style="height: 45px;" id="outros_contato" name="outros_contato" value="<?= $usuario_outros_contato; ?>"/>
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12 col-12" style="margin-top: 30px;">
                                        <button <?= $usuario_outros == "" || $usuario_outros == null ? "" : "style='display: none'"; ?> title="Adicionar Novo Demandante" type="button" id="add_outros" name="add_outros" class="btn btn-outline-primary"><i class="fas fa-info mr-1"></i>Outros</button>
                                        <button <?= $usuario_outros != "" && $usuario_outros != null ? "" : "style='display: none'"; ?> title="Listar Demandantes" type="button" id="lista_demandante" name="lista_demandante" class="btn btn-outline-primary"><i class="fas fa-info mr-1"></i>Lista de Fiscais</button>
                                    </div>

                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mb-2">
                                        <div id="div_descricao_ocorrencia" class="form-group">
                                            <label for="descricao_ocorrencia">Descreva a ocorrência</label>
                                            <textarea class="form-control" id="descricao_ocorrencia" name="descricao_ocorrencia" rows="3"><?= $usuario_ocorrencia; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-xl-5 col-lg-6 col-md-12 col-sm-12 col-12 mb-2">
                                        <div id="div_local_votacao" class="form-group">
                                            <label for="local_votacao">Local de Votação</label>
                                            <select id="local_votacao" name="local_votacao" class="form-control form-control-lg select2" style="width: 100%;">
                                                <option value="">Escolha o local de votação</option>
                                                <?php
                                                if (!is_numeric($usuario_local)) {
                                                    $result89 = $db->prepare("SELECT lv.ID, lv.NM_LOCAL_VOTACAO  
                                                                             FROM 2024_locais_votacao lv 
                                                                             WHERE lv.MUNICIPIO_ID = 94  
                                                                             ORDER BY lv.NM_LOCAL_VOTACAO ASC");
                                                    $result89->execute();
                                                    while ($local = $result89->fetch(PDO::FETCH_ASSOC)) {
                                                        ?>
                                                        <option value='<?= $local['ID']; ?>'><?= ctexto($local['NM_LOCAL_VOTACAO'], "pri"); ?></option>
                                                        <?php
                                                    }
                                                } else {
                                                    $result89 = $db->prepare("SELECT lv.ID, lv.NM_LOCAL_VOTACAO  
                                                                             FROM 2024_locais_votacao lv 
                                                                             WHERE lv.MUNICIPIO_ID = 94  
                                                                             ORDER BY lv.NM_LOCAL_VOTACAO ASC");
                                                    $result89->execute();
                                                    while ($local = $result89->fetch(PDO::FETCH_ASSOC)) {
                                                        if ($usuario_local == $local['ID']) {
                                                            ?>
                                                            <option selected="true" value='<?= $local['ID']; ?>'><?= ctexto($local['NM_LOCAL_VOTACAO'], "pri"); ?></option>
                                                            <?php
                                                        }
                                                    }

                                                    $result87 = $db->prepare("SELECT lv.ID, lv.NM_LOCAL_VOTACAO  
                                                                             FROM 2024_locais_votacao lv 
                                                                             WHERE lv.ID <> ? AND lv.MUNICIPIO_ID = 94 
                                                                             ORDER BY lv.NM_LOCAL_VOTACAO ASC");
                                                    $result87->bindValue(1, $usuario_local);
                                                    $result87->execute();
                                                    while ($local2 = $result87->fetch(PDO::FETCH_ASSOC)) {
                                                        ?>
                                                        <option value='<?= $local2['ID']; ?>'><?= ctexto($local2['NM_LOCAL_VOTACAO'], "pri"); ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-md-12 col-sm-12 col-12 mb-2">
                                        <div id="div_bairro" class="form-group">
                                            <label for="bairro">Bairro <b class="error">*</b></label>
                                            <select id="bairro" name="bairro" class="form-control form-control-lg select2" style="width: 100%;">
                                                <option value="">Escolha o bairro</option>
                                                <?php
                                                if (!is_numeric($usuario_bairro)) {
                                                    $result8 = $db->prepare("SELECT NM_BAIRRO AS BAIRRO, ID 
                                                                             FROM bsc_bairros
                                                                             WHERE MUNICIPIO_ID = 94  
                                                                             ORDER BY NM_BAIRRO ASC");
                                                    $result8->execute();
                                                    while ($bairro = $result8->fetch(PDO::FETCH_ASSOC)) {
                                                        ?>
                                                        <option value='<?= $bairro['ID']; ?>'><?= ctexto($bairro['BAIRRO'], "pri"); ?></option>
                                                        <?php
                                                    }
                                                } else {
                                                    $result8 = $db->prepare("SELECT NM_BAIRRO AS BAIRRO, ID 
                                                                             FROM bsc_bairros
                                                                             WHERE MUNICIPIO_ID = 94 
                                                                             ORDER BY NM_BAIRRO ASC");
                                                    $result8->execute();
                                                    while ($bairro = $result8->fetch(PDO::FETCH_ASSOC)) {
                                                        if ($usuario_bairro == $bairro['ID']) {
                                                            ?>
                                                            <option selected="true" value='<?= $bairro['ID']; ?>'><?= ctexto($bairro['BAIRRO'], "pri"); ?></option>
                                                            <?php
                                                        }
                                                    }

                                                    $result82 = $db->prepare("SELECT NM_BAIRRO AS BAIRRO, ID  
                                                                              FROM bsc_bairros 
                                                                              WHERE ID <> ? AND MUNICIPIO_ID = 94 
                                                                              ORDER BY NM_BAIRRO ASC");
                                                    $result82->bindValue(1, $usuario_bairro);
                                                    $result82->execute();
                                                    while ($bairro2 = $result82->fetch(PDO::FETCH_ASSOC)) {
                                                        ?>
                                                        <option value='<?= $bairro2['ID']; ?>'><?= ctexto($bairro2['BAIRRO'], "pri"); ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12 col-12 mb-2">
                                        <div id="div_situacao" class="form-group">
                                            <label for="situacao">Situação <b class="error">*</b></label>
                                            <select id="situacao" name="situacao" class="form-control form-control-lg select2" style="width: 100%;">
                                                <option <?= $usuario_situacao == 0 ? "selected='true'" : ""; ?> value="0">Não solucionado</option>
                                                <option <?= $usuario_situacao == 1 ? "selected='true'" : ""; ?> value="1">Solucionado</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <hr width="100%" class="mt-4">

                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ">
                                    <button class="btn btn-primary" type="submit"><?= $usuario_id != "" && is_numeric($usuario_id) ? "ATUALIZAR" : "CADASTRAR" ?></button>
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

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/fiscal/cadastro-ocorrencia.js"></script>