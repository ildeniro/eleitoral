<?php
include("template/layout/dashboard/topo.php");
?>

<?php
$perfil = "";

$id = (!isset($_POST['id']) && isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : 0 ) );
$param = Url::getURL(3);
$param = $param == '' && $id != '' ? $id : $param;

if (!ver_nivel(2) && $_SESSION['id'] != $param) {
    msg('Você não possui permissão para acessar essa área.');
    url(PORTAL_URL . 'view/admin/dashboard');
}

if ($param != null && $param != '' && $param != NULL && $param != 0) {
    $id = $param;

    $result = $db->prepare("SELECT *  
                 FROM seg_usuarios u 
                 WHERE u.id = ?");
    $result->bindValue(1, $id);
    $result->execute();
    $dados_usuario = $result->fetch(PDO::FETCH_ASSOC);

    $usuario_id = $dados_usuario['id'];
    $usuario_nome = $dados_usuario['nome'];
    $usuario_login = $dados_usuario['login'];
    $usuario_status = $dados_usuario['status'];
    $usuario_email = $dados_usuario['email'];
    $usuario_contato = $dados_usuario['telefone_celular'];
    $usuario_foto = $dados_usuario['foto'];
} else {
    $usuario_id = "";
    $usuario_nome = "";
    $usuario_login = "";
    $usuario_status = 1;
    $usuario_email = "";
    $usuario_foto = "";
    $usuario_contato = "";
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
                                            <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/admin/dashboard" class="breadcrumb-link">Início</a></li>
                                            <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/usuarios" class="breadcrumb-link">Usuários</a></li>
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
                                <input type="hidden" id="id" name="id" value="<?= $usuario_id ?>"/>
                                <div class="row">
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <div class="row col-xl-12 col-lg-12">
                                            <h5 class="alt-h5-border-blue">DADOS BÁSICOS</h5>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-2">
                                        <div id="div_nome" class="form-group">
                                            <label for="nome">NOME <b class="error">*</b></label>
                                            <input type="text" class="form-control" name="nome" id="nome" placeholder="Nome Completo" value="<?= $usuario_nome; ?>">
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12 mb-2">
                                        <div id="div_email" class="form-group">
                                            <label for="email">E-MAIL</label>
                                            <input type="email" class="form-control" name="email" id="email" placeholder="E-mail" value="<?= $usuario_email; ?>">
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12 mb-2">
                                        <div id="div_contato" class="form-group">
                                            <label for="contato">CONTATO <b class="error">*</b></label>
                                            <input type="text" class="form-control" name="contato" id="contato"  data-mask="(99)99999-9999" placeholder="Telefone de Contato" value="<?= $usuario_contato; ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <hr width="100%" class="mt-4">
                                        <div class="row col-sm-12 col-xl-12 col-lg-12">
                                            <h5 class="alt-h5-border-red">ACESSO</h5>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12 mb-2">
                                        <div id="div_login" class="form-group">
                                            <label for="login">LOGIN <b class="error">*</b></label>
                                            <input type="text" class="form-control" name="login" id="login" placeholder="Login" value="<?= $usuario_login; ?>">
                                        </div>  
                                    </div>

                                    <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12 mb-2">
                                        <div id="div_senha" class="form-group">
                                            <label for="senha">SENHA</label>
                                            <input type="password" class="form-control" name="senha" id="senha" placeholder="Senha">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12 mb-2">
                                        <div id="div_conf_senha" class="form-group">
                                            <label for="confirmar_senha">CONFIRMAR SENHA</label>
                                            <input type="password" class="form-control" name="confirmar_senha" id="confirmar_senha" placeholder="Confirmar Senha">
                                        </div>
                                    </div>
                                </div>

                                <div <?= ver_nivel(1, "") || ver_nivel(3, "") ? "" : "style='display: none'"; ?> class="form-row">
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <hr width="100%" class="mt-4">
                                        <div class="row col-xl-12 col-lg-12">
                                            <h5 class="alt-h5 alt-h5-border-green">NÍVEL DE ACESSO</h5>
                                        </div>
                                    </div>
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <select multiple="multiple" id="my-select" name="my-select[]">
                                                    <?php
                                                    $result6 = $db->prepare("SELECT nome, nivel, descricao 
                                                                         FROM seg_niveis  
                                                                         WHERE 1 
                                                                         ORDER BY nivel ASC");
                                                    $result6->execute();
                                                    while ($permissao = $result6->fetch(PDO::FETCH_ASSOC)) {
                                                        if (is_numeric(pesquisar2("user_id", "seg_permissoes", "user_id", "=", $usuario_id, "nivel", "=", $permissao['nivel'], ""))) {
                                                            ?>
                                                            <option selected="true" value='<?= $permissao['nivel']; ?>'><?= $permissao['nome']; ?> - <?= $permissao['descricao']; ?></option>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <option value='<?= $permissao['nivel']; ?>'><?= $permissao['nome']; ?> - <?= $permissao['descricao']; ?></option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr width="100%" class="mt-4">

                                <div  class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ">
                                    <button class="btn btn-primary" type="submit"><?= $usuario_id == "" ? "Cadastrar" : "Atualizar" ?></button>
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

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/usuarios/cadastrar.js"></script>