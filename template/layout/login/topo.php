<?php
// VERIFICAÇÕES DE SESSÕES
if (isset($_SESSION['id'])) {
    echo "<script>window.location = '" . PORTAL_URL . "view/admin/dashboard';</script>";
    exit();
}
?>

<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title><?= TITULO; ?></title>
        
         <!-- Favicon -->
        <link rel="icon" href="<?= PORTAL_URL; ?>template/assets/images/intelivoto.jpeg">
        
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="<?= PORTAL_URL; ?>template/assets/vendor/bootstrap/css/bootstrap.min.css">
        <link href="<?= PORTAL_URL; ?>template/assets/libs/css/fonts/Poppins/style.css" rel="stylesheet">
        <link rel="stylesheet" href="<?= PORTAL_URL; ?>template/assets/libs/css/style.css">
        <link rel="stylesheet" href="<?= PORTAL_URL; ?>template/assets/vendor/fonts/fontawesome/css/fontawesome-all.css">
        <link rel="stylesheet" href="<?= PORTAL_URL; ?>template/assets/libs/css/login.css">
        <!-- Sweetalert --> 
        <link href="<?= PORTAL_URL; ?>template/assets/vendor/sweetalert/sweetalert2.min.css" rel="stylesheet" type="text/css">
    </head>

    <body>
        <canvas id="background-login"></canvas>
        <div class="splash-container">