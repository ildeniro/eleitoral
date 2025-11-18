<?php

@session_start();

include_once('../config/geral.php');
include_once('../config/funcoes.php');
$db = Conexao::getInstance();

if ($_GET['pesquisa'] != "" && strlen($_GET['pesquisa']) > 0 && antiSQL($_GET['pesquisa']) == true) {

    $condicao = "";
    $pesquisa = $_GET['pesquisa'];

    if (contemLetra($pesquisa)) {
        $condicao = "AND p.nome LIKE ?";
    } else {
        $condicao = "AND REPLACE(REPLACE(REPLACE(p.cpf, '.', ''), '-', ''), ' ', '') LIKE ?";
    }

    $result = $db->prepare("SELECT p.resp_cancelamento, p.motivo, p.data_cancelamento, p.endereco, p.nm_mae, p.bsc_bairros_id, p.numero, p.nascimento, p.id, p.nome, p.cpf, p.funcao_id, p.celular, p.zona, p.secao_numero, p.local_votacao, p.cidade, p.tipo, p.usuario_id,      
                            p.secao_numero_2, p.bairro_2, p.regional_2, p.municipio_2, p.status  
                            FROM 2024_voluntarios p  
                            INNER JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero 
                            LEFT JOIN 2024_voluntarios_indicacoes AS i ON i.voluntario_id = p.id 
                            WHERE 1 $condicao  
                            GROUP BY p.id 
                            ORDER BY p.nome ASC");

    if (contemLetra($pesquisa)) {
        $result->bindValue(1, "%" . $pesquisa . "%");
    } else {
        $result->bindValue(1, "%" . removerMascara($pesquisa) . "%");
    }

    $result->execute();
    while ($pessoas = $result->fetch(PDO::FETCH_ASSOC)) {

        echo '<div class="card px-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-xl-8 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="pl-xl-2 pb-1">
                                        <div class="m-b-0">
                                            <div class="user-avatar-name d-inline-block">
                                                <h2 class="font-24 m-b-10 strong-alt" style="margin-bottom: 0px;">' . ctexto($pessoas['nome'], "mai") . '</h2>
                                            </div>';

        if ($pessoas['secao_numero_2'] != NULL && $pessoas['bairro_2'] != NULL && $pessoas['regional_2'] != NULL) {
            echo "<b style='color: green; margin-left: 10px; font-size: 15px;'>Distribuição Realizada</b>";
        }

        echo '</div>
                                        <div class="user-avatar-address mt-2">
                                            <p class=" text-dark" style="margin-bottom: -5px;">
                                                <small class="small-alt"><span class="badge ' . status_voluntario($pessoas['status']) . '">' . status_voluntario_nome($pessoas['status']) . '</span></small> |
                                                <strong class="strong-alt">CPF: </strong>
                                                <small class="small-alt">' . $pessoas['cpf'] . '</small> |
                                                <strong class="strong-alt">Função: </strong>
                                                <small class="small-alt">' . pesquisar("nome", "sys_funcoes", "id", "=", $pessoas["funcao_id"], "") . '</small> |
                                                <strong class="strong-alt">Zona:</strong>
                                                <small class="small-alt">' . $pessoas['zona'] . '</small> |
                                                <strong class="strong-alt">Seção: </strong>
                                                <small class="small-alt">' . $pessoas['secao_numero'] . retornar_agregacao($pessoas['zona'], $pessoas['secao_numero']) . '</small> |
                                                <strong class="strong-alt">Local: </strong>
                                                <small class="small-alt">' . wordwrap($pessoas['local_votacao'], 50, "<br/>") . '</small> |
                                                <strong class="strong-alt">Município: </strong>
                                                <small class="small-alt">' . pesquisar("nome", "bsc_municipios", "id", "=", $pessoas['cidade'], "") . '</small>
                                            </p>                                       
                                            <div class="row">
                                                <button onclick="mais_detalhes(' . $pessoas['id'] . ')" class="btn btn-link" data-toggle="collapse" data-target="#collapseSeven_' . $pessoas['id'] . '" aria-expanded="true" aria-controls="collapseSeven_' . $pessoas['id'] . '">
                                                    <span class="fas fa-angle-down mr-3"></span><span id="mais_detalhes_' . $pessoas['id'] . '">Mais detalhes</span>
                                                </button>
                                                <div id="collapseSeven_' . $pessoas['id'] . '" class="collapse" aria-labelledby="headingSeven" data-parent="#accordion4">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                                                <div class="form-inline ml-4">
                                                                    <p class="text-dark mt-1" style="margin-bottom: -5px;">
                                                                        <strong class="strong-alt">Cadastrado por: </strong>
                                                                        <small class="small-alt">' . pesquisar("nome", "seg_usuarios", "id", "=", $pessoas['usuario_id'], "") . '</small> |
                                                                        <strong class="strong-alt">Data de Nascimento: </strong>
                                                                        <small class="small-alt">' . obterDataBRTimestamp($pessoas["nascimento"]) . '</small> |
                                                                        <strong class="strong-alt">Nome da Mãe: </strong>
                                                                        <small class="small-alt">' . $pessoas["nm_mae"] . '</small> |
                                                                        <strong class="strong-alt">Celular: </strong>
                                                                        <small class="small-alt">' . $pessoas['celular'] . '</small> |<br />
                                                                        <strong class="strong-alt">Endereço: </strong>
                                                                        <small class="small-alt">' . $pessoas['endereco'] . '</small> |
                                                                        <strong class="strong-alt">Número: </strong>
                                                                        <small class="small-alt">' . $pessoas['numero'] . '</small> |
                                                                        <strong class="strong-alt">Bairro: </strong>
                                                                        <small class="small-alt">' . pesquisar("NM_BAIRRO", "bsc_bairros", "ID", "=", $pessoas['bsc_bairros_id'], "") . '</small>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                              <div id="cancelamento" ' . ($pessoas['status'] != 1 ? "" : "style='display: none;'") . ' class="card-body">';

        echo ' <div class="row">
                                                            <p>
                                                            <div class="col-md-12 detalhes-log">
                                                                Desativado por: <b>' . (is_numeric($pessoas['resp_cancelamento']) ? pesquisar("nome", "seg_usuarios", "id", "=", $pessoas['resp_cancelamento'], "") : "") . '</b> em <b>' . obterDataBRTimestamp($pessoas['data_cancelamento']) . '</b> às <b>' . obterHoraTimestamp($pessoas['data_cancelamento']) . '</b>
                                                                - Motivo: <b>' . $pessoas['motivo'] . '</b>
                                                            </div>
                                                            </p>
                                                        </div>';

        echo '</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="float-xl-right float-none mt-xl-0 mt-4">';

        echo '<a href="' . PORTAL_URL . 'view/fiscal/cadastro/' . $pessoas['id'] . '" class="btn btn-outline-light btn-sm"><i class="fas fa-edit fa-lg mr-1"></i>Editar</a>&nbsp;';

        if ($pessoas['secao_numero_2'] == NULL && $pessoas['bairro_2'] == NULL && $pessoas['regional_2'] == NULL) {

            echo '<button ' . ($pessoas['status'] == 0 ? "style='cursor: pointer;'" : "style='display: none;'") . ' onclick="ativar_voluntario(' . $pessoas['id'] . ')" title="Ativar Voluntário" id="ativar" rel="' . $pessoas['id'] . '" class="btn btn-outline-success btn-sm"><i class="fas fa-check mr-1"></i> Ativar</button>';
            echo '<button ' . ($pessoas['status'] == 1 ? "style='cursor: pointer;'" : "style='display: none;'") . ' onclick="desativar_voluntario(' . $pessoas['id'] . ')" title="Desativar Voluntário" id="desativar" rel="' . $pessoas['id'] . '" class="btn btn-outline-danger btn-sm"><i class="fas fa-power-off mr-1"></i>Desativar</button>';
        } //else if (ver_nivel(1)) {
        //echo "<b style='color: green;'>Distribuição Realizada</b>";
        //}

        echo '</div>
                                </div>
                            </div>
                        </div>
                    </div>';
    }
} else {

    $result = $db->prepare("SELECT p.resp_cancelamento, p.motivo, p.data_cancelamento, p.endereco, p.nm_mae, p.bsc_bairros_id, p.numero, p.nascimento, p.id, p.nome, p.cpf, p.funcao_id, p.celular, p.zona, p.secao_numero, p.local_votacao, p.cidade, p.tipo, p.usuario_id,      
                            p.secao_numero_2, p.bairro_2, p.regional_2, p.municipio_2, p.status  
                            FROM 2024_voluntarios p  
                            INNER JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero 
                            LEFT JOIN 2024_voluntarios_indicacoes AS i ON i.voluntario_id = p.id 
                            WHERE 1  
                            GROUP BY p.id 
                            ORDER BY p.nome ASC");
    $result->execute();
    while ($pessoas = $result->fetch(PDO::FETCH_ASSOC)) {

        echo '<div class="card px-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-xl-8 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="pl-xl-2 pb-1">
                                        <div class="m-b-0">
                                            <div class="user-avatar-name d-inline-block">
                                                <h2 class="font-24 m-b-10 strong-alt" style="margin-bottom: 0px;">' . ctexto($pessoas['nome'], "mai") . '</h2>
                                            </div>';

                                        if ($pessoas['secao_numero_2'] != NULL && $pessoas['bairro_2'] != NULL && $pessoas['regional_2'] != NULL) {
                                            echo "<b style='color: green; margin-left: 10px; font-size: 15px;'>Distribuição Realizada</b>";
                                        }

                                        echo '</div>
                                        <div class="user-avatar-address mt-2">
                                            <p class=" text-dark" style="margin-bottom: -5px;">
                                                <small class="small-alt"><span class="badge ' . status_voluntario($pessoas['status']) . '">' . status_voluntario_nome($pessoas['status']) . '</span></small> |
                                                <strong class="strong-alt">CPF: </strong>
                                                <small class="small-alt">' . $pessoas['cpf'] . '</small> |
                                                <strong class="strong-alt">Função: </strong>
                                                <small class="small-alt">' . pesquisar("nome", "sys_funcoes", "id", "=", $pessoas["funcao_id"], "") . '</small> |
                                                <strong class="strong-alt">Zona:</strong>
                                                <small class="small-alt">' . $pessoas['zona'] . '</small> |
                                                <strong class="strong-alt">Seção: </strong>
                                                <small class="small-alt">' . $pessoas['secao_numero'] . retornar_agregacao($pessoas['zona'], $pessoas['secao_numero']) . '</small> |
                                                <strong class="strong-alt">Local: </strong>
                                                <small class="small-alt">' . wordwrap($pessoas['local_votacao'], 50, "<br/>") . '</small> |
                                                <strong class="strong-alt">Município: </strong>
                                                <small class="small-alt">' . pesquisar("nome", "bsc_municipios", "id", "=", $pessoas['cidade'], "") . '</small>
                                            </p>                                       
                                            <div class="row">
                                                <button onclick="mais_detalhes(' . $pessoas['id'] . ')" class="btn btn-link" data-toggle="collapse" data-target="#collapseSeven_' . $pessoas['id'] . '" aria-expanded="true" aria-controls="collapseSeven_' . $pessoas['id'] . '">
                                                    <span class="fas fa-angle-down mr-3"></span><span id="mais_detalhes_' . $pessoas['id'] . '">Mais detalhes</span>
                                                </button>
                                                <div id="collapseSeven_' . $pessoas['id'] . '" class="collapse" aria-labelledby="headingSeven" data-parent="#accordion4">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                                                <div class="form-inline ml-4">
                                                                    <p class="text-dark mt-1" style="margin-bottom: -5px;">
                                                                        <strong class="strong-alt">Cadastrado por: </strong>
                                                                        <small class="small-alt">' . pesquisar("nome", "seg_usuarios", "id", "=", $pessoas['usuario_id'], "") . '</small> |
                                                                        <strong class="strong-alt">Data de Nascimento: </strong>
                                                                        <small class="small-alt">' . obterDataBRTimestamp($pessoas["nascimento"]) . '</small> |
                                                                        <strong class="strong-alt">Nome da Mãe: </strong>
                                                                        <small class="small-alt">' . $pessoas["nm_mae"] . '</small> |
                                                                        <strong class="strong-alt">Celular: </strong>
                                                                        <small class="small-alt">' . $pessoas['celular'] . '</small> |<br />
                                                                        <strong class="strong-alt">Endereço: </strong>
                                                                        <small class="small-alt">' . $pessoas['endereco'] . '</small> |
                                                                        <strong class="strong-alt">Número: </strong>
                                                                        <small class="small-alt">' . $pessoas['numero'] . '</small> |
                                                                        <strong class="strong-alt">Bairro: </strong>
                                                                        <small class="small-alt">' . pesquisar("NM_BAIRRO", "bsc_bairros", "ID", "=", $pessoas['bsc_bairros_id'], "") . '</small>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                              <div id="cancelamento" ' . ($pessoas['status'] != 1 ? "" : "style='display: none;'") . ' class="card-body">';

        echo ' <div class="row">
                                                            <p>
                                                            <div class="col-md-12 detalhes-log">
                                                                Desativado por: <b>' . (is_numeric($pessoas['resp_cancelamento']) ? pesquisar("nome", "seg_usuarios", "id", "=", $pessoas['resp_cancelamento'], "") : "") . '</b> em <b>' . obterDataBRTimestamp($pessoas['data_cancelamento']) . '</b> às <b>' . obterHoraTimestamp($pessoas['data_cancelamento']) . '</b>
                                                                - Motivo: <b>' . $pessoas['motivo'] . '</b>
                                                            </div>
                                                            </p>
                                                        </div>';

        echo '</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="float-xl-right float-none mt-xl-0 mt-4">';

        echo '<a href="' . PORTAL_URL . 'view/fiscal/cadastro/' . $pessoas['id'] . '" class="btn btn-outline-light btn-sm"><i class="fas fa-edit fa-lg mr-1"></i>Editar</a>&nbsp;';

        if ($pessoas['secao_numero_2'] == NULL && $pessoas['bairro_2'] == NULL && $pessoas['regional_2'] == NULL) {

            echo '<button ' . ($pessoas['status'] == 0 ? "style='cursor: pointer;'" : "style='display: none;'") . ' onclick="ativar_voluntario(' . $pessoas['id'] . ')" title="Ativar Voluntário" id="ativar" rel="' . $pessoas['id'] . '" class="btn btn-outline-success btn-sm"><i class="fas fa-check mr-1"></i> Ativar</button>';
            echo '<button ' . ($pessoas['status'] == 1 ? "style='cursor: pointer;'" : "style='display: none;'") . ' onclick="desativar_voluntario(' . $pessoas['id'] . ')" title="Desativar Voluntário" id="desativar" rel="' . $pessoas['id'] . '" class="btn btn-outline-danger btn-sm"><i class="fas fa-power-off mr-1"></i>Desativar</button>';
        } //else if (ver_nivel(1)) {
        //echo "<b style='color: green;'>Distribuição Realizada</b>";
        //}

        echo '</div>
                                </div>
                            </div>
                        </div>
                    </div>';
    }
}
?> 