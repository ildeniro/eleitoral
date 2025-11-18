<?php
include("template/layout/dashboard/topo.php");
?>

<?php
if (!ver_nivel(1) && !ver_nivel(23) && !ver_nivel(6)) {
    msg('Você não possui permissão para acessar essa área.');
    url(PORTAL_URL . 'view/admin/dashboard');
}

$perfil = "";

$id = (!isset($_POST['id']) && isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : 0 ) );
$param = Url::getURL(3);
$param = $param == '' && $id != '' ? $id : $param;

if ($param != null && $param != '' && $param != NULL && $param != 0) {
    $id = $param;

    $result = $db->prepare("SELECT *  
                 FROM 2024_indicacoes u 
                 WHERE u.id = ?");
    $result->bindValue(1, $id);
    $result->execute();
    $dados_usuario = $result->fetch(PDO::FETCH_ASSOC);

    $usuario_id = $dados_usuario['id'];
    $usuario_nome = $dados_usuario['nome'];
    $usuario_telefone = $dados_usuario['telefone'];
    $usuario_funcao = $dados_usuario['funcao'];
} else {
    $usuario_id = "";
    $usuario_nome = "";
    $usuario_telefone = "";
    $usuario_funcao = "";
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
                                            <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/indicadores" class="breadcrumb-link">Indicadores de Referência</a></li>
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
                            <form id="form_indicador" name="form_indicador" action="#" method="POST">
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
                                        <div id="div_contato" class="form-group">
                                            <label for="contato">TELEFONE <b class="error">*</b></label>
                                            <input type="text" class="form-control" name="contato" id="contato"  data-mask="(99) 9 9999-9999" placeholder="Telefone de Contato" value="<?= $usuario_telefone; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-2">
                                        <div id="div_funcao" class="form-group">
                                            <label for="funcao">FUNÇÃO <b class="error">*</b></label>
                                            <input type="text" class="form-control" name="funcao" id="funcao" placeholder="Função" value="<?= $usuario_funcao; ?>">
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

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/indicadores/cadastrar.js"></script>