<?php

$output_file_1 = 'C:\xampp8\htdocs\siseleitoral\sn1\downloads\\';

// Verifica se o diretório existe
if (is_dir($output_file_1)) {
    // Abre o diretório
    if ($handle = opendir($output_file_1)) {
        // Itera sobre cada item no diretório
        while (false !== ($file = readdir($handle))) {
            // Verifica se o arquivo tem a extensão .json
            if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {

                $output_file = 'C:\xampp8\htdocs\siseleitoral\sn1\downloads\\' . $file . '';

                if (file_exists($output_file)) {
                    // Lendo o arquivo JSON
                    $json_data = file_get_contents($output_file);
                    $data = json_decode($json_data, true);

                    // Percorre cada eleição dentro de 'resultadosVotacaoPorEleicao'
                    foreach ($data['EntidadeBoletimUrna']['resultadosVotacaoPorEleicao'] as $eleicao) {
                        // Extrai os dados comuns
                        $municipio = $data['EntidadeBoletimUrna']['identificacaoSecao']['municipioZona']['municipio'];
                        $zona = $data['EntidadeBoletimUrna']['identificacaoSecao']['municipioZona']['zona'];
                        $secao = $data['EntidadeBoletimUrna']['identificacaoSecao']['secao'];
                        $eleitores_aptos = $eleicao['qtdEleitoresAptos'];
                        $local = $data['EntidadeBoletimUrna']['identificacaoSecao']['local'];

                        // Percorre cada resultado de votação
                        foreach ($eleicao['resultadosVotacao'] as $resultadoVotacao) {

                            $tipoCargo = $resultadoVotacao['tipoCargo'];
                            $qtdComparecimento = $resultadoVotacao['qtdComparecimento'];

                            foreach ($resultadoVotacao['totaisVotosCargo'] as $cargo) {

                                $codigoCargo = "";

                                foreach ($cargo['codigoCargo'] as $cargos) {
                                    $codigoCargo = $cargos;
                                }

                                if (in_array($codigoCargo, ["vereador"])) {
                                    // Percorre os votos de cada cargo
                                    foreach ($cargo['votosVotaveis'] as $voto) {
                                        $tipo = $voto['tipoVoto'];
                                        $quantidade_votos = $voto['quantidadeVotos'];

                                        // Identifica candidato ou trata votos brancos e nulos
                                        if ($tipo == "nominal") {
                                            $candidato = isset($voto['identificacaoVotavel']['codigo']) ? $voto['identificacaoVotavel']['codigo'] : "Não encontrado";
                                            $partido = isset($voto['identificacaoVotavel']['partido']) ? $voto['identificacaoVotavel']['partido'] : "Não encontrado";
                                        } else {
                                            $candidato = strtoupper($tipo); // Para "BRANCO" ou "NULO"
                                            $partido = null; // Não há partido para votos brancos ou nulos
                                        }

                                        // Verifica e insere os resultados no banco de dados
                                        if (!is_numeric(pesquisar5("ID", "2024_resultados", "ZONA", "=", $zona, "SECAO", "=", $secao, "NUM_CANDIDATO", "=", $candidato, "TIPO_VOTO", "=", $tipo, "COD_CARGO", "=", $codigoCargo, ""))) {
                                            $result2 = $db->prepare("INSERT INTO 2024_resultados (ZONA, SECAO, LOCAL_VOTACAO, NUM_CANDIDATO, COD_CARGO, PARTIDO, ANO_ELEICAO, COD_MUNICIPIO_TSE, TIPO_VOTO, QTD_VOTOS, TURNO, UF, DATA_CADASTRO) 
                                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'AC', NOW())");
                                            $result2->bindValue(1, $zona);
                                            $result2->bindValue(2, $secao);
                                            $result2->bindValue(3, $local);
                                            $result2->bindValue(4, $candidato);
                                            $result2->bindValue(5, $codigoCargo);
                                            $result2->bindValue(6, $partido);
                                            $result2->bindValue(7, "2024");
                                            $result2->bindValue(8, "01392"); // Rio Branco
                                            $result2->bindValue(9, $tipo);
                                            $result2->bindValue(10, $quantidade_votos);
                                            $result2->bindValue(11, "1");
                                            $result2->execute();
                                        }
                                    }
                                }
                                //}
                            }
                        }
                    }
                } else {
                    echo "Erro ao gerar o arquivo JSON.";
                }
            }
        }
    }
}
?>
