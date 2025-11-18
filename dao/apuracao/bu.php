<?php

$db = Conexao::getInstance();

$msg = array();
$error = false;
$hash = NULL;

$zona = isset($_POST['zona']) && $_POST['zona'] != "" ? $_POST['zona'] : NULL;
$secao = isset($_POST['secao']) && $_POST['secao'] != "" ? $_POST['secao'] : NULL;
$local = isset($_POST['local']) && $_POST['local'] != "" ? str_replace("&nbsp;", " ", $_POST['local']) : NULL;
$brancos = isset($_POST['brancos']) && $_POST['brancos'] != "" ? $_POST['brancos'] : NULL;
$nulos = isset($_POST['nulos']) && $_POST['nulos'] != "" ? $_POST['nulos'] : NULL;

$candidato = isset($_POST['candidato']) && $_POST['candidato'] != "" ? $_POST['candidato'] : NULL;
$votos = isset($_POST['votos']) && $_POST['votos'] != "" ? $_POST['votos'] : NULL;
$partidos = isset($_POST['partidos']) && $_POST['partidos'] != "" ? $_POST['partidos'] : NULL;

if ($local != null) {
    $local_explode = explode("<br>", $local);

    $novo_local = isset($local_explode[1]) != "" ? $local_explode[1] : "";

//VERIFICAÇÃO SE JÁ FOI INSERIDO UM RESULTLADO DO BU NO BANCO DE DADOS
    if (is_numeric(pesquisar3("id", "2024_resultados", "ZONA", "=", $zona, "SECAO", "=", $secao, "LOCAL_VOTACAO", "=", $novo_local, ""))) {
        $msg['msg'] = 'error';
        $msg['retorno'] = "Os votos desta Zona, Secao e Local ja foram inseridos.";
        echo json_encode($msg);
        exit();
    } else {
        try {

            $db->beginTransaction();

            //BRANCOS
            $stmt1 = $db->prepare("INSERT INTO 2024_resultados (ZONA, SECAO, LOCAL_VOTACAO, NUM_CANDIDATO, COD_CARGO, ANO_ELEICAO, COD_MUNICIPIO_TSE, TIPO_VOTO, QTD_VOTOS, TURNO, DATA_CADASTRO)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt1->bindValue(1, $zona);
            $stmt1->bindValue(2, $secao);
            $stmt1->bindValue(3, $novo_local);
            $stmt1->bindValue(4, NULL);
            $stmt1->bindValue(5, NULL);
            $stmt1->bindValue(6, "2024");
            $stmt1->bindValue(7, "1392");
            $stmt1->bindValue(8, "branco");
            $stmt1->bindValue(9, $brancos);
            $stmt1->bindValue(10, 1);
            $stmt1->execute();

            //NULOS
            $stmt2 = $db->prepare("INSERT INTO 2024_resultados (ZONA, SECAO, LOCAL_VOTACAO, NUM_CANDIDATO, COD_CARGO, ANO_ELEICAO, COD_MUNICIPIO_TSE, TIPO_VOTO, QTD_VOTOS, TURNO, DATA_CADASTRO)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt2->bindValue(1, $zona);
            $stmt2->bindValue(2, $secao);
            $stmt2->bindValue(3, $novo_local);
            $stmt2->bindValue(4, NULL);
            $stmt2->bindValue(5, NULL);
            $stmt2->bindValue(6, "2024");
            $stmt2->bindValue(7, "1392");
            $stmt2->bindValue(8, "nulo");
            $stmt2->bindValue(9, $nulos);
            $stmt2->bindValue(10, 1);
            $stmt2->execute();

            if (isset($candidato) && isset($votos) && isset($partidos)) {
                foreach ($candidato as $key => $val) {
                    if ($val != "") {
                        //NOMINAL
                        $stmt3 = $db->prepare("INSERT INTO 2024_resultados (ZONA, SECAO, LOCAL_VOTACAO, NUM_CANDIDATO, COD_CARGO, ANO_ELEICAO, COD_MUNICIPIO_TSE, TIPO_VOTO, QTD_VOTOS, TURNO, DATA_CADASTRO)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                        $stmt3->bindValue(1, $zona);
                        $stmt3->bindValue(2, $secao);
                        $stmt3->bindValue(3, $novo_local);
                        $stmt3->bindValue(4, $val);
                        $stmt3->bindValue(5, "Prefeito");
                        $stmt3->bindValue(6, "2024");
                        $stmt3->bindValue(7, "1392");
                        $stmt3->bindValue(8, "nominal");
                        $stmt3->bindValue(9, $votos[$key]);
                        $stmt3->bindValue(10, 1);
                        $stmt3->execute();
                    }
                }
            }

            $db->commit();
        } catch (PDOException $e) {
            $db->rollback();
            $msg['msg'] = 'error';
            $msg['retorno'] = "Erro ao tentar salvar os dados enviados:" . $e->getMessage();
            echo json_encode($msg);
            exit();
        }
    }
} else {
    $msg['msg'] = 'error';
    $msg['retorno'] = "É necessário escolher a Zona e Seção para continur.";
    echo json_encode($msg);
    exit();
}
?>