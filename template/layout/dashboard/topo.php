<?php
// VERIFICAÇÕES DE SESSÕES
if (!isset($_SESSION['id'])) {
    echo "<script>window.location = '" . PORTAL_URL . "logout';</script>";
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
        <link rel="stylesheet" href="<?= PORTAL_URL; ?>template/assets/libs/css/style-alt.css">
        <link rel="stylesheet" href="<?= PORTAL_URL; ?>template/assets/vendor/fonts/fontawesome/css/fontawesome-all.css">
        <link rel="stylesheet" href="<?= PORTAL_URL; ?>template/assets/vendor/fonts/material-design-iconic-font/css/materialdesignicons.min.css">
        <link rel="stylesheet" href="<?= PORTAL_URL; ?>template/assets/vendor/charts/chartist-bundle/chartist.css">
        <link rel="stylesheet" href="<?= PORTAL_URL; ?>template/assets/vendor/charts/morris-bundle/morris.css">
        <link rel="stylesheet" href="<?= PORTAL_URL; ?>template/assets/vendor/charts/c3charts/c3.css">
        <link rel="stylesheet" href="<?= PORTAL_URL; ?>template/assets/vendor/fonts/flag-icon-css/flag-icon.min.css">
        <link rel="stylesheet" href="<?= PORTAL_URL; ?>template/assets/vendor/vector-map/jqvmap.css">
        <link href="<?= PORTAL_URL; ?>template/assets/vendor/jvectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="<?= PORTAL_URL; ?>template/assets/vendor/daterangepicker/daterangepicker.css" />
        <link rel="stylesheet" type="text/css" href="<?= PORTAL_URL; ?>template/assets/vendor/multi-select/css/multi-select.css" />

        <!-- Select2 -->
        <link href="<?= PORTAL_URL; ?>template/assets/vendor/select2/css/select2.css" rel="stylesheet" type="text/css" />
        <!-- Sweetalert -->
        <!-- Font POPS -->
        <link href="<?= PORTAL_URL; ?>template/assets/vendor/sweetalert/sweetalert2.min.css" rel="stylesheet" type="text/css">

        <!-- DataTable -->
        <link href="<?= PORTAL_URL; ?>template/assets/vendor/datatables/css/dataTables.bootstrap4.css" rel="stylesheet" type="text/css">
        <link href="<?= PORTAL_URL; ?>template/assets/vendor/datatables/css/buttons.bootstrap4.css" rel="stylesheet" type="text/css">

       
        <title><?= TITULO; ?></title>

        <!-- Favicon -->
        <link rel="icon" href="<?= PORTAL_URL; ?>template/assets/images/intelivoto.jpeg">
    </head>

    <body>
        <?php
        include("template/layout/dashboard/navbar.php");
        ?>
        <div class=" dashboard-main-wrapper">
            <div class="dashboard-wrapper">
                <div class="dashboard-finance">
                    <div class="container-fluid dashboard-content">