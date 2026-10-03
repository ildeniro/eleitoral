<?php
// scripts/create_geocode_table.php

// Include configuration
require_once '../config/geral.php';
$db = Conexao::getInstance();

try {
    echo "Checking for geocode_cache table...\n";
    
    // Check if table exists
    $stmt = $db->query("SHOW TABLES LIKE 'geocode_cache'");
    if ($stmt->rowCount() > 0) {
        echo "Table 'geocode_cache' already exists.\n";
    } else {
        echo "Creating 'geocode_cache' table...\n";
        
        $sql = "CREATE TABLE geocode_cache (
            nr_local_votacao INT NOT NULL,
            nr_zona INT NOT NULL,
            nm_municipio VARCHAR(255) DEFAULT NULL,
            ds_endereco TEXT DEFAULT NULL,
            latitude DECIMAL(10,8) DEFAULT NULL,
            longitude DECIMAL(11,8) DEFAULT NULL,
            source VARCHAR(50) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (nr_local_votacao, nr_zona)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        
        $db->exec($sql);
        echo "Table 'geocode_cache' created successfully.\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
