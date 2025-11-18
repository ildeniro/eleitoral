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
//PRIMEIRA FASE DA INSERAÇÃO
//POR ZONA, SESSAO, LOCAL, BAIRRO, REGIONAL
//------------------------------------------------------------------------------
//    $result8 = $db->prepare("SELECT s.NR_ZONA, s.NR_SECAO, s.LOCAL_VOTACAO_ID, v.CD_BAIRRO, v.REGIONAL_ID, v.CD_MUNICIPIO     
//                             FROM 2024_secoes AS s  
//                             INNER JOIN 2024_locais_votacao AS v ON v.ID = s.LOCAL_VOTACAO_ID 
//                             WHERE s.TIPO = 'Principal' AND v.MUNICIPIO_ID = 94 AND s.ID NOT IN(SELECT ID FROM 2024_secoes WHERE NR_ZONA = 1 AND TIPO = 'Principal' AND NR_SECAO IN(9, 11, 13, 45, 192, 193, 297, 327, 419, 477, 483, 520, 523, 548, 550, 542, 694, 714, 771, 830, 833, 837, 840, 845, 854) OR TIPO = 'Principal' AND NR_ZONA = 9 AND NR_SECAO IN(230, 239, 248, 275, 301, 378)) 
//                             ORDER BY s.ID ASC");
//    $result8->execute();
//    while ($sessoes = $result8->fetch(PDO::FETCH_ASSOC)) {
//
//        $pessoa_zona = $sessoes['NR_ZONA'];
//        $pessoa_sessao = $sessoes['NR_SECAO'];
//        $pessoa_local = $sessoes['LOCAL_VOTACAO_ID'];
//        $pessoa_regional = $sessoes['REGIONAL_ID'];
//        $pessoa_bairro = $sessoes['CD_BAIRRO'];
//        $pessoa_municipio = $sessoes['CD_MUNICIPIO'];
//
//        $result88 = $db->prepare("SELECT v.MUNICIPIO_ID, p.secao_numero, p.bsc_bairros_id, p.zona, v.NM_LOCAL_VOTACAO AS local_votacao, p.id, p.nome, b.REGIONAL_ID     
//                                  FROM 2024_voluntarios p
//                                  INNER JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero 
//                                  INNER JOIN 2024_locais_votacao AS v ON v.ID = s.LOCAL_VOTACAO_ID 
//                                  INNER JOIN bsc_bairros AS b ON b.ID = v.CD_BAIRRO 
//                                  WHERE p.funcao_id = 5 AND p.secao_numero_2 IS NULL AND p.status = 1 AND s.LOCAL_VOTACAO_ID = ? AND b.REGIONAL_ID = ? AND v.CD_BAIRRO = ? AND s.NR_SECAO = ? AND s.NR_ZONA = ? OR
//                                  p.funcao_id = 5 AND p.secao_numero_2 = ' ' AND p.status = 1 AND s.LOCAL_VOTACAO_ID = ? AND b.REGIONAL_ID  = ? AND v.CD_BAIRRO = ? AND s.NR_SECAO = ? AND s.NR_ZONA = ?   
//                                  ORDER BY p.deficiencia ASC, p.nascimento ASC, p.nome ASC 
//                                  LIMIT 0, 2");
//        $result88->bindValue(1, $pessoa_local);
//        $result88->bindValue(2, $pessoa_regional);
//        $result88->bindValue(3, $pessoa_bairro);
//        $result88->bindValue(4, $pessoa_sessao);
//        $result88->bindValue(5, $pessoa_zona);
//        $result88->bindValue(6, $pessoa_local);
//        $result88->bindValue(7, $pessoa_regional);
//        $result88->bindValue(8, $pessoa_bairro);
//        $result88->bindValue(9, $pessoa_sessao);
//        $result88->bindValue(10, $pessoa_zona);
//        $result88->execute();
//
//        while ($pessoa = $result88->fetch(PDO::FETCH_ASSOC)) {//POR SESSAO, LOCAL, BAIRRO, REGIONAL
//            $vf_distribuicao = qtd_pessoas_sessao($pessoa_zona, $pessoa_sessao, 5);
//
//            if ($vf_distribuicao < 2 && vf_regras_fiscais($pessoa_zona, $vf_distribuicao, $pessoa_local)) {
//
//                $pessoas = $pessoa['id'];
//
//                $local = pesquisar("NM_LOCAL_VOTACAO", "2024_locais_votacao", "ID", "=", $pessoa_local, "");
//
//                if ($vf_distribuicao > 0 && qtd_pessoas_local($pessoa_local) >= 6) {// 4 - SE TIVER 6 OU MAIS SEÇÕES PRINCIPAIS POR LOCAL PERMITIR 2 PARA CADA SEÇÃO PRINCIPAL
//                    $nova_sessao = carregar_secao_vazio($pessoa_local, 5);
//                    $pessoa_sessao = is_numeric($nova_sessao) ? $nova_sessao : $pessoa_sessao;
//                }
//
//                $sql = $db->prepare("UPDATE 2024_voluntarios SET zona_2 = ?, secao_numero_2 = ?, regional_2 = ?, local_votacao_2 = ?, usuario_id_2 = ?, bairro_2 = ?, municipio_2 = ?, data_update_2 = NOW() WHERE id = ?");
//                $sql->bindValue(1, $pessoa_zona);
//                $sql->bindValue(2, $pessoa_sessao);
//                $sql->bindValue(3, $pessoa_regional);
//                $sql->bindValue(4, $local);
//                $sql->bindValue(5, $_SESSION['id']);
//                $sql->bindValue(6, $pessoa_bairro);
//                $sql->bindValue(7, $pessoa_municipio);
//                $sql->bindValue(8, $pessoas);
//                $sql->execute();
//            }
//        }
//    }
//------------------------------------------------------------------------------
//SEGUNDA FASE DA INSERAÇÃO
//POR ZONA, LOCAL, BAIRRO, REGIONAL
//------------------------------------------------------------------------------
//    $result2 = $db->prepare("SELECT s.NR_ZONA, s.NR_SECAO, s.LOCAL_VOTACAO_ID, v.CD_BAIRRO, v.REGIONAL_ID, v.CD_MUNICIPIO     
//                             FROM 2024_secoes AS s  
//                             INNER JOIN 2024_locais_votacao AS v ON v.ID = s.LOCAL_VOTACAO_ID 
//                             WHERE s.TIPO = 'Principal' AND v.MUNICIPIO_ID = 94 AND s.ID NOT IN(SELECT ID FROM 2024_secoes WHERE NR_ZONA = 1 AND TIPO = 'Principal' AND NR_SECAO IN(9, 11, 13, 45, 192, 193, 297, 327, 419, 477, 483, 520, 523, 548, 550, 542, 694, 714, 771, 830, 833, 837, 840, 845, 854) OR TIPO = 'Principal' AND NR_ZONA = 9 AND NR_SECAO IN(230, 239, 248, 275, 301, 378)) 
//                             ORDER BY s.ID ASC");
//    $result2->execute();
//    while ($sessoes2 = $result2->fetch(PDO::FETCH_ASSOC)) {
//
//        $pessoa_zona_2 = $sessoes2['NR_ZONA']; //1
//        $pessoa_local_2 = $sessoes2['LOCAL_VOTACAO_ID']; //64 - COLEGEO ACREANO
//        $pessoa_regional_2 = $sessoes2['REGIONAL_ID']; //4 - CADEIA VELHA
//        $pessoa_bairro_2 = $sessoes2['CD_BAIRRO']; //23 - CENTRO
//        $pessoa_secao_3 = $sessoes2['NR_SECAO']; //38
//
//        $result22 = $db->prepare("SELECT v.MUNICIPIO_ID, p.secao_numero, p.bsc_bairros_id, p.zona, v.NM_LOCAL_VOTACAO AS local_votacao, p.id, p.nome, b.REGIONAL_ID     
//                                  FROM 2024_voluntarios p
//                                  INNER JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero 
//                                  INNER JOIN 2024_locais_votacao AS v ON v.ID = s.LOCAL_VOTACAO_ID 
//                                  INNER JOIN bsc_bairros AS b ON b.ID = v.CD_BAIRRO 
//                                  WHERE p.funcao_id = 5 AND p.secao_numero_2 IS NULL AND p.status = 1 AND v.ID = ?  OR
//                                  p.funcao_id = 5 AND p.secao_numero_2 = ' ' AND p.status = 1 AND v.ID = ?    
//                                  ORDER BY p.deficiencia ASC, p.nascimento ASC, p.nome ASC");
//
//        $result22->bindValue(1, $pessoa_local_2);
//        $result22->bindValue(2, $pessoa_local_2);
//        $result22->execute();
//
//        while ($pessoa2 = $result22->fetch(PDO::FETCH_ASSOC)) {//POR SESSAO, LOCAL, BAIRRO, REGIONAL
//            $vf_distribuicao = qtd_pessoas_sessao($pessoa_zona_2, $pessoa_secao_3, 5);
//
//            if ($vf_distribuicao < 2 && vf_regras_fiscais($pessoa_zona_2, $vf_distribuicao, $pessoa_local_2)) {
//
//                $pessoas2 = $pessoa2['id'];
//
//                if ($vf_distribuicao > 0 && qtd_pessoas_local($pessoa_local_2) >= 6) {// 4 - SE TIVER 6 OU MAIS SEÇÕES PRINCIPAIS POR LOCAL PERMITIR 2 PARA CADA SEÇÃO PRINCIPAL
//                    $nova_sessao = carregar_secao_vazio($pessoa_local_2, 5);
//                    $pessoa_secao_3 = is_numeric($nova_sessao) ? $nova_sessao : $pessoa_secao_3;
//                }
//
//                $sql2 = $db->prepare("UPDATE 2024_voluntarios SET zona_2 = ?, secao_numero_2 = ?, regional_2 = ?, local_votacao_2 = ?, usuario_id_2 = ?, bairro_2 = ?, data_update_2 = NOW() WHERE id = ?");
//                $sql2->bindValue(1, $pessoa_zona_2);
//                $sql2->bindValue(2, $pessoa_secao_3);
//                $sql2->bindValue(3, $pessoa_regional_2);
//                $sql2->bindValue(4, $pessoa_local_2);
//                $sql2->bindValue(5, $_SESSION['id']);
//                $sql2->bindValue(6, $pessoa_bairro_2);
//                $sql2->bindValue(7, $pessoas2);
//                $sql2->execute();
//            }
//        }
//    }
//------------------------------------------------------------------------------
//TERCEIRA FASE DA INSERAÇÃO
//POR ZONA, BAIRRO, REGIONAL
//------------------------------------------------------------------------------
//
//    $result3 = $db->prepare("SELECT s.NR_ZONA AS ZONA, s.NR_SECAO, s.LOCAL_VOTACAO_ID, e.CD_BAIRRO, e.REGIONAL_ID       
//                             FROM 2024_locais_votacao AS e 
//                             INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = e.ID 
//                             WHERE s.TIPO = 'Principal' AND e.MUNICIPIO_ID = 94 AND 
//                             (SELECT COUNT(id) AS QTD FROM 2024_voluntarios WHERE funcao_id = 5 AND zona_2 = s.NR_ZONA AND secao_numero_2 = s.NR_SECAO) < 2 
//                             GROUP BY s.NR_ZONA, s.NR_SECAO 
//                             ORDER BY e.ID ASC");
//    $result3->execute();
//    while ($sessoes3 = $result3->fetch(PDO::FETCH_ASSOC)) {
//
//        $pessoa_zona_3 = $sessoes3['NR_ZONA'];
//        $pessoa_regional_3 = $sessoes3['REGIONAL_ID'];
//        $pessoa_bairro_3 = $sessoes3['CD_BAIRRO'];
//        $pessoa_local_3 = $sessoes3['LOCAL_VOTACAO_ID'];
//        $pessoa_secao_3 = $sessoes3['NR_SECAO'];
//
//        $result33 = $db->prepare("SELECT v.MUNICIPIO_ID, p.secao_numero, p.bsc_bairros_id, p.zona, v.NM_LOCAL_VOTACAO AS local_votacao, p.id, p.nome, b.REGIONAL_ID     
//                                  FROM 2024_voluntarios p
//                                  INNER JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero 
//                                  INNER JOIN 2024_locais_votacao AS v ON v.ID = s.LOCAL_VOTACAO_ID 
//                                  INNER JOIN bsc_bairros AS b ON b.ID = v.CD_BAIRRO 
//                                  WHERE p.funcao_id = 5 AND p.secao_numero_2 IS NULL AND p.status = 1 AND b.REGIONAL_ID = ? AND v.CD_BAIRRO = ? AND s.NR_ZONA = ? OR
//                                  p.funcao_id = 5 AND p.secao_numero_2 = ' ' AND p.status = 1 AND b.REGIONAL_ID  = ? AND v.CD_BAIRRO = ? AND s.NR_ZONA = ?   
//                                  ORDER BY p.deficiencia ASC, p.nascimento ASC, p.nome ASC 
//                                  LIMIT 0, 2");
//        $result33->bindValue(1, $pessoa_regional_3);
//        $result33->bindValue(2, $pessoa_bairro_3);
//        $result33->bindValue(3, $pessoa_zona_3);
//        $result33->bindValue(4, $pessoa_regional_3);
//        $result33->bindValue(5, $pessoa_bairro_3);
//        $result33->bindValue(6, $pessoa_zona_3);
//        $result33->execute();
//
//        while ($pessoa3 = $result33->fetch(PDO::FETCH_ASSOC)) {//POR SESSAO, LOCAL, BAIRRO, REGIONAL
//            if (qtd_pessoas_sessao($pessoa_zona_3, $pessoa_secao_3, 5) < 2) {
//
//                $pessoas3 = $pessoa3['id'];
//
//                $sql3 = $db->prepare("UPDATE 2024_voluntarios SET zona_2 = ?, secao_numero_2 = ?, regional_2 = ?, local_votacao_2 = ?, usuario_id_2 = ?, bairro_2 = ?, data_update_2 = NOW() WHERE id = ?");
//                $sql3->bindValue(1, $pessoa_zona_3);
//                $sql3->bindValue(2, $pessoa_secao_3);
//                $sql3->bindValue(3, $pessoa_regional_3);
//                $sql3->bindValue(4, $pessoa_local_3);
//                $sql3->bindValue(5, $_SESSION['id']);
//                $sql3->bindValue(6, $pessoa_bairro_3);
//                $sql3->bindValue(7, $pessoas3);
//                $sql3->execute();
//            }
//        }
//    }
////------------------------------------------------------------------------------
////QUARTA FASE DA INSERAÇÃO
////POR LATITUDE E LONGITUDE
////------------------------------------------------------------------------------
    $result5 = $db->prepare("SELECT v.LATITUDE, v.LONGITUDE, s.NR_ZONA, s.NR_SECAO, s.LOCAL_VOTACAO_ID, v.CD_BAIRRO, v.REGIONAL_ID      
                             FROM 2024_secoes AS s  
                             INNER JOIN 2024_locais_votacao AS v ON v.ID = s.LOCAL_VOTACAO_ID 
                             WHERE s.TIPO = 'Principal' AND v.MUNICIPIO_ID = 94 
                             GROUP BY s.NR_ZONA, s.NR_SECAO 
                             ORDER BY s.ID ASC");
    $result5->execute();

    $secoes = $result5->fetchAll();

    foreach ($secoes as $secao) {

        $latitudeSecao = $secao['LATITUDE'];
        $longitudeSecao = $secao['LONGITUDE'];
        $pessoa_zona_5 = $secao['NR_ZONA'];
        $pessoa_regional_5 = $secao['REGIONAL_ID'];
        $pessoa_bairro_5 = $secao['CD_BAIRRO'];
        $pessoa_local_5 = $secao['LOCAL_VOTACAO_ID'];
        $pessoa_secao_5 = $secao['NR_SECAO'];

        $vf_distribuicao = qtd_pessoas_sessao($pessoa_zona_5, $pessoa_secao_5, 5);

        if ($vf_distribuicao < 2 && vf_regras_fiscais($pessoa_zona_5, $vf_distribuicao, $pessoa_local_5)) {

            $maxDistanciaMetros = 1200; // Limite máximo de 1.000 metros
            $incrementoMetros = 100; // Incremento de 200 metros
            //
            // Grupos de regionais
            $grupoA = [2, 4, 5, 6, 7, 8, 9]; // Grupo A - 1º Distrito
            $grupoB = [1, 10, 3]; // Grupo B - 2º Distrito
            //
            // Determinar a qual grupo pertence a regional da pessoa
            $pessoaRegionalGrupo = in_array($pessoa_regional_5, $grupoA) ? 'A' : (in_array($pessoa_regional_5, $grupoB) ? 'B' : null);

            // Loop para verificar os voluntários em cada raio de 50 em 50 metros até 1.000 metros
            for ($distanciaMetros = $incrementoMetros; $distanciaMetros <= $maxDistanciaMetros; $distanciaMetros += $incrementoMetros) {
                // Converter metros para quilômetros
                $distanciaKm = $distanciaMetros / 1000;

                // Preparar a consulta SQL com a distância atual
                $stmt = $db->prepare("SELECT 
                                        p.id, 
                                        p.nome, 
                                        p.zona, 
                                        l.REGIONAL_ID, 
                                        (6371 * acos(cos(radians(:latSecao)) * cos(radians(l.LATITUDE)) * cos(radians(l.LONGITUDE) - radians(:lonSecao)) + sin(radians(:latSecao)) * sin(radians(l.LATITUDE)))) AS distancia
                                      FROM 
                                        2024_voluntarios AS p
                                      INNER JOIN 
                                        2024_secoes AS s ON p.zona = s.NR_ZONA AND p.secao_numero = s.NR_SECAO 
                                      INNER JOIN 
                                        2024_locais_votacao AS l ON l.ID = s.LOCAL_VOTACAO_ID
                                      WHERE 
                                        p.funcao_id = 5 
                                        AND p.status = 1 
                                        AND (p.secao_numero_2 IS NULL OR p.secao_numero_2 = ' ')
                                      HAVING 
                                        distancia <= :kmDistancia
                                      ORDER BY 
                                          p.deficiencia ASC, p.nascimento ASC, p.nome ASC 
                                      LIMIT 2");

                // Executar a consulta com os valores atuais
                $stmt->execute([
                    'latSecao' => $latitudeSecao,
                    'lonSecao' => $longitudeSecao,
                    'kmDistancia' => $distanciaKm
                ]);

                // Buscar os resultados
                $voluntarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Verificar se há voluntários no raio atual
                if (!empty($voluntarios)) {
                    // Exibir os voluntários encontrados ou realizar alguma ação
                    foreach ($voluntarios as $voluntario) {

                        $regionalVoluntarioGrupo = in_array($voluntario['REGIONAL_ID'], $grupoA) ? 'A' : (in_array($voluntario['REGIONAL_ID'], $grupoB) ? 'B' : null);

                        // Verificar se os grupos são compatíveis
                        if ($pessoaRegionalGrupo !== null && $regionalVoluntarioGrupo !== null && $pessoaRegionalGrupo !== $regionalVoluntarioGrupo) {
                            continue; // Pular voluntários de grupos incompatíveis
                        }

                        $vf_distribuicao2 = qtd_pessoas_sessao($pessoa_zona_5, $pessoa_secao_5, 5);

                        if ($vf_distribuicao2 < 2 && vf_regras_fiscais($pessoa_zona_5, $vf_distribuicao2, $pessoa_local_5)) {

                                $pessoas5 = $voluntario['id'];

//                                if ($vf_distribuicao2 > 0 && qtd_pessoas_local($pessoa_local_5) >= 6) {// 4 - SE TIVER 6 OU MAIS SEÇÕES PRINCIPAIS POR LOCAL PERMITIR 2 PARA CADA SEÇÃO PRINCIPAL
//                                    $nova_sessao = carregar_secao_vazio($pessoa_local_5, 5);
//                                    $pessoa_secao_5 = is_numeric($nova_sessao) ? $nova_sessao : $pessoa_secao_5;
//                                }

                                $sql5 = $db->prepare("UPDATE 2024_voluntarios SET zona_2 = ?, secao_numero_2 = ?, regional_2 = ?, local_votacao_2 = ?, usuario_id_2 = ?, bairro_2 = ?, data_update_2 = NOW() WHERE id = ?");
                                $sql5->bindValue(1, $pessoa_zona_5);
                                $sql5->bindValue(2, $pessoa_secao_5);
                                $sql5->bindValue(3, $pessoa_regional_5);
                                $sql5->bindValue(4, $pessoa_local_5);
                                $sql5->bindValue(5, $_SESSION['id']);
                                $sql5->bindValue(6, $pessoa_bairro_5);
                                $sql5->bindValue(7, $pessoas5);
                                $sql5->execute();
                        }
                    }
                }
            }
        }
    }

    $sql_configuracoes = $db->prepare("UPDATE sys_configuracoes SET fiscal = 1 WHERE id = 1");
    $sql_configuracoes->execute();

    $db->commit();

    //MENSAGEM DE SUCESSO
    $msg['msg'] = 'success';
    $msg['retorno'] = 'Distribuição gerada com sucesso!';
    echo json_encode($msg);
    exit();
} catch (PDOException $e) {
    $db->rollback();
    $msg['msg'] = 'error';
    $msg['retorno'] = "Erro ao tentar gerar a distribuição desejada:" . $e->getMessage();
    echo json_encode($msg);
    exit();
}
?>


