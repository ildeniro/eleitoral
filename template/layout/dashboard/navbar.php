<div class="dashboard-header">
    <nav class="navbar navbar-expand-lg bg-white fixed-top">
        <a class="navbar-brand" href="<?= PORTAL_URL; ?>view/admin/dashboard"><?= SUBTITULO ?></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon "></span>
        </button>
        
        <div class="collapse navbar-collapse " id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto navbar-right-top">
<!--                <li class="nav-item">
                    <div id="custom-search" class="top-search-bar">
                        <input class="form-control" id="pesquisa" name="pesquisa" type="text" placeholder="Pesquisar..">
                    </div>
                </li>-->
                <?php
                //include("template/layout/dashboard/notificacoes.php");
                include("template/layout/dashboard/menu.php");
                ?>
                <li class="nav-item">
                <a class="nav-link" href="<?= PORTAL_URL; ?>logout"><i class="fas fa-power-off mr-2 text-primary"></i><span class="text-primary">Sair</span></a>
                </li>
                <!-- <li class="nav-item dropdown nav-user">
                    <a class="dropdown-item" href="<?= PORTAL_URL; ?>logout"><i class="fas fa-power-off mr-2"></i>Sair</a>
                    <div class="dropdown-menu dropdown-menu-right nav-user-dropdown" aria-labelledby="navbarDropdownMenuLink2">
                        <div class="nav-user-info">
                            <h5 class="mb-0 text-white nav-user-name"><?= $_SESSION['nome']; ?> </h5>
                            <span class="status"></span><span class="ml-2">Available</span>
                        </div>
                        <a class="dropdown-item" href="<?= PORTAL_URL; ?>view/usuarios/cadastro/<?= $_SESSION['id']; ?>"><i class="fas fa-user mr-2"></i>Perfil</a>
                        <a class="dropdown-item" href="#"><i class="fas fa-cog mr-2"></i>Setting</a>
                        <a class="dropdown-item" href="<?= PORTAL_URL; ?>logout"><i class="fas fa-power-off mr-2"></i>Sair</a>
                    </div>
                </li> -->
            </ul>
        </div>
    </nav>
</div>