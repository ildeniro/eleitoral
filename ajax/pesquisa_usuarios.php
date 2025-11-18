<?php

@session_start();

include_once('../config/geral.php');
include_once('../config/funcoes.php');
$db = Conexao::getInstance();

$condicao = "";

//Ordinarias
if ($_GET['pesquisa'] != "" && strlen($_GET['pesquisa']) > 0 && antiSQL($_GET['pesquisa']) == true) {

    $pesquisa = $_GET['pesquisa'];

    $ativo = isset($_GET['ativo']) ? $_GET['ativo'] : "";
    $cancelado = isset($_GET['cancelado']) ? $_GET['cancelado'] : "";

    if ($ativo == 1 && $cancelado == 0) {
        $condicao = "AND u.status IN(1,0)";
    } else if ($ativo == 1 && $cancelado == "") {
        $condicao = "AND u.status = 1";
    } else if ($ativo == "" && $cancelado == 0) {
        $condicao = "AND u.status = 0";
    }

    $result = $db->prepare("SELECT *   
                            FROM seg_usuarios u 
                            WHERE u.nome LIKE ? $condicao 
                            ORDER BY u.status DESC, u.nome ASC");
    $result->bindValue(1, "%" . $pesquisa . "%");
    $result->execute();
    while ($usuario = $result->fetch(PDO::FETCH_ASSOC)) {

        echo '<div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-xl-9 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="user-avatar float-xl-left pr-4 float-none">
                                        <img src="' . PORTAL_URL . 'template/assets/images/picture.jpg" alt="User Avatar" class="rounded-circle user-avatar-lg">
                                    </div>
                                    <div class="pl-xl-3">
                                        <div class="m-b-0">
                                            <div class="user-avatar-name d-inline-block">
                                                <h2 class="font-24 m-b-10" style="margin-bottom: 0px;">' . $usuario['nome'] . '</h2>
                                            </div>
                                        </div>
                                        <div class="user-avatar-address mt-2">
                                            <p class=" text-dark">
                                                <strong style="font-size: 16px; font-weight: 400;">Status:</strong>
                                                <span class="badge ' . ($usuario['status'] == 1 ? "badge-success" : "badge-danger") . '">' . ($usuario['status'] == 1 ? "Ativo" : "Inativo") . '</span>
                                                <strong style="font-size: 16px; font-weight: 400; '.($usuario['email'] == "" ? "display: none;" : "").'">E-mail:</strong> |
                                                <small style="font-size: 16px; font-weight: 700; '.($usuario['email'] == "" ? "display: none" : "").'">'.$usuario['email'].'</small> '.($usuario['email'] == "" ? "" : "|").'
                                                <strong style="font-size: 16px; font-weight: 400;">Celular: </strong>
                                                <small style="font-size: 16px; font-weight: 700;">'.$usuario['telefone_celular'].'</small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
                                    <div class="float-xl-right float-none mt-xl-0 mt-4">
                                    <a ' . ($usuario['id'] != 1 && ver_nivel(1, "") || $usuario['id'] != 1 && ver_nivel(3, "") || $usuario['id'] == $_SESSION['id'] ? "" : "style='display: none'") . ' title="Editar Usuário" href="' . PORTAL_URL . 'view/usuarios/cadastro/' . $usuario['id'] . '"><button class="btn btn-outline-light btn-sm" ><i class="fas fa-edit mr-1"></i>Editar</button></a>
                                    ';

        if (ver_nivel(1, "") || ver_nivel(3, "")) {
            echo '<button ' . ($usuario['id'] != 1 && ver_nivel(1, "") || $usuario['id'] != 1 && ver_nivel(3, "") || $usuario['id'] == $_SESSION['id'] ? "" : "style='display: none'") . ' onclick="ativar_usuario(' . $usuario['id'] . ')" title="Ativar Usuário" ' . ($usuario['status'] == 0 ? "" : "style='display: none'") . ' class="btn btn-outline-success btn-sm" ><i class="fas fa-check mr-1"></i>Ativar</button>';
            echo '<button ' . ($usuario['id'] != 1 && ver_nivel(1, "") || $usuario['id'] != 1 && ver_nivel(3, "") || $usuario['id'] == $_SESSION['id'] ? "" : "style='display: none'") . ' onclick="cancelar_usuario(' . $usuario['id'] . ')" title="Cancelar Usuário" ' . ($usuario['status'] == 1 ? "" : "style='display: none'") . ' class="btn btn-outline-danger btn-sm" ><i class="fas fa-power-off mr-1"></i>Cancelar</button>';
        }
                 
        echo '</div>
                            </div>
                        </div>
                    </div>
                </div>';
    }
} else {

    $ativo = isset($_GET['ativo']) ? $_GET['ativo'] : "";
    $cancelado = isset($_GET['cancelado']) ? $_GET['cancelado'] : "";

    if ($ativo == 1 && $cancelado == 0) {
        $condicao = "AND u.status IN(1,0)";
    } else if ($ativo == 1 && $cancelado == "") {
        $condicao = "AND u.status = 1";
    } else if ($ativo == "" && $cancelado == 0) {
        $condicao = "AND u.status = 0";
    }

    $result = $db->prepare("SELECT *   
                            FROM seg_usuarios u 
                            WHERE 1 $condicao 
                            ORDER BY u.status DESC, u.nome ASC");
    $result->execute();
    while ($usuario = $result->fetch(PDO::FETCH_ASSOC)) {

        echo '<div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-xl-9 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="user-avatar float-xl-left pr-4 float-none">
                                        <img src="' . PORTAL_URL . 'template/assets/images/picture.jpg" alt="User Avatar" class="rounded-circle user-avatar-lg">
                                    </div>
                                    <div class="pl-xl-3">
                                        <div class="m-b-0">
                                            <div class="user-avatar-name d-inline-block">
                                                <h2 class="font-24 m-b-10" style="margin-bottom: 0px;">' . $usuario['nome'] . '</h2>
                                            </div>
                                        </div>
                                        <div class="user-avatar-address mt-2">
                                            <p class=" text-dark">
                                                <strong style="font-size: 16px; font-weight: 400;">Status:</strong>
                                                <span class="badge ' . ($usuario['status'] == 1 ? "badge-success" : "badge-danger") . '">' . ($usuario['status'] == 1 ? "Ativo" : "Inativo") . '</span>
                                                <strong style="font-size: 16px; font-weight: 400; '.($usuario['email'] == "" ? "display: none;" : "").'">E-mail:</strong> |
                                                <small style="font-size: 16px; font-weight: 700; '.($usuario['email'] == "" ? "display: none" : "").'">'.$usuario['email'].'</small> '.($usuario['email'] == "" ? "" : "|").'
                                                <strong style="font-size: 16px; font-weight: 400;">Celular: </strong>
                                                <small style="font-size: 16px; font-weight: 700;">'.$usuario['telefone_celular'].'</small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
                                    <div class="float-xl-right float-none mt-xl-0 mt-4">
                                    <a ' . ($usuario['id'] != 1 && ver_nivel(1, "") || $usuario['id'] != 1 && ver_nivel(3, "") || $usuario['id'] == $_SESSION['id'] ? "" : "style='display: none'") . ' title="Editar Usuário" href="' . PORTAL_URL . 'view/usuarios/cadastro/' . $usuario['id'] . '"><button class="btn btn-outline-light btn-sm" ><i class="fas fa-edit mr-1"></i>Editar</button></a>
                                    ';

        if (ver_nivel(1, "") || ver_nivel(3, "")) {
            echo '<button ' . ($usuario['id'] != 1 && ver_nivel(1, "") || $usuario['id'] != 1 && ver_nivel(3, "") || $usuario['id'] == $_SESSION['id'] ? "" : "style='display: none'") . ' onclick="ativar_usuario(' . $usuario['id'] . ')" title="Ativar Usuário" ' . ($usuario['status'] == 0 ? "" : "style='display: none'") . ' class="btn btn-outline-success btn-sm" ><i class="fas fa-check mr-1"></i>Ativar</button>';
            echo '<button ' . ($usuario['id'] != 1 && ver_nivel(1, "") || $usuario['id'] != 1 && ver_nivel(3, "") || $usuario['id'] == $_SESSION['id'] ? "" : "style='display: none'") . ' onclick="cancelar_usuario(' . $usuario['id'] . ')" title="Cancelar Usuário" ' . ($usuario['status'] == 1 ? "" : "style='display: none'") . ' class="btn btn-outline-danger btn-sm" ><i class="fas fa-power-off mr-1"></i>Cancelar</button>';
        }
                 
        echo '</div>
                            </div>
                        </div>
                    </div>
                </div>';
    }
}
?> 