<?php
@session_start();
include_once('config/geral.php');
include_once('config/Url.php');

if (isset($_SESSION['id'])) {
    $sessao = $_SESSION['id'];
} else {
    $sessao = 0;
}

$idsessao = session_id();

$db = Conexao::getInstance();

$sair = $db->prepare("UPDATE seg_sessoes_voluntarios SET data_logout = NOW() WHERE usuario_id = ?");
$sair->bindValue(1, $sessao);
$sair->execute();
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <title><?= TITULO ?></title>
        <!-- BEGIN META -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="keywords" content="your,keywords">
        <meta name="description" content="Short explanation about this website">
    </head>
    <body>
        <?php
        $_SESSION['voluntario_id'] = "";
        $_SESSION['voluntario_timeout'] = "";
        $_SESSION['voluntario_nome'] = "";
        $_SESSION['voluntario_funcao'] = "";
        $_SESSION['voluntario_nascimento'] = "";
        $_SESSION['voluntario_cpf'] = "";

        unset($_SESSION['voluntario_id']);
        unset($_SESSION['voluntario_timeout']);
        unset($_SESSION['voluntario_nome']);
        unset($_SESSION['voluntario_funcao']);
        unset($_SESSION['voluntario_nascimento']);
        unset($_SESSION['voluntario_cpf']);
        echo "<script 'text/javascript'>window.location = '" . PORTAL_URL . "portaldofiscal/';</script>";
        ?>
    </body>
</html>