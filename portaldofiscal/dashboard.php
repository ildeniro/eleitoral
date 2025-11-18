<?php
include("template/layout/portaldofiscal/dashboard/topo.php");

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
    $usuario_endereco_local = $dados_usuario['DS_ENDERECO'];
    $usuario_bairro_local = $dados_usuario['NM_BAIRRO'];
    $usuario_regional_local = $dados_usuario['REGIONAL'];
}
?>

<div class="container col-xl-10 col-lg-12 mt-3">
    <div class="row">
        <div class="col-xl-12 col-lg-8 col-md-12 col-sm-12 col-12">
            <div class="influence-profile-content pills-regular">
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-campaign" role="tabpanel" aria-labelledby="pills-campaign-tab">
                        <div class="row">
                            <div class="col-xl-7 col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                                <div class="card pb-3">
								
                                    <div class="card-header <?= $dados_usuario_status == 0 ? "bg-aguardando-confirmacao" : ($dados_usuario_status == 1 ? "bg-confirmado" : "bg-desistencia") ?> d-flex">
                                        <div class="container mt-4 mb-1">
                                            <h3 class="text-light text-center"><?= $dados_usuario_status == 0 ? "INATIVO" : ($dados_usuario_status == 1 ? "PRESENÇA CONFIRMADA" : "DISPENSADO") ?> </h3>
                                        </div>
                                    </div>
									
                                    <!-- PARA VOLUNTÁRIO FISCAL -->
                                    <div <?= $_SESSION['voluntario_funcao'] == 5 && $dados_usuario_status != 5 ? "" : "style='display: none;'"; ?> class="card-body border-top p-2">
                                        <div class="container">
                                            <h4 class="text-center">Olá, <b><?= $usuario_nome; ?></b> (<b><?= mascararCPF($usuario_cpf); ?></b>)</h4>
                                            <p class="text-center">
                                                A Coligação Produzir para Empregar agradece
                                                profundamente a sua valiosa colaboração como <?= $usuario_sexo == 1 ? "voluntário" : "voluntária"; ?>,
                                                contribuindo com o futuro da nossa cidade.
                                            </p>
                                            <p class="text-center">
                                                Você foi <?= $usuario_sexo == 1 ? "designado" : "designada"; ?> para atuar na função de <b>FISCAL
                                                    na Seção</b> <b><?= $usuario_secao_numero_2; ?><?= retornar_agregacao($usuario_zona_2, $usuario_secao_numero_2); ?></b> da Zona <b><?= $usuario_zona_2; ?></b>, localizada no(a) <b><?= $usuario_local_votacao_2; ?></b>,
                                                no endereço <b><?= $usuario_endereco_local; ?></b>, Bairro <b><?= $usuario_bairro_local; ?></b>, Regional <b><?= $usuario_regional_local; ?></b>.
                                            </p>
                                            <p class="text-center">
                                                Nos próximos dias, a Coordenação da Regional <b><?= $usuario_regional_local; ?></b>
                                                entrará em contato para fornecer mais informações e entregar
                                                o Crachá de Fiscal, um item essencial que garante seu acesso
                                                à sala da seção eleitoral.
                                            </p>
                                            <!-- <p <?= $usuario_confirmacao == 0 ? "" : "style='display: none;'"; ?> class="text-center">
                                                Por favor, confirme sua presença na seção eleitoral
                                                através do campo "<b>Confirmação de Presença</b>" ao lado,
                                                indicando que estará disponível no horário combinado
                                                durante o Treinamento da Fiscalização.
                                            </p> -->
                                            <p class="text-center">
                                                Desejamos que você desempenhe um excelente trabalho,
                                                contribuindo para eleições justas e transparentes, onde
                                                o povo possa exercer sua vontade livremente e decidir o
                                                futuro de nossa capital com Tião Bocalom e Alysson Bestene.
                                            </p>
                                            <p class="text-center"> Atenciosamente,<br>Coordenação de Fiscalização do Dia "D"</p>
                                        </div>
                                    </div>

                                    <!-- PARA VOLUNTÁRIO SUPERVISOR DE LV -->
                                    <div <?= $_SESSION['voluntario_funcao'] == 4 && $dados_usuario_status != 5 ? "" : "style='display: none;'"; ?> class="card-body border-top p-2 ">
                                        <div class="container">
                                            <h4 class="text-center">Olá, <b><?= $usuario_nome; ?></b> (<b><?= mascararCPF($usuario_cpf); ?></b>)</h4>
                                            <p class="text-center">
                                                A Coligação Produzir para Empregar agradece profundamente
                                                a sua valiosa colaboração como voluntário(a), contribuindo
                                                com o futuro da nossa cidade.
                                            </p>
                                            <p class="text-center">
                                                Você foi designado(a) para atuar como
                                                <b>SUPERVISOR DE LOCAL DE VOTAÇÃO</b> no(a) <b><?= $usuario_local_votacao_2; ?></b>,
                                                no endereço <b><?= $usuario_endereco_local; ?></b>, Bairro <b><?= $usuario_bairro_local; ?></b>, Regional <b><?= $usuario_regional_local; ?></b>
                                            </p>
                                            <p class="text-center">
                                                Nos próximos dias, a Coordenação da Regional <b><?= $usuario_regional_local; ?></b> entrará em contato para fornecer mais informações
                                                e entregar o Crachá de Fiscal, um item essencial que garante seu acesso às
                                                salas das seções eleitorais do local de votação sob sua responsabilidade, bem como
                                                servirá de identificação para a equipe da coordenação.
                                            </p>
                                            <!-- <p class="text-center">
                                                Por favor, confirme sua presença no local de votação através do campo
                                                "<b>Confirmação de Presença</b>" ao lado, indicando que estará disponível
                                                no horário combinado durante o Treinamento da Fiscalização.
                                            </p> -->
                                            <p class="text-center">
                                                Desejamos que você desempenhe um excelente trabalho, contribuindo para
                                                eleições justas e transparentes, onde o povo possa exercer sua vontade
                                                livremente e decidir o futuro de nossa capital com Tião Bocalom e Alysson Bestene.
                                            </p>
                                            <p class="text-center"> Atenciosamente,<br>Coordenação de Fiscalização do Dia "D"</p>
                                        </div>
                                    </div>
                                    <!-- DISPENSA DE VOLUNTÁRIO -->
                                    <div <?= $dados_usuario_status == 5 ? "" : "style='display: none;'"; ?> class="card-body border-top p-2 pb-4">
                                        <div class="container">
                                            <h4 class="text-center">Olá, <b><?= $usuario_nome; ?></b> (<b><?= $usuario_cpf; ?></b>)</h4>
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

                                            <p class="text-center"> Atenciosamente,<br>Coordenação de Fiscalização do Dia "D"</p>
                                        </div>
                                    </div>
                                    <div class="col-xl-12 col-lg-8 col-md-12 col-sm-12 col-12">
                                        <div class="card-body text-center mt-3">
                                            <a class="btn btn btn-primary btn-sm" <?= $usuario_confirmacao == 2 ? "style='display: none;'" : ""; ?> href="<?= PORTAL_URL ?>portaldofiscal/pdf" target="_blank"> Imprimir PDF<i class="fas fa-fw fa-file-pdf fa-2x "></i></a>
                                        </div>								
                                        
                                        <div class="botao-dashboard card-body text-center pb-5">
                                            <a class="btn botao-sair" href="<?= PORTAL_URL ?>portaldofiscal/logout" target="_blank">Sair</a>
                                        </div>
                                    </div>
									
                                    <!-- <div class="card-body border-top mb-2 p-4">
                                        <h3 class="mb-2 text-brand">Contatos</h3>
                                        <div class="row">
                                            <div class="col-xl-4 mb-3" style="font-size: 12px;">
                                                <ul class="list-unstyled mb-0 mr-3 border-left-danger  pl-2 ">
                                                    <li>Nome: Gerson Correia Lima Neto</li>
                                                    <li> Coordenador Regional</li>
                                                    <li class="mb-2">Whatsapp: <?= $usuario_celular; ?></li>
                                                </ul>
                                            </div>
                                            <div <?= $_SESSION['voluntario_funcao'] == 5 ? "" : "style='display: none;'"; ?> class="col-xl-3 mb-3 " style="font-size: 12px;">
                                                <ul class="list-unstyled mb-0  mr-3 border-left-brand  pl-2">
                                                    <li> Nome: <?= contato_supervisor($usuario_local_votacao_2, $usuario_zona_2, $usuario_secao_numero_2, "nome"); ?></li>
                                                    <li>Supervisor local de votação</li>
                                                    <li class="mb-2">Whatsapp: <?= contato_supervisor($usuario_local_votacao_2, $usuario_zona_2, $usuario_secao_numero_2, "contato"); ?></li>
                                                </ul>
                                            </div>
                                            <div class="col-xl-5 mb-3">
                                                <ul class="list-unstyled mb-0 border-left-info  pl-2 " style="font-size: 12px;">
                                                    <li> Central de Apoio ao Fiscal</li>
                                                    <li class="mb-2">contato: (68) 99958-8453 | (68) 9224-8913 | (68) 99930-1671 | (68) 992377879 | (68) 98118-5522 | (68) 99228-6807 | (68) 99605-2792 | (68) 99978-7735</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div> -->
                                </div>

                            </div>
                            <div class="col-xl-5 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="card">
                                        <h3 class="card-header p-2">Informativo</h3>
                                        <div class="card-body">
                                            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                                                <!-- <ol class="carousel-indicators">
                                                    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                                                    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                                                    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                                                    <li data-target="#carouselExampleIndicators" data-slide-to="3"></li>
                                                    <li data-target="#carouselExampleIndicators" data-slide-to="4"></li>
                                                    <li data-target="#carouselExampleIndicators" data-slide-to="5"></li>
                                                    <li data-target="#carouselExampleIndicators" data-slide-to="6"></li>
                                                    <li data-target="#carouselExampleIndicators" data-slide-to="7"></li>
                                                    <li data-target="#carouselExampleIndicators" data-slide-to="8"></li>
                                                    <li data-target="#carouselExampleIndicators" data-slide-to="9"></li>
                                                    <li data-target="#carouselExampleIndicators" data-slide-to="10"></li>
                                                    <li data-target="#carouselExampleIndicators" data-slide-to="11"></li>
                                                    <li data-target="#carouselExampleIndicators" data-slide-to="12"></li>
                                                </ol> -->
                                                <div class="carousel-inner">
                                                    <div class="carousel-item active">
                                                        <img class="d-block w-100" src="<?= PORTAL_URL; ?>template/assets/images/informativo/informativo_1.png" alt="First slide">
                                                    </div>
                                                    <div class="carousel-item">
                                                        <img class="d-block w-100" src="<?= PORTAL_URL; ?>template/assets/images/informativo/informativo_2.png" alt="Second slide">
                                                    </div>
                                                    <div class="carousel-item">
                                                        <img class="d-block w-100" src="<?= PORTAL_URL; ?>template/assets/images/informativo/informativo_3.png" alt="Third slide">
                                                    </div>
                                                    <div class="carousel-item">
                                                        <img class="d-block w-100" src="<?= PORTAL_URL; ?>template/assets/images/informativo/informativo_4.png" alt="Third slide">
                                                    </div>
                                                    <div class="carousel-item">
                                                        <img class="d-block w-100" src="<?= PORTAL_URL; ?>template/assets/images/informativo/informativo_5.png" alt="Third slide">
                                                    </div>
                                                    <div class="carousel-item">
                                                        <img class="d-block w-100" src="<?= PORTAL_URL; ?>template/assets/images/informativo/informativo_6.png" alt="Third slide">
                                                    </div>
                                                    <div class="carousel-item">
                                                        <img class="d-block w-100" src="<?= PORTAL_URL; ?>template/assets/images/informativo/informativo_7.png" alt="Third slide">
                                                    </div>
                                                    <div class="carousel-item">
                                                        <img class="d-block w-100" src="<?= PORTAL_URL; ?>template/assets/images/informativo/informativo_8.png" alt="Third slide">
                                                    </div>
                                                    <div class="carousel-item">
                                                        <img class="d-block w-100" src="<?= PORTAL_URL; ?>template/assets/images/informativo/informativo_9.png" alt="Third slide">
                                                    </div>
                                                    <div class="carousel-item">
                                                        <img class="d-block w-100" src="<?= PORTAL_URL; ?>template/assets/images/informativo/informativo_10.png" alt="Third slide">
                                                    </div>
                                                    <div class="carousel-item">
                                                        <img class="d-block w-100" src="<?= PORTAL_URL; ?>template/assets/images/informativo/informativo_11.png" alt="Third slide">
                                                    </div>
                                                </div>
                                                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                    <span class="sr-only">Anterior</span> </a>
                                                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                    <span class="sr-only">Próximo</span> </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 pt-3">
                                    <div class="card">
                                        <h3 class="card-header p-2">Contatos</h3>
                                        <div class="card-body border-top p-4">
                                            <h3 class="mb-2 text-brand">Supervisor</h3>
                                            <div class="row">
                                                <!--<div class="col-xl-12 mb-3" style="font-size: 12px;">
                                                    <ul class="list-unstyled mb-0 mr-3 border-left-danger  pl-2 ">
                                                        <li></li>
                                                        <li> Coordenador Regional</li>
                                                        <li class="mb-2">Whatsapp:</li>
                                                    </ul>
                                                </div>-->
                                                <div <?= $_SESSION['voluntario_funcao'] == 5 ? "" : "style='display: none;'"; ?> class="ol-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mb-3 " style="font-size: 12px;">
                                                    <ul class="list-unstyled mb-0  mr-3 border-left-brand  pl-2">
                                                        <li><b style="font-size: 16px;"><?= contato_supervisor($usuario_local_votacao_id_2, $usuario_zona_2, $usuario_secao_numero_2, "nome"); ?></b></li>
                                                        <li style="font-size: 14px;">Local: <?= ctexto($usuario_local_votacao_2, "pri"); ?></li>
                                                        <li class="mb-2" style="font-size: 14px;">Whatsapp: <?= contato_supervisor($usuario_local_votacao_id_2, $usuario_zona_2, $usuario_secao_numero_2, "contato"); ?></li>
                                                    </ul>
                                                </div>
                                                <!-- <div class="ol-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                                                    <ul class="list-unstyled mb-0 border-left-info  pl-2 " style="font-size: 12px;">
                                                        <li> Central de Apoio ao Fiscal</li>
                                                        <li class="mb-2">contato: (68) 99958-8453 | (68) 9224-8913 | (68) 99930-1671 | (68) 992377879 | (68) 98118-5522 | (68) 99228-6807 | (68) 99605-2792 | (68) 99978-7735</li>
                                                    </ul>
                                                </div> -->
                                            </div>
											<div class="row card-body p-3">
												<ul class="list-unstyled mb-0 border-left-info pl-2">
													<h3 class="mb-2 text-brand"> Central de Apoio ao Fiscal</h3>
													<li class="mb-2" style="font-size: 13px;">Contato: (68) 99958-8453 | (68) 9224-8913 | (68) 99930-1671 | (68) 992377879 | (68) 98118-5522 | (68) 99228-6807 | (68) 99605-2792 | (68) 99978-7735</li>
												</ul>
											</div>
                                        </div>
										
										
										
                                    </div>
                                </div>
                                <!-- ============================================================== -->
                                <!-- end slides with indicator  -->
                                <!-- ============================================================== -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div <?= $usuario_confirmacao == 0 ? "" : "style='display: none;'"; ?> class="col-xl-3 col-lg-4 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-body bg-personalizado">

                    <h3 class="font-24 mb-0 text-light"><?= ctexto($_SESSION['voluntario_nome'], 'mai'); ?> <i class="fa fa-check" style="color: green; font-size: 30px; <?= $usuario_confirmacao == 1 ? "" : "display: none;'"; ?>"></i><i class="fa fa-times-circle" style="color: red; font-size: 30px; <?= $usuario_confirmacao == 2 ? "" : "display: none;'"; ?>"></i></h3>
                        <p class="text-light"><?= pesquisar("nome", "sys_funcoes", "id", "=", $_SESSION['voluntario_funcao'], ""); ?></p> 
                    <h5 class="mb-0 text-light"> Confirmação de presença</h5>

                </div>
                <p class="p-2 text-center">Informe nossa equipe que você estará presente no local designado e no horário programado no dia da eleição.</p>
                <div class="card-body border-top text-center">
                    <button type="button" id="confirmar_presenca" class="btn btn-outline-success float-center btn-sm"><i class="fas fa-fw fa-check mr-1 "></i>Confirmar </button>
                     <button type="button" id="cancelar_confirmacao" class="btn btn-outline-danger float-center btn-sm"><i class="fas fa-fw fa-times-circle mr-1 "></i>Desistir</button>
                </div>
            </div>
        </div> -->
    </div>
</div>

<?php
include("template/layout/portaldofiscal/dashboard/rodape.php");
?>

<!-- JS DO LOGIN -->
<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/portaldofiscal/dashboard.js"></script>