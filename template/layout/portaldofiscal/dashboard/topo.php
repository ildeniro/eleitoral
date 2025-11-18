<?php
// VERIFICAÇÕES DE SESSÕES
if (!isset($_SESSION['voluntario_id'])) {
    echo "<script>window.location = '" . PORTAL_URL . "portaldofiscal/logout';</script>";
    exit();
}
?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= PORTAL_URL; ?>template/assets/vendor/bootstrap/css/bootstrap.min.css">
    <link href="<?= PORTAL_URL; ?>template/assets/libs/css/fonts/Poppins/style.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= PORTAL_URL; ?>template/assets/libs/css/style.css">
    <link rel="stylesheet" href="<?= PORTAL_URL; ?>template/assets/vendor/fonts/fontawesome/css/fontawesome-all.css">

    <!-- Sweetalert -->
    <link href="<?= PORTAL_URL; ?>template/assets/vendor/sweetalert/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <title><?= TITULO_PORTAL; ?></title>
    <!-- Favicon -->
    <link rel="icon" href="<?= PORTAL_URL; ?>template/assets/images/intelivoto.jpeg">

    <style>
        html,
        body {
            height: 100%;

        }

        body {

            background: url('<?= PORTAL_URL; ?>template/assets/images/bg-bocalom/bg-partido.jpeg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        .overlay {
            position: absolute;
            /* Para garantir que o overlay cubra toda a tela */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(101, 101, 101, 0.25);
            /* Transparência aplicada sobre a imagem */
            z-index: 0;
            /* Coloca o overlay acima do background */
        }
    </style>
</head>

<body>
    <?php
    include("template/layout/portaldofiscal/dashboard/navbar.php");
    ?>
    <div class="overlay"></div>
    <div class=" dashboard-main-wrapper">
        <div class="dashboard-wrapper">
            <div class="dashboard-finance">
                <div class="container-fluid dashboard-content">