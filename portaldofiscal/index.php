<?php
include("template/layout/portaldofiscal/acesso/topo.php");
?>

<div class="card">
    <div class="card-header text-center">
        <img class="logo-img pb-3" src="<?= PORTAL_URL; ?>template/assets/images/logo_portal_do_fiscal.png" alt="logo">
        <!-- <h2 style="color: #062261; font-weight: 600;"><?= TITULO_PORTAL; ?></h2> -->
        <p>Portal de informações para o Voluntário de Fiscalização da Coligação Produzir para Empregar.</p>
    </div>
    <div class="card-body">
        <div class="container pt-3 pb-4">
            <form id="form_login" name="form_login" method="post" action="#">
                <div class="form-group">
                    <input class="form-control form-control-lg" type="text" id="cpf" name="cpf" data-mask="999.999.999-99" required placeholder="Informe seu CPF" autocomplete="off">
                </div>
                <div class="form-group">
                    <input class="form-control form-control-lg" type="text" data-mask="99/99/9999" id="data_nascimento" name="data_nascimento" placeholder="Informe a data de nascimento" required>
                </div>
                <div class="form-group pt-1">
                    <button type="submit" class="btn btn-block btn-personalizado btn-xl">Acessar</button>
                </div>
            </form>
        </div>
        
            <div class="col-xl-12 mb-3">
                <ul class="list-unstyled mb-0  p-2">
                    <li class="text-center" style="font-size: 14px;">Central de Apoio ao Fiscal</li>
                    <li><img src="<?= PORTAL_URL; ?>template/assets/images/whatsapp.png" alt="" style="width: 20px; height: 20px;"> whatsapp : (68) 99930-1671 | (68) 99228-6807 </li>
                </ul>
            </div>
   
    </div>
</div>


<?php
include("template/layout/portaldofiscal/acesso/rodape.php");
?>

<!-- JS DO LOGIN -->
<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/portaldofiscal/index.js"></script>