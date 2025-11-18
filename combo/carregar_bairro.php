<?php

//------------------------------------------------------------------------------
include_once('../config/geral.php');
include_once('../config/funcoes.php');
$db = Conexao::getInstance();

$id = $_POST['id'];

$total_votos = carregar_votos_bairro_totoal($id, 'nominal', 2024);

$candidatos_array = array();

$cand_22 = carregar_votos_bairro(22, 'nominal', $id, 2024);
$cand_15 = carregar_votos_bairro(15, 'nominal', $id, 2024);
$cand_30 = carregar_votos_bairro(30, 'nominal', $id, 2024);
$cand_40 = carregar_votos_bairro(40, 'nominal', $id, 2024);

$candidatos_array = array("30" => $cand_30, "40" => $cand_40, "15" => $cand_15, "22" => $cand_22);

arsort($candidatos_array);

$porc_30 = $cand_30 > 0 ? ($cand_30 / $total_votos) * 100 : 0;
$porc_40 = $cand_40 > 0 ? ($cand_40 / $total_votos) * 100 : 0;
$porc_15 = $cand_15 > 0 ? ($cand_15 / $total_votos) * 100 : 0;
$porc_22 = $cand_22 > 0 ? ($cand_22 / $total_votos) * 100 : 0;

echo '<div class="modal-header">
                <h5 class="modal-title" id="bairro_nome" style="color: white">BAIRRO ' . ctexto(nome_bairro($id), "mai") . '</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <h3 class="text-center" id="regional_nome">REGIONAL ' . ctexto(nome_regional($id), "mai") . '</h3>
                <ul>';

foreach ($candidatos_array as $chave => $valor) {
    if ($chave == 30) {
        echo '<li id="cand_30"><img src="template/assets/images/candidatos/2024/FAC10001913167_div.jpeg" alt=""> <strong class="sn40">30</strong> <div>Jarude </div><span id="votos_30">' . $cand_30 . ' <small>votos</small>  | ' . fdec($porc_30) . ' %</span></li>';
    }

    if ($chave == 40) {
        echo '<li id="cand_40"><img src="template/assets/images/candidatos/2024/FAC10002171655_div.jp" alt=""> <strong class="dz13">40</strong> <div>Dr. Jenilson </div><span id="votos_40">' . $cand_40 . ' <small>votos</small> | ' . fdec($porc_40) . ' %</span></li>';
    }

    if ($chave == 15) {
        echo '<li id="cand_15"><img src="template/assets/images/candidatos/2024/FAC10001984285_div.jpg" alt=""> <strong class="ja20">15</strong> <div>Marcus Alexandre </div><span id="votos_15">' . $cand_15 . ' <small>votos</small> | ' . fdec($porc_15) . ' %</span></li>';
    }

    if ($chave == 22) {
        echo '<li id="cand_22"><img src="template/assets/images/candidatos/2024/FAC10001908807_div.jpg" alt=""> <strong class="mk45">22</strong> <div>Tião Bocalom </div><span id="votos_22">' . $cand_22 . ' <small>votos</small> | ' . fdec($porc_22) . ' %</span></li>';
    }
}

echo '</ul>
                <hr>
                <div class="dados">
                    <div class="row">
                        <div class="col-6">
                            <strong>SEÇÃO:</strong>
                            <small>' . carregar_secao_bairro($id) . '</small>
                        </div>
                        <div class="col-6">
                            <span>' . $total_votos . ' <small>votos</small> | 100%</span>
                        </div>
                    </div>
                </div>
            </div>';
//------------------------------------------------------------------------------
?>

