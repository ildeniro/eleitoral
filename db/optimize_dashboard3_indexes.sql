-- ============================================================================
-- Dashboard3 Performance Optimization - Database Indexes
-- ============================================================================
-- Purpose: Speed up electoral history queries by creating indexes on
--          frequently joined and filtered columns
-- ============================================================================

-- Index for candidatos_detalhados lookups by CPF
CREATE INDEX IF NOT EXISTS idx_candidatos_cpf 
ON candidatos_detalhados(nr_cpf_candidato, ano_eleicao);

-- Index for candidatos_detalhados lookups by título eleitoral
CREATE INDEX IF NOT EXISTS idx_candidatos_titulo 
ON candidatos_detalhados(nr_titulo_eleitoral_candidato, ano_eleicao);

-- Index for resultados_eleitorais JOIN by sq_candidato (years 2016+)
CREATE INDEX IF NOT EXISTS idx_resultados_sq_ano 
ON resultados_eleitorais(sq_candidato, ano, qtd_votos);

-- Index for resultados_eleitorais JOIN by cargo/num_candidato (years 2012, 2014)
CREATE INDEX IF NOT EXISTS idx_resultados_cargo_num_ano 
ON resultados_eleitorais(cod_cargo, num_candidato, ano, qtd_votos);

-- Index for resultados_eleitorais by ano (general queries)
CREATE INDEX IF NOT EXISTS idx_resultados_ano 
ON resultados_eleitorais(ano);

-- ============================================================================
-- Expected Performance Improvement:
-- - Before: ~60+ seconds page load
-- - After:  < 3 seconds page load
-- ============================================================================
