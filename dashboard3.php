<?php
// dashboard3.php → CANDIDATO PRIMEIRO → ANOS QUE ELE DISPUTOU → MAPA COMPLETO

$cpf = $_GET['cpf'] ?? null;
$titulo = $_GET['titulo'] ?? null;
$ano = (int) ($_GET['ano'] ?? 0);
$sq_candidato = $_GET['sq'] ?? null;

$candidato = null;
$anos_disponiveis = [];
$locais = [];
$total_votos = 0;

// 1. BUSCA DE CANDIDATOS AGORA É VIA AJAX (ver final do arquivo)
$candidatos = []; // Mantendo vazio apenas para não quebrar se algo depender (não deve)

// 2. SE ESCOLHEU CANDIDATO → BUSCA ANOS QUE ELE DISPUTOU
if ($cpf || $titulo) {
    // Primeiro, busca o título eleitoral se foi passado CPF
    // Isso garante que encontremos todos os anos, mesmo quando CPF está null em alguns registros
    if ($cpf && !$titulo) {
        $stmt_titulo = $db->prepare("
            SELECT nr_titulo_eleitoral_candidato 
            FROM candidatos_detalhados 
            WHERE nr_cpf_candidato = ? 
              AND nr_titulo_eleitoral_candidato IS NOT NULL 
              AND nr_titulo_eleitoral_candidato != ''
            LIMIT 1
        ");
        $stmt_titulo->execute([$cpf]);
        $result_titulo = $stmt_titulo->fetch(PDO::FETCH_ASSOC);
        if ($result_titulo) {
            $titulo = $result_titulo['nr_titulo_eleitoral_candidato'];
        }
    }
    
    // Monta a query priorizando título eleitoral, depois CPF
    if ($titulo) {
        $where = "(nr_titulo_eleitoral_candidato = ? OR nr_cpf_candidato = ?)";
        $params = [$titulo, $cpf ?: $titulo];
    } else {
        $where = "nr_cpf_candidato = ?";
        $params = [$cpf];
    }

    $stmt = $db->prepare("
        SELECT DISTINCT c.ano_eleicao, c.sq_candidato, c.nm_urna_candidato, c.ds_cargo, c.sg_partido
        FROM candidatos_detalhados c
        WHERE $where
          AND EXISTS (
            SELECT 1 FROM resultados_eleitorais r
            WHERE (
              (c.ano_eleicao IN (2012, 2014) AND r.ano = c.ano_eleicao AND r.cod_cargo = c.cd_cargo AND r.num_candidato = c.nr_candidato)
              OR (c.ano_eleicao NOT IN (2012, 2014) AND r.ano = c.ano_eleicao AND r.sq_candidato = c.sq_candidato)
            )
            AND r.qtd_votos > 0
            LIMIT 1
          )
        ORDER BY c.ano_eleicao DESC
    ");
    $stmt->execute($params);
    $anos_disponiveis = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Se já escolheu o ano → carrega dados completos
    if ($ano && count($anos_disponiveis) > 0) {

        $stmt = $db->prepare("
            SELECT * FROM candidatos_detalhados
            WHERE ($where) AND ano_eleicao = ?
            LIMIT 1
        ");
        // Adiciona o ano aos parâmetros existentes
        $params_with_year = array_merge($params, [$ano]);
        $stmt->execute($params_with_year);
        $candidato = $stmt->fetch(PDO::FETCH_ASSOC);

        $sq_candidato = isset($candidato['sq_candidato']) ? $candidato['sq_candidato'] : '';
        $nr_candidato = isset($candidato['nr_candidato']) ? $candidato['nr_candidato'] : '';
        $cd_cargo = isset($candidato['cd_cargo']) ? $candidato['cd_cargo'] : '';

        if ($ano == 2012 || $ano == 2014) {//Não existia SQ_CANDIDATO
            // Total de votos
            $t = $db->prepare("SELECT COALESCE(SUM(qtd_votos),0) FROM resultados_eleitorais WHERE ano = ? AND cod_cargo = ? AND num_candidato = ?");
            $t->execute([$ano, $cd_cargo, $nr_candidato]);
            $total_votos = $t->fetchColumn();

            // Detectar tipo de eleição e município do candidato
            $cargos_estaduais = ['GOVERNADOR', 'SENADOR', 'DEPUTADO FEDERAL', 'DEPUTADO ESTADUAL', 'PRESIDENTE'];
            $nm_ue = $candidato['nm_ue'] ?? null;
            
            // Se nm_ue = "ACRE", é eleição estadual
            $is_estadual = (strtoupper($nm_ue) == 'ACRE') || in_array(strtoupper($candidato['ds_cargo']), $cargos_estaduais);
            
            // Pegar município do candidato através do nm_ue e buscar o cod_tse
            $cod_municipio_tse = null;
            
            if ($nm_ue && !$is_estadual) {
                $stmt_mun = $db->prepare("SELECT cod_tse FROM bsc_municipios WHERE UPPER(nome) = UPPER(?) AND estado_id = 1 LIMIT 1");
                $stmt_mun->execute([$nm_ue]);
                $result = $stmt_mun->fetch(PDO::FETCH_ASSOC);
                $cod_municipio_tse = $result['cod_tse'] ?? null;
            }
            
            if ($is_estadual) {
                // CONSULTA 3: Eleição ESTADUAL - Todos os municípios do Acre
                // Mapeia zona eleitoral -> município para garantir que todos os municípios apareçam
                $mapa = $db->prepare("
                    SELECT 
                        r.nr_local_votacao,
                        r.nr_zona,
                        l.nm_local_votacao, 
                        COALESCE(l.nm_bairro, m.nome) AS nm_bairro, 
                        COALESCE(l.regional, m.nome, 'Área Rural') AS regional,
                        l.latitude, 
                        l.longitude,
                        l.ds_endereco,
                        m.nome AS nm_municipio, 
                        COALESCE(SUM(r.qtd_votos), 0) AS votos
                    FROM resultados_eleitorais r
                    LEFT JOIN (
                        SELECT DISTINCT nr_zona, municipio_id 
                        FROM locais_votacao_mestre 
                        WHERE municipio_id IS NOT NULL
                    ) zona_mun ON r.nr_zona = zona_mun.nr_zona
                    LEFT JOIN bsc_municipios m ON zona_mun.municipio_id = m.id
                    LEFT JOIN locais_votacao_mestre l ON r.nr_local_votacao = l.nr_local_votacao 
                        AND r.nr_zona = l.nr_zona 
                        AND l.ano_primeira_eleicao <= ?
                    WHERE r.ano = ? 
                      AND r.cod_cargo = ? 
                      AND r.num_candidato = ?
                    GROUP BY r.nr_local_votacao, r.nr_zona
                    HAVING votos > 0
                    ORDER BY votos DESC
                ");
                $mapa->execute([$ano, $ano, $cd_cargo, $nr_candidato]);
            } else {
                // Eleição MUNICIPAL
                if ($cod_municipio_tse == 1392) {
                    // CONSULTA 1: Municipal de RIO BRANCO (com regional)
                    $mapa = $db->prepare("
                        SELECT 
                            l.nm_local_votacao, 
                            l.nm_bairro, 
                            COALESCE(l.regional, 'Área Rural') AS regional,
                            l.latitude, 
                            l.longitude,
                            l.ds_endereco,
                            'Rio Branco' AS nm_municipio, 
                            COALESCE(SUM(r.qtd_votos), 0) AS votos
                        FROM locais_votacao_mestre l
                        LEFT JOIN resultados_eleitorais r ON r.nr_local_votacao = l.nr_local_votacao 
                            AND r.nr_zona = l.nr_zona
                            AND r.ano = ? 
                            AND r.cod_cargo = ? 
                            AND r.num_candidato = ?
                        WHERE l.municipio_id = 94
                          AND l.ano_primeira_eleicao <= ?
                        GROUP BY l.id 
                        HAVING votos > 0 
                        ORDER BY votos DESC
                    ");
                    $mapa->execute([$ano, $cd_cargo, $nr_candidato, $ano]);
                } else {
                    // CONSULTA 2: Municipal de OUTRO MUNICÍPIO (sem regional)
                    $mapa = $db->prepare("
                        SELECT 
                            l.nm_local_votacao, 
                            COALESCE(m.nome, 'Município') AS nm_bairro,
                            COALESCE(m.nome, l.nm_bairro) AS regional,
                            l.latitude, 
                            l.longitude,
                            l.ds_endereco,
                            m.nome AS nm_municipio, 
                            COALESCE(SUM(r.qtd_votos), 0) AS votos
                        FROM locais_votacao_mestre l
                        LEFT JOIN bsc_municipios m ON l.municipio_id = m.id
                        LEFT JOIN resultados_eleitorais r ON r.nr_local_votacao = l.nr_local_votacao 
                            AND r.nr_zona = l.nr_zona
                            AND r.ano = ? 
                            AND r.cod_cargo = ? 
                            AND r.num_candidato = ?
                        WHERE m.cod_tse = ?
                          AND l.ano_primeira_eleicao <= ?
                        GROUP BY l.id 
                        HAVING votos > 0 
                        ORDER BY votos DESC
                    ");
                    $mapa->execute([$ano, $cd_cargo, $nr_candidato, $cod_municipio_tse, $ano]);
                }
            }
            
            $locais = $mapa->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Total de votos
            $t = $db->prepare("SELECT COALESCE(SUM(qtd_votos),0) FROM resultados_eleitorais WHERE ano = ? AND sq_candidato = ?");
            $t->execute([$ano, $sq_candidato]);
            $total_votos = $t->fetchColumn();

            // Detectar tipo de eleição e município do candidato
            $cargos_estaduais = ['GOVERNADOR', 'SENADOR', 'DEPUTADO FEDERAL', 'DEPUTADO ESTADUAL', 'PRESIDENTE'];
            $nm_ue = $candidato['nm_ue'] ?? null;
            
            // Se nm_ue = "ACRE", é eleição estadual
            $is_estadual = (strtoupper($nm_ue) == 'ACRE') || in_array(strtoupper($candidato['ds_cargo']), $cargos_estaduais);
            
            // Pegar município do candidato através do nm_ue e buscar o cod_tse
            $cod_municipio_tse = null;
            
            if ($nm_ue && !$is_estadual) {
                $stmt_mun = $db->prepare("SELECT cod_tse FROM bsc_municipios WHERE UPPER(nome) = UPPER(?) AND estado_id = 1 LIMIT 1");
                $stmt_mun->execute([$nm_ue]);
                $result = $stmt_mun->fetch(PDO::FETCH_ASSOC);
                $cod_municipio_tse = $result['cod_tse'] ?? null;
            }

            if ($is_estadual) {
                // CONSULTA 3: Eleição ESTADUAL - Todos os municípios do Acre
                // Mapeia zona eleitoral -> município para garantir que todos os municípios apareçam
                /*$mapa = $db->prepare("
                    SELECT 
                        r.nr_local_votacao,
                        r.nr_zona,
                        l.nm_local_votacao, 
                        COALESCE(l.nm_bairro, m.nome) AS nm_bairro, 
                        COALESCE(l.regional, m.nome, 'Área Rural') AS regional,
                        l.latitude, 
                        l.longitude,
                        l.ds_endereco,
                        m.nome AS nm_municipio, 
                        COALESCE(SUM(r.qtd_votos), 0) AS votos
                    FROM resultados_eleitorais r
                    LEFT JOIN (
                        SELECT DISTINCT nr_zona, municipio_id, nr_local_votacao  
                        FROM locais_votacao_mestre 
                        WHERE municipio_id IS NOT NULL
                    ) zona_mun ON r.nr_zona = zona_mun.nr_zona AND r.nr_local_votacao = zona_mun.nr_local_votacao 
                    LEFT JOIN bsc_municipios m ON zona_mun.municipio_id = m.id
                    LEFT JOIN locais_votacao_mestre l ON r.nr_local_votacao = l.nr_local_votacao 
                        AND r.nr_zona = l.nr_zona 
                        AND l.ano_primeira_eleicao <= ?
                    WHERE r.ano = ? 
                      AND r.sq_candidato = ?
                    GROUP BY r.nr_local_votacao, r.nr_zona
                    HAVING votos > 0
                    ORDER BY votos DESC
                ");*/

                $mapa = $db->prepare("
                   SELECT 
                        r.nr_local_votacao,
                        r.nr_zona,
                        l.nm_local_votacao, 
                        COALESCE(l.nm_bairro, m.nome) AS nm_bairro, 
                        COALESCE(l.regional, m.nome, 'Área Rural') AS regional,
                        l.latitude, 
                        l.longitude,
                        l.ds_endereco,
                        m.nome AS nm_municipio, 
                        COALESCE(SUM(r.qtd_votos), 0) AS votos
                    FROM resultados_eleitorais r
                    LEFT JOIN locais_votacao_mestre l ON r.nr_local_votacao = l.nr_local_votacao 
                        AND r.nr_zona = l.nr_zona 
                        AND l.ano_primeira_eleicao <= ? 
                    LEFT JOIN bsc_municipios m ON m.id = l.municipio_id 
                    WHERE r.ano = ?  
                      AND r.sq_candidato = ? 
                    GROUP BY r.nr_local_votacao, r.nr_zona
                    HAVING votos > 0
                    ORDER BY votos DESC
                ");
                $mapa->execute([$ano, $ano, $sq_candidato]);
            } else {
                // Eleição MUNICIPAL
                if ($cod_municipio_tse == 1392) {
                    // CONSULTA 1: Municipal de RIO BRANCO (com regional)
                    $mapa = $db->prepare("
                        SELECT 
                            l.nm_local_votacao, 
                            l.nm_bairro, 
                            COALESCE(l.regional, 'Área Rural') AS regional,
                            l.latitude, 
                            l.longitude,
                            l.ds_endereco,
                            'Rio Branco' AS nm_municipio, 
                            COALESCE(SUM(r.qtd_votos), 0) AS votos
                        FROM locais_votacao_mestre l
                        LEFT JOIN resultados_eleitorais r ON r.nr_local_votacao = l.nr_local_votacao 
                            AND r.nr_zona = l.nr_zona
                            AND r.ano = ? 
                            AND r.sq_candidato = ?
                        WHERE l.municipio_id = 94
                          AND l.ano_primeira_eleicao <= ?
                        GROUP BY l.id 
                        HAVING votos > 0 
                        ORDER BY votos DESC
                    ");
                    $mapa->execute([$ano, $sq_candidato, $ano]);
                } else {
                    // CONSULTA 2: Municipal de OUTRO MUNICÍPIO (sem regional)
                    $mapa = $db->prepare("
                        SELECT 
                            l.nm_local_votacao, 
                            COALESCE(m.nome, 'Município') AS nm_bairro,
                            COALESCE(m.nome, l.nm_bairro) AS regional,
                            l.latitude, 
                            l.longitude,
                            l.ds_endereco,
                            m.nome AS nm_municipio, 
                            COALESCE(SUM(r.qtd_votos), 0) AS votos
                        FROM locais_votacao_mestre l
                        LEFT JOIN bsc_municipios m ON l.municipio_id = m.id
                        LEFT JOIN resultados_eleitorais r ON r.nr_local_votacao = l.nr_local_votacao 
                            AND r.nr_zona = l.nr_zona
                            AND r.ano = ? 
                            AND r.sq_candidato = ?
                        WHERE m.cod_tse = ?
                          AND l.ano_primeira_eleicao <= ?
                        GROUP BY l.id 
                        HAVING votos > 0 
                        ORDER BY votos DESC
                    ");
                    $mapa->execute([$ano, $sq_candidato, $cod_municipio_tse, $ano]);
                }
            }
            
            $locais = $mapa->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= $candidato ? htmlspecialchars($candidato['nm_urna_candidato']) . ' • Histórico Eleitoral' : 'Histórico por Candidato • Eleições Acre' ?></title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <style type="text/css">
        :root {
            --charcoal-900: #1a1a1a;
            --charcoal-800: #262626;
            --charcoal-700: #363636;
            --charcoal-600: #4a4a4a;
            --charcoal-500: #5e5e5e;
            --charcoal-400: #7d7d7d;
            --charcoal-300: #a0a0a0;
            --charcoal-200: #c9c9c9;
            --charcoal-100: #f2f2f2;
            --dark-teal-accent: #0f766e;
            --dark-teal-accent-hover: #13948b;
            --blue-accent: #3b82f6;
            --light-grey-bg: #e5e7eb;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--charcoal-100);
            color: var(--charcoal-900);
        }
        
        .container-fluid {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        
        /* Sidebar */
        .sidebar {
            width: 20rem;
            flex-shrink: 0;
            background: var(--charcoal-100);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            overflow-y: auto;
            padding: 1.5rem;
        }
        
        .sidebar::-webkit-scrollbar {
            width: 8px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: var(--charcoal-200);
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: var(--charcoal-400);
            border-radius: 4px;
        }
        
        .candidate-photo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .candidate-photo {
            width: 90%;
            aspect-ratio: 1;
            object-fit: cover;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .candidate-name {
            font-size: 26px;
            font-weight: 600;
            color: var(--charcoal-900);
            margin-bottom: 0.25rem;
        }
        
        .candidate-full-name {
            font-size: 14px;
            font-weight: 400;
            color: var(--charcoal-500);
        }
        
        .section-title {
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--charcoal-500);
            margin-bottom: 1rem;
            margin-top: 2rem;
        }
        
        .form-select {
            width: 100%;
            padding: 0.75rem;
            background: white;
            border: 2px solid var(--charcoal-200);
            border-radius: 0.5rem;
            color: var(--charcoal-900);
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .form-select:hover {
            border-color: var(--blue-accent);
        }
        
        .form-select:focus {
            outline: none;
            border-color: var(--blue-accent);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--charcoal-600);
            margin-bottom: 0.5rem;
        }
        
        /* Election History Cards */
        .election-history {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .election-card {
            display: block;
            padding: 1rem;
            margin-bottom: 0.75rem;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
        }
        
        .election-card.active {
            background: white;
            border: 2px solid var(--blue-accent);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .election-card.inactive {
            background: var(--light-grey-bg);
            border: 2px solid transparent;
        }
        
        .election-card.inactive:hover {
            background: var(--charcoal-200);
        }
        
        .election-year-container {
            display: flex;
            align-items: center;
            margin-bottom: 0.25rem;
        }
        
        .election-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--blue-accent);
            margin-right: 0.5rem;
            flex-shrink: 0;
        }
        
        .election-year {
            font-weight: 600;
            font-size: 16px;
            color: var(--charcoal-900);
            opacity: 0.7;
        }
        
        .election-position {
            display: block;
            font-size: 14px;
            font-weight: 400;
            color: var(--charcoal-900);
            opacity: 0.7;
            margin-bottom: 0.25rem;
        }
        
        .election-votes {
            display: block;
            font-size: 16px;
            font-weight: 500;
            color: var(--charcoal-900);
            opacity: 0.7;
            margin-top: 0.25rem;
        }
        
        /* Map Container */
        .map-container {
            flex: 1;
            position: relative;
            background-image: url(https://lh3.googleusercontent.com/aida-public/AB6AXuCB3L5rXaVSe5v4XnczhVJN8MOPe5PkdjyP5UKBgmLpI0mFwkPXNd9t1_IhMJsIDMamGmzEo2cxihMu7tHdC-Je1mOr6bAs5th89fHah-Gj_ULfrabkPUOBIqGQrarPtqNZTFYNlTe7UtRW8j29yCeRxvWaOYK5GET4s3Rk9sTcpUiongDzopYAdq82uwKbxl4ynccIMHVxVV1lHKVDm1tK16zIMNXzIyb2PyNDJKN7bBtIz1t_z5bpQ8aIQebibJDqcTOyaZ3XDPc);
            background-size: cover;
            background-position: center;
        }
        
        #map {
            width: 100%;
            height: 100%;
        }
        
        .map-controls {
            position: absolute;
            top: 1rem;
            left: 1rem;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            overflow: hidden;
        }
        
        .map-control-btn {
            width: 40px;
            height: 40px;
            border: none;
            background: white;
            color: var(--charcoal-500);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        
        .map-control-btn:hover {
            background: var(--charcoal-200);
            color: var(--charcoal-900);
        }
        
        .map-control-btn:not(:last-child) {
            border-bottom: 1px solid var(--charcoal-200);
        }
        
        .empty-state {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 500;
        }
        
        .empty-state h1 {
            font-size: 2.5rem;
            font-weight: 600;
            color: var(--charcoal-900);
            margin-bottom: 0.5rem;
        }
        
        .empty-state p {
            font-size: 1.1rem;
            color: var(--charcoal-600);
        }
        .search-results {
            margin-top: 0.5rem;
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid var(--charcoal-200);
            border-radius: 0.5rem;
            background: white;
            display: none; /* Hidden by default */
        }
        .search-result-item {
            padding: 0.75rem;
            border-bottom: 1px solid var(--charcoal-100);
            cursor: pointer;
            transition: background 0.2s;
        }
        .search-result-item:last-child {
            border-bottom: none;
        }
        .search-result-item:hover {
            background: var(--charcoal-100);
        }
        .search-result-name {
            font-weight: 600;
            color: var(--charcoal-900);
            display: block;
        }
        .search-result-info {
            font-size: 0.8rem;
            color: var(--charcoal-500);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Sidebar -->
        <aside class="sidebar">
            <?php if (($cpf || $titulo) && !empty($anos_disponiveis)): ?>
                <?php if ($candidato && $ano): ?>
                    <!-- Candidato com ano selecionado - mostra foto e dados completos -->
                    <div class="candidate-photo-container">
                        <?php
                        if ($ano == 2012 || $ano == 2014) {
                            $fac = "AC";
                        } else {
                            $fac = "FAC";
                        }

                        $foto = "template/assets/images/candidatos/" . $ano . "/$fac" . $sq_candidato . "_div.jpg";

                        if (!file_exists($foto)) {
                            $foto = "template/assets/images/candidatos/" . $ano . "/$fac" . $sq_candidato . "_div.jpeg";

                            if (!file_exists($foto)) {
                                $foto = "template/assets/images/candidatos/sem_foto.jpg";
                            }
                        }
                        ?>
                        <img src="<?= $foto ?>" alt="<?= htmlspecialchars($candidato['nm_urna_candidato']) ?>" class="candidate-photo"/>
                        <h1 class="candidate-name"><?= strtoupper(htmlspecialchars($candidato['nm_urna_candidato'])) ?></h1>
                        <p class="candidate-full-name"><?= htmlspecialchars($candidato['nm_urna_candidato']) ?></p>
                    </div>
                <?php else: ?>
                    <!-- Candidato selecionado mas sem ano - mostra apenas nome -->
                    <div class="candidate-photo-container">
                        <h1 class="candidate-name" style="font-size: 22px;"><?= strtoupper(htmlspecialchars($anos_disponiveis[0]['nm_urna_candidato'])) ?></h1>
                        <p class="candidate-full-name">Selecione um ano para ver os detalhes</p>
                    </div>
                <?php endif; ?>
                
                <h2 class="section-title">Histórico Eleitoral</h2>
                <ul class="election-history">
                    <?php foreach ($anos_disponiveis as $a): ?>
                        <li>
                            <a href="?cpf=<?= urlencode($cpf ?: $titulo) ?>&ano=<?= $a['ano_eleicao'] ?>" 
                               class="election-card <?= $a['ano_eleicao'] == $ano ? 'active' : 'inactive' ?>">
                                <?php if ($a['ano_eleicao'] == $ano): ?>
                                    <div class="election-year-container">
                                        <span class="election-dot"></span>
                                        <span class="election-year"><?= $a['ano_eleicao'] ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="election-year"><?= $a['ano_eleicao'] ?></span>
                                <?php endif; ?>
                                <span class="election-position"><?= htmlspecialchars($a['ds_cargo']) ?> (<?= htmlspecialchars($a['sg_partido']) ?>)</span>
                                <?php if ($a['ano_eleicao'] == $ano): ?>
                                    <span class="election-votes"><?= number_format($total_votos, 0, ',', '.') ?> Votos</span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                
                <!-- Botão para trocar de candidato -->
                <div style="margin-top: 2rem; text-align: center;">
                    <a href="dashboard3" class="text-decoration-none" style="color: var(--blue-accent); font-weight: 500; font-size: 14px;">
                        ← Escolher outro candidato
                    </a>
                </div>
                
            <?php else: ?>
                <h1 class="candidate-name" style="font-size: 20px; margin-bottom: 1rem;">Histórico por Candidato</h1>
                <p class="candidate-full-name" style="margin-bottom: 2rem;">Pesquise um candidato para ver seu histórico eleitoral completo</p>
                
                <div class="search-container">
                    <label class="form-label">Pesquisar Candidato</label>
                    <input type="text" id="search-candidate" class="form-select" placeholder="Digite o nome (min. 3 letras)..." autocomplete="off">
                    <div id="loading-indicator" style="display:none; color: var(--charcoal-500); font-size: 0.9rem; margin-top: 0.5rem;">
                        <span class="material-icons" style="font-size: 16px; vertical-align: text-bottom; animation: spin 1s linear infinite;">refresh</span> Buscando...
                    </div>
                    <div id="search-results" class="search-results"></div>
                </div>
                
                <style>
                    @keyframes spin { 100% { transform: rotate(360deg); } }
                </style>
            <?php endif; ?>
        </aside>

        <!-- Map -->
        <main class="map-container">
            <?php if (!$candidato): ?>
                <div class="empty-state">
                    <h1>Histórico Eleitoral</h1>
                    <p>Selecione um candidato para visualizar seu histórico de votação</p>
                </div>
            <?php endif; ?>
            
            <div class="map-controls">
                <button class="map-control-btn" onclick="map.zoomIn()">
                    <span class="material-icons">add</span>
                </button>
                <button class="map-control-btn" onclick="map.zoomOut()">
                    <span class="material-icons">remove</span>
                </button>
            </div>
            
            <div id="map"></div>
        </main>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Definição padrão (Rio Branco)
        let mapCenter = [-9.974, -67.807];
        let mapZoom = 12;

        <?php if ($candidato): ?>
            // Verifica se é eleição estadual ou municipal baseado no cargo
            const cargo = "<?= strtoupper($candidato['ds_cargo']) ?>";
            // Cargos Estaduais/Federais que abrangem o estado todo
            const cargosEstaduais = ['GOVERNADOR', 'SENADOR', 'DEPUTADO FEDERAL', 'DEPUTADO ESTADUAL', 'PRESIDENTE'];
            
            if (cargosEstaduais.some(c => cargo.includes(c))) {
                // Centro aproximado do Acre e zoom menor
                mapCenter = [-9.0, -70.0];
                mapZoom = 7;
            }
        <?php endif; ?>

        const map = L.map('map', { zoomControl: false }).setView(mapCenter, mapZoom);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© Ildeniro Lima | Histórico Eleitoral'
        }).addTo(map);

        <?php if ($candidato && $locais): ?>
            const dados = <?= json_encode($locais) ?>;
            const nome = "<?= addslashes($candidato['nm_urna_candidato']) ?>";

            // 1. Encontrar Maior e Menor quantidade de votos
            let maxVotos = 0;
            let minVotos = Infinity;

            dados.forEach(l => {
                const v = parseInt(l.votos);
                if (v > maxVotos) maxVotos = v;
                if (v < minVotos) minVotos = v;
            });

            // Se só tiver 1 local ou min == max, evita divisão por zero
            if (minVotos === Infinity) minVotos = 0;
            const range = maxVotos - minVotos;

            // Função para definir a cor baseada na porcentagem do range
            function getColor(votos) {
                if (range === 0) return "#3b82f6"; // Azul padrão se não houver variação

                const diff = votos - minVotos;
                const percent = diff / range; // 0.0 a 1.0

                // 5 Cores: Azul > Verde > Amarelo > Laranja > Vermelho
                // Azul (Maior) -> Vermelho (Menor)
                // O usuário pediu: Maior = Azul, Menor = Vermelho.
                // Então vamos inverter a lógica percentual ou os ifs.
                // 80-100% = Azul
                // 60-80% = Verde
                // 40-60% = Amarelo
                // 20-40% = Laranja
                // 0-20% = Vermelho

                if (percent >= 0.8) return "#3b82f6"; // Azul (Top 20%)
                if (percent >= 0.6) return "#10b981"; // Verde
                if (percent >= 0.4) return "#facc15"; // Amarelo
                if (percent >= 0.2) return "#f97316"; // Laranja
                return "#ef4444"; // Vermelho (Bottom 20%)
            }

            // Função para adicionar marcador no mapa
            function addMarker(lat, lng, local) {
                const v = parseInt(local.votos);
                const raio = Math.max(5, Math.sqrt(v) * 0.6);
                const cor = getColor(v);

                L.circleMarker([lat, lng], {
                    radius: raio,
                    fillColor: cor,
                    color: "#000",
                    weight: 1,
                    fillOpacity: 0.6
                }).bindPopup(`
                    <div style="font-family: 'Poppins', sans-serif;">
                        <b>${local.nm_local_votacao}</b><br>
                        <small>${local.nm_bairro} • ${local.regional}</small><hr style="margin: 0.5rem 0;">
                        <strong style="color:${cor}">${nome}</strong><br>
                        <strong style="font-size:1.5em">${v.toLocaleString('pt-BR')} votos</strong>
                    </div>
                `).addTo(map);
            }

            // Coordenadas aproximadas de municípios do Acre (fallback para áreas rurais)
            const municipioCoords = {
                'Feijó': { lat: -8.1638, lng: -70.3519, offset: 0.05 },
                'Tarauacá': { lat: -8.1611, lng: -70.7656, offset: 0.05 },
                'Cruzeiro do Sul': { lat: -7.6278, lng: -72.6753, offset: 0.05 },
                'Sena Madureira': { lat: -9.0658, lng: -68.6572, offset: 0.05 },
                'Xapuri': { lat: -10.6519, lng: -68.4944, offset: 0.03 },
                'Brasiléia': { lat: -10.9989, lng: -68.7486, offset: 0.03 },
                'Epitaciolândia': { lat: -11.0264, lng: -68.7342, offset: 0.02 },
                'Acrelândia': { lat: -9.8247, lng: -66.8989, offset: 0.03 },
                'Plácido de Castro': { lat: -10.3364, lng: -67.1850, offset: 0.03 },
                'Capixaba': { lat: -10.5850, lng: -67.7008, offset: 0.03 },
                'Assis Brasil': { lat: -10.9403, lng: -69.5697, offset: 0.03 },
                'Bujari': { lat: -9.8189, lng: -67.9528, offset: 0.03 },
                'Senador Guiomard': { lat: -10.1464, lng: -67.7336, offset: 0.03 },
                'Porto Acre': { lat: -9.5897, lng: -67.5386, offset: 0.03 },
                'Mâncio Lima': { lat: -7.6144, lng: -72.8961, offset: 0.03 },
                'Rodrigues Alves': { lat: -7.7508, lng: -72.6658, offset: 0.03 },
                'Marechal Thaumaturgo': { lat: -8.9392, lng: -72.7928, offset: 0.05 },
                'Porto Walter': { lat: -8.2628, lng: -72.7467, offset: 0.05 },
                'Jordão': { lat: -9.1867, lng: -71.9644, offset: 0.05 },
                'Santa Rosa do Purus': { lat: -9.4258, lng: -70.4928, offset: 0.05 }
            };

            // Função de geocodificação com cache via proxy PHP
            // LocationIQ permite 50 req/s, muito mais rápido que Nominatim (1 req/s)
            async function geocodeAddress(address, municipio, localName, nrLocal, nrZona) {
                // Validar entrada - se não tem endereço E não tem município, usar fallback direto
                if ((!address || address.trim() === '' || address === 'null') && 
                    (!municipio || municipio === 'null')) {
                    return null;
                }
                
                // Se só tem município (sem endereço válido), ir direto para fallback do município
                if (!address || address.trim() === '' || address === 'null') {
                    if (municipio && municipioCoords[municipio]) {
                        const coords = municipioCoords[municipio];
                        const offset = coords.offset;
                        return {
                            lat: coords.lat + (Math.random() - 0.5) * offset,
                            lng: coords.lng + (Math.random() - 0.5) * offset,
                            fromFallback: true
                        };
                    }
                    return null;
                }
                
                // Estratégia 1: Tentar endereço completo via proxy PHP (com cache + LocationIQ)
                let query = municipio && municipio !== 'null'
                    ? `${municipio}, ${address}, Acre, Brasil`
                    : `${address}, Acre, Brasil`;
                    
                // Passar nr_local e nr_zona para o proxy fazer cache
                let url = `ajax/geocode_proxy.php?q=${encodeURIComponent(query)}`;
                if (nrLocal && nrZona) {
                    url += `&nr_local=${nrLocal}&nr_zona=${nrZona}`;
                }
                
                try {
                    let response = await fetch(url);
                    let data = await response.json();
                    
                    if (data && data.length > 0 && !data.error) {
                        return {
                            lat: parseFloat(data[0].lat),
                            lng: parseFloat(data[0].lon),
                            cached: data[0].cached || false
                        };
                    }
                    
                    // Estratégia 2: Se falhou, tentar apenas município + estado
                    if (municipio && municipio !== 'null') {
                        await new Promise(resolve => setTimeout(resolve, 35)); // LocationIQ: 50 req/s
                        
                        query = `${municipio}, Acre, Brasil`;
                        url = `ajax/geocode_proxy.php?q=${encodeURIComponent(query)}`;
                        if (nrLocal && nrZona) {
                            url += `&nr_local=${nrLocal}&nr_zona=${nrZona}`;
                        }
                        
                        response = await fetch(url);
                        data = await response.json();
                        
                        if (data && data.length > 0 && !data.error) {
                            // Adicionar offset aleatório para não sobrepor marcadores
                            const baseCoords = {
                                lat: parseFloat(data[0].lat),
                                lng: parseFloat(data[0].lon)
                            };
                            
                            // Offset aleatório de até 0.02 graus (~2km)
                            const offset = 0.02;
                            return {
                                lat: baseCoords.lat + (Math.random() - 0.5) * offset,
                                lng: baseCoords.lng + (Math.random() - 0.5) * offset
                            };
                        }
                    }
                    
                    // Estratégia 3: Usar coordenadas aproximadas do município (fallback final)
                    if (municipio && municipioCoords[municipio]) {
                        const coords = municipioCoords[municipio];
                        const offset = coords.offset;
                        
                        return {
                            lat: coords.lat + (Math.random() - 0.5) * offset,
                            lng: coords.lng + (Math.random() - 0.5) * offset,
                            fromFallback: true
                        };
                    }
                    
                } catch (error) {
                    console.warn(`Erro ao geocodificar ${localName}:`, error);
                }
                return null;
            }

            // Separar locais com e sem coordenadas
            const locaisComCoords = [];
            const locaisSemCoords = [];

            dados.forEach(l => {
                if (l.latitude && l.longitude && l.latitude !== null && l.longitude !== null) {
                    locaisComCoords.push(l);
                } else {
                    locaisSemCoords.push(l);
                }
            });

            // Renderizar imediatamente os locais com coordenadas
            locaisComCoords.forEach(l => {
                addMarker(l.latitude, l.longitude, l);
            });

            // Função para salvar coordenadas no banco (cache)
            async function saveGeocode(nr_local, nr_zona, lat, lng) {
                try {
                    const formData = new FormData();
                    formData.append('nr_local_votacao', nr_local);
                    formData.append('nr_zona', nr_zona);
                    formData.append('latitude', lat);
                    formData.append('longitude', lng);
                    
                    await fetch('ajax/save_geocode.php', {
                        method: 'POST',
                        body: formData
                    });
                } catch (e) {
                    // Silently fail - caching is optional
                }
            }

            // Geocodificar e renderizar locais sem coordenadas
            if (locaisSemCoords.length > 0) {
                console.log(`🔄 Geocodificando ${locaisSemCoords.length} locais sem coordenadas (usando LocationIQ + Cache)...`);
                
                let fromCache = 0;
                let fromAPI = 0;
                let fromFallback = 0;
                
                // Processar rapidamente - LocationIQ permite 50 req/s
                (async function processGeocode() {
                    for (let i = 0; i < locaisSemCoords.length; i++) {
                        const local = locaisSemCoords[i];
                        
                        if (local.ds_endereco || local.nm_municipio) {
                            const coords = await geocodeAddress(
                                local.ds_endereco, 
                                local.nm_municipio, 
                                local.nm_local_votacao,
                                local.nr_local_votacao,  // Novo parâmetro para cache
                                local.nr_zona            // Novo parâmetro para cache
                            );
                            
                            if (coords) {
                                addMarker(coords.lat, coords.lng, local);
                                
                                if (coords.cached) {
                                    fromCache++;
                                    console.log(`📦 Cache: ${local.nm_local_votacao}`);
                                } else if (coords.fromFallback) {
                                    fromFallback++;
                                    console.log(`📍 Fallback: ${local.nm_local_votacao}`);
                                } else {
                                    fromAPI++;
                                    console.log(`✓ API: ${local.nm_local_votacao} (${local.nm_municipio})`);
                                }
                            } else {
                                console.warn(`✗ Falhou: ${local.nm_local_votacao}`);
                            }
                            
                            // LocationIQ: 50 req/s = 20ms entre requisições
                            // Usando 35ms para margem de segurança
                            if (i < locaisSemCoords.length - 1) {
                                await new Promise(resolve => setTimeout(resolve, 35));
                            }
                        }
                    }
                    console.log(`✅ Geocodificação concluída! Cache: ${fromCache}, API: ${fromAPI}, Fallback: ${fromFallback}`);
                })();
            }
        <?php endif; ?>

    </script>
    <script>
        const searchInput = document.getElementById('search-candidate');
        const resultsContainer = document.getElementById('search-results');
        const loadingIndicator = document.getElementById('loading-indicator');
        let debounceTimer;

        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const term = e.target.value.trim();
                
                clearTimeout(debounceTimer);
                
                if (term.length < 3) {
                    resultsContainer.style.display = 'none';
                    resultsContainer.innerHTML = '';
                    return;
                }

                loadingIndicator.style.display = 'block';
                resultsContainer.style.display = 'none';

                debounceTimer = setTimeout(() => {
                    fetch(`ajax/busca_candidatos_dashboard3.php?q=${encodeURIComponent(term)}`)
                        .then(response => response.json())
                        .then(data => {
                            loadingIndicator.style.display = 'none';
                            resultsContainer.innerHTML = '';
                            
                            if (data.length > 0) {
                                resultsContainer.style.display = 'block';
                                data.forEach(c => {
                                    const div = document.createElement('div');
                                    div.className = 'search-result-item';
                                    
                                    // Determina o parâmetro correto (CPF ou Título)
                                    const param = c.cpf ? `cpf=${c.cpf}` : `titulo=${c.titulo}`;
                                    
                                    div.innerHTML = `
                                        <span class="search-result-name">${c.nome_urna}</span>
                                        <span class="search-result-info">${c.total_eleicoes} eleições (${c.primeira_eleicao} - ${c.ultima_eleicao})</span>
                                    `;
                                    
                                    div.addEventListener('click', () => {
                                        window.location.href = `dashboard3?${param}`;
                                    });
                                    
                                    resultsContainer.appendChild(div);
                                });
                            } else {
                                resultsContainer.style.display = 'block';
                                resultsContainer.innerHTML = '<div class="search-result-item" style="cursor: default;">Nenhum candidato encontrado.</div>';
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            loadingIndicator.style.display = 'none';
                        });
                }, 500); // 500ms debounce
            });
        }
    </script>
</body>
</html>