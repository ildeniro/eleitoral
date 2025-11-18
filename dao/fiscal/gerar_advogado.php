<?php

$db = Conexao::getInstance();

$error = false;

try {

    $db->beginTransaction();

    $pessoa_sessao = "";
    $pessoa_local = "";
    $pessoa_regional = "";
    $pessoa_bairro = "";

//------------------------------------------------------------------------------
//QUARTA FASE DA INSERAÇÃO
//POR ZONA, REGIONAL
//------------------------------------------------------------------------------

    $result4 = $db->prepare("SELECT e.*     
                             FROM eleicoes_localidades_2022 e
                             WHERE e.REGIONAL_RBO <> '' AND 
                             (SELECT COUNT(id) AS QTD FROM pessoas WHERE funcao_id = 2 AND zona_2 = e.ZONA AND regional_2 = e.REGIONAL_RBO) < 1 
                             GROUP BY e.REGIONAL_RBO 
                             ORDER BY e.ID ASC");
    $result4->execute();
    while ($sessoes4 = $result4->fetch(PDO::FETCH_ASSOC)) {

        $pessoa_zona_3 = $sessoes4['ZONA'];
        $pessoa_regional_3 = $sessoes4['REGIONAL_RBO'];

        $result33 = $db->prepare("SELECT p.secao_numero, p.bairro, p.zona, p.local_votacao, p.id, p.nome, p.indicacao, e2j.REGIONAL_RBO     
                                  FROM pessoas p
                                  INNER JOIN eleicoes_localidades_2022 AS e2j ON e2j.REGIONAL_RBO = p.regional  
                                  WHERE p.funcao_id = 2 AND p.regional_2 IS NULL AND p.status = 1 AND e2j.REGIONAL_RBO = ? AND e2j.ZONA = ? OR
                                  p.funcao_id = 2 AND p.regional_2 = ' ' AND p.status = 1 AND e2j.REGIONAL_RBO  = ? AND e2j.ZONA = ?   
                                  GROUP BY p.id 
                                  ORDER BY p.deficiencia DESC, p.nascimento DESC, p.nome ASC
                                  LIMIT 0, 1");
        $result33->bindValue(1, $pessoa_regional_3);
        $result33->bindValue(2, $pessoa_zona_3);
        $result33->bindValue(3, $pessoa_regional_3);
        $result33->bindValue(4, $pessoa_zona_3);
        $result33->execute();

        while ($pessoa3 = $result33->fetch(PDO::FETCH_ASSOC)) {//POR SESSAO, LOCAL, BAIRRO, REGIONAL
            if (qtd_pessoas_regional($pessoa_regional_3, 2) < 1) {

                $pessoas3 = $pessoa3['id'];

                $sql3 = $db->prepare("UPDATE pessoas SET regional_2 = ?, usuario_id_2 = ?, data_update_2 = NOW() WHERE id = ?");
                $sql3->bindValue(1, $pessoa_regional_3);
                $sql3->bindValue(2, $_SESSION['id']);
                $sql3->bindValue(3, $pessoas3);
                $sql3->execute();
            }
        }
    }

    $sql_configuracoes = $db->prepare("UPDATE configuracoes SET advogado = 1 WHERE id = 1");
    $sql_configuracoes->execute();

    $db->commit();

    //MENSAGEM DE SUCESSO
    $msg['msg'] = 'success';
    $msg['retorno'] = 'Distribuição de advogados gerada com sucesso!';
    echo json_encode($msg);
    exit();
} catch (PDOException $e) {
    $db->rollback();
    $msg['msg'] = 'error';
    $msg['retorno'] = "Erro ao tentar gerar a distribuição de advogados desejada:" . $e->getMessage();
    echo json_encode($msg);
    exit();
}
?>


