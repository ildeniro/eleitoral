<?php

// Caminhos dos arquivos
$output_file = 'C:\\xampp8\\htdocs\\siseleitoral\\sn1\\json\\bu_output.json';
$pythonScript = escapeshellarg("C:\\xampp8\\htdocs\\siseleitoral\\sn1\\json\\bu_dump.py");
$asnFile = escapeshellarg("C:\\xampp8\\htdocs\\siseleitoral\\sn1\\python\\spec\\bu.asn1");

// Carregando todos os arquivos .dat na pasta downloads
$arquivos = glob("C:\\xampp8\\htdocs\\siseleitoral\\sn1\\downloads\\*.dat");

foreach ($arquivos as $arquivo) {
    // Extraindo o nome do arquivo
    $nome_arquivo = basename($arquivo);

    // Montando o comando
    $command = "python $pythonScript -a $asnFile -b \"" . $arquivo . "\" 2>&1";
    
    // Executando o comando
    $output = shell_exec($command);

    // Verificando se o arquivo JSON foi criado
    if (file_exists($output_file)) {
        // Mudando nome do arquivo JSON para o nome do bu
        $new_file_name = str_replace(".dat", ".json", $arquivo);
        rename($output_file, $new_file_name);
    } else {
        echo "Erro ao gerar o arquivo JSON para $nome_arquivo.<br>";
        echo "Saída do comando: $output<br>"; // Mostrando a saída do comando para diagnóstico
        exit(); // Encerra o script se ocorrer um erro
    }
}
?>