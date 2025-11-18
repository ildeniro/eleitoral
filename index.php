<?php

ob_start();
session_start();

//ADICIONAR A CONEXAO E URL AMIGAVEL
include_once("config/Url.php");
include_once("config/geral.php");
include_once("config/session.php");
include_once("config/funcoes.php");

//INSTANCIA A CONEXAO
$db = Conexao::getInstance();

//ATUALIZANDO O CAMPO DO USUÁRIO PARA ONLINE
if (isset($_SESSION['id']) && is_numeric($_SESSION['id'])) {

    $id = $_SESSION['id'];

    //ATUALIZANDO DADOS DA SESSÃO DO USUÁRIO
    $useragent = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match('|MSIE ([0-9].[0-9]{1,2})|', $useragent, $matched)) {
        $browser_version = $matched[1];
        $browser = 'IE';
    } elseif (preg_match('|Opera/([0-9].[0-9]{1,2})|', $useragent, $matched)) {
        $browser_version = $matched[1];
        $browser = 'Opera';
    } elseif (preg_match('|Firefox/([0-9\.]+)|', $useragent, $matched)) {
        $browser_version = $matched[1];
        $browser = 'Firefox';
    } elseif (preg_match('|Chrome/([0-9\.]+)|', $useragent, $matched)) {
        $browser_version = $matched[1];
        $browser = 'Chrome';
    } elseif (preg_match('|Safari/([0-9\.]+)|', $useragent, $matched)) {
        $browser_version = $matched[1];
        $browser = 'Safari';
    } else {
        $browser_version = 0;
        $browser = 'Desconhecido';
    }

    $separa = explode(";", $useragent);
    $so = $separa[1];

    $result2 = $db->prepare("UPDATE seg_sessoes SET host = ?, ip = ?, navegador = ?, sistema_operacional = ?, numero_sessao = ?, data_login = NOW(), atualizacao = NOW() WHERE usuario_id = ?");
    $result2->bindValue(1, $_SERVER["SERVER_NAME"]);
    $result2->bindValue(2, $_SERVER['REMOTE_ADDR']);
    $result2->bindValue(3, $browser . " " . $browser_version);
    $result2->bindValue(4, $so);
    $result2->bindValue(5, session_id());
    $result2->bindValue(6, $id);
    $result2->execute();
}

$modulo = Url::getURL(0);
$mvc = Url::getURL(1);
$arquivomodulo = Url::getURL(2);
$parametromodulo = Url::getURL(3);

//VERIFICA SE O ARQUIVO EXISTE E EXIBI
if (file_exists($modulo . ".php") && $modulo != 'portal') {
    include_once $modulo . ".php";
    sessionOn();
    exit();
}

if ($modulo == 'index.php' || $modulo == 'index' || $modulo == '' || $modulo == null) {
    $modulo = "login";
    include_once $modulo . ".php";
    sessionOn();
    exit();
} else if ($mvc == 'login.php' || $mvc == 'login') {
    include_once $modulo . "/" . $mvc . ".php";
    sessionOn();
    exit();
} else if ($mvc == 'painel.php' || $mvc == 'painel') {
    include_once $modulo . "/" . $mvc . ".php";
    sessionOn();
    exit();
} else if ($mvc == 'logout.php' || $mvc == 'logout') {
    include_once $modulo . "/" . $mvc . ".php";
    sessionOn();
    exit();
} else if ($mvc == 'autenticar.php' || $mvc == 'autenticar') {
    include_once $modulo . "/" . $mvc . ".php";
    sessionOn();
    exit();
} else if ($mvc == 'recuperar.php' || $mvc == 'recuperar') {
    include_once $modulo . "/" . $mvc . ".php";
    sessionOn();
    exit();
} else if ($mvc == 'esqueceu_senha' || $mvc == 'esqueceu_senha.php') {
    include_once $modulo . "/" . $mvc . ".php";
    sessionOn();
    exit();
} else if ($mvc == 'dashboard' || $mvc == 'dashboard.php') {
    include_once $modulo . "/" . $mvc . ".php";
    sessionOn();
    exit();
} else if ($mvc == 'pdf' || $mvc == 'pdf.php') {
    include_once $modulo . "/" . $mvc . ".php";
    sessionOn();
    exit();
}  else {
    if ($arquivomodulo == 'index.php' || $arquivomodulo == 'index' || $arquivomodulo == '' || $arquivomodulo == null) {
        //VERIFICA SE O ARQUIVO EXISTE E EXIBI
        if (file_exists($modulo . '/' . $mvc . '/' . "index.php")) {
            include_once $modulo . '/' . $mvc . '/' . "index.php";
            sessionOn();
            exit();
        } else {
            include_once "404.php";
            sessionOn();
            exit();
        }
    } else {
        if ($arquivomodulo == '' || $arquivomodulo == null) {
            //VERIFICA SE O ARQUIVO EXISTE E EXIBI
            if (file_exists($modulo . '/' . $mvc . '/' . "index.php")) {
                include_once $modulo . '/' . $mvc . '/' . "index.php";
                sessionOn();
                exit();
            } else {
                include_once "404.php";
                sessionOn();
                exit();
            }
        } else {
            //VERIFICA SE O ARQUIVO EXISTE E EXIBI
            if (file_exists($modulo . '/' . $mvc . '/' . $arquivomodulo . ".php")) {
                include_once $modulo . '/' . $mvc . '/' . $arquivomodulo . ".php";
                sessionOn();
                exit();
            } else {
                include_once "404.php";
                sessionOn();
                exit();
            }
        }
    }//END IF
}//END IF
?>  