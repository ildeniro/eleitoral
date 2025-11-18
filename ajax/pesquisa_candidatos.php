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
        $condicao = "AND c.STATUS IN(1,0)";
    } else if ($ativo == 1 && $cancelado == "") {
        $condicao = "AND c.STATUS = 1";
    } else if ($ativo == "" && $cancelado == 0) {
        $condicao = "AND c.STATUS = 0";
    }

    echo '<div class="row">';

    $result = $db->prepare("SELECT *   
                            FROM 2024_candidatos c 
                            WHERE c.SG_UE = 01392 AND c.NM_CANDIDATO LIKE ? $condicao OR c.NM_URNA_CANDIDATO LIKE ? $condicao  
                            ORDER BY c.STATUS DESC, c.NM_CANDIDATO ASC");
    $result->bindValue(1, "%" . $pesquisa . "%");
    $result->bindValue(2, "%" . $pesquisa . "%");
    $result->execute();
    while ($candidato = $result->fetch(PDO::FETCH_ASSOC)) {

        $foto_caminho = "template/assets/images/candidatos/2024/FAC" . $candidato['SQ_CANDIDATO'];

        if (is_file(PORTAL_URL_SERVER . $foto_caminho . "_div.jpg")) {
            $foto_caminho = $foto_caminho . "_div.jpg";
        } else if (is_file(PORTAL_URL_SERVER . $foto_caminho . "_div.jpeg")) {
            $foto_caminho = $foto_caminho . "_div.jpeg";
        }

        echo '<div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
                                <div class="product-thumbnail">
                                    <div class="product-img-head">
                                        <div class="product-img">
                                            <img src="' . PORTAL_URL . "" . $foto_caminho . '" alt="' . $candidato['NM_URNA_CANDIDATO'] . '" alt="" class="img-fluid">
                                        </div>
                                        <div class="ribbons ' . ($candidato['STATUS'] == 1 ? "bg-success" : "bg-danger") . '"></div>
                                        <div class="ribbons-text">' . ($candidato['STATUS'] == 1 ? "Ativo" : "Inativo") . '</div>

                                    </div>
                                    <div class="product-content">
                                        <div class="product-content-head">
                                            <h3 class="product-title">' . $candidato['NM_CANDIDATO'] . ' - ' . $candidato['NM_URNA_CANDIDATO'] . '</h3>
                                            <div class=" d-inline-block">
                                                <strong style="font-size: 16px; font-weight: 400;">Número:</strong>
                                                <small style="font-size: 16px; font-weight: 700;">' . $candidato['NR_CANDIDATO'] . '</small> |
                                                <strong style="font-size: 16px; font-weight: 400;">Cargo: </strong>
                                                <small style="font-size: 16px; font-weight: 700;">' . $candidato['DS_CARGO'] . '</small> |
                                                <strong style="font-size: 16px; font-weight: 400;">Partido: </strong>
                                                <small style="font-size: 16px; font-weight: 700;">' . $candidato['NM_PARTIDO'] . '</small>
                                            </div>
                                            <!-- <div class="product-price">' . $candidato['NR_CANDIDATO'] . '</div> -->
                                        </div>
                                        <div class="product-btn">
                                            <a href="#" class="btn  btn-personalizado">Details</a>
                                        </div>
                                    </div>
                                </div>
                            </div>';
    }

    echo '</div>';
} else {

    $ativo = isset($_GET['ativo']) ? $_GET['ativo'] : "";
    $cancelado = isset($_GET['cancelado']) ? $_GET['cancelado'] : "";

    if ($ativo == 1 && $cancelado == 0) {
        $condicao = "AND c.STATUS IN(1,0)";
    } else if ($ativo == 1 && $cancelado == "") {
        $condicao = "AND c.STATUS = 1";
    } else if ($ativo == "" && $cancelado == 0) {
        $condicao = "AND c.STATUS = 0";
    }

    echo '<div class="row">';

    $result = $db->prepare("SELECT *   
                            FROM 2024_candidatos c 
                            WHERE c.SG_UE = 01392 $condicao  
                            ORDER BY c.STATUS DESC, c.NM_CANDIDATO ASC");
    $result->execute();
    while ($candidato = $result->fetch(PDO::FETCH_ASSOC)) {

        $foto_caminho = "template/assets/images/candidatos/2024/FAC" . $candidato['SQ_CANDIDATO'];

        if (is_file(PORTAL_URL_SERVER . $foto_caminho . "_div.jpg")) {
            $foto_caminho = $foto_caminho . "_div.jpg";
        } else if (is_file(PORTAL_URL_SERVER . $foto_caminho . "_div.jpeg")) {
            $foto_caminho = $foto_caminho . "_div.jpeg";
        }

        echo '<div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
                                <div class="product-thumbnail">
                                    <div class="product-img-head">
                                        <div class="product-img">
                                            <img src="' . PORTAL_URL . "" . $foto_caminho . '" alt="' . $candidato['NM_URNA_CANDIDATO'] . '" alt="" class="img-fluid">
                                        </div>
                                        <div class="ribbons ' . ($candidato['STATUS'] == 1 ? "bg-success" : "bg-danger") . '"></div>
                                        <div class="ribbons-text">' . ($candidato['STATUS'] == 1 ? "Ativo" : "Inativo") . '</div>

                                    </div>
                                    <div class="product-content">
                                        <div class="product-content-head">
                                            <h3 class="product-title">' . $candidato['NM_CANDIDATO'] . ' - ' . $candidato['NM_URNA_CANDIDATO'] . '</h3>
                                            <div class=" d-inline-block">
                                                <strong style="font-size: 16px; font-weight: 400;">Número:</strong>
                                                <small style="font-size: 16px; font-weight: 700;">' . $candidato['NR_CANDIDATO'] . '</small> |
                                                <strong style="font-size: 16px; font-weight: 400;">Cargo: </strong>
                                                <small style="font-size: 16px; font-weight: 700;">' . $candidato['DS_CARGO'] . '</small> |
                                                <strong style="font-size: 16px; font-weight: 400;">Partido: </strong>
                                                <small style="font-size: 16px; font-weight: 700;">' . $candidato['NM_PARTIDO'] . '</small>
                                            </div>
                                            <!-- <div class="product-price">' . $candidato['NR_CANDIDATO'] . '</div> -->
                                        </div>
                                        <div class="product-btn">
                                            <a href="#" class="btn  btn-personalizado">Details</a>
                                        </div>
                                    </div>
                                </div>
                            </div>';
    }
    echo '</div>';
}
?> 