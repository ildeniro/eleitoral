<?php
/**
 * Proxy para Geocodificação com Cache e LocationIQ
 * 
 * Fluxo:
 * 1. Verifica se já existe no cache (geocode_cache)
 * 2. Se não existe, busca via LocationIQ (50 req/s) ou Nominatim (1 req/s)
 * 3. Salva resultado no cache para próximas requisições
 * 
 * Uso: ajax/geocode_proxy.php?q=endereço
 * Opcional: &nr_local=123&nr_zona=1 (para cache por local)
 */

// Start output buffering to capture any unwanted output (warnings, etc.)
ob_start();

// Disable error display to avoid breaking JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/geral.php';
$db = Conexao::getInstance();

// ============================================
// CONFIGURAÇÃO - LocationIQ API Key
// Crie conta gratuita em: https://locationiq.com/
// 10.000 requisições/dia grátis, 50 req/segundo
// ============================================
$LOCATIONIQ_API_KEY = 'pk.c89ad10c8b64b4f2b2ae67ba1b4bab34'; // Substitua pela sua chave

$query = $_GET['q'] ?? '';
$nr_local = isset($_GET['nr_local']) ? (int)$_GET['nr_local'] : null;
$nr_zona = isset($_GET['nr_zona']) ? (int)$_GET['nr_zona'] : null;

if (empty($query)) {
    ob_clean();
    echo json_encode(['error' => 'Query parameter required']);
    exit;
}

try {
    // 1. VERIFICAR CACHE PRIMEIRO
    if ($nr_local && $nr_zona) {
        try {
            $stmt = $db->prepare("SELECT latitude, longitude, source FROM geocode_cache WHERE nr_local_votacao = ? AND nr_zona = ? LIMIT 1");
            $stmt->execute([$nr_local, $nr_zona]);
            $cached = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($cached) {
                // Retornar do cache no formato esperado
                ob_clean();
                echo json_encode([
                    [
                        'lat' => $cached['latitude'],
                        'lon' => $cached['longitude'],
                        'cached' => true,
                        'source' => $cached['source']
                    ]
                ]);
                exit;
            }
        } catch (PDOException $e) {
            // Se cache falhar, continua para API
        }
    }

    // 2. BUSCAR VIA API (LocationIQ ou Nominatim)
    $result = null;
    $source = 'locationiq';

    // Tentar LocationIQ primeiro (mais rápido)
    if (!empty($LOCATIONIQ_API_KEY)) {
        $url = 'https://us1.locationiq.com/v1/search?' . http_build_query([
            'key' => $LOCATIONIQ_API_KEY,
            'q' => $query,
            'format' => 'json',
            'limit' => 1
        ]);
        
        $opts = [
            'http' => [
                'method' => 'GET',
                'timeout' => 10,
                'header' => "User-Agent: Dashboard Eleitoral Acre/1.0\r\n"
            ]
        ];
        
        $context = stream_context_create($opts);
        $response = @file_get_contents($url, false, $context);
        
        if ($response !== false) {
            $data = json_decode($response, true);
            if (is_array($data) && !empty($data) && isset($data[0]['lat'])) {
                $result = $data;
            }
        }
    }

    // Fallback para Nominatim se LocationIQ falhar
    if (!$result) {
        $source = 'nominatim';
        $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
            'format' => 'json',
            'q' => $query,
            'limit' => 1
        ]);
        
        $opts = [
            'http' => [
                'method' => 'GET',
                'timeout' => 10,
                'header' => "User-Agent: Dashboard Eleitoral Acre/1.0\r\n"
            ]
        ];
        
        $context = stream_context_create($opts);
        $response = @file_get_contents($url, false, $context);
        
        if ($response !== false) {
            $data = json_decode($response, true);
            if (is_array($data) && !empty($data)) {
                $result = $data;
            }
        }
    }

    // 3. SALVAR NO CACHE se encontrou resultado
    if ($result && $nr_local && $nr_zona) {
        try {
            $lat = $result[0]['lat'];
            $lon = $result[0]['lon'];
            
            // Extrair info adicional da query
            $parts = explode(',', $query);
            $municipio = trim($parts[0] ?? '');
            $endereco = trim($parts[1] ?? '');
            
            $stmt = $db->prepare("
                INSERT INTO geocode_cache (nr_local_votacao, nr_zona, nm_municipio, ds_endereco, latitude, longitude, source)
                VALUES (?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE latitude = VALUES(latitude), longitude = VALUES(longitude), source = VALUES(source)
            ");
            $stmt->execute([$nr_local, $nr_zona, $municipio, $endereco, $lat, $lon, $source]);
        } catch (PDOException $e) {
            // Cache save failed, continue anyway
        }
    }

    if ($result) {
        ob_clean(); // Discard any previous output
        echo json_encode($result);
    } else {
        ob_clean(); // Discard any previous output
        echo json_encode(['error' => 'No results found']);
    }

} catch (Throwable $e) {
    ob_clean();
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
