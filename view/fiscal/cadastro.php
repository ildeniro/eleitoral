<?php
include("template/layout/dashboard/topo.php");
?>

<?php
ver_nivel(6, 1);

$perfil = "";

$id = (!isset($_POST['id']) && isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : 0));
$param = Url::getURL(3);
$param = $param == '' && $id != '' ? $id : $param;

if ($param != null && $param != '' && $param != NULL && $param != 0) {
    $id = $param;

    $result = $db->prepare("SELECT *       
                            FROM 2024_voluntarios p 
                            WHERE p.id = ?
                            GROUP BY p.id");
    $result->bindValue(1, $id);
    $result->execute();
    $dados_pessoa = $result->fetch(PDO::FETCH_ASSOC);

    $pessoa_id = $dados_pessoa['id'];
    $pessoa_nome = $dados_pessoa['nome'];
    $pessoa_sexo = $dados_pessoa['sexo'];
    $pessoa_tipo = $dados_pessoa['tipo'];
    $pessoa_cpf = $dados_pessoa['cpf'];
    $pessoa_celular = $dados_pessoa['celular'];
    $pessoa_funcao = $dados_pessoa['funcao_id'];
    $pessoa_titulo = $dados_pessoa['titulo_eleitor'];
    $pessoa_endereco = $dados_pessoa['endereco'];
    $pessoa_bairro = $dados_pessoa['bsc_bairros_id'];
    $pessoa_numero = $dados_pessoa['numero'];
    $pessoa_mae = $dados_pessoa['nm_mae'];
    $pessoa_whatsapp = $dados_pessoa['whatsapp'];
    $pessoa_deficiencia = $dados_pessoa['deficiencia'];
    $pessoa_pai = $dados_pessoa['nm_pai'];
    $pessoa_nascimento = $dados_pessoa['nascimento'];
    $pessoa_zona = $dados_pessoa['zona'];
    $pessoa_secao = $dados_pessoa['secao_numero'];
    $pessoa_local = $dados_pessoa['local_votacao'];
    $pessoa_rg = $dados_pessoa['rg'];
    $pessoa_orgao_emissor = $dados_pessoa['orgao_expedidor'];
    $pessoa_cep = $dados_pessoa['cep'];
    $pessoa_estado = $dados_pessoa['estado'];
    $pessoa_cidade = $dados_pessoa['cidade'];
    $distribuido = $dados_pessoa['secao_numero_2'] == NULL && $dados_pessoa['bairro_2'] == NULL && $dados_pessoa['regional_2'] == NULL ? false : true;
    $pessoa_pix = $dados_pessoa['pix'];
    $pessoa_banco = $dados_pessoa['banco_id'];
    $pessoa_agencia = $dados_pessoa['agencia'];
    $pessoa_conta = $dados_pessoa['conta'];
    $pessoa_treinamento = is_numeric($dados_pessoa['treinamento']) ? $dados_pessoa['treinamento'] : "";
    $pessoa_data_participacao = $dados_pessoa['treinamento_data_participacao'];
    $pessoa_treinamento_motivo = $dados_pessoa['treinamento_motivo'];
} else {
    $pessoa_id = "";
    $pessoa_nome = "";
    $pessoa_sexo = "";
    $pessoa_tipo = "";
    $pessoa_cpf = "";
    $pessoa_celular = "";
    $pessoa_funcao = "";
    $pessoa_bairro = "";
    $pessoa_titulo = "";
    $pessoa_endereco = "";
    $pessoa_numero = "";
    $pessoa_mae = "";
    $pessoa_pai = "";
    $pessoa_whatsapp = 0;
    $pessoa_deficiencia = 3;
    $pessoa_nascimento = "";
    $pessoa_zona = "";
    $pessoa_secao = "";
    $pessoa_local = "";
    $pessoa_rg = "";
    $pessoa_orgao_emissor = "";
    $pessoa_cep = "";
    $pessoa_estado = "";
    $pessoa_cidade = "";
    $distribuido = false;
    $pessoa_pix = "";
    $pessoa_banco = "";
    $pessoa_agencia = "";
    $pessoa_conta = "";
    $pessoa_treinamento = "";
    $pessoa_data_participacao = "";
    $pessoa_treinamento_motivo = "";
}
?>

<div class="row">
    <div class="container col-xl-alt-10 col-lg-10">
        <div class="row">
            <div class="container col-xl-9 col-xl-alt-10 col-md-12 col-sm-12 col-12">
                <div class="col-xl-10 col-lg-12">
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
                                            <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/admin/dashboard" class="breadcrumb-link">Início</a></li>
                                            <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/fiscal/dashboard-fiscalizacao" class="breadcrumb-link">Fiscalização</a></li>
                                            <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/fiscal" class="breadcrumb-link">Voluntários</a></li>
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
                        <div class="container mt-3">
                            <form id="form_fiscalizacao" name="form_fiscalizacao" action="#" method="POST">
                                <input type="hidden" id="id" name="id" value="<?= $pessoa_id ?>" />
                                <input type="hidden" id="distribuicao" name="distribuicao" value="<?= $distribuicao ?>" />
                                <div class="row">
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <div class="row col-sm-12 col-xl-12 col-lg-12">
                                            <h5 class="alt-h5-border-red">INDICAÇÃO</h5>
                                        </div>
                                    </div>

                                    <div class="col-md-12 col-xl-9 ">
                                        <div id="div_indicacao" class="form-group">
                                            <label for="indicacao">Quem indicou?</label>
                                            <select class="form-control form-control-lg select2" name="indicacao" id="indicacao" multiple="true">
                                                <?php
                                                if (is_numeric($pessoa_id)) {

                                                    $result81 = $db->prepare("SELECT i.id, i.nome   
                                                                                 FROM 2024_voluntarios_indicacoes AS v 
                                                                                 INNER JOIN 2024_indicacoes AS i ON i.id = v.indicacao_id 
                                                                                 WHERE v.voluntario_id = ?
                                                                                 GROUP BY i.id
                                                                                 ORDER BY i.nome ASC");
                                                    $result81->bindValue(1, $pessoa_id);
                                                    $result81->execute();
                                                    while ($indicacao1 = $result81->fetch(PDO::FETCH_ASSOC)) {
                                                        ?>
                                                        <option selected="true" value='<?= $indicacao1['id']; ?>'><?= $indicacao1['nome']; ?></option>
                                                        <?php
                                                    }

                                                    $result811 = $db->prepare("SELECT i.id, i.nome   
                                                                                 FROM 2024_indicacoes AS i
                                                                                 WHERE i.id NOT IN(SELECT indicacao_id FROM 2024_voluntarios_indicacoes WHERE voluntario_id = ?) 
                                                                                 ORDER BY i.nome ASC");
                                                    $result811->bindValue(1, $pessoa_id);
                                                    $result811->execute();
                                                    while ($indicacao11 = $result811->fetch(PDO::FETCH_ASSOC)) {
                                                        ?>
                                                        <option value='<?= $indicacao11['id']; ?>'><?= $indicacao11['nome']; ?></option>
                                                        <?php
                                                    }
                                                } else {
                                                    $result8 = $db->prepare("SELECT nome, id 
                                                                             FROM 2024_indicacoes
                                                                             WHERE status = 1 
                                                                             ORDER BY nome ASC");
                                                    $result8->execute();
                                                    while ($indicacao = $result8->fetch(PDO::FETCH_ASSOC)) {
                                                        ?>
                                                        <option value='<?= $indicacao['id']; ?>'><?= $indicacao['nome']; ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-xl-3 ">
                                        <button class="btn btn-success btn-alt"  type="button" data-toggle="modal" data-target="#exampleModal">Cadastrar Novo</button>
                                    </div>

                                </div>

                                <div <?= is_numeric($pessoa_id) ? "" : "style='display: none;'" ?> id="div_indicador">
                                    <div <?= $distribuido == true ? "style='display: none;'" : ""; ?> class="row">
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                            <hr width="100%" class="mt-4">
                                            <div class="row col-sm-12 col-xl-12 col-lg-12">
                                                <h5 class="alt-h5-border-yello">PERFIL DO VOLUNTÁRIO</h5>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 mb-2">
                                            <label for="funcao_id">Função <b class="error">*</b></label>
                                            <div id="div_funcao_id" class="input-group">

                                                <label class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" <?= $pessoa_funcao == "" || $pessoa_funcao == 5 ? "checked='true'" : ""; ?> id="funcao" name="funcao" value="5" class="custom-control-input" /><span class="custom-control-label">Fiscal de Urna Eleitoral</span>
                                                </label>

                                                <label class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" <?= $pessoa_funcao == 4 ? "checked='true'" : ""; ?> id="funcao" name="funcao" value="4" class="custom-control-input" /><span class="custom-control-label">Supervisor de Local de Votação</span>
                                                </label>

                                            <!--<select <?= $distribuido == true ? "disabled='true'" : ""; ?> name="funcao_id" id="funcao_id" class="form-control select2">
                                                <option value="">Selecione a Função</option>
                                                <?php
                                                /*
                                                  if (!is_numeric($pessoa_id)) {
                                                  $result81 = $db->prepare("SELECT nome, id
                                                  FROM sys_funcoes
                                                  WHERE status = 1 AND id = 5
                                                  ORDER BY nome ASC");
                                                  $result81->execute();
                                                  while ($funcoes = $result81->fetch(PDO::FETCH_ASSOC)) {
                                                  ?>
                                                  <option selected="true" value='<?= $funcoes['id']; ?>'><?= $funcoes['nome']; ?></option>
                                                  <?php
                                                  }

                                                  $result82 = $db->prepare("SELECT nome, id
                                                  FROM sys_funcoes
                                                  WHERE status = 1 AND id = 6
                                                  ORDER BY nome ASC");
                                                  $result82->execute();
                                                  while ($funcoes = $result82->fetch(PDO::FETCH_ASSOC)) {
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
                                                  } else {
                                                  $result83 = $db->prepare("SELECT nome, id
                                                  FROM sys_funcoes
                                                  WHERE status = 1 AND id IN(5, 6)
                                                  ORDER BY nome ASC");
                                                  $result83->execute();
                                                  while ($funcoes = $result83->fetch(PDO::FETCH_ASSOC)) {
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
                                                  } */
                                                ?>
                                            </select>-->
                                            </div>
                                        </div>
                                        <!--                                    <div class="col-md-3">
                                                                                <div id="div_tipo" class="form-group">
                                                                                    <label for="tipo">Tipo <b class="error">*</b></label></label>
                                                                                    <select id="tipo" name="tipo" class="form-control select2" style="width: 100%;">
                                                                                        <option value="">Selecione o Tipo</option>
                                                                                        <option <?= $pessoa_tipo == 1 ? "selected='true'" : ""; ?> value="1">Com ajuda de custo</option>
                                                                                        <option <?= $pessoa_tipo == 2 || !is_numeric($pessoa_id) ? "selected='true'" : ""; ?> value="2">Sem ajuda de custo</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>-->
                                    </div>

                                    <!-- DADOS PESSOAIS -->

                                    <div class="row">
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                            <hr width="100%" class="mt-4">
                                            <div class="row col-sm-12 col-xl-12 col-lg-12">
                                                <h5 class="alt-h5-border-green">DADOS PESSOAIS</h5>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div id="div_cpf_fiscal" class="form-group">
                                                <label for="cpf_fiscal">CPF <b class="error">*</b></label>
                                                <input type="text" class="form-control form-control-lg" data-mask="999.999.999-99" name="cpf_fiscal" id="cpf_fiscal" value="<?= $pessoa_cpf; ?>" />
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div id="div_nome_fiscal" class="form-group">
                                                <label for="nome_fiscal">Nome Completo <b class="error">*</b></label></label>
                                                <input type="text" class="form-control form-control-lg" name="nome_fiscal" id="nome_fiscal" value="<?= $pessoa_nome; ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div id="div_sexo" class="form-group">
                                                <label for="sexo">Sexo </label></label>
                                                <select id="sexo" name="sexo" class="form-control form-control-lg select2" style="width: 100%;">
                                                    <option value="">Escolha o sexo</option>
                                                    <option <?= $pessoa_sexo == 1 ? "selected='true'" : "" ?> value="1">Masculino</option>
                                                    <option <?= $pessoa_sexo == 2 ? "selected='true'" : "" ?> value="2">Feminino</option>
                                                    <option <?= $pessoa_sexo == 3 ? "selected='true'" : "" ?> value="3">Outros</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 col-lg-3">
                                            <div id="div_nascimento" class="form-group">
                                                <label for="nascimento">Data de Nascimento <b class="error">*</b></label>
                                                <input type="date" class="form-control form-control-lg" name="nascimento" id="nascimento" value="<?= $pessoa_nascimento; ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <div id="div_celular_fiscal" class="form-group">
                                                <label for="celular_fiscal">WhatsApp <b class="error">*</b></label>
                                                <input type="text" class="form-control form-control-lg" data-mask="(99) 9 9999-9999" name="celular_fiscal" id="celular_fiscal" value="<?= $pessoa_celular; ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xl-6 col-lg-6">
                                            <div id="div_mae" class="form-group">
                                                <label for="mae">Nome da Mãe <b class="error">*</b></label>
                                                <input type="text" class="form-control form-control-lg" name="mae" id="mae" value="<?= $pessoa_mae; ?>" />
                                            </div>
                                        </div>
                                    </div>
                                    <!-- ENDEREÇO -->
                                    <div class="form-row">
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                            <hr width="100%" class="mt-4">
                                            <div class="row col-sm-12 col-xl-12 col-lg-12">
                                                <h5 class="alt-h5-border-blue">DADOS DO ENDEREÇO</h5>
                                            </div>
                                        </div>
                                        <!--                                    <div class="col-md-2">
                                                                                <div id="div_cep" class="form-group">
                                                                                    <label for="cep">CEP</label>
                                                                                    <input type="text" class="form-control" name="cep" id="cep" placeholder="CEP" value="<?= $pessoa_cep; ?>"/>
                                                                                </div>
                                                                            </div>-->
                                        <!--<div class="col-md-5">
                                            <div id="div_estado" class="form-group">
                                                <label for="estado">Estado <b class="error">*</b></label></label>
                                                <select id="estado" name="estado" class="form-control select2" style="width: 100%;">
                                        <?php
                                        $result8 = $db->prepare("SELECT nome, id 
                                                                             FROM bsc_estados
                                                                             WHERE id = 1 
                                                                             GROUP BY nome 
                                                                             ORDER BY nome ASC");
                                        $result8->execute();
                                        while ($estado = $result8->fetch(PDO::FETCH_ASSOC)) {

                                            if ($pessoa_id == "") {
                                                if ($estado['id'] == 1 || $estado['nome'] == 'Acre') {
                                                    ?>
                                                                                                                                                                                                                                                    <option selected="true" value='<?= $estado['id']; ?>'><?= $estado['nome']; ?></option>
                                                    <?php
                                                } else {
                                                    ?>
                                                                                                                                                                                                                                                    <option value='<?= $estado['id']; ?>'><?= $estado['nome']; ?></option>
                                                    <?php
                                                }
                                            } else {
                                                if ($pessoa_estado == $estado['id']) {
                                                    ?>
                                                                                                                                                                                                                                                    <option selected="true" value='<?= $estado['id']; ?>'><?= $estado['nome']; ?></option>
                                                    <?php
                                                } else {
                                                    ?>
                                                                                                                                                                                                                                                    <option value='<?= $estado['id']; ?>'><?= $estado['nome']; ?></option>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div id="div_cidade" class="form-group">
                                                <label for="cidade">Cidade <b class="error">*</b></label></label>
                                                <select id="cidade" name="cidade" placeholder="Cidade" class="form-control" style="width: 100%">
                                                    <option value="">Escolha primeiro o estado</option>
                                        <?php
                                        if ($pessoa_id != "" && $pessoa_cidade != "" && is_numeric($pessoa_cidade)) {
                                            $result = $db->prepare("SELECT * 
                                                                        FROM bsc_municipios     
                                                                        WHERE id = ?   
                                                                        ORDER BY nome");
                                            $result->bindValue(1, $pessoa_cidade);
                                            $result->execute();
                                            while ($municipio = $result->fetch(PDO::FETCH_ASSOC)) {
                                                ?>
                                                                                                                                                                                    <option selected="true" value="<?= $municipio['id'] ?>"><?= $municipio['nome'] ?></option>
                                                <?php
                                            }

                                            $result2 = $db->prepare("SELECT * 
                                                                        FROM bsc_municipios     
                                                                        WHERE id <> ?  AND estado_id = ?  
                                                                        ORDER BY nome");
                                            $result2->bindValue(1, $pessoa_cidade);
                                            $result2->bindValue(2, $pessoa_estado);
                                            $result2->execute();
                                            while ($municipio2 = $result2->fetch(PDO::FETCH_ASSOC)) {
                                                ?>
                                                                                                                                                                                    <option value="<?= $municipio2['id'] ?>"><?= $municipio2['nome'] ?></option>
                                                <?php
                                            }
                                        } else {
                                            $result = $db->prepare("SELECT * 
                                                                        FROM bsc_municipios     
                                                                        WHERE estado_id = 1    
                                                                        ORDER BY nome");
                                            $result->execute();
                                            while ($municipio = $result->fetch(PDO::FETCH_ASSOC)) {
                                                if ($municipio['id'] == 94) {
                                                    ?>
                                                                                                                                                                                                                                                    <option selected="true" value="<?= $municipio['id'] ?>"><?= $municipio['nome'] ?></option>
                                                    <?php
                                                } else {
                                                    ?>
                                                                                                                                                                                                                                                    <option value="<?= $municipio['id'] ?>"><?= $municipio['nome'] ?></option>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                                </select>
                                            </div>
                                        </div>-->
                                    </div>

                                    <div class="row">
                                        <div class="col-md-10">
                                            <div id="div_endereco_fiscal" class="form-group">
                                                <label for="endereco_fiscal">Endereço <b class="error">*</b></label></label>
                                                <input type="text" class="form-control form-control-lg" name="endereco_fiscal" id="endereco_fiscal" placeholder="Avenida, Rua, Travessa" value="<?= $pessoa_endereco; ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div id="div_numero" class="form-group">
                                                <label for="numero">Número</label>
                                                <input type="text" class="form-control form-control-lg" data-mask="999999" name="numero" id="numero" value="<?= $pessoa_numero == 0 ? "" : $pessoa_numero; ?>" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div <?= $pessoa_cidade == "" || $pessoa_cidade == 94 ? "" : "style='display: none;'"; ?> id="div_bairro" class="form-group">
                                                <label>Bairro <b class="error">*</b></label>
                                                <select id="bairro" name="bairro" class="form-control form-control-lg select2" style="width: 100%;">
                                                    <option value="">Todos os bairros</option>
                                                    <?php
                                                    if (!is_numeric($pessoa_id)) {
                                                        $result8 = $db->prepare("SELECT NM_BAIRRO AS BAIRRO, ID 
                                                                                 FROM bsc_bairros
                                                                                 WHERE MUNICIPIO_ID = 94  
                                                                                 GROUP BY NM_BAIRRO 
                                                                                 ORDER BY NM_BAIRRO ASC");
                                                        $result8->execute();
                                                        while ($bairro = $result8->fetch(PDO::FETCH_ASSOC)) {
                                                            ?>
                                                            <option value='<?= $bairro['ID']; ?>'><?= $bairro['BAIRRO']; ?></option>
                                                            <?php
                                                        }
                                                    } else {
                                                        $result8 = $db->prepare("SELECT NM_BAIRRO AS BAIRRO, ID 
                                                                                 FROM bsc_bairros
                                                                                 WHERE 1 
                                                                                 GROUP BY NM_BAIRRO 
                                                                                 ORDER BY NM_BAIRRO ASC");
                                                        $result8->execute();
                                                        while ($bairro = $result8->fetch(PDO::FETCH_ASSOC)) {
                                                            if ($pessoa_bairro == $bairro['ID']) {
                                                                ?>
                                                                <option selected="true" value='<?= $bairro['ID']; ?>'><?= $bairro['BAIRRO']; ?></option>
                                                                <?php
                                                            }
                                                        }

                                                        if ($pessoa_cidade != "") {
                                                            $result82 = $db->prepare("SELECT NM_BAIRRO AS BAIRRO, ID  
                                                                                                  FROM bsc_bairros 
                                                                                                  WHERE MUNICIPIO_ID = ? 
                                                                                                  GROUP BY NM_BAIRRO 
                                                                                                  ORDER BY NM_BAIRRO ASC");
                                                            $result82->bindValue(1, $pessoa_cidade);
                                                            $result82->execute();
                                                            while ($bairro2 = $result82->fetch(PDO::FETCH_ASSOC)) {
                                                                if ($pessoa_bairro != $bairro2['BAIRRO']) {
                                                                    ?>
                                                                    <option value='<?= $bairro2['ID']; ?>'><?= $bairro2['BAIRRO']; ?></option>
                                                                    <?php
                                                                }
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- FIM ENDEREÇO -->

                                    <div <?= $distribuido == true ? "style='display: none;'" : ""; ?> class="form-row">
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                            <hr width="100%" class="mt-4">
                                            <div class="row col-sm-12 col-xl-12 col-lg-12">
                                                <h5 class="alt-h5-border-rosa">DADOS ELEITORAIS</h5>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12 mb-2">
                                            <label for="validationCustomUsername">Título de Eleitor</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control form-control-lg" name="titulo_fiscal" id="titulo_fiscal" aria-describedby="inputGroupPrepend" value="<?= $pessoa_titulo; ?>">
                                            </div>
                                        </div>

                                        <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12 mb-2">
                                            <div id="div_zona" class="form-group">
                                                <label>Zona <b class="error">*</b></label>
                                                <select id="zona" name="zona" class="form-control form-control-lg select2" style="width: 100%;">
                                                    <option value="">Selecione a Zona</option>
                                                    <?php
                                                    $result85 = $db->prepare("SELECT numero, id 
                                                             FROM sys_zonas 
                                                             WHERE numero IN(1, 9) 
                                                             GROUP BY numero 
                                                             ORDER BY numero ASC");
                                                    $result85->execute();
                                                    while ($zona = $result85->fetch(PDO::FETCH_ASSOC)) {
                                                        if ($pessoa_zona == $zona['id']) {
                                                            ?>
                                                            <option selected="true" value='<?= $zona['id']; ?>'><?= $zona['numero']; ?></option>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <option value='<?= $zona['id']; ?>'><?= $zona['numero']; ?></option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12 mb-2">
                                            <div id="div_secao" class="form-group">
                                                <label>Seção <b class="error">*</b></label>
                                                <select id="secao" name="secao" class="form-control form-control-lg select2" style="width: 100%;">
                                                    <option value="">Selecione primeiro a zona</option>
                                                    <?php
                                                    if ($pessoa_zona != "" && $pessoa_secao != "") {
                                                        $result8 = $db->prepare("SELECT e.NR_SECAO AS SECAO    
                                                            FROM 2024_secoes e 
                                                            WHERE e.NR_ZONA = ? AND e.TIPO = 'Principal'
                                                            ORDER BY e.NR_SECAO ASC");
                                                        $result8->bindValue(1, $pessoa_zona);
                                                        $result8->execute();
                                                        while ($secao = $result8->fetch(PDO::FETCH_ASSOC)) {
                                                            if ($pessoa_secao == $secao['SECAO']) {
                                                                ?>
                                                                <option selected="true" value='<?= $secao['SECAO']; ?>'><?= $secao['SECAO']; ?><?= is_numeric(pesquisar("ID", "2024_secoes", 'NR_SECAO_PRINCIPAL', "=", $secao['SECAO'], "")) ? retornar_agregacao($pessoa_zona, $secao['SECAO']) : ""; ?></option>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <option value='<?= $secao['SECAO']; ?>'><?= $secao['SECAO']; ?><?= is_numeric(pesquisar("ID", "2024_secoes", 'NR_SECAO_PRINCIPAL', "=", $secao['SECAO'], "")) ? retornar_agregacao($pessoa_zona, $secao['SECAO']) : ""; ?></option>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="div_local" <?= $pessoa_local == "" ? "style='display: none;'" : ""; ?> class="row">
                                        <div <?= $distribuido == true ? "style='display: none;'" : ""; ?> class="col-md-12">
                                            <div class="row margin-top-10px margin-bottom-10px">
                                                <div class="col-md-12">
                                                    <div id="div_local_texto" class="title text-center rs-local-blue"><?= $pessoa_local; ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr width="100%" class="mt-4">

                                    <div class="col-md-12">
                                        <label>Condição Fisica Especial?</label>
                                        <div class="input-group">
                                            <label class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" <?= $pessoa_deficiencia == 3 || $pessoa_deficiencia == "" ? "checked='true'" : "" ?> id="condicao" name="condicao" value="3" class="custom-control-input" /><span class="custom-control-label">Não</span>
                                            </label>

                                            <label class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" <?= $pessoa_deficiencia == 1 ? "checked='true'" : "" ?> id="condicao" name="condicao" value="1" class="custom-control-input" /><span class="custom-control-label">Deficiente</span>
                                            </label>

                                            <label class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" <?= $pessoa_deficiencia == 2 ? "checked='true'" : "" ?> id="condicao" name="condicao" value="2" class="custom-control-input" /><span class="custom-control-label">Mobilidade Reduzida</span>
                                            </label>
                                        </div>

                                        <!--                                    <div class="form-check">
                                                                                <input id="deficiencia" name="deficiencia" value="1" <?= $pessoa_deficiencia == 1 ? "checked='true'" : "" ?> class="form-check-input" type="checkbox" />
                                                                                Pessoa com deficiência ou mobilidade reduzida
                                                                                <span class="form-check-sign">
                                                                                    <span class="check"></span>
                                                                                </span>
                                                                            </div>-->
                                    </div>

                                    <div <?= is_numeric($pessoa_id) || is_numeric($pessoa_treinamento) ? "" : "style='display: none;'"; ?> class="div-treinamento" id="div_treinamento">
                                        <div class="form-row treinamento-padding">
                                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                                <div class="row col-sm-12 col-xl-12 col-lg-12">
                                                    <h5 class="alt-h5-border-red">TREINAMENTO</h5>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-row treinamento-padding">
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 mb-2">
                                                <label for="treinamento">O voluntário fez o treinamento?</label>
                                                <div id="div_treinamento" class="input-group">

                                                    <label class="custom-control custom-radio custom-control-inline">
                                                        <input type="radio" <?= $pessoa_treinamento == 1 ? "checked='true'" : "" ?> id="treinamento_sim" name="treinamento" value="1" class="custom-control-input" /><span class="custom-control-label">Sim</span>
                                                    </label>

                                                    <label class="custom-control custom-radio custom-control-inline">
                                                        <input type="radio" <?= $pessoa_treinamento == 0 ? "checked='true'" : "" ?> id="treinamento_nao" name="treinamento" value="0" class="custom-control-input" /><span class="custom-control-label">Não</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="div_data_participacao" <?= $pessoa_treinamento == 1 ? "" : "style='display: none;'" ?> class="form-row treinamento-padding">
                                            <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12 mb-2">
                                                <label for="data_participacao">Data de Participação <b class="error">*</b></label>
                                                <div class="input-group">
                                                    <input type="date" class="form-control form-control-lg" name="data_participacao" id="data_participacao" aria-describedby="inputGroupPrepend" value="<?= convertDataBR2ISO(obterDataBRTimestamp($pessoa_data_participacao)); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div id="div_motivo" <?= $pessoa_treinamento == 0 ? "" : "style='display: none;'" ?> class="form-row treinamento-padding">
                                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mb-2">
                                                <label for="motivo">Motivo <b class="error">*</b></label>
                                                <div class="input-group">
                                                    <textarea id="motivo" name="motivo" class="form-control"><?= $pessoa_treinamento_motivo; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr width="100%" class="mt-4">

                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <button class="btn btn-primary" type="submit"><?= is_numeric($pessoa_id) ? "Atualizar" : "Cadastrar"; ?></button>
                                    </div>
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
include("template/layout/componets/modal-form-fiscal.php");
include("template/layout/dashboard/rodape.php");
?>

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/fiscal/cadastrar.js"></script>