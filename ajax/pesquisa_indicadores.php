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
        $condicao = "AND c.status IN(1,0)";
    } else if ($ativo == 1 && $cancelado == "") {
        $condicao = "AND c.status = 1";
    } else if ($ativo == "" && $cancelado == 0) {
        $condicao = "AND c.status = 0";
    }

    $result = $db->prepare("SELECT *   
                            FROM 2024_indicacoes c 
                            WHERE c.nome LIKE ? $condicao  
                            ORDER BY c.status DESC, c.nome ASC");
    $result->bindValue(1, "%" . $pesquisa . "%");
    $result->execute();
    while ($indicadores = $result->fetch(PDO::FETCH_ASSOC)) {

        echo '<div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-xl-9 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="pl-xl-3">
                                        <div class="m-b-0">
                                            <div class="user-avatar-name d-inline-block">
                                                <h2 class="font-24 m-b-10 strong-alt" style="margin-bottom: 0px;">' . $indicadores['nome'] . '</h2>
                                            </div>
                                        </div>
                                        <div class="user-avatar-address mt-2">
                                            <p class=" text-dark">
                                                <span class="badge ' . ($indicadores['status'] == 1 ? "badge-success" : "badge-danger") . '">' . ($indicadores['status'] == 1 ? "Ativo" : "Inativo") . '</span>
                                                <strong class="strong-alt">Função:</strong>
                                                <small class="small-alt">' . $indicadores['funcao'] . '</small> |
                                                <strong class="strong-alt">Contato: </strong>
                                                <small class="small-alt">' . $indicadores['telefone'] . '</small> <br>
                                                <strong class="strong-alt">Indicações: </strong>
                                                <small class="small-alt">' . qtd_indicacao($indicadores['id'], 5) . '</small>
                                                <small class="small-alt">Fiscais de Seção - </small>  
                                                <small class="small-alt">' . qtd_indicacao($indicadores['id'], 4) . '</small>
                                                <small class="small-alt">Supervisores de Local</small>
                                            </p>
                                          
                                           
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
                                    <div class="float-xl-right float-none mt-xl-0 mt-4">
                                        <a title="Editar Indicador" href="' . PORTAL_URL . 'view/indicadores/cadastro/' . $indicadores['id'] . '"><button class="btn btn-outline-light btn-sm" ><i class="fas fa-edit mr-1"></i>Editar</button></a>
                                        <button ' . (ver_nivel(1, "") && $indicadores['status'] == 0 ? "" : "style='display: none'") . ' onclick="ativar_indicador(' . $indicadores['id'] . ')" title="Ativar Indicadores" class="btn btn-outline-success btn-sm" ><i class="fas fa-check mr-1"></i>Ativar</button>
                                        <button ' . (ver_nivel(1, "") && $indicadores['status'] == 1 ? "" : "style='display: none'") . ' onclick="cancelar_indicador(' . $indicadores['id'] . ')" title="Cancelar Indicadores" class="btn btn-outline-danger btn-sm" ><i class="fas fa-power-off mr-1"></i>Cancelar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
    }
} else {

    $ativo = isset($_GET['ativo']) ? $_GET['ativo'] : "";
    $cancelado = isset($_GET['cancelado']) ? $_GET['cancelado'] : "";

    if ($ativo == 1 && $cancelado == 0) {
        $condicao = "AND c.status IN(1,0)";
    } else if ($ativo == 1 && $cancelado == "") {
        $condicao = "AND c.status = 1";
    } else if ($ativo == "" && $cancelado == 0) {
        $condicao = "AND c.status = 0";
    }

    $result = $db->prepare("SELECT *   
                            FROM 2024_indicacoes c 
                            WHERE 1 $condicao  
                            ORDER BY c.status DESC, c.nome ASC");
    $result->execute();
    while ($indicadores = $result->fetch(PDO::FETCH_ASSOC)) {

echo '<div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-xl-9 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="pl-xl-3">
                                        <div class="m-b-0">
                                            <div class="user-avatar-name d-inline-block">
                                                <h2 class="font-24 m-b-10 strong-alt" style="margin-bottom: 0px;">' . $indicadores['nome'] . '</h2>
                                            </div>
                                        </div>
                                        <div class="user-avatar-address mt-2">
                                            <p class=" text-dark">
                                                <span class="badge ' . ($indicadores['status'] == 1 ? "badge-success" : "badge-danger") . '">' . ($indicadores['status'] == 1 ? "Ativo" : "Inativo") . '</span>
                                                <strong class="strong-alt">Função:</strong>
                                                <small class="small-alt">' . $indicadores['funcao'] . '</small> |
                                                <strong class="strong-alt">Contato: </strong>
                                                <small class="small-alt">' . $indicadores['telefone'] . '</small> <br>
                                                <strong class="strong-alt">Indicações: </strong>
                                                <small class="small-alt">' . qtd_indicacao($indicadores['id'], 5) . '</small>
                                                <small class="small-alt">Fiscais de Seção - </small>  
                                                <small class="small-alt">' . qtd_indicacao($indicadores['id'], 4) . '</small>
                                                <small class="small-alt">Supervisores de Local</small>
                                            </p>
                                          
                                           
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
                                    <div class="float-xl-right float-none mt-xl-0 mt-4">
                                        <a title="Editar Indicador" href="' . PORTAL_URL . 'view/indicadores/cadastro/' . $indicadores['id'] . '"><button class="btn btn-outline-light btn-sm" ><i class="fas fa-edit mr-1"></i>Editar</button></a>
                                        <button ' . (ver_nivel(1, "") && $indicadores['status'] == 0 ? "" : "style='display: none'") . ' onclick="ativar_indicador(' . $indicadores['id'] . ')" title="Ativar Indicadores" class="btn btn-outline-success btn-sm" ><i class="fas fa-check mr-1"></i>Ativar</button>
                                        <button ' . (ver_nivel(1, "") && $indicadores['status'] == 1 ? "" : "style='display: none'") . ' onclick="cancelar_indicador(' . $indicadores['id'] . ')" title="Cancelar Indicadores" class="btn btn-outline-danger btn-sm" ><i class="fas fa-power-off mr-1"></i>Cancelar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
    }
}
?> 