<?php

include_once('config/geral.php');
$db = Conexao::getInstance();

$msg = array();

try {
    //PEGAR DADOS DE LOGIN
    $cpf = $_POST['cpf'];
    $data_nascimento = convertDataBR2ISO($_POST['data_nascimento']);

    //SQL PARA VERIFICAÇÃO DE LOGIN EXISTENTE
    $result = $db->prepare("SELECT *     
                            FROM 2024_voluntarios AS v 
                            WHERE v.cpf = ? AND v.nascimento = ? AND v.status = 5 OR 
                             v.cpf = ? AND v.nascimento = ? AND v.status = 1 AND v.funcao_id = 4 OR 
                             v.cpf = ? AND v.nascimento = ? AND v.status = 1 AND v.funcao_id = 5");
    $result->bindParam(1, $cpf);
    $result->bindParam(2, $data_nascimento);
    $result->bindParam(3, $cpf);
    $result->bindParam(4, $data_nascimento);
    $result->bindParam(5, $cpf);
    $result->bindParam(6, $data_nascimento);
    $result->execute();

    $num = $result->rowCount();

    if ($num > 0) {
        //PEGA OS DADOS DO USUARIO, CASO TENHA ACESSO
        $dadosUsuario = $result->fetch(PDO::FETCH_ASSOC);

        //VERIFICA SE A SENHA INFORMADA É IGUAL DO USUARIO
        if ($dadosUsuario['status'] == 1 || $dadosUsuario['status'] == 5) {

            //SQL PARA VERIFICAÇÃO DE LOGIN EXISTENTE
            $result3 = $db->prepare("SELECT *     
                            FROM 2024_voluntarios AS v 
                            WHERE v.cpf = ? AND v.nascimento = ? AND v.status = 5 OR
                             v.cpf = ? AND v.nascimento = ? AND v.zona_2 <> '' AND v.secao_numero_2 <> '' AND v.zona_2 IS NOT NULL AND v.secao_numero_2 IS NOT NULL AND v.status = 1 AND v.funcao_id = 4 OR 
                             v.secao_numero_2 <> '' AND v.regional_2 <> '' AND v.local_votacao_2 <> '' AND
                             v.secao_numero_2 IS NOT NULL AND v.regional_2 IS NOT NULL AND v.local_votacao_2 IS NOT NULL AND v.cpf = ? AND v.nascimento = ? AND v.status = 1 AND v.funcao_id = 5");
            $result3->bindParam(1, $cpf);
            $result3->bindParam(2, $data_nascimento);
            $result3->bindParam(3, $cpf);
            $result3->bindParam(4, $data_nascimento);
            $result3->bindParam(5, $cpf);
            $result3->bindParam(6, $data_nascimento);
            $result3->execute();

            $num3 = $result3->rowCount();

            if ($num3 > 0) {

                //PEGA OS DADOS DO USUARIO, CASO TENHA ACESSO
                $dadosUsuario2 = $result3->fetch(PDO::FETCH_ASSOC);

                $id = $dadosUsuario2['id'];

                //CRIAR O TIMEOUT DA SESSÃO PARA EXPIRAR
                $_SESSION['voluntario_timeout'] = time();
                //CRIAR AS SESSÕES DO USUARIO
                $_SESSION['voluntario_id'] = $id;
                $_SESSION['voluntario_nome'] = $dadosUsuario2['nome'];
                $_SESSION['voluntario_funcao'] = $dadosUsuario2['funcao_id'];
                $_SESSION['voluntario_nascimento'] = $data_nascimento;
                $_SESSION['voluntario_cpf'] = $cpf;

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

                $result2 = $db->prepare("UPDATE seg_sessoes_voluntarios SET host = ?, ip = ?, navegador = ?, sistema_operacional = ?, numero_sessao = ?, data_login = NOW(), atualizacao = NOW() WHERE usuario_id = ?");
                $result2->bindValue(1, $_SERVER["SERVER_NAME"]);
                $result2->bindValue(2, $_SERVER['REMOTE_ADDR']);
                $result2->bindValue(3, $browser . " " . $browser_version);
                $result2->bindValue(4, $so);
                $result2->bindValue(5, session_id());
                $result2->bindValue(6, $id);
                $result2->execute();

                //MENSAGEM DE SUCESSO
                $msg['id'] = $id;
                $msg['msg'] = 'success';
                $msg['retorno'] = 'Login efetuado com sucesso!';
                echo json_encode($msg);
                exit();
            } else {
                $msg['msg'] = 'error';
                $msg['retorno'] = 'Você ainda não foi distribuído para poder acessar o portal do fiscal, caso tenha dúvidas entre em contato com a central de apoio ao fiscal.';
                echo json_encode($msg);
                exit();
            }
        } else {
            $msg['msg'] = 'error';
            $msg['retorno'] = 'Você não tem permissão de acesso ao sistema!';
            echo json_encode($msg);
            exit();
        }
    } else {
        $msg['msg'] = 'error';
        $msg['retorno'] = 'O CPF ou data de nascimento inseridos estão incorretos! Qualquer dúvida entre em contato com a cenral de apoio ao fiscal.';
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