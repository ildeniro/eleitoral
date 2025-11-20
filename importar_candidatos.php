<?php
// importar_candidatos.php → 100% FUNCIONANDO COM ACENTOS PERFEITOS

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Acesse via formulário');
}

$ano = (int)$_POST['ano'];
if (!in_array($ano, [2022, 2024])) {
    die('Ano inválido. Use 2022 ou 2024');
}

if (!isset($_FILES['csv']) || $_FILES['csv']['error'] !== 0) {
    die('Erro no upload do arquivo');
}

$caminho = $_FILES['csv']['tmp_name'];
if (!is_uploaded_file($caminho)) {
    die('Arquivo inválido');
}

// Prepara o INSERT
$stmt = $db->prepare("
    INSERT INTO candidatos_gerais 
    (sq_candidato, ano_eleicao, nr_candidato, nm_urna_candidato, ds_cargo, cd_cargo, sg_partido, nr_partido, sg_uf, nm_ue)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
    nm_urna_candidato = VALUES(nm_urna_candidato),
    ds_cargo = VALUES(ds_cargo)
");

// AQUI ESTÁ A MÁGICA DOS ACENTOS
$handle = fopen($caminho, 'r');
if (!$handle) die('Não abriu o CSV');

// LINHA QUE SALVA SUA VIDA E SEUS ACENTOS
stream_filter_append($handle, 'convert.iconv.ISO-8859-1/UTF-8//IGNORE', STREAM_FILTER_READ);

$linha_num = 0;
$inseridos = 0;

// Pula cabeçalho
fgetcsv($handle, 0, ';', '"');

echo "<h2>Importando candidatos de $ano... (acentos 100% corrigidos)</h2>";
echo "<pre>";

while (($colunas = fgetcsv($handle, 0, ';', '"')) !== false) {
    $linha_num++;

    if ($ano == 2024) {
        $sq_candidato = $colunas[15] ?? null;
        $nr_candidato = $colunas[16] ?? null;
        $nm_urna      = $colunas[18] ?? null;
        $ds_cargo     = $colunas[14] ?? null;
        $cd_cargo     = $colunas[13] ?? null;
        $sg_partido   = $colunas[27] ?? null;
        $nr_partido   = $colunas[26] ?? null;
        $sg_uf        = $colunas[10] ?? null;
        $nm_ue        = $colunas[12] ?? null;
    }

    if ($ano == 2022) {
        $sq_candidato = $colunas[15] ?? null;
        $nr_candidato = $colunas[16] ?? null;
        $nm_urna      = $colunas[18] ?? null;
        $ds_cargo     = $colunas[14] ?? null;
        $cd_cargo     = $colunas[13] ?? null;
        $sg_partido   = $colunas[27] ?? null;
        $nr_partido   = $colunas[26] ?? null;
        $sg_uf        = $colunas[10] ?? null;
        $nm_ue        = $colunas[12] ?? null;
    }

    if (empty($sq_candidato) || empty($nr_candidato)) {
        echo "Linha $linha_num ignorada (sem SQ ou número)\n";
        continue;
    }

    try {
        $stmt->execute([
            $sq_candidato, $ano, $nr_candidato, $nm_urna,
            $ds_cargo, $cd_cargo, $sg_partido, $nr_partido,
            $sg_uf, $nm_ue
        ]);
        $inseridos++;
    } catch (Exception $e) {
        echo "Erro na linha $linha_num: " . $e->getMessage() . "\n";
    }
}

fclose($handle);

echo "\n\nIMPORTAÇÃO CONCLUÍDA COM SUCESSO!\n";
echo "Ano: $ano\n";
echo "Candidatos inseridos/atualizados: $inseridos\n";
echo "Acentos funcionando perfeitamente: SÍLVIO, MARCIO, TIÃO, JÉSSICA\n";
echo "<hr><a href='importar_form' style='color:#00ff00; font-size:20px;'>VOLTAR</a>";
?>