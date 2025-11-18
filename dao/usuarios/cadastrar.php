<?php

$db = Conexao::getInstance();

$msg = array();
$error = false;

$id = isset($_POST['id']) && $_POST['id'] != "" ? $_POST['id'] : 0;
$nome = isset($_POST['nome']) ? $_POST['nome'] : NULL;
$login = isset($_POST['login']) ? $_POST['login'] : NULL;
$senha = isset($_POST['senha']) ? $_POST['senha'] : NULL;
$nivel = isset($_POST['nivel']) && $_POST['nivel'] != "" ? $_POST['nivel'] : null;
$contato = isset($_POST['contato']) ? $_POST['contato'] : NULL;
$email = isset($_POST['email']) ? $_POST['email'] : NULL;

//VERIFICAÇÃO SE O USUÁRIO INFORMADO JÁ EXISTE NA BASE DE DADOS
$codigo = pesquisa("id", "seg_usuarios", "nome = ?", $nome, "", "", "", "", "", "", "");

if (is_numeric($codigo) && $codigo != $id) {
    $error = true;
    echo "O nome do usuário informado já existe no sistema.";
    exit();
}

if ($error == false) {
    try {

        $db->beginTransaction();

        if ($id == 0) {
            $sql = $db->prepare("INSERT INTO seg_usuarios (nome, login, senha, email, telefone_celular, data_update, usuario_id, data_cadastro, status) VALUES (?, ?, ?, ?, ?, NOW(), ?, NOW(), 1)");
            $sql->bindValue(1, $nome);
            $sql->bindValue(2, $login);
            $sql->bindValue(3, sha1($senha));
            $sql->bindValue(4, $email);
            $sql->bindValue(5, $contato);
            $sql->bindValue(6, $_SESSION['id']);
            $sql->execute();

            $id = $db->lastInsertId();

            //INSERINDO SESSÃO
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

            $stmt1 = $db->prepare("INSERT INTO seg_sessoes 
                     (usuario_id, usuario_pai_id, host, ip, navegador, sistema_operacional, numero_sessao)
                      VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt1->bindValue(1, $id);
            $stmt1->bindValue(2, $_SESSION['id']);
            $stmt1->bindValue(3, $_SERVER["SERVER_NAME"]);
            $stmt1->bindValue(4, $_SERVER['REMOTE_ADDR']);
            $stmt1->bindValue(5, $browser . " " . $browser_version);
            $stmt1->bindValue(6, $so);
            $stmt1->bindValue(7, session_id());
            $stmt1->execute();
        } else {
            if ($senha != "" && $senha != NULL && !is_numeric(pesquisar2("id", "seg_usuarios", "id", "=", $id, "senha", "=", $senha, ""))) {
                $sql = $db->prepare("UPDATE seg_usuarios SET nome = ?, login = ?, senha = ?, email = ?, telefone_celular = ?, usuario_id = ? WHERE id = ?");
                $sql->bindValue(1, $nome);
                $sql->bindValue(2, $login);
                $sql->bindValue(3, sha1($senha));
                $sql->bindValue(4, $email);
                $sql->bindValue(5, $contato);
                $sql->bindValue(6, $_SESSION['id']);
                $sql->bindValue(7, $id);
                $sql->execute();
            } else {
                $sql = $db->prepare("UPDATE seg_usuarios SET nome = ?, login = ?,  email = ?, telefone_celular = ?, usuario_id = ? WHERE id = ?");
                $sql->bindValue(1, $nome);
                $sql->bindValue(2, $login);
                $sql->bindValue(3, $email);
                $sql->bindValue(4, $contato);
                $sql->bindValue(5, $_SESSION['id']);
                $sql->bindValue(6, $id);
                $sql->execute();
            }
        }

        //PERMISSÕES DE ACESSO AO SISTEMA
        if (ver_nivel(1) || ver_nivel(3)) {//ADMIN OU SUPER ADMIN
            //Removendo Níveis de Acesso
            $stmt3 = $db->prepare("DELETE FROM seg_permissoes WHERE user_id = ?");
            $stmt3->bindValue(1, $_POST['id']);
            $stmt3->execute();

            //Adicionando Níveis de Acesso
            if (isset($nivel)) {
                foreach ($nivel AS $key => $val) {
                    if (isset($val)) {
                        $stmt33 = $db->prepare("INSERT INTO seg_permissoes (user_id, nivel) VALUES (?, ?)");
                        $stmt33->bindValue(1, $id);
                        $stmt33->bindValue(2, $val);
                        $stmt33->execute();
                    }
                }
            }
        }

//        if (isset($_SESSION['foto_cut']) && isset($_SESSION['foto_origin']) && $_SESSION['foto_cut'] != "" && $_SESSION['foto_origin'] != "") {
//            $stmt5 = $db->prepare("UPDATE seg_usuarios SET foto = ? WHERE id = ?");
//            $stmt5->bindValue(1, PORTAL_URL . "" . $_SESSION['foto_cut']);
//            $stmt5->bindValue(2, $id);
//            $stmt5->execute();
//        }

        $db->commit();
    } catch (PDOException $e) {
        $db->rollback();
        $msg['msg'] = 'error';
        $msg['retorno'] = "Erro ao tentar realizar a ação desejada:" . $e->getMessage();
        echo json_encode($msg);
        exit();
    }
}
?>