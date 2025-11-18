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
//SEGUNDA FASE DA INSERAÇÃO
//POR ZONA, LOCAL, BAIRRO, REGIONAL
//------------------------------------------------------------------------------

    $result2 = $db->prepare("SELECT e.*     
                             FROM eleicoes_localidades_2022 e
                             WHERE e.REGIONAL_RBO <> '' AND 
                             (SELECT COUNT(id) AS QTD FROM pessoas WHERE funcao_id = 6 AND zona_2 = e.ZONA AND local_votacao_2 = e.LOCAL_VOTACAO) < 1 
                             GROUP BY e.ZONA, e.LOCAL_VOTACAO 
                             ORDER BY e.ID ASC");
    $result2->execute();
    while ($sessoes2 = $result2->fetch(PDO::FETCH_ASSOC)) {

        $pessoa_zona_2 = $sessoes2['ZONA'];
        $pessoa_local_2 = $sessoes2['LOCAL_VOTACAO'];
        $pessoa_regional_2 = $sessoes2['REGIONAL_RBO'];
        $pessoa_bairro_2 = $sessoes2['BAIRRO'];
        $pessoa_secao_2 = $sessoes2['NR_SECAO'];

        $result22 = $db->prepare("SELECT p.secao_numero, p.bairro, p.zona, p.local_votacao, p.id, p.nome, p.indicacao, e2j.REGIONAL_RBO     
                                  FROM pessoas p
                                  INNER JOIN eleicoes_localidades_2022 AS e2j ON e2j.ZONA = p.zona AND e2j.LOCAL_VOTACAO = p.local_votacao 
                                  WHERE p.funcao_id = 6 AND p.secao_numero_2 IS NULL AND p.status = 1 AND e2j.LOCAL_VOTACAO = ? AND e2j.REGIONAL_RBO = ? AND e2j.BAIRRO = ? AND e2j.ZONA = ? OR
                                  p.funcao_id = 6 AND p.secao_numero_2 = ' ' AND p.status = 1 AND e2j.LOCAL_VOTACAO = ? AND e2j.REGIONAL_RBO  = ? AND e2j.BAIRRO = ? AND e2j.ZONA = ?  
                                  GROUP BY p.id 
                                  ORDER BY p.deficiencia DESC, p.nascimento DESC, p.nome ASC
                                  LIMIT 0, 1");
        $result22->bindValue(1, $pessoa_local_2);
        $result22->bindValue(2, $pessoa_regional_2);
        $result22->bindValue(3, $pessoa_bairro_2);
        $result22->bindValue(4, $pessoa_zona_2);
        $result22->bindValue(5, $pessoa_local_2);
        $result22->bindValue(6, $pessoa_regional_2);
        $result22->bindValue(7, $pessoa_bairro_2);
        $result22->bindValue(8, $pessoa_zona_2);
        $result22->execute();

        while ($pessoa2 = $result22->fetch(PDO::FETCH_ASSOC)) {//POR SESSAO, LOCAL, BAIRRO, REGIONAL
            if (qtd_pessoas_sessao_2($pessoa_zona_2, $pessoa_local_2, $pessoa_secao_2, 6) < 1) {

                $pessoas2 = $pessoa2['id'];
                $secao2 = $pessoa_secao_2;
                $regional2 = $pessoa_regional_2;
                $local2 = $pessoa_local_2;
                $zona2 = $pessoa_zona_2;
                $bairro2 = $pessoa_bairro_2;

                $sql2 = $db->prepare("UPDATE pessoas SET zona_2 = ?, secao_numero_2 = ?, regional_2 = ?, local_votacao_2 = ?, usuario_id_2 = ?, bairro_2 = ?, data_update_2 = NOW() WHERE id = ?");
                $sql2->bindValue(1, $zona2);
                $sql2->bindValue(2, $secao2);
                $sql2->bindValue(3, $regional2);
                $sql2->bindValue(4, $local2);
                $sql2->bindValue(5, $_SESSION['id']);
                $sql2->bindValue(6, $bairro2);
                $sql2->bindValue(7, $pessoas2);
                $sql2->execute();
            }
        }
    }
    
//------------------------------------------------------------------------------
//TERCEIRA FASE DA INSERAÇÃO
//POR ZONA, BAIRRO, REGIONAL
//------------------------------------------------------------------------------

    $result3 = $db->prepare("SELECT e.*     
                             FROM eleicoes_localidades_2022 e
                             WHERE e.REGIONAL_RBO <> '' AND 
                             (SELECT COUNT(id) AS QTD FROM pessoas WHERE funcao_id = 6 AND zona_2 = e.ZONA AND local_votacao_2 = e.LOCAL_VOTACAO) < 1 
                             GROUP BY e.ZONA, e.LOCAL_VOTACAO 
                             ORDER BY e.ID ASC");
    $result3->execute();
    while ($sessoes3 = $result3->fetch(PDO::FETCH_ASSOC)) {

        $pessoa_zona_3 = $sessoes3['ZONA'];
        $pessoa_regional_3 = $sessoes3['REGIONAL_RBO'];
        $pessoa_bairro_3 = $sessoes3['BAIRRO'];
        $pessoa_local_3 = $sessoes3['LOCAL_VOTACAO'];
        $pessoa_secao_3 = $sessoes3['NR_SECAO'];

        $result33 = $db->prepare("SELECT p.secao_numero, p.bairro, p.zona, p.local_votacao, p.id, p.nome, p.indicacao, e2j.REGIONAL_RBO     
                                  FROM pessoas p
                                  INNER JOIN eleicoes_localidades_2022 AS e2j ON e2j.ZONA = p.zona AND e2j.BAIRRO = p.bairro 
                                  WHERE p.funcao_id = 6 AND p.secao_numero_2 IS NULL AND p.status = 1 AND e2j.REGIONAL_RBO = ? AND e2j.BAIRRO = ? AND e2j.ZONA = ? OR
                                  p.funcao_id = 6 AND p.secao_numero_2 = ' ' AND p.status = 1 AND e2j.REGIONAL_RBO  = ? AND e2j.BAIRRO = ? AND e2j.ZONA = ?  
                                  GROUP BY p.id 
                                  ORDER BY p.deficiencia DESC, p.nascimento DESC, p.nome ASC
                                  LIMIT 0, 1");
        $result33->bindValue(1, $pessoa_regional_3);
        $result33->bindValue(2, $pessoa_bairro_3);
        $result33->bindValue(3, $pessoa_zona_3);
        $result33->bindValue(4, $pessoa_regional_3);
        $result33->bindValue(5, $pessoa_bairro_3);
        $result33->bindValue(6, $pessoa_zona_3);
        $result33->execute();

        while ($pessoa3 = $result33->fetch(PDO::FETCH_ASSOC)) {//POR SESSAO, LOCAL, BAIRRO, REGIONAL
            if (qtd_pessoas_sessao($pessoa_zona_3, $pessoa_secao_3, 6) < 1) {

                $pessoas3 = $pessoa3['id'];
                $secao3 = $pessoa_secao_3;
                $regional3 = $pessoa_regional_3;
                $local3 = $pessoa_local_3;
                $zona3 = $pessoa_zona_3;
                $bairro3 = $pessoa_bairro_3;

                $sql3 = $db->prepare("UPDATE pessoas SET zona_2 = ?, secao_numero_2 = ?, regional_2 = ?, local_votacao_2 = ?, usuario_id_2 = ?, bairro_2 = ?, data_update_2 = NOW() WHERE id = ?");
                $sql3->bindValue(1, $zona3);
                $sql3->bindValue(2, $secao3);
                $sql3->bindValue(3, $regional3);
                $sql3->bindValue(4, $local3);
                $sql3->bindValue(5, $_SESSION['id']);
                $sql3->bindValue(6, $bairro3);
                $sql3->bindValue(7, $pessoas3);
                $sql3->execute();
            }
        }
    }

    $sql_configuracoes = $db->prepare("UPDATE configuracoes SET coordenador = 1 WHERE id = 1");
    $sql_configuracoes->execute();

    $db->commit();

    //MENSAGEM DE SUCESSO
    $msg['msg'] = 'success';
    $msg['retorno'] = 'Distribuição de coordenadores gerada com sucesso!';
    echo json_encode($msg);
    exit();
} catch (PDOException $e) {
    $db->rollback();
    $msg['msg'] = 'error';
    $msg['retorno'] = "Erro ao tentar gerar a distribuição de coordenadores desejada:" . $e->getMessage();
    echo json_encode($msg);
    exit();
}
?>


