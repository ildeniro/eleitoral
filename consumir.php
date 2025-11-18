<?php

// URL do arquivo JSON
$eleicao = "ele2024";
$pleito = "452";
$estado_sigla = "ac";
$cd_municipio = "01392"; // Rio Branco
$qtd_bu = 0;
$ambiente = "oficial";
$url = "https://resultados.tse.jus.br";
$max_requests_per_second = 100; // Limite de requisições por segundo
$start_time = microtime(true); // Marca o tempo inicial
$requests = 0; // Contador de requisições

$result2 = $db->prepare("SELECT numero  
                         FROM sys_zonas     
                         WHERE id IN(1,9)    
                         ORDER BY numero ASC");
$result2->execute();
while ($zona = $result2->fetch(PDO::FETCH_ASSOC)) {
    $result3 = $db->prepare("SELECT s.NR_SECAO    
                             FROM 2024_secoes AS s  
                             INNER JOIN 2024_locais_votacao AS v ON v.ID = s.LOCAL_VOTACAO_ID 
                             WHERE s.NR_ZONA = ? AND v.MUNICIPIO_ID = 94  
                             ORDER BY s.NR_SECAO ASC");
    $result3->bindValue(1, $zona['numero']);
    $result3->execute();
    while ($secao = $result3->fetch(PDO::FETCH_ASSOC)) {

        // Controle de limite de requisições por segundo
        $requests++;
        $elapsed_time = microtime(true) - $start_time;

        if ($elapsed_time < 1 && $requests > $max_requests_per_second) {
            // Se mais de 100 requisições ocorrerem em menos de 1 segundo, aguardar o tempo restante
            usleep((1 - $elapsed_time) * 1000000); // Converte para microsegundos
            $start_time = microtime(true); // Reinicia o tempo
            $requests = 0;
        } elseif ($elapsed_time >= 1) {
            // Reinicia o contador se mais de 1 segundo se passou
            $start_time = microtime(true);
            $requests = 0;
        }

        $json_url_2 = $url . '/' . $ambiente . '/' . $eleicao . '/arquivo-urna/' . $pleito . '/dados/' . $estado_sigla . '/' . $cd_municipio . '/' . formatarNumero($zona['numero']) . '/' . formatarNumero($secao['NR_SECAO']) . '/p000' . $pleito . '-' . $estado_sigla . '-m' . $cd_municipio . '-z' . formatarNumero($zona['numero']) . '-s' . formatarNumero($secao['NR_SECAO']) . '-aux.json';

        // Verificação se o link é válido
        $headers = @get_headers($json_url_2); // Supressão de erro com @ para não exibir warning
        if ($headers && strpos($headers[0], '200') !== false) {
            // O link é válido, continua com a obtenção do JSON
            $json_data_2 = file_get_contents($json_url_2);

            if ($json_data_2 === false) {
                die('Erro ao obter o JSON.');
            }

            $data_2 = json_decode($json_data_2, true);

            if ($data_2 === null) {
                die('Erro ao decodificar o JSON: ' . json_last_error_msg());
            }

            foreach ($data_2['hashes'] as $hash) {
                foreach ($hash['arq'] as $arquivo) {
                    // O campo 'nm' é uma string, então basta acessar diretamente
                    if (strpos($arquivo['nm'], '-bu.dat') !== false && !file_exists("C:/xampp8/htdocs/siseleitoral/sn1/downloads/{$arquivo['nm']}")) {
                        $link = $url . "/" . $ambiente . "/" . $eleicao . "/arquivo-urna/" . $pleito . "/dados/" . $estado_sigla . "/" . $cd_municipio . "/" . formatarNumero($zona['numero']) . "/" . formatarNumero($secao['NR_SECAO']) . "/" . $hash['hash'] . "/" . $arquivo['nm'];
                        exec_python($arquivo, $link);
                    }
                }
            }
        } else {
            // O link não é válido, pode pular para a próxima iteração ou registrar log
            echo "Link inválido: $json_url_2\n";
        }
    }
}

//------------------------------------------------------------------------------
function exec_python($arquivo, $link) {

    downloadFile($link, "sn1/downloads/");

//// Caminhos dos arquivos
//    $output_file = 'C:\xampp8\htdocs\siseleitoral\sn1\json\bu_output.json';
//
//    $pythonScript = escapeshellarg("C:\\xampp8\\htdocs\\siseleitoral\\sn1\\json\\bu_dump.py");
//    $asnFile = escapeshellarg("C:\\xampp8\\htdocs\\siseleitoral\\sn1\\python\\spec\\bu.asn1");
//
//    $command = "python $pythonScript -a $asnFile -b \"C:\\xampp8\\htdocs\\siseleitoral\\sn1\\downloads\\$arquivo\" 2>&1";
//    shell_exec($command);
//
//// Verificando se o arquivo JSON foi criado
//    if (file_exists($output_file)) {
//        // Lendo o arquivo JSON
//        //$json_data = file_get_contents($output_file);
//        //$data = json_decode($json_data, true);
//        // Agora você pode usar os dados JSON na sua aplicação PHP
//        // Inspecione a estrutura completa do array decodificado
//        //echo "<pre>";
//        //print_r($data);
//        //echo "</pre>";
//        //Mudando nome do arquivo json para o nome do bu
//        renameFile($output_file, str_replace(".bu", ".json", $arquivo));
//    } else {
//        echo "Erro ao gerar o arquivo JSON.";
//    }
}

function downloadFile($url, $saveDir) {
    // Verifica se o diretório de destino existe, se não, cria o diretório
    if (!is_dir($saveDir)) {
        mkdir($saveDir, 0777, true);
    }

    // Extrai o nome do arquivo do URL
    $fileName = basename($url);

    // Define o caminho completo onde o arquivo será salvo
    $savePath = $saveDir . '/' . $fileName;

    // Inicializa o cURL
    $ch = curl_init($url);

    // Configurações do cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 50);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36');

    // Executa o download e armazena o conteúdo do arquivo
    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Verifica se o download foi bem-sucedido
    if ($httpCode == 200 && $data !== false) {
        // Salva o arquivo no caminho especificado
        file_put_contents($savePath, $data);
    }

    // Fecha o cURL
    curl_close($ch);
}

function renameFile($currentFilePath, $newFileName) {
    // Extrai o diretório do arquivo atual
    $directory = dirname($currentFilePath);

    // Define o novo caminho completo para o arquivo com o novo nome
    $newFilePath = $directory . '/' . $newFileName;

    // Tenta renomear o arquivo
    rename($currentFilePath, $newFilePath);
}

function formatarNumero($numero) {
    // Usa a função sprintf para garantir que o número tenha sempre 4 dígitos
    return sprintf('%04d', $numero);
}

?>