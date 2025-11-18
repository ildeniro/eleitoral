<li class="nav-item dropdown connection">
    <a class="nav-link" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="fas fa-fw fa-th text-primary"></i> </a>
    <ul class="dropdown-menu dropdown-menu-right connection-dropdown">
        <li class="connection-list">
            <div class="row">

                <div <?= ver_nivel(1) || ver_nivel(21) ? "" : "style='display: none;'"; ?> class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 ">
                    <a href="<?= PORTAL_URL; ?>view/apuracao" class="connection-item"><img src="<?= PORTAL_URL; ?>template/assets/images/img-svg/stationery-svgrepo-com.svg" alt=""> <span>Apuração</span></a>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 ">
                    <a href="<?= PORTAL_URL; ?>view/admin/dashboard" class="connection-item"><img src="<?= PORTAL_URL; ?>template/assets/images/img-svg/imac-like-desktop-pc-svgrepo-com.svg" alt=""> <span>Dashboard</span></a>
                </div>
                <div <?= ver_nivel(1) || ver_nivel(23) || ver_nivel(6) ? "" : "style='display: none;'"; ?> class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 ">
                    <a href="<?= PORTAL_URL; ?>view/admin/dashboard" class="connection-item"><img src="<?= PORTAL_URL; ?>template/assets/images/img-svg/network-marketing-svgrepo-com.svg" alt=""> <span>Indicadores</span></a>
                </div>
                <div <?= ver_nivel(1) || ver_nivel(6) ? "" : "style='display: none;'"; ?> class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 ">
                    <a href="<?= PORTAL_URL; ?>view/fiscal" class="connection-item"><img src="<?= PORTAL_URL; ?>template/assets/images/img-svg/clipboard-list-svgrepo-com.svg" alt=""><span>Fiscalização</span></a>
                </div>
                <div <?= ver_nivel(1) || ver_nivel(2) || ver_nivel(6) ? "" : "style='display: none;'"; ?> class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 ">
                    <a href="<?= PORTAL_URL; ?>view/usuarios" class="connection-item"><img src="<?= PORTAL_URL; ?>template/assets/images/img-svg/male-svgrepo-com.svg" alt=""> <span>Usuários</span></a>
                </div>

            </div>
        </li>
        <!-- <li>
            <div class="conntection-footer"><a href="#">Mais</a></div>
        </li> -->
    </ul>
</li>