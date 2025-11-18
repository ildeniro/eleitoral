<?php
// VERIFICAÇÕES DE SESSÕES
if (isset($_SESSION['voluntario_id'])) {
    echo "<script>window.location = '" . PORTAL_URL . "portaldofiscal/dashboard';</script>";
    exit();
}
?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= TITULO_PORTAL; ?></title>

    <!-- Favicon -->
    <link rel="icon" href="<?= PORTAL_URL; ?>template/assets/images/intelivoto.jpeg">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= PORTAL_URL; ?>template/assets/vendor/bootstrap/css/bootstrap.min.css">
    <link href="<?= PORTAL_URL; ?>template/assets/libs/css/fonts/Poppins/style.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= PORTAL_URL; ?>template/assets/libs/css/style.css">
    <link rel="stylesheet" href="<?= PORTAL_URL; ?>template/assets/libs/css/style-alt.css">
    <link rel="stylesheet" href="<?= PORTAL_URL; ?>template/assets/vendor/fonts/fontawesome/css/fontawesome-all.css">

    <!-- Sweetalert -->
    <link href="<?= PORTAL_URL; ?>template/assets/vendor/sweetalert/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <style>
        html,
        body {
            height: 100%;

        }

        body {
            display: -ms-flexbox;
            display: flex;
            -ms-flex-align: center;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background: url('<?= PORTAL_URL; ?>template/assets/images/bg-bocalom/bg-bocalom.jpeg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }


        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 17, 0.1);
            z-index: 0;

        }

    </style>
</head>

<body>

    <div class="overlay"></div>
    <div class="splash-container">