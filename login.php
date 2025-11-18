<?php
include("template/layout/login/topo.php");
?>

<div class="card">
    <div class="card-header text-center">
        <a href="<?= PORTAL_URL; ?>index.html">
            <img class="logo-img" src="<?= PORTAL_URL; ?>template/assets/images/logo3.jpg" alt="logo">
        </a>
        <span class="splash-description"><?= TITULO ?></span>
    </div>
    <div class="card-body">
        <div class="container mb-3">
            <form id="form_login" name="form_login" method="post" action="#">
                <div id="div_login" class="form-group">
                    <input class="form-control form-control-lg" id="login" name="login" type="text" placeholder="Login" autocomplete="off" required="true" value="<?= isset($_COOKIE['siseleitorallogin']) && strlen($_COOKIE['siseleitorallogin']) > 1 ? $_COOKIE['siseleitorallogin'] : "" ?>">
                </div>
                <div id="div_senha" class="form-group">
                    <input class="form-control form-control-lg" id="senha" name="senha" type="password" placeholder="Senha" required="true" value="<?= isset($_COOKIE['siseleitoralsenha']) && strlen($_COOKIE['siseleitoralsenha']) > 1 ? $_COOKIE['siseleitoralsenha'] : "" ?>">
                </div>
                <div class="form-group">
                    <label class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox" id="login_check" name="login_check" <?= isset($_COOKIE['siseleitoralsenha']) && strlen($_COOKIE['siseleitoralsenha']) > 1 ? "checked='true'" : "" ?> value="1"><span class="custom-control-label">Lembrar-me</span>
                    </label>
                </div>
                <button type="submit" class="btn btn-brand btn-lg btn-block">Entrar</button>
            </form>
        </div>
    </div>
    <!--    <div class="card-footer bg-white p-0  ">
            <div class="card-footer-item card-footer-item-bordered">
                <a href="#" class="footer-link">Create An Account</a>
            </div>
            <div class="card-footer-item card-footer-item-bordered">
                <a href="#" class="footer-link">Forgot Password</a>
            </div>
        </div>-->
</div>
<?php
include("template/layout/login/rodape.php");
?>

<!-- JS DO LOGIN -->
<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/login.js"></script>