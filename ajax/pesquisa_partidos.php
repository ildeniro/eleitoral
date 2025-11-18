<?php

@session_start();

include_once('../config/geral.php');
include_once('../config/funcoes.php');
$db = Conexao::getInstance();

if ($_GET['pesquisa'] != "" && strlen($_GET['pesquisa']) > 0 && antiSQL($_GET['pesquisa']) == true) {

    $pesquisa = $_GET['pesquisa'];

    $result = $db->prepare("SELECT c.ANO_ELEICAO, c.ID_PARTIDO, c.SG_PARTIDO  
                            FROM 2024_candidatos AS c 
                            WHERE c.ANO_ELEICAO = 2024 AND c.SG_PARTIDO LIKE ? 
                            GROUP BY c.SG_PARTIDO");
    $result->bindValue(1, "%" . $pesquisa . "%");
    $result->execute();
    while ($partidos = $result->fetch(PDO::FETCH_ASSOC)) {

        echo '     <li class="list-group-item social-sales-content">
                                    <span class="social-sales-name">' . $partidos['SG_PARTIDO'] . '</span>
                                    <span class="social-sales-count text-dark">' . (qtd_candidatos_partido($partidos['ANO_ELEICAO'], $partidos['ID_PARTIDO'], 1392)) . ' Candidatos</span>
                                </li>';
    }
} else {

    $result = $db->prepare("SELECT c.ANO_ELEICAO, c.ID_PARTIDO, c.SG_PARTIDO  
                            FROM 2024_candidatos AS c 
                            WHERE c.ANO_ELEICAO = 2024
                            GROUP BY c.SG_PARTIDO");
    $result->execute();
    while ($partidos = $result->fetch(PDO::FETCH_ASSOC)) {

        echo '     <li class="list-group-item social-sales-content">
                                    <span class="social-sales-name">' . $partidos['SG_PARTIDO'] . '</span>
                                    <span class="social-sales-count text-dark">' . (qtd_candidatos_partido($partidos['ANO_ELEICAO'], $partidos['ID_PARTIDO'], 1392)) . ' Candidatos</span>
                                </li>';
    }
}
?> 