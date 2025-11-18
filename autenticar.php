<?php

include_once('config/geral.php');
$db = Conexao::getInstance();

$msg = array();

$login_check = isset($_POST['login_check']) && $_POST['login_check'] == 1 ? 1 : 0;

try {
    //PEGAR DADOS DE LOGIN
    $login = strip_tags($_POST['login']);
    //PEGAR DADOS DE LOGIN
    $login = strip_tags($_POST['login']);
    $senha = isset($_COOKIE['siseleitoralsenha']) && strlen($_COOKIE['siseleitoralsenha']) > 1 ? $_COOKIE['siseleitoralsenha'] : strip_tags(sha1($_POST['senha']));

    if ($login_check == 1) {
        $expire = ( time() + ( 30 * 24 * 3600 ) );
        setcookie("siseleitorallogin", $login, $expire);
        setcookie("siseleitoralsenha", $senha, $expire);
    } else {
        $expire = ( time() + ( 30 * 24 * 3600 ) );
        setcookie("siseleitorallogin", "", $expire);
        setcookie("siseleitoralsenha", "", $expire);
        unset($_COOKIE['siseleitorallogin']);
        unset($_COOKIE['siseleitoralsenha']);
        setcookie('siseleitorallogin', null, -1, '/');
        setcookie('siseleitoralsenha', null, -1, '/');
    }

    //SQL PARA VERIFICAÇÃO DE LOGIN EXISTENTE
    $result = $db->prepare("SELECT *     
                            FROM seg_usuarios AS su 
                            WHERE su.login = ?");
    $result->bindParam(1, $login);
    $result->execute();
    $num = $result->rowCount();

    if ($num > 0) {
        //PEGA OS DADOS DO USUARIO, CASO TENHA ACESSO
        $dadosUsuario = $result->fetch(PDO::FETCH_ASSOC);

        //VERIFICA SE A SENHA INFORMADA É IGUAL DO USUARIO
        if ($senha == $dadosUsuario['senha']) {

            if ($dadosUsuario['status'] == 1) {

                $id = $dadosUsuario['id'];

                //CRIAR O TIMEOUT DA SESSÃO PARA EXPIRAR
                $_SESSION['timeout'] = time();
                //CRIAR AS SESSÕES DO USUARIO
                $_SESSION['id'] = $id;
                $_SESSION['nome'] = $dadosUsuario['nome'];
                $_SESSION['telefone1'] = $dadosUsuario['telefone_celular'];
                $_SESSION['email1'] = $dadosUsuario['email'];
                $_SESSION['telefone2'] = $dadosUsuario['telefone_institucional'];
                $_SESSION['email2'] = $dadosUsuario['email_institucional'];
                $_SESSION['login'] = $login;
                $_SESSION['foto'] = $dadosUsuario['foto'];
                $_SESSION['online'] = 1;
                $_SESSION['foto_cut'] = "";
                $_SESSION['foto_origin'] = "";

                //ATUALIZANDO O STATUS ONLINE DO USUARIO
                $result = $db->prepare("UPDATE seg_usuarios SET status = '1' WHERE id = ?");
                $result->bindValue(1, $id);
                $result->execute();

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

                //ATUALIZANDO O CAMPO DO USUÁRIO PARA ONLINE
                $atualizar = $db->prepare("UPDATE seg_usuarios SET online = 1 WHERE id = ?");
                $atualizar->bindValue(1, $id);
                $atualizar->execute();

                //MENSAGEM DE SUCESSO
                $msg['id'] = $id;
                $msg['msg'] = 'success';
                $msg['retorno'] = 'Login efetuado com sucesso!';
                echo json_encode($msg);
                exit();
            } else {
                $msg['msg'] = 'error';
                $msg['retorno'] = 'Você não tem permissão de acesso ao sistema!';
                echo json_encode($msg);
                exit();
            }
        } else {
            $msg['msg'] = 'error';
            $msg['retorno'] = 'O usuário ou a senha inseridos estão incorretos!';
            echo json_encode($msg);
            exit();
        }
    } else {
        $msg['msg'] = 'error';
        $msg['retorno'] = 'O usuário ou a senha inseridos estão incorretos!';
        echo json_encode($msg);
        exit();
    }
} catch (PDOException $e) {
    $db->rollback();
    $msg['msg'] = 'error';
    $msg['retorno'] = "Erro ao tentar efeturar o login. :" . $e->getMessage();
    echo json_encode($msg);
    exit();
}
?>