<?php

@session_start();

include_once('../config/geral.php');
include_once('../config/funcoes.php');
$db = Conexao::getInstance();

if ($_GET['pesquisa'] != "" && strlen($_GET['pesquisa']) > 0 && antiSQL($_GET['pesquisa']) == true) {

    $pesquisa = $_GET['pesquisa'];

    $result = $db->prepare("SELECT vo.bairro_id, vo.local_votacao_id, vo.responsavel_id, vo.outros, vo.outros_contato, vo.ocorrencia, vo.data_cadastro, vo.id AS ocorrencia_id, vo.situacao, 
                            p.endereco, p.nm_mae, p.bsc_bairros_id, p.numero, p.nascimento, 
                            p.id, p.nome, p.cpf, p.funcao_id, p.celular, p.zona, p.secao_numero, 
                            p.local_votacao, p.cidade, p.tipo, p.usuario_id,      
                            p.zona_2, p.secao_numero_2, p.bairro_2, p.regional_2, p.municipio_2, 
                            p.local_votacao_2, p.status  
                            FROM 2024_voluntarios_ocorrencias AS vo 
                            LEFT JOIN 2024_voluntarios AS p ON p.id = vo.voluntario_id 
                            LEFT JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero
                            INNER JOIN 2024_locais_votacao AS lv ON lv.ID = vo.local_votacao_id 
                            LEFT JOIN 2024_voluntarios_indicacoes AS i ON i.voluntario_id = p.id 
                            WHERE vo.status = 1 AND p.nome LIKE ? OR vo.status = 1 AND vo.outros LIKE ?  
                            AND (p.secao_numero_2 IS NOT NULL AND p.bairro_2 IS NOT NULL AND p.regional_2 IS NOT NULL OR p.id IS NULL)
                            GROUP BY vo.id  
                            ORDER BY p.nome ASC");
    $result->bindValue(1, "%" . $pesquisa . "%");
    $result->bindValue(2, "%" . $pesquisa . "%");
    $result->execute();
    while ($pessoas = $result->fetch(PDO::FETCH_ASSOC)) {

        $result335 = $db->prepare("SELECT ve.id, ve.descricao, u.nome AS responsavel, ve.data_update       
                                               FROM 2024_voluntarios_encaminhamentos AS ve
                                               INNER JOIN seg_usuarios AS u ON u.id = ve.responsavel_id 
                                               WHERE ve.ocorrencia_id = ? AND ve.status = 1");
        $result335->bindValue(1, $pessoas["ocorrencia_id"]);
        $result335->execute();

        $qtd_encaminhamentos = $result335->rowCount();

        $bairro = is_numeric($pessoas['bairro_id']) ? pesquisar("NM_BAIRRO", "bsc_bairros", "ID", "=", $pessoas['bairro_id'], "") : "";
        $regional = is_numeric($pessoas['bairro_id']) ? pesquisar("REGIONAL_NOME", "bsc_bairros", "ID", "=", $pessoas['bairro_id'], "") : "";

        echo '<div class="card px-3">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-xl-8 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <div class="pl-xl-2 pb-1">
                                            <div class="m-b-0">
                                                <div class="user-avatar-name d-inline-block">
                                                    <h2 class="font-20 m-b-10 strong-alt" style="margin-bottom: 0px;"><strong class="strong-alt">Demandante: </strong>' . ($pessoas['nome'] != "" && $pessoas['nome'] != null ? ctexto($pessoas['nome'], "mai") : ctexto($pessoas['outros'], "mai")) . '</h2>
                                                </div>
                                            </div>

                                            <div ' . ($pessoas['outros_contato'] != "" && $pessoas['outros_contato'] != null ? "" : "style='display: none;'") . ' class="m-b-0 mt-2">
                                                <div class="user-avatar-name d-inline-block">
                                                    <h2 class="font-20 m-b-10 strong-alt" style="margin-bottom: -5px;"><strong class="strong-alt">Contato: <b>' . $pessoas['outros_contato'] . '</b></strong></h2>
                                                </div>
                                            </div>

                                            <p class="text-dark mt-3" style="margin-bottom: -2px;">
                                                <strong class="strong-alt">Data/Hora: </strong>
                                                <small class="small-alt">' . (obterDataBRTimestamp($pessoas['data_cadastro']) . " - " . obterHoraTimestamp($pessoas['data_cadastro']) . ' -  ' . (is_numeric($pessoas['responsavel_id']) ? ctexto(pesquisar("nome", "seg_usuarios", "id", "=", $pessoas['responsavel_id'], ""), "pri") : "")) . '</small>
                                            </p>

                                            <p class="text-dark mt-3" style="margin-bottom: -5px;">
                                                <span class="badge ' . ($pessoas['situacao'] == 1 ? "bg-solucionado" : "badge-danger") . '">' . ($pessoas['situacao'] == 1 ? "Solucionado" : "Não solucionado") . '</span>

                                                <span class="badge ' . ($qtd_encaminhamentos >= 1 ? "badge-success" : "badge-grey") . '">' . ($qtd_encaminhamentos >= 1 ? "Encaminhado" : "Sem Encaminhamento") . '</span>
                                            </p>

                                            <div class="user-avatar-address mt-3">

                                                <p class="text-dark">
                                                    <strong class="strong-alt">Ocorrência: </strong>
                                                    <small class="small-alt">' . resume($pessoas['ocorrencia'], "200") . '</small>
                                                </p>

                                                <div class="row">
                                                    <button onclick="mais_detalhes_ocorrencia(' . "'" . $pessoas['ocorrencia_id'] . "'" . ')" class="btn btn-link btn-link-brand" data-toggle="collapse" data-target="#collapseSeven_' . $pessoas['ocorrencia_id'] . '" aria-expanded="true" aria-controls="collapseSeven_' . $pessoas['ocorrencia_id'] . '">
                                                        <span class="fas fa-angle-down mr-3"></span><span id="mais_detalhes_' . $pessoas['ocorrencia_id'] . '">Mais detalhes</span>
                                                    </button>
                                                </div>

                                                <div id="collapseSeven_' . $pessoas['ocorrencia_id'] . '" class="collapse row" aria-labelledby="headingSeven" data-parent="#accordion4">
                                                    <div class="card-body">

                                                        <div class="row">
                                                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                                                <div class="form-inline ml-4">
                                                                    <strong class="strong-alt">Local de Votação: </strong>
                                                                    <small class="small-alt">&nbsp;&nbsp;' . (is_numeric($pessoas['local_votacao_id']) ? pesquisar("NM_LOCAL_VOTACAO", "2024_locais_votacao", "ID", "=", $pessoas['local_votacao_id'], "") : "") . '</small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                                                <div class="form-inline ml-4">
                                                                    <strong class="strong-alt">Bairro: </strong>
                                                                    <small class="small-alt">&nbsp;&nbsp;' . $bairro . '</small>&nbsp;&nbsp;&nbsp;&nbsp;
                                                                    <strong class="strong-alt">Regional: </strong>
                                                                    <small class="small-alt">&nbsp;&nbsp;' . $regional . '</small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row mt-2">
                                                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                                                <div class="form-inline ml-4">
                                                                    <p class="text-dark mt-1">
                                                                        <strong class="strong-alt"><b>Encaminhamento(s):</b> <button onclick="add_encaminhamento(' . "'" . $pessoas['ocorrencia_id'] . "'" . ')" id="add_encaminhamento" name="add_encaminhamento" class="btn btn-success btn-xs">Novo</button></strong>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="cancelamento" class="card-body scroll-distribuidos border mt-2 row">';

        while ($encaminhamentos = $result335->fetch(PDO::FETCH_ASSOC)) {

            echo '<div class="row mb-3">
                                                                    <div class="col-md-12 detalhes-log">
                                                                        * Usuário: <b>' . $encaminhamentos['responsavel'] . '</b> em <b>' . obterDataBRTimestamp($encaminhamentos['data_update']) . '</b> às <b>' . obterHoraTimestamp($encaminhamentos['data_update']) . '</b>
                                                                        <p>* Descrição: <b>' . $encaminhamentos['descricao'] . '</b></p>
                                                                    </div>
                                                                </div>';
        }

        echo '</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <div class="float-xl-right float-none mt-xl-0 mt-4">
                                            <a title="Editar Usuário" href="' . PORTAL_URL . 'view/fiscal/cadastro-ocorrencias/' . $pessoas['ocorrencia_id'] . '"><button class="btn btn-outline-light btn-sm" ><i class="fas fa-edit mr-1"></i>Editar</button></a>
                                            <a href="#" onclick="remover(' . "'" . $pessoas['ocorrencia_id'] . "'" . ')" class="btn btn-outline-danger btn-sm"><i class="fas fa-trash mr-1"></i>Remover</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>';
    }
} else {

    $result = $db->prepare("SELECT vo.bairro_id, vo.local_votacao_id, vo.responsavel_id, vo.outros, vo.outros_contato, vo.ocorrencia, vo.data_cadastro, vo.id AS ocorrencia_id, vo.situacao, 
                            p.endereco, p.nm_mae, p.bsc_bairros_id, p.numero, p.nascimento, 
                            p.id, p.nome, p.cpf, p.funcao_id, p.celular, p.zona, p.secao_numero, 
                            p.local_votacao, p.cidade, p.tipo, p.usuario_id,      
                            p.zona_2, p.secao_numero_2, p.bairro_2, p.regional_2, p.municipio_2, 
                            p.local_votacao_2, p.status  
                            FROM 2024_voluntarios_ocorrencias AS vo 
                            LEFT JOIN 2024_voluntarios AS p ON p.id = vo.voluntario_id 
                            LEFT JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero
                            INNER JOIN 2024_locais_votacao AS lv ON lv.ID = vo.local_votacao_id 
                            LEFT JOIN 2024_voluntarios_indicacoes AS i ON i.voluntario_id = p.id 
                            WHERE vo.status = 1  
                            AND (p.secao_numero_2 IS NOT NULL AND p.bairro_2 IS NOT NULL AND p.regional_2 IS NOT NULL OR p.id IS NULL)
                            GROUP BY vo.id  
                            ORDER BY p.nome ASC");
    $result->execute();
    while ($pessoas = $result->fetch(PDO::FETCH_ASSOC)) {

        $result335 = $db->prepare("SELECT ve.id, ve.descricao, u.nome AS responsavel, ve.data_update       
                                               FROM 2024_voluntarios_encaminhamentos AS ve
                                               INNER JOIN seg_usuarios AS u ON u.id = ve.responsavel_id 
                                               WHERE ve.ocorrencia_id = ? AND ve.status = 1");
        $result335->bindValue(1, $pessoas["ocorrencia_id"]);
        $result335->execute();

        $qtd_encaminhamentos = $result335->rowCount();

        $bairro = is_numeric($pessoas['bairro_id']) ? pesquisar("NM_BAIRRO", "bsc_bairros", "ID", "=", $pessoas['bairro_id'], "") : "";
        $regional = is_numeric($pessoas['bairro_id']) ? pesquisar("REGIONAL_NOME", "bsc_bairros", "ID", "=", $pessoas['bairro_id'], "") : "";

        echo '<div class="card px-3">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-xl-8 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <div class="pl-xl-2 pb-1">
                                            <div class="m-b-0">
                                                <div class="user-avatar-name d-inline-block">
                                                    <h2 class="font-20 m-b-10 strong-alt" style="margin-bottom: 0px;"><strong class="strong-alt">Demandante: </strong>' . ($pessoas['nome'] != "" && $pessoas['nome'] != null ? ctexto($pessoas['nome'], "mai") : ctexto($pessoas['outros'], "mai")) . '</h2>
                                                </div>
                                            </div>

                                            <div ' . ($pessoas['outros_contato'] != "" && $pessoas['outros_contato'] != null ? "" : "style='display: none;'") . ' class="m-b-0 mt-2">
                                                <div class="user-avatar-name d-inline-block">
                                                    <h2 class="font-20 m-b-10 strong-alt" style="margin-bottom: -5px;"><strong class="strong-alt">Contato: <b>' . $pessoas['outros_contato'] . '</b></strong></h2>
                                                </div>
                                            </div>

                                            <p class="text-dark mt-3" style="margin-bottom: -2px;">
                                                <strong class="strong-alt">Data/Hora: </strong>
                                                <small class="small-alt">' . (obterDataBRTimestamp($pessoas['data_cadastro']) . " - " . obterHoraTimestamp($pessoas['data_cadastro']) . ' -  ' . (is_numeric($pessoas['responsavel_id']) ? ctexto(pesquisar("nome", "seg_usuarios", "id", "=", $pessoas['responsavel_id'], ""), "pri") : "")) . '</small>
                                            </p>

                                            <p class="text-dark mt-3" style="margin-bottom: -5px;">
                                                <span class="badge ' . ($pessoas['situacao'] == 1 ? "bg-solucionado" : "badge-danger") . '">' . ($pessoas['situacao'] == 1 ? "Solucionado" : "Não solucionado") . '</span>

                                                <span class="badge ' . ($qtd_encaminhamentos >= 1 ? "badge-success" : "badge-grey") . '">' . ($qtd_encaminhamentos >= 1 ? "Encaminhado" : "Sem Encaminhamento") . '</span>
                                            </p>

                                            <div class="user-avatar-address mt-3">

                                                <p class="text-dark">
                                                    <strong class="strong-alt">Ocorrência: </strong>
                                                    <small class="small-alt">' . resume($pessoas['ocorrencia'], "200") . '</small>
                                                </p>

                                                <div class="row">
                                                    <button onclick="mais_detalhes_ocorrencia(' . "'" . $pessoas['ocorrencia_id'] . "'" . ')" class="btn btn-link btn-link-brand" data-toggle="collapse" data-target="#collapseSeven_' . $pessoas['ocorrencia_id'] . '" aria-expanded="true" aria-controls="collapseSeven_' . $pessoas['ocorrencia_id'] . '">
                                                        <span class="fas fa-angle-down mr-3"></span><span id="mais_detalhes_' . $pessoas['ocorrencia_id'] . '">Mais detalhes</span>
                                                    </button>
                                                </div>

                                                <div id="collapseSeven_' . $pessoas['ocorrencia_id'] . '" class="collapse row" aria-labelledby="headingSeven" data-parent="#accordion4">
                                                    <div class="card-body">

                                                        <div class="row">
                                                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                                                <div class="form-inline ml-4">
                                                                    <strong class="strong-alt">Local de Votação: </strong>
                                                                    <small class="small-alt">&nbsp;&nbsp;' . (is_numeric($pessoas['local_votacao_id']) ? pesquisar("NM_LOCAL_VOTACAO", "2024_locais_votacao", "ID", "=", $pessoas['local_votacao_id'], "") : "") . '</small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                                                <div class="form-inline ml-4">
                                                                    <strong class="strong-alt">Bairro: </strong>
                                                                    <small class="small-alt">&nbsp;&nbsp;' . $bairro . '</small>&nbsp;&nbsp;&nbsp;&nbsp;
                                                                    <strong class="strong-alt">Regional: </strong>
                                                                    <small class="small-alt">&nbsp;&nbsp;' . $regional . '</small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row mt-2">
                                                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                                                <div class="form-inline ml-4">
                                                                    <p class="text-dark mt-1">
                                                                        <strong class="strong-alt"><b>Encaminhamento(s):</b> <button onclick="add_encaminhamento(' . "'" . $pessoas['ocorrencia_id'] . "'" . ')" id="add_encaminhamento" name="add_encaminhamento" class="btn btn-success btn-xs">Novo</button></strong>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="cancelamento" class="card-body scroll-distribuidos border mt-2 row">';

        while ($encaminhamentos = $result335->fetch(PDO::FETCH_ASSOC)) {

            echo '<div class="row mb-3">
                                                                    <div class="col-md-12 detalhes-log">
                                                                        * Usuário: <b>' . $encaminhamentos['responsavel'] . '</b> em <b>' . obterDataBRTimestamp($encaminhamentos['data_update']) . '</b> às <b>' . obterHoraTimestamp($encaminhamentos['data_update']) . '</b>
                                                                        <p>* Descrição: <b>' . $encaminhamentos['descricao'] . '</b></p>
                                                                    </div>
                                                                </div>';
        }

        echo '</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <div class="float-xl-right float-none mt-xl-0 mt-4">
                                            <a title="Editar Usuário" href="' . PORTAL_URL . 'view/fiscal/cadastro-ocorrencias/' . $pessoas['ocorrencia_id'] . '"><button class="btn btn-outline-light btn-sm" ><i class="fas fa-edit mr-1"></i>Editar</button></a>
                                            <a href="#" onclick="remover(' . "'" . $pessoas['ocorrencia_id'] . "'" . ')" class="btn btn-outline-danger btn-sm"><i class="fas fa-trash mr-1"></i>Remover</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>';
    }
}
?> 