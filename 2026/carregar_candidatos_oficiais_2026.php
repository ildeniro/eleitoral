<?php
/**
 * carregar_candidatos_oficiais_2026.php
 * Sincronização dos Candidatos Oficiais de 2026 do TSE
 * Lê diretamente dos arquivos oficiais de divulgação da CDN do TSE (Fase Oficial)
 * e substitui qualquer massa de teste/mock pelo cadastro 100% oficial.
 */

$root_dir = dirname(__DIR__);
require_once $root_dir . '/config/geral.php';
require_once __DIR__ . '/config_apuracao_2026.php';

$db = Conexao::getInstance();
$config = carregar_config_apuracao_2026();

echo "=========================================================\n";
echo "🏛️ SINCRONIZAÇÃO OFICIAL DE CANDIDATOS TSE 2026\n";
echo "=========================================================\n\n";

// 1. Zera dados de apuração e resultados do simulado
echo "1. Limpando tabelas de resultados e estatísticas...\n";
$db->exec("TRUNCATE TABLE `2026_resultados`");
$db->exec("UPDATE `2026_apuracao_municipios` SET `SECOES_APURADAS` = 0, `PORC_APURADA` = 0.00, `DATA_ATUALIZACAO` = NOW()");
echo "   ✔ 2026_resultados zerada!\n";
echo "   ✔ 2026_apuracao_municipios resetada!\n\n";

// 2. Limpa candidatos mock/simulado
echo "2. Limpando candidatos antigos e testes de simulado em 2026_candidatos...\n";
$db->exec("TRUNCATE TABLE `2026_candidatos`");
echo "   ✔ Tabela 2026_candidatos esvaziada para receber a lista oficial.\n\n";

// 3. Arquivos oficiais na CDN do TSE (Eleições 6257 e 6259)
$fontes_tse = [
    [
        'cargo_cd' => 1,
        'cargo_nome' => 'PRESIDENTE',
        'sg_ue' => 'BR',
        'nm_ue' => 'BRASIL',
        'url' => 'https://resultados.tse.jus.br/oficial/ele2026/6257/dados/br/br-c0001-e006257-u.jws'
    ],
    [
        'cargo_cd' => 3,
        'cargo_nome' => 'GOVERNADOR',
        'sg_ue' => 'AC',
        'nm_ue' => 'ACRE',
        'url' => 'https://resultados.tse.jus.br/oficial/ele2026/6259/dados/ac/ac-c0003-e006259-u.jws'
    ],
    [
        'cargo_cd' => 5,
        'cargo_nome' => 'SENADOR',
        'sg_ue' => 'AC',
        'nm_ue' => 'ACRE',
        'url' => 'https://resultados.tse.jus.br/oficial/ele2026/6259/dados/ac/ac-c0005-e006259-u.jws'
    ],
    [
        'cargo_cd' => 6,
        'cargo_nome' => 'DEPUTADO FEDERAL',
        'sg_ue' => 'AC',
        'nm_ue' => 'ACRE',
        'url' => 'https://resultados.tse.jus.br/oficial/ele2026/6259/dados/ac/ac-c0006-e006259-u.jws'
    ],
    [
        'cargo_cd' => 7,
        'cargo_nome' => 'DEPUTADO ESTADUAL',
        'sg_ue' => 'AC',
        'nm_ue' => 'ACRE',
        'url' => 'https://resultados.tse.jus.br/oficial/ele2026/6259/dados/ac/ac-c0007-e006259-u.jws'
    ]
];

$stmtCand = $db->prepare("
    INSERT INTO 2026_candidatos 
        (ANO_ELEICAO, NR_TURNO, SG_UF, SG_UE, NM_UE, CD_CARGO, DS_CARGO, SQ_CANDIDATO, NR_CANDIDATO, NM_CANDIDATO, NM_URNA_CANDIDATO, SG_PARTIDO, NM_PARTIDO, NM_COLIGACAO, STATUS)
    VALUES 
        (2026, 1, 'AC', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
    ON DUPLICATE KEY UPDATE 
        NM_URNA_CANDIDATO = VALUES(NM_URNA_CANDIDATO),
        NM_CANDIDATO = VALUES(NM_CANDIDATO),
        SG_PARTIDO = VALUES(SG_PARTIDO),
        NM_PARTIDO = VALUES(NM_PARTIDO),
        NM_COLIGACAO = VALUES(NM_COLIGACAO)
");

echo "3. Baixando e importando candidatos oficiais diretamente da CDN do TSE...\n";

$total_candidatos_geral = 0;

foreach ($fontes_tse as $f) {
    echo "   Baixando: " . $f['cargo_nome'] . " (" . $f['url'] . ")...\n";
    $ch = curl_init($f['url']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) EleitoralBot/2026');
    $content = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http != 200 || empty($content)) {
        echo "   ❌ Erro ao baixar arquivo HTTP {$http}\n";
        continue;
    }

    $parts = explode('.', $content);
    if (count($parts) < 2) {
        echo "   ❌ Formato JWS inválido\n";
        continue;
    }

    $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
    if (!$payload || !isset($payload['carg'][0]['agr'])) {
        echo "   ❌ Payload sem dados de coligações/candidatos\n";
        continue;
    }

    $inseridos_cargo = 0;

    foreach ($payload['carg'][0]['agr'] as $agr) {
        $nm_coligacao = $agr['nm'] ?? '';
        foreach ($agr['par'] as $par) {
            $sg_partido = $par['sg'] ?? 'PARTIDO';
            $nm_partido = $par['nm'] ?? $sg_partido;

            foreach ($par['cand'] as $cand) {
                $num = (int)($cand['n'] ?? 0);
                $nm_urna = (string)($cand['nmu'] ?? $cand['nm'] ?? "CANDIDATO {$num}");
                $nm_cand = (string)($cand['nm'] ?? $nm_urna);
                $sq_cand = isset($cand['sqcand']) ? (int)$cand['sqcand'] : null;

                $stmtCand->execute([
                    $f['sg_ue'],
                    $f['nm_ue'],
                    $f['cargo_cd'],
                    $f['cargo_nome'],
                    $sq_cand,
                    $num,
                    $nm_cand,
                    $nm_urna,
                    $sg_partido,
                    $nm_partido,
                    $nm_coligacao
                ]);

                $inseridos_cargo++;
                $total_candidatos_geral++;
            }
        }
    }

    echo "   ✔ {$inseridos_cargo} candidatos oficiais cadastrados para " . $f['cargo_nome'] . "!\n";
}

echo "\n=========================================================\n";
echo "🎉 CONCLUÍDO COM SUCESSO!\n";
echo "Total de Candidatos Oficiais Gravados: {$total_candidatos_geral}\n";
echo "Tabelas de Resultados: 0 votos (prontas para domingo)\n";
echo "=========================================================\n";
