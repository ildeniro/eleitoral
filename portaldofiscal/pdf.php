<?php

require 'C:/xampp8/htdocs/siseleitoral/vendor/autoload.php'; // Carrega o autoload do Composer

if (pesquisar("status", "2024_voluntarios", "id", "=", $_SESSION['voluntario_id'], "") == 5) {
    $result = $db->prepare("SELECT *   
                 FROM 2024_voluntarios v
                 WHERE v.id = ?
                 GROUP BY v.id");
    $result->bindValue(1, $_SESSION['voluntario_id']);
    $result->execute();
    $dados_usuario = $result->fetch(PDO::FETCH_ASSOC);

    $usuario_id = $dados_usuario['id'];
    $usuario_nome = $dados_usuario['nome'];
    $usuario_sexo = $dados_usuario['sexo'];
    $usuario_cpf = $dados_usuario['cpf'];
    $usuario_celular = $dados_usuario['celular'];
    $usuario_nascimento = $dados_usuario['nascimento'];
    $usuario_mae = $dados_usuario['nm_mae'];

    $usuario_confirmacao = $dados_usuario['confirmacao'];
    $usuario_motivo_desconfirmacao = $dados_usuario['motivo_desconfirmacao'];

    $dados_usuario_status = $dados_usuario['status'];

    $usuario_zona_2 = "";
    $usuario_secao_numero_2 = "";
    $usuario_local_votacao_id_2 = "";
    $usuario_local_votacao_2 = "";
    $usuario_endereco_local = "";
    $usuario_bairro_local = "";
    $usuario_regional_local = "";
} else {
    $result = $db->prepare("SELECT v.status, s.LOCAL_VOTACAO_ID, v.confirmacao, v.motivo_desconfirmacao, v.id, v.nome, v.sexo, v.cpf, v.celular, v.nascimento, v.nm_mae, v.zona_2, v.secao_numero_2, lv.NM_LOCAL_VOTACAO,
                 lv.DS_ENDERECO, lv.NM_BAIRRO, r.nome AS REGIONAL   
                 FROM 2024_voluntarios v
                 INNER JOIN 2024_secoes AS s ON s.NR_ZONA = v.zona_2 AND s.NR_SECAO = v.secao_numero_2 
                 INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID 
                 INNER JOIN sys_regionais AS r ON r.id = lv.REGIONAL_ID 
                 WHERE v.id = ?
                 GROUP BY v.id");
    $result->bindValue(1, $_SESSION['voluntario_id']);
    $result->execute();
    $dados_usuario = $result->fetch(PDO::FETCH_ASSOC);

    $usuario_id = $dados_usuario['id'];
    $usuario_nome = $dados_usuario['nome'];
    $usuario_sexo = $dados_usuario['sexo'];
    $usuario_cpf = $dados_usuario['cpf'];
    $usuario_celular = $dados_usuario['celular'];
    $usuario_nascimento = $dados_usuario['nascimento'];
    $usuario_mae = $dados_usuario['nm_mae'];

    $usuario_confirmacao = $dados_usuario['confirmacao'];
    $usuario_motivo_desconfirmacao = $dados_usuario['motivo_desconfirmacao'];

    $dados_usuario_status = $dados_usuario['status'];

    $usuario_zona_2 = $dados_usuario['zona_2'];
    $usuario_secao_numero_2 = $dados_usuario['secao_numero_2'];
    $usuario_local_votacao_id_2 = $dados_usuario['LOCAL_VOTACAO_ID'];
    $usuario_local_votacao_2 = $dados_usuario['NM_LOCAL_VOTACAO'];
    $usuario_endereco_local = ctexto($dados_usuario['DS_ENDERECO'], "pri");
    $usuario_bairro_local = ctexto($dados_usuario['NM_BAIRRO'], "pri");
    $usuario_regional_local = ctexto($dados_usuario['REGIONAL'], "pri");
}

use Mpdf\Mpdf;

try {
    // Cria uma instância do mPDF
    $mpdf = new Mpdf(['debug' => true]);
    $html = '<html lang="en" debug="true">
                <head>
                    <link rel="stylesheet" href="' . PORTAL_URL . 'portaldofiscal/style.css">
                    <link href="<?= PORTAL_URL; ?>template/assets/libs/css/fonts/Poppins/style.css" rel="stylesheet">
                    <title>Portal de Fiscalização Eleições 2024</title>
                </head>
                <body>
                    <div class="row">
                        <div class="container">
                            <div class="titule-header text-center">
                                <div class="logo">
                                    <img src="' . PORTAL_URL . 'portaldofiscal/s2.png" alt="" style="width: 90px; height: 90px;">
                                    <br>
                                    <b style="font-size: 24px; color: #525252;  margin-bottom: 30px;">Coligação Produzir para Empregar</b>
                                    <p style="font-size: 14px; color: #2d2d2e;  margin-bottom: -5px; margin-top: -5x">PP / PL / UNIÃO / PODE / Federação PSDB CIDADANIA(PSDB/CIDADANIA) / SOLIDARIEDADE</p>
                                </div>
                                <h2 class="titulo">PORTAL DO FISCAL</h2>
                            </div>

                            <div class="card">
                                <div class="card-header text-center">
                                    <h3 class="titulo-card" style="font-weight: 400; font-size: 20px;">' . ($usuario_confirmacao == 0 ? "AGUARDANDO CONFIRMAÇÃO DE PRESENÇA" : ($usuario_confirmacao == 1 ? "PRESENÇA CONFIRMADA" : "DESISTÊNCIA")) . '</h3>
                                </div>';

    if ($_SESSION['voluntario_funcao'] == 5 && $dados_usuario_status != 5) {
        $html .= '<div class="card-body">
                                        <p class="text-center" style="margin-bottom: 0;">Olá, <b style="font-size: 22px; line-height: 1;">' . $usuario_nome . '</b></p>
                                        <p style="font-size: 12px; text-align: center; margin-top: -8px;">(' . mascararCPF($usuario_cpf) . ')</p>
                                        <p class="text-center">
                                            A Coligação Produzir para Empregar agradece
                                            profundamente a sua valiosa colaboração como ' . ($usuario_sexo == 1 ? "voluntário" : "voluntária") . ',
                                            contribuindo com o futuro da nossa cidade.
                                        </p>
                                        <p class="text-center">
                                            Você foi ' . ($usuario_sexo == 1 ? "designado" : "designada") . ' para atuar na função de FISCAL
                                            na Seção <b>' . $usuario_secao_numero_2 . retornar_agregacao($usuario_zona_2, $usuario_secao_numero_2) . '</b> da Zona <b>' . $usuario_zona_2 . '</b> , localizada no(a) <b>' . $usuario_local_votacao_2 . '</b>,
                                            no endereço <b>' . $usuario_endereco_local . '</b>, Bairro <b>' . $usuario_bairro_local . '</b>, Regional <b>' . $usuario_regional_local . '</b>.
                                        </p>
                                        <p class="text-center">
                                            Nos próximos dias, a Coordenação da Regional <b>' . $usuario_regional_local . '</b>
                                            entrará em contato para fornecer mais informações e entregar
                                            o <b>Crachá de Fiscal</b>, item essencial que garante seu acesso
                                            à sala da seção eleitoral.
                                        </p>
                                        <p class="text-center">
                                            Desejamos que você desempenhe um excelente trabalho,
                                            contribuindo para eleições justas e transparentes, onde
                                            o povo possa exercer sua vontade livremente e decidir o
                                            futuro de nossa capital com Tião Bocalom e Alysson Bestene.
                                        </p>
                                        <p class="text-center"> Atenciosamente, <br><b>Coordenação de Fiscalização do Dia "D"</b></p>
                                </div>';
    } else if ($_SESSION['voluntario_funcao'] == 4 && $dados_usuario_status != 5) {
        $html .= '<div class="card-body">
                                        <p class="text-center" style="margin-bottom: 0;">Olá, <b style="font-size: 22px; line-height: 1;">' . $usuario_nome . '</b></p>
                                        <p style="font-size: 12px; text-align: center; margin-top: -8px;">(' . mascararCPF($usuario_cpf) . ')</p>

                                        <p class="text-center style="letter-spacing: 0.8px;"">
                                            A Coligação Produzir para Empregar agradece
                                            profundamente a sua valiosa colaboração como ' . ($usuario_sexo == 1 ? "voluntário" : "voluntária") . ',
                                            contribuindo com o futuro da nossa cidade.
                                        </p>
                                        <p class="text-center style="letter-spacing: 0.8px;"">
                                            Você foi ' . ($usuario_sexo == 1 ? "designado" : "designada") . '  para atuar como
                                             Supervisor de Local de Votação no(a) <b>' . $usuario_local_votacao_2 . '</b>,
                                            no endereço <b>' . $usuario_endereco_local . '</b>, Bairro <b>' . $usuario_bairro_local . '</b>, Regional <b>' . $usuario_regional_local . '</b>.
                                        </p>
                                        <p class="text-center style="letter-spacing: 0.8px;"">
                                            Nos próximos dias, a Coordenação da Regional <b>' . $usuario_regional_local . '</b>
                                            entrará em contato para fornecer mais informações e entregar
                                            o <b>Crachá de Fiscal</b>, item essencial que garante seu acesso
                                            à sala da seção eleitoral.
                                        </p>
                                        <p class="text-center style="letter-spacing: 0.8px;"">
                                            Desejamos que você desempenhe um excelente trabalho,
                                            contribuindo para eleições justas e transparentes, onde
                                            o povo possa exercer sua vontade livremente e decidir o
                                            futuro de nossa capital com Tião Bocalom e Alysson Bestene.
                                        </p>
                                        <p class="text-center"> Atenciosamente, <br><b>Coordenação de Fiscalização do Dia "D"</b></p>
                                </div>';
    } else if ($dados_usuario_status == 5) {
        $html .= '<div class="card-body">
                                        <p class="text-center" style="margin-bottom: 0;">Olá, <b style="font-size: 22px; line-height: 1;">' . $usuario_nome . '</b></p>
                                        <p style="font-size: 12px; text-align: center; margin-top: -8px;">(' . mascararCPF($usuario_cpf) . ')</p>
                                        <p class="text-center">
                                            A Coligação Produzir para Empregar agradece sinceramente
                                            sua valiosa intenção de colaborar na construção de um futuro
                                            melhor para nossa cidade. Infelizmente, desta vez não conseguimos
                                            alocar um local para você atuar como fiscal.
                                        </p>
                                        <p class="text-center">
                                                Mas não desanime! Sua participação ainda é muito importante.
                                                No dia da eleição, manifeste seu apoio de forma silenciosa:
                                                vá votar vestindo azul, use seu adesivo e leve sua bandeira
                                                com orgulho.
                                        </p>
                                        <p class="text-center">
                                              Depois, reúna-se com a nossa equipe e
                                              celebre com alegria a apuração dos resultados.
                                              Estamos juntos nessa!
                                        </p>
                                        <p class="text-center"> Atenciosamente, <br><b>Coordenação de Fiscalização do Dia "D"</b></p>
                                </div>';
    }

    $html .= '<div class="card-footer text-center" style="padding: 20px; margin-bottom: 15px; font-size: 13px;">
                                    Central de Apoio ao Fiscal  <img src="' . PORTAL_URL . 'portaldofiscal/whatsapp.png" alt="" style="width: 16px; height: 16px; margin: 5px 2px  0px 10px;"> Contato (68) 9 9999-9999 | (68) 9 9999-9999 
                                </div>
                            </div>
                        </div>
                    </div>
                </body>
                </html>';

    // Escreve o HTML no PDF
    $mpdf->WriteHTML($html);
    $mpdf->Output();
    // Saída do PDF para o navegador
    $mpdf->Output('exemplo.pdf', 'I'); // 'I' para abrir no navegador, 'D' para forçar download
} catch (\Mpdf\MpdfException $e) {
    // Se ocorrer um erro, exibe a mensagem de erro
    echo $e->getMessage();
}
?>
