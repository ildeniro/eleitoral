-- Tabela de cache para coordenadas geocodificadas
-- Armazena coordenadas por nr_local_votacao + nr_zona para evitar chamadas repetidas à API

CREATE TABLE IF NOT EXISTS geocode_cache (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nr_local_votacao INT NOT NULL,
    nr_zona INT NOT NULL,
    nm_local_votacao VARCHAR(255),
    nm_municipio VARCHAR(100),
    ds_endereco VARCHAR(500),
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    source ENUM('nominatim', 'locationiq', 'fallback') DEFAULT 'locationiq',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_local_zona (nr_local_votacao, nr_zona),
    INDEX idx_coords (latitude, longitude)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
