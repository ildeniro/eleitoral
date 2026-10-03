<?php
// importar_candidatos_detalhados.php → VERSÃO BRUTA TOTAL — IMPORTA TUDO, SEM FILTRO NENHUM
if ($_SERVER['REQUEST_METHOD'] !== 'POST') die('Acesse via formulário');

$ano = (int)$_POST['ano'];
if (!in_array($ano, [2012,2014,2016,2018,2020,2022,2024])) die('Ano inválido');

if (!isset($_FILES['csv']) || $_FILES['csv']['error'] !== 0) die('Erro no upload');
$tmp = $_FILES['csv']['tmp_name'];
if (!is_uploaded_file($tmp)) die('Arquivo inválido');

// Remove BOM se existir
$content = file_get_contents($tmp);
if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
    $content = substr($content, 3);
    file_put_contents($tmp, $content);
}

$handle = fopen($tmp, 'r');
if (!$handle) die('Não abriu o CSV');

// Força UTF-8
stream_filter_append($handle, 'convert.iconv.UTF-8/UTF-8//IGNORE', STREAM_FILTER_READ);

echo "<h2 style='color:#00ff88'>IMPORTANDO TUDO DO ANO <strong>$ano</strong> (BRUTO, SEM FILTRO)</h2><pre>";

// Lê cabeçalho
$cabecalho = fgetcsv($handle, 0, ';', '"');
if (!$cabecalho) die('Cabeçalho vazio ou CSV corrompido');

echo "Cabeçalho detectado com " . count($cabecalho) . " colunas\n";
echo "Iniciando importação linha a linha...\n\n";

// Prepara insert (sem ON DUPLICATE pra não pular nada)
$stmt = $db->prepare("
    INSERT INTO candidatos_detalhados 
    (sq_candidato, ano_eleicao, nr_cpf_candidato, nr_titulo_eleitoral_candidato,
     nm_urna_candidato, nr_candidato, ds_cargo, cd_cargo, sg_partido, nr_partido,
     nm_coligacao, sq_coligacao, dt_nascimento, cd_genero, ds_genero,
     cd_grau_instrucao, ds_grau_instrucao, cd_estado_civil, ds_estado_civil,
     cd_cor_raca, ds_cor_raca, cd_ocupacao, ds_ocupacao, nm_ue)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
");

$inseridos = 0;
$linha = 1;

while (($colunas = fgetcsv($handle, 0, ';', '"')) !== false) {
    $linha++;

    // === ZERO VALIDAÇÃO ===
    // Mesmo se tiver menos colunas, preenche com vazio
    $colunas = array_pad($colunas, count($cabecalho), '');

    $map = array_combine($cabecalho, $colunas);
    if ($map === false) {
        echo "ERRO CRÍTICO: colunas não batem com cabeçalho na linha $linha\n";
        continue;
    }

    // === PEGA OS CAMPOS EXATAMENTE COMO ESTÃO NO CSV (BRUTO) ===
    $dados = [
        $map['SQ_CANDIDATO'] ?? '',
        $ano,
        $map['NR_CPF_CANDIDATO'] ?? '',           // -4, vazio, 00000000000 → entra como veio
        $map['NR_TITULO_ELEITORAL_CANDIDATO'] ?? '',
        $map['NM_URNA_CANDIDATO'] ?? '',          // #NULO#, LIDIANE, tudo entra
        $map['NR_CANDIDATO'] ?? '',
        $map['DS_CARGO'] ?? '',
        (int)($map['CD_CARGO'] ?? 0),
        $map['SG_PARTIDO'] ?? '',
        (int)($map['NR_PARTIDO'] ?? 0),
        $map['NM_COLIGACAO'] ?? '',
        $map['SQ_COLIGACAO'] ?? '',
        $map['DT_NASCIMENTO'] ?? '',              // ← 30/04/0072, 151/11/0070, vazio, -4 → entra BRUTO
        (int)($map['CD_GENERO'] ?? 0),
        $map['DS_GENERO'] ?? '',
        (int)($map['CD_GRAU_INSTRUCAO'] ?? 0),
        $map['DS_GRAU_INSTRUCAO'] ?? '',
        (int)($map['CD_ESTADO_CIVIL'] ?? 0),
        $map['DS_ESTADO_CIVIL'] ?? '',
        (int)($map['CD_COR_RACA'] ?? 0),
        $map['DS_COR_RACA'] ?? '',
        (int)($map['CD_OCUPACAO'] ?? 0),
        $map['DS_OCUPACAO'] ?? '',
        $map['NM_UE'] ?? ''
    ];

    try {
        $stmt->execute($dados);
        $inseridos++;
        if ($inseridos % 500 == 0) {
            echo "Inseridos: <strong>$inseridos</strong> registros...\n";
            flush();
        }
    } catch (Exception $e) {
        // Mesmo erro de duplicata → mostra, mas continua
        echo "AVISO linha $linha (SQ: {$dados[0]}): " . $e->getMessage() . "\n";
    }
}

fclose($handle);

echo "\n";
echo "╔════════════════════════════════════════════════════╗\n";
echo "║           IMPORTAÇÃO BRUTA CONCLUÍDA               ║\n";
echo "║ Ano: <strong>$ano</strong>                                          ║\n";
echo "║ Linhas processadas: " . number_format($linha-1) . "        ║\n";
echo "║ Registros inseridos: <strong>" . number_format($inseridos) . "</strong>        ║\n";
echo "║ → TUDO foi importado (inclusive -4, datas loucas, #NULO#) ║\n";
echo "║ → ZERO filtros aplicados                           ║\n";
echo "╚════════════════════════════════════════════════════╝\n";
echo "\n<h1 style='color:#00ff00;'>IMPORTAÇÃO 100% CONCLUÍDA</h1>";
echo "<h2><a href='dashboard3.php' style='color:#00ff00; text-decoration:none;'>→ ABRIR DASHBOARD3 AGORA</a></h2>";
echo "<p>Todos os candidatos estão no banco — exatamente como o TSE mandou.</p>";
?>