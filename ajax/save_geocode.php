<?php
/**
 * Salva coordenadas geocodificadas no banco de dados
 * Uso: ajax/save_geocode.php
 * POST: nr_local_votacao, nr_zona, latitude, longitude
 */

// Start output buffering
ob_start();
// Disable error display
ini_set('display_errors', 0);

header('Content-Type: application/json');

// Validação dos parâmetros
$nr_local = $_POST['nr_local_votacao'] ?? null;
$nr_zona = $_POST['nr_zona'] ?? null;
$latitude = $_POST['latitude'] ?? null;
$longitude = $_POST['longitude'] ?? null;

if (!$nr_local || !$nr_zona || !$latitude || !$longitude) {
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
    exit;
}

// Validação de tipos
$nr_local = (int)$nr_local;
$nr_zona = (int)$nr_zona;
$latitude = (float)$latitude;
$longitude = (float)$longitude;

// Valida coordenadas do Acre (aprox. -7 a -11 lat, -66 a -74 lng)
if ($latitude < -12 || $latitude > -6 || $longitude < -75 || $longitude > -65) {
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'Coordinates out of Acre range']);
    exit;
}

try {
    require_once '../config/geral.php';
    $db = Conexao::getInstance();
    
    $stmt = $db->prepare("
        UPDATE locais_votacao_mestre 
        SET latitude = ?, longitude = ?
        WHERE nr_local_votacao = ? AND nr_zona = ?
        AND (latitude IS NULL OR longitude IS NULL)
    ");
    
    $stmt->execute([$latitude, $longitude, $nr_local, $nr_zona]);
    
    $affected = $stmt->rowCount();
    
    ob_clean();
    echo json_encode([
        'success' => true,
        'updated' => $affected > 0,
        'nr_local_votacao' => $nr_local,
        'nr_zona' => $nr_zona
    ]);
    
} catch (PDOException $e) {
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
