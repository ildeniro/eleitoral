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
//SEGUNDA FASE DA INSERÇÃO
//POR ZONA, LOCAL, BAIRRO, REGIONAL
//------------------------------------------------------------------------------

    $result2 = $db->prepare("SELECT e.NR_ZONA AS ZONA, s.NR_SECAO, s.LOCAL_VOTACAO_ID, e.CD_BAIRRO, e.REGIONAL_ID       
                             FROM 2024_locais_votacao e
                             INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = e.ID 
                             WHERE e.MUNICIPIO_ID = 94 AND s.TIPO = 'Principal' AND 
                             (SELECT COUNT(id) AS QTD FROM 2024_voluntarios WHERE funcao_id = 4 AND local_votacao_2 = e.ID) < 1 
                             GROUP BY e.ID");
    $result2->execute();
    while ($sessoes2 = $result2->fetch(PDO::FETCH_ASSOC)) {

        $pessoa_zona_2 = $sessoes2['ZONA'];
        $pessoa_local_2 = $sessoes2['LOCAL_VOTACAO_ID'];
        $pessoa_regional_2 = $sessoes2['REGIONAL_ID'];
        $pessoa_bairro_2 = $sessoes2['CD_BAIRRO'];
        $pessoa_secao_2 = $sessoes2['NR_SECAO'];

        $resultado_qtd = is_numeric(pesquisar("id", "2024_voluntarios", "local_votacao_2", "=", $pessoa_local_2, "AND funcao_id = 4")) ? 1 : 0;

        $result22 = $db->prepare("SELECT v.MUNICIPIO_ID, p.secao_numero, p.bsc_bairros_id, p.zona, v.NM_LOCAL_VOTACAO AS local_votacao, p.id, p.nome     
                                  FROM 2024_voluntarios p
                                  INNER JOIN 2024_secoes AS s ON s.NR_ZONA = p.zona AND s.NR_SECAO = p.secao_numero 
                                  INNER JOIN 2024_locais_votacao AS v ON v.ID = s.LOCAL_VOTACAO_ID 
                                  WHERE p.deficiencia IN(3, 2) AND p.funcao_id = 4 AND p.secao_numero_2 IS NULL AND p.status = 1 AND s.LOCAL_VOTACAO_ID = ? OR
                                  p.deficiencia IN(3, 2) AND p.funcao_id = 4 AND p.secao_numero_2 = ' ' AND p.status = 1 AND s.LOCAL_VOTACAO_ID = ?    
                                  ORDER BY p.deficiencia ASC, p.nascimento ASC, p.nome ASC
                                  LIMIT 1");
        $result22->bindValue(1, $pessoa_local_2);
        $result22->bindValue(2, $pessoa_local_2);
        $result22->execute();

        while ($pessoa2 = $result22->fetch(PDO::FETCH_ASSOC)) {//POR SESSAO, LOCAL, BAIRRO, REGIONAL
            
            if ($resultado_qtd == 0) {

                $pessoas2 = $pessoa2['id'];

                $sql2 = $db->prepare("UPDATE 2024_voluntarios SET zona_2 = ?, secao_numero_2 = ?, regional_2 = ?, local_votacao_2 = ?, usuario_id_2 = ?, bairro_2 = ?, data_update_2 = NOW() WHERE id = ?");
                $sql2->bindValue(1, $pessoa_zona_2);
                $sql2->bindValue(2, $pessoa_secao_2);
                $sql2->bindValue(3, $pessoa_regional_2);
                $sql2->bindValue(4, $pessoa_local_2);
                $sql2->bindValue(5, $_SESSION['id']);
                $sql2->bindValue(6, $pessoa_bairro_2);
                $sql2->bindValue(7, $pessoas2);
                $sql2->execute();
            }
        }
    }
    
////------------------------------------------------------------------------------
////QUARTA FASE DA INSERÇÃO
////POR LATITUDE E LONGITUDE
////------------------------------------------------------------------------------
    $result5 = $db->prepare("SELECT e.LATITUDE, e.LONGITUDE, s.NR_ZONA, s.NR_SECAO, s.LOCAL_VOTACAO_ID, e.CD_BAIRRO, e.REGIONAL_ID       
                             FROM 2024_locais_votacao e
                             INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = e.ID 
                             WHERE e.MUNICIPIO_ID = 94 AND s.TIPO = 'Principal' AND 
                             (SELECT COUNT(id) AS QTD FROM 2024_voluntarios WHERE funcao_id = 4 AND local_votacao_2 = e.ID) < 1 
                             GROUP BY e.ID");
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

        //if (vf_regra_sessao($pessoa_zona_5, $pessoa_local_5)) {
        if (qtd_pessoas_local_2024($pessoa_local_5, 4) < 1) {//VERIFICA SE AINDA TEM VAGA NA ZONA E SEÇÃO
            $maxDistanciaMetros = 1600; // Limite máximo de 1.000 metros
            $incrementoMetros = 200; // Incremento de 50 metros
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
                                        p.funcao_id = 4 AND p.status = 1 AND p.deficiencia IN(3, 2) 
                                      HAVING 
                                        distancia <= :kmDistancia
                                      ORDER BY 
                                        distancia ASC
                                      LIMIT 1");

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

                            $pessoas5 = $voluntario['id'];

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
        //}
    }

    $sql_configuracoes = $db->prepare("UPDATE sys_configuracoes SET supervisor = 1 WHERE id = 1");
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


