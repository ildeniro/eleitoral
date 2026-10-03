<?php
/**
 * config_apuracao_2026.php
 * Configurações Centrais do Módulo de Apuração 2026 (Acre)
 * Permite alternar facilmente entre Simulado e Ambiente Oficial (Produção)
 */

define('CONFIG_2026_FILE', __DIR__ . '/config_apuracao.json');

function carregar_config_apuracao_2026() {
    $defaults = [
        'ambiente' => 'oficial', // 'oficial' ou 'simulado'
        'eleicao_federal' => '6257', // Oficial 2026: 6257 (1º Turno Presidente) | Simulado: 21270
        'eleicao_estadual' => '6259', // Oficial 2026: 6259 (1º Turno Acre) | Simulado: 21272
        'pleito' => '3220', // Pleito Oficial TSE: 3220 (04/10/2026)
        'ano' => '2026',
        'turno' => 1,
        'intervalo_segundos' => 30,
        'auto_refresh_painel' => 30,
        'data_eleicao' => '2026-10-04',
        'descricao_ambiente' => 'Transmissão Oficial do TSE (Produção)'
    ];

    if (file_exists(CONFIG_2026_FILE)) {
        $conteudo = file_get_contents(CONFIG_2026_FILE);
        $json = json_decode($conteudo, true);
        if (is_array($json)) {
            return array_merge($defaults, $json);
        }
    }

    return $defaults;
}

function salvar_config_apuracao_2026(array $novos_dados) {
    $atual = carregar_config_apuracao_2026();
    $merged = array_merge($atual, $novos_dados);
    
    // Atualiza descrição amigável
    if ($merged['ambiente'] === 'oficial') {
        $merged['descricao_ambiente'] = 'Transmissão Oficial do TSE (Produção)';
    } else {
        $merged['descricao_ambiente'] = 'Simulado de Testes do TSE';
    }

    file_put_contents(CONFIG_2026_FILE, json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    return $merged;
}

/**
 * Retorna a URL exata do TSE CDN para o arquivo JWS consolidado
 * O TSE padroniza o código da eleição no nome do arquivo com exatamente 6 dígitos após o 'e' (ex: e006257 ou e021270)
 */
function obter_url_tse_jws($cod_municipio_tse, $cargo_code, $eleicao_id, $ambiente = null) {
    if ($ambiente === null) {
        $config = carregar_config_apuracao_2026();
        $ambiente = $config['ambiente'];
    }

    $cod = str_pad(ltrim($cod_municipio_tse, '0'), 5, '0', STR_PAD_LEFT);
    $cargo_pad = str_pad($cargo_code, 4, '0', STR_PAD_LEFT);
    $eleicao_pad = str_pad($eleicao_id, 6, '0', STR_PAD_LEFT);

    if ($ambiente === 'oficial') {
        // Oficial TSE 2026:
        // https://resultados.tse.jus.br/oficial/ele2026/{eleicao}/dados/ac/ac{cod}-c{code}-e{eleicao_6digitos}-u.jws
        return "https://resultados.tse.jus.br/oficial/ele2026/{$eleicao_id}/dados/ac/ac{$cod}-c{$cargo_pad}-e{$eleicao_pad}-u.jws";
    } else {
        // Simulado TSE 2026:
        return "https://resultados-sim.tse.jus.br/simulado/simulado2026/ele2026/{$eleicao_id}/dados/ac/ac{$cod}-c{$cargo_pad}-e{$eleicao_pad}-u.jws";
    }
}

/**
 * Zera com segurança os dados de apuração de 2026 para iniciar o dia da eleição limpo
 */
function zerar_base_apuracao_2026($db) {
    // 1. Limpa resultados
    $db->exec("TRUNCATE TABLE `2026_resultados`");

    // 2. Reseta estatísticas de apuração dos 22 municípios
    $db->exec("
        UPDATE `2026_apuracao_municipios` 
        SET `SECOES_APURADAS` = 0, `PORC_APURADA` = 0.00, `DATA_ATUALIZACAO` = NOW()
    ");

    return true;
}
