<?php
// leitura_historica.php - Processa BU históricos e insere em resultados_eleitorais
if ($argc < 4) die("Uso: php leitura_historica.php <ANO> <TURNO> <DIRETORIO>\n");

$ano = $argv[1];
$turno = $argv[2];
$diretorio = $argv[3];
require_once 'config.php'; // $db PDO

$files = glob($diretorio . '*.json');
foreach ($files as $file) {
    $json_data = file_get_contents($file);
    $data = json_decode($json_data, true);
    if (!$data) continue;

    $bu = $data['EntidadeBoletimUrna'];
    $idSecao = $bu['identificacaoSecao'];
    $nr_zona = $idSecao['municipioZona']['zona'];
    $nr_secao = $idSecao['secao'];
    $nr_local = $idSecao['local']; // NR_LOCAL_VOTACAO

    // Garantir local na base mestra (2024 como referência)
    garantirLocalMestre($db, $nr_local, $nr_zona, $bu);

    foreach ($bu['resultadosVotacaoPorEleicao'] as $eleicao) {
        foreach ($eleicao['resultadosVotacao'] as $resultado) {
            foreach ($resultado['totaisVotosCargo'] as $cargo) {
                $cod_cargo = $cargo['codigoCargo'][0] ?? null; // Ex.: 'vereador'
                if ($cod_cargo && in_array($cod_cargo, ['vereador', 'prefeito'])) { // Filtre cargos relevantes
                    foreach ($cargo['votosVotaveis'] as $voto) {
                        $tipo = $voto['tipoVoto'];
                        $qtd = $voto['quantidadeVotos'];
                        $sq_candidato = $num_candidato = $partido = null;

                        if ($tipo === 'nominal') {
                            $cand = $voto['identificacaoVotavel'];
                            $sq_candidato = $cand['codigo']; // SQ_CANDIDATO
                            $num_candidato = $cand['codigo'];
                            $partido = $cand['partido'] ?? null;
                        } else {
                            $sq_candidato = strtoupper($tipo); // branco/nulo
                        }

                        // INSERT otimizado em resultados_eleitorais
                        $stmt = $db->prepare("
                            INSERT INTO resultados_eleitorais 
                                (ano, turno, nr_zona, nr_secao, nr_local_votacao, sq_candidato, num_candidato, cod_cargo, partido, tipo_voto, qtd_votos)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE qtd_votos = VALUES(qtd_votos)
                        ");
                        $stmt->execute([
                            $ano, $turno, $nr_zona, $nr_secao, $nr_local,
                            $sq_candidato, $num_candidato, $cod_cargo, $partido, $tipo, $qtd
                        ]);
                    }
                }
            }
        }
    }
    unlink($file); // Limpa JSON processado
}

function garantirLocalMestre($db, $nr_local, $nr_zona, $bu) {
    $stmt = $db->prepare("
        SELECT id FROM locais_votacao_mestre 
        WHERE nr_local_votacao = ? AND nr_zona = ?
    ");
    $stmt->execute([$nr_local, $nr_zona]);
    if (!$stmt->fetch()) {
        // Insert com dados do BU (aproxima geolocalização se não existir)
        $ins = $db->prepare("
            INSERT INTO locais_votacao_mestre 
                (nr_local_votacao, nr_zona, nm_local_votacao, ds_endereco, latitude, longitude, municipio_id)
            VALUES (?, ?, ?, ?, ?, ?, 94)
        ");
        $ins->execute([
            $nr_local,
            $nr_zona,
            $bu['identificacaoSecao']['descricaoLocal'] ?? 'Local histórico',
            $bu['identificacaoSecao']['endereco'] ?? null,
            $bu['identificacaoSecao']['latitude'] ?? null,
            $bu['identificacaoSecao']['longitude'] ?? null
        ]);
        echo "Novo local histórico adicionado: Z{$nr_zona} L{$nr_local}\n";
    }
}
?>