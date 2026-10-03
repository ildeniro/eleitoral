<?php
/**
 * gerar_simulado_2026.php
 * Script utilitário para gerar um CSV simulado de votação por seção para 2026
 * cobrindo os 22 municípios do Acre para testes de importação e exibição no painel
 */

$root_dir = dirname(__DIR__);
require_once $root_dir . '/config/geral.php';

$db = Conexao::getInstance();

$diretorio = __DIR__ . '/downloads_historico/';
if (!is_dir($diretorio)) {
    mkdir($diretorio, 0777, true);
}
$csv_path = $diretorio . 'votacao_secao_2026_AC.csv';

// Cabeçalho padrão do TSE (votacao_secao)
$header = [
    'DT_GERACAO',
    'HH_GERACAO',
    'ANO_ELEICAO',
    'CD_TIPO_ELEICAO',
    'NM_TIPO_ELEICAO',
    'NR_TURNO',
    'CD_ELEICAO',
    'DS_ELEICAO',
    'DT_ELEICAO',
    'TP_ABRANGENCIA',
    'SG_UF',
    'SG_UE',
    'NM_UE',
    'CD_MUNICIPIO',
    'NM_MUNICIPIO',
    'NR_ZONA',
    'NR_SECAO',
    'CD_CARGO',
    'DS_CARGO',
    'NR_VOTAVEL',
    'NM_VOTAVEL',
    'QT_VOTOS',
    'NR_LOCAL_VOTACAO',
    'SQ_CANDIDATO'
];

$handle = fopen($csv_path, 'w');
fputcsv($handle, $header, ';');

// Busca seções dos 22 municípios do Acre a partir de locais_votacao_2026
$sql = "
    SELECT lv.nr_zona, lv.nr_secao, lv.nr_local_votacao, lv.municipio_id, lv.nm_municipio, m.cod_tse
    FROM locais_votacao_2026 lv
    INNER JOIN bsc_municipios m ON m.id = lv.municipio_id
    WHERE m.estado_id = 1
    ORDER BY lv.municipio_id, lv.nr_zona, lv.nr_secao ASC
";
$secoes = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

echo "Gerando dados simulados para " . count($secoes) . " seções do Acre...\n";

// Candidatos a Governador do Acre 2026 (CD_CARGO = 3)
$cands_gov = [
    ['11', 'MAILZA ASSIS', 12000010011, 88],   // Peso proporcional da intenção de votos
    ['10', 'ALAN RICK', 12000010010, 82],
    ['22', 'TIÃO BOCALOM', 12000010022, 52],
    ['40', 'THOR DANTAS', 12000010040, 35],
    ['36', 'DR. LUISINHO', 12000010036, 12],
    ['21', 'EUDO RAFFAEL', 12000010021, 6],
    ['95', 'BRANCO', null, 9],
    ['96', 'NULO', null, 14]
];

// Candidatos a Senador do Acre 2026 (CD_CARGO = 5) - Eleição de 2 Vagas
$cands_sen = [
    ['111', 'GLADSON CAMELI', 12000010111, 95],
    ['222', 'MARCIO BITTAR', 12000010222, 75],
    ['131', 'JORGE VIANA', 12000010131, 68],
    ['100', 'ROBERTO DUARTE', 12000010100, 48],
    ['151', 'JÉSSICA SALES', 12000010151, 42],
    ['95', 'BRANCO', null, 12],
    ['96', 'NULO', null, 16]
];

$linhas_geradas = 0;

// Gera para uma amostra expressiva de seções (ex: 200 seções distribuídas entre todos os municípios para teste rápido e realista)
$secoes_amostra = array_filter($secoes, function($idx) {
    return ($idx % 5 === 0); // ~480 seções (20% das urnas apuradas no estado)
}, ARRAY_FILTER_USE_KEY);

foreach ($secoes_amostra as $s) {
    $cd_muni = sprintf('%05d', (int)$s['cod_tse']);
    $nm_muni = $s['nm_municipio'];
    $zona    = (int)$s['nr_zona'];
    $secao   = (int)$s['nr_secao'];
    $local   = (int)$s['nr_local_votacao'];

    // Votação para Governador
    foreach ($cands_gov as $cg) {
        $nr_votavel = $cg[0];
        $nm_votavel = $cg[1];
        $sq_cand    = $cg[2];
        $base_votos = $cg[3];
        $votos = max(1, (int)($base_votos * (rand(70, 130) / 100)));

        $row = [
            '22/09/2026',
            '17:00:00',
            2026,
            2,
            'ELEICAO ORDINARIA',
            1,
            999,
            'ELEICOES GERAIS 2026',
            '04/10/2026',
            'ESTADUAL',
            'AC',
            'AC',
            'ACRE',
            $cd_muni,
            $nm_muni,
            $zona,
            $secao,
            3,
            'GOVERNADOR',
            $nr_votavel,
            $nm_votavel,
            $votos,
            $local,
            $sq_cand
        ];
        fputcsv($handle, $row, ';');
        $linhas_geradas++;
    }

    // Votação para Senador
    foreach ($cands_sen as $cs) {
        $nr_votavel = $cs[0];
        $nm_votavel = $cs[1];
        $sq_cand    = $cs[2];
        $base_votos = $cs[3];
        $votos = max(1, (int)($base_votos * (rand(70, 130) / 100)));

        $row = [
            '22/09/2026',
            '17:00:00',
            2026,
            2,
            'ELEICAO ORDINARIA',
            1,
            999,
            'ELEICOES GERAIS 2026',
            '04/10/2026',
            'ESTADUAL',
            'AC',
            'AC',
            'ACRE',
            $cd_muni,
            $nm_muni,
            $zona,
            $secao,
            5,
            'SENADOR',
            $nr_votavel,
            $nm_votavel,
            $votos,
            $local,
            $sq_cand
        ];
        fputcsv($handle, $row, ';');
        $linhas_geradas++;
    }
}

fclose($handle);

echo "Arquivo CSV simulado gerado com sucesso em:\n$csv_path\n";
echo "Total de linhas geradas: $linhas_geradas\n";
