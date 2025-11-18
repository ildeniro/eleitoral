<?php
include("template/layout/dashboard/topo.php");
?>

<?php
if (!ver_nivel(1) && !ver_nivel(3)) {
    msg('Você não possui permissão para acessar essa área.');
    url(PORTAL_URL . 'view/admin/dashboard');
}
?>

<style type="text/css">
    .num44{
        fill:#F9A003;
        stroke:#3E3E3D;
        color:#F9A003;
        stroke-width:2;
        stroke-linecap:round;
        stroke-linejoin:round;
        stroke-miterlimit:10;
    } /* JARUDE */
    .num50{
        fill:#01489e;
        stroke:#3E3E3D;
        color:#01489e;
        stroke-width:2;
        stroke-linecap:round;
        stroke-linejoin:round;
        stroke-miterlimit:10;
    } /* TIÃO BOCALOM */
    .num15{
        fill:#1c9703;
        stroke:#7ecc6e;
        color:#1c9703;
        stroke-width:2;
        stroke-linecap:round;
        stroke-linejoin:round;
        stroke-miterlimit:10;
    } /* MARCUS ALEXANDRE */
    .num36{
        fill: #F05C55;
        stroke: #DD7F7A;
        color: #F05C55;
        stroke-width:2;
        stroke-linecap:round;
        stroke-linejoin:round;
        stroke-miterlimit:10;
    } /* DR. JENILSON' */
    .st1{
        fill:#ffffff;
        stroke:#3E3E3D;
        stroke-width:2;
        stroke-linecap:round;
        stroke-linejoin:round;
        stroke-miterlimit:10;
    } /* TESTE */
</style>

<div class="row">
    <div class="container col-xl-12 col-lg-12">
        <div class="row">
            <div class="container col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="col-xl-10">
                    <!-- ============================================================== -->
                    <!-- pageheader -->
                    <!-- ============================================================== -->
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="page-header" id="top">
                                <h2 class="pageheader-title">Bairros</h2>
                                <div class="page-breadcrumb">
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/admin/dashboard" class="breadcrumb-link-alt">Início</a></li>
                                            <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/apuracao" class="breadcrumb-link-alt">Apuração</a></li>
                                            <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>view/apuracao/resultado-apuracao" class="breadcrumb-link-alt">Resultados</a></li>
                                            <li class="breadcrumb-item active" aria-current="page">Bairros</li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ============================================================== -->
                    <!-- end pageheader  -->
                    <!-- ============================================================== -->
                </div>

                <?php
                if (isset($_POST['guard_22'])) {
                    if ($_POST['guard_22'] == 1) {
                        $reg_22 = 1;
                    } else {
                        $reg_22 = 0;
                    }
                } else {
                    $reg_22 = 1;
                }

                if (isset($_POST['guard_15'])) {
                    if ($_POST['guard_15'] == 1) {
                        $reg_15 = 1;
                    } else {
                        $reg_15 = 0;
                    }
                } else {
                    $reg_15 = 1;
                }

                if (isset($_POST['guard_30'])) {
                    if ($_POST['guard_30'] == 1) {
                        $reg_30 = 1;
                    } else {
                        $reg_30 = 0;
                    }
                } else {
                    $reg_30 = 1;
                }

                if (isset($_POST['guard_40'])) {
                    if ($_POST['guard_40'] == 1) {
                        $reg_40 = 1;
                    } else {
                        $reg_40 = 0;
                    }
                } else {
                    $reg_40 = 1;
                }
                ?>

                <div class="card">
                    <div class="card-body">
                        <div class="container" style="max-width: 90%;">
                            <div class="content-wrapper">

                                <link rel="stylesheet" href="<?= PORTAL_URL; ?>template/assets/libs/css/apuracao_style.css">
                                <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/webrtc-adapter/3.3.3/adapter.min.js"></script>
                                <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.1.10/vue.min.js"></script>

                                <form id="form_check" namme="form_check" action="#" method="POST">

                                    <input type="hidden" id="guard_22" name="guard_22" value="<?= $reg_22; ?>"/>
                                    <input type="hidden" id="guard_30" name="guard_30" value="<?= $reg_30; ?>"/>
                                    <input type="hidden" id="guard_15" name="guard_15" value="<?= $reg_15; ?>"/>
                                    <input type="hidden" id="guard_40" name="guard_40" value="<?= $reg_40; ?>"/>

                                    <div class="lista_candidatos">
                                        <h1>CANDIDATOS</h1>
                                        <ul>
                                            <li>
                                                <div class="form-check">
                                                    <input onclick="enviar_bairros()" id="reg_22" name="reg_22" class="form-check-input" <?= $reg_22 == 1 ? "checked='true'" : ""; ?> type="checkbox" value="1">
                                                    <label class="form-check-label">
                                                        TIÃO BOCALOM
                                                        <span class="form-check-sign">
                                                            <span class="check"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="form-check">
                                                    <input onclick="enviar_bairros()" id="reg_15" name="reg_15" class="form-check-input" <?= $reg_15 == 1 ? "checked='true'" : ""; ?> type="checkbox" value="1">
                                                    <label class="form-check-label">
                                                        MARCUS ALEXANDRE
                                                        <span class="form-check-sign">
                                                            <span class="check"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="form-check">
                                                    <input onclick="enviar_bairros()" id="reg_30" name="reg_30" class="form-check-input" <?= $reg_30 == 1 ? "checked='true'" : ""; ?> type="checkbox" value="1">
                                                    <label class="form-check-label">
                                                        JARUDE
                                                        <span class="form-check-sign">
                                                            <span class="check"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="form-check">
                                                    <input onclick="enviar_bairros()" id="reg_40" name="reg_40" class="form-check-input" <?= $reg_40 == 1 ? "checked='true'" : ""; ?> type="checkbox" value="1">
                                                    <label class="form-check-label">
                                                        DR. JENILSON 
                                                        <span class="form-check-sign">
                                                            <span class="check"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </li>                   
                                        </ul>
                                    </div>
                                </form>

                                <div class="zona_rural">
                                    <h1>ZONA RURAL</h1>
                                    <ul>
                                        <li class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 12); ?>"><a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="12"><i class="fas fa-circle"></i> BAIXA VERDE</a></li>
                                        <li class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 74); ?>"><a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="74"><i class="fas fa-circle"></i> CAIPORA</a></li>
                                        <li class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 39); ?>"><a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="39"><i class="fas fa-circle"></i> DOM MOACYR</a></li>
                                        <li class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 72); ?>"><a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="72"><i class="fas fa-circle"></i> QUIXADA</a></li>
                                        <li class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 83); ?>"><a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="83"><i class="fas fa-circle"></i> SANTA MARIA</a></li>
                                        <li class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 79); ?>"><a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="79"><i class="fas fa-circle"></i> TRANSACREANA I</a></li>
                                        <li class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 94); ?>"><a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="94"><i class="fas fa-circle"></i> VILA ALBERT SAMPAIO</a></li>
                                        <li class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 95); ?>"><a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="95"><i class="fas fa-circle"></i> VILA BELO JARDIM</a></li>
                                    </ul>
                                </div>

                                <div class="mapa_bairro">
                                    <svg version="1.1" id="Camada_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 4135 2109" style="enable-background:new 0 0 4135 2109;" xml:space="preserve">
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="97" data-tooltip="tooltip" data-placement="top" title="CUSTODIO FREIRE">
                                        <path class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 97); ?>" d="M1016.1,133.3c0,0,6.2,7.2,7,8.7s11.5,21.3,11.5,21.3l8.6,20.6l39.9,76.2l13.7,30.1l13.5,22.7l-28.7,11.9
                                              l-24.4,1.1l-23.2-6.1l-17.7-11.2l-6.3,10.5l-2.9,1.9L977,295.6l-11.6-12.3l-13.6-28.6l-7-21.9l-10.7-41.5l-1.1-9L1016.1,133.3z"></path>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="51" data-tooltip="tooltip" data-placement="top" title="PRIMAVERA">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 51); ?>" points="1350.3,507.4 1342.9,530.7 1336.2,532 1335.3,553.5 1300.5,579.8 1314.6,608.4 1325.4,625.5 
                                                 1333.3,627 1344.5,625.2 1350.3,638.7 1360.9,638.7 1375.1,633.2 1391.2,623.3 1405.1,605.2 1391.7,604.4 1387.3,556 1389.8,548.8 
                                                 1402.6,531.9 "></polygon>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="66" data-tooltip="tooltip" data-placement="top" title="PETROPOLIS"> -->
                                    <polygon class="st1" points="1448.6,623.4 1481.8,628.6 1462,543.8 1457,546.9 1447.3,548.8 1442.3,546.3 1442.3,622.7 "></polygon>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="26" data-tooltip="tooltip" data-placement="top" title="CIDADE DO POVO">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 26); ?>" points="2410.9,1466.8 2437.8,1468.8 2497.6,1444 2516.1,1421.3 2540.7,1402.2 2560.9,1407 2614,1444.6 
                                                 2589.5,1506.5 2565,1507.3 2558.1,1525.4 2562,1559 2569.7,1583.2 2592.5,1606.4 2642.5,1681.5 2755.1,1710.9 2799.8,1679.5 
                                                 2798.2,1626.6 2804.4,1556.6 2813.2,1308.3 2816.4,1254.1 2814.6,1197.5 2607.7,1198 2612.8,1315.8 2427.1,1315.8 2409.5,1451 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="93" data-tooltip="tooltip" data-placement="top" title="VILA ACRE">
                                        <path class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 93); ?>" d="M1912.4,1505.9l1.3,20.2l2.3,15.2l-0.3,15.6l-21.8,29.9l-7,4.8l90.7,49.9l40.1,28l88.8,66.3l86.5-163.9
                                              l-19.3-34.7c0,0-17.4-68.2-18-69.8s-2.5-48.6-2.5-48.6l-101.4,11.7l-0.3,38.8l-137.9,1.3L1912.4,1505.9z"></path>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="5" data-tooltip="tooltip" data-placement="top" title="" data-original-title="AMAPA">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 5); ?>" points="1629.8,1181.2 1612.4,1188.3 1594.9,1191.4 1565.4,1190.8 1546.3,1186.3 1529.3,1186.3 1522.5,1192.2 
                                                 1515.4,1206.6 1509.8,1215.7 1501.4,1236.1 1466.6,1273.7 1487.3,1339.2 1520.1,1383.7 1539.4,1407.8 1567.8,1444.4 1575.6,1451.2 
                                                 1673.7,1492.9 1721,1493.6 1748.2,1501.8 1771.9,1513.7 1790.3,1521.7 1806.9,1531.2 1858.4,1576.5 1885.3,1590.7 1894.1,1586.6 
                                                 1915.7,1556.9 1915.7,1537.3 1912.6,1507 1913.7,1470.7 1918.3,1339.3 2045,1337.5 2051,1332 2050.4,1295.6 2054,1284.7 
                                                 2048.7,1209.8 2042.5,1202.6 1987.1,1140 1957.2,1132.4 1943.3,1126.5 1796.2,1218.9 1797.2,1237.3 1784.2,1279.4 1759.3,1322.1 
                                                 1681.6,1212 1655.4,1158.2 1639.6,1173.6 "></polygon>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="46" data-tooltip="tooltip" data-placement="top" title="FLORESTA SUL"> -->
                                    <polygon class="st1" points="1357.1,801.9 1356.5,886.2 1342.7,933.3 1334.9,976.6 1332.3,997.2 1333.1,1022.5 1383.7,1184.1 
                                             1402.6,1206 1461.3,1260 1468.8,1259.7 1508.3,1192.6 1475.1,1062.3 1471.3,1041.7 1481.8,1032.7 1542.3,1000.9 1556.9,1012.3 
                                             1568,1007.5 1585.4,989.9 1591.4,988.2 1590.6,963.6 1601.5,962.3 1607.7,958.6 1607.6,954.9 1594.5,953.6 1594.3,922.3 
                                             1577.5,913.9 1568.5,900.2 1548.4,856.9 1517.9,876.2 1506.7,848.2 1485.8,852.4 1448.6,851.4 1455.4,809.6 1435.8,806.1 
                                             1422.6,806.1 1425.3,800.8 1401.4,805.8 1370.6,801.7 "></polygon>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="18" data-tooltip="tooltip" data-placement="top" title="BOA VISTA">
                                        <path class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 18); ?>" d="M1508.3,1192.6c0.9-1.4,5.3-8.5,5.3-8.5l3.2-7.4l9.7-0.3l12.6-7.1l15.2,6.9l25.7,5.9l22.4-3.7l8.1-0.3
                                              l18.4-11.2l13.2-13.6l4-2.6l-4.6-8l-12,5.7l-15.6-17.3l-18.1-30.7l-16.6,6.3l-84.7,33.7L1508.3,1192.6z"></path>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="10" data-tooltip="tooltip" data-placement="top" title="BAHIA NOVA">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 10); ?>" points="1591.3,986.3 1616.4,1012.4 1686.3,974.5 1692.4,983.6 1713.9,965.4 1689.9,942.3 1675.8,949.2 
                                                 1619.5,953.3 1607.7,956.7 1608.2,959.1 1591.1,964.1 "></polygon>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="66" data-tooltip="tooltip" data-placement="top" title="NOVO HORIZONTE"> -->
                                    <polygon class="st1" points="1575.2,826.3 1581.9,826.1 1587.4,834.1 1633.5,832.7 1639.5,811.1 1628.2,794.4 1614.9,806.1 
                                             1578,792.8 1571,819.7 "></polygon>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="43" data-tooltip="tooltip" data-placement="top" title="FLORESTA">
                                        <path class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 43); ?>" d="M1475.1,699l1.9,35l103.4,17.4l19.6,22.7l-20.5,12.6l-1.6,6c0,0,35.7,13.3,36.9,13.3s13.3-11.7,13.3-11.7
                                              l11.4,16.7l-6,21.5l71.5-2.3l-0.7-8.5l10.3-19.3l-0.6-5.6l-16.7-8.8l-27.8-45.2l1.9-29.4l-39.5-6l-26.8-1.4l-49.4,0.9l-40.6-4.1
                                              L1475.1,699z"></path>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="69" data-tooltip="tooltip" data-placement="top" title="" data-original-title="PORTAL DA AMAZONIA">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 69); ?>" points="1017.2,638.2 971.3,704.5 966.6,735.2 959,766.8 946.4,793.3 941.6,822.5 977.6,820.6 1037,827.9 
                                                 1088.2,808.6 1096.7,808 1123.5,823.2 1136.5,827.9 1144.4,827.9 1166.2,814.9 1177.2,808.9 1191.1,808.9 1203.1,812.4 
                                                 1220.2,818.4 1231.2,818.1 1242.9,812.7 1259.6,804.2 1273.8,804.2 1339.2,799.5 1357.4,801.9 1370.6,801.7 1400.6,805.8 
                                                 1409.7,804.2 1336,657.1 1309.1,671.9 1290.9,701.9 1242.9,665.3 1141.2,667.5 1136.5,688 1117.5,684.5 1079,684.5 1050.9,655.8 
                                                 1026.5,624.8 "></polygon>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="66" data-tooltip="tooltip" data-placement="top" title="JARDIM SÃO FRANCISCO"> -->
                                    <polygon class="st1" points="2174.7,468.8 2184.3,465.4 2195,455.6 2195.4,442.8 2190.5,434.2 2189.7,427.3 2193.9,420.4 
                                             2193.5,407.9 2198.5,397.2 2198.9,365.1 2196.3,353.2 2187.1,349.1 2157.8,345.5 2109.8,346.3 2068.4,370.6 2074,389.3 
                                             2068.8,405.1 2077.2,407.1 2098.6,422.1 2129.5,450.2 "></polygon>
                                    <!-- </a> -->
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="66" data-tooltip="tooltip" data-placement="top" title="BASE"> -->
                                    <path class="st1" d="M1881.1,778.9l-20.5,8.8l2.7,13.6l3.6,19l7.5,14.7l6.6,1.6l7.9-6.9c0,0,16.3-15.1,17.5-16.1
                                          c1.2-1.1,10.3-8.6,10.3-8.6l3.5-4.7l-5.4-5.1l-19.9,12.5L1881.1,778.9z"></path>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="23" data-tooltip="tooltip" data-placement="top" title="CENTRO">
                                        <path class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 23); ?>" d="M1850.5,711.7l2.5,11.1l42,85.1l19.9-12.5l5.4,3.5c0,0,24.1-29.9,48.9-37.3l-15.2-37.9L1850.5,711.7z"></path>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="24" data-tooltip="tooltip" data-placement="top" title="CERAMICA">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 24); ?>" points="1957.5,724 2002,730 2005.2,704.7 1991.3,694.3 "></polygon>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="66" data-tooltip="tooltip" data-placement="top" title="CAPOEIRA"> -->
                                    <polygon class="st1" points="1926.3,637.2 1929.4,658.6 1890.4,672.5 1909.9,718.6 1957.5,724 1981.4,703.1 "></polygon>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="54" data-tooltip="tooltip" data-placement="top" title="JOSE AUGUSTO">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 54); ?>" points="1908.6,615.1 1860.6,638.1 1862.8,661.2 1887.4,661.2 1890.4,672.5 1929.4,658.6 1926.3,637.2 
                                                 1915.9,624.3 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="8" data-tooltip="tooltip" data-placement="top" title="AVIARIO">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 8); ?>" points="1944.9,617.9 1950.9,626.4 2001.4,611.9 1988.2,648.5 1974.6,659.6 1991.3,694.3 1981.4,703.1 
                                                 1917,625.6 1924.4,620.1 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="61" data-tooltip="tooltip" data-placement="top" title="MORADA DO SOL">
                                        <path class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 61); ?>" d="M2005.2,685.2l1.4-1.4l17.5-17.2l49.3-18.3c0,0,12.3,0.9,13.6,0c1.3-0.9,36.6-13.3,36.6-13.3l30.9-31.9
                                              l-34.4-16.1l36-34.7v-2.8l-6.1-3.4l-2.8-4.5l-38.8,36.6l-8.2-3.5l-53.1,32.8l0.3,7.6l16.4-9.2l1.3,28.1l-25.6,3.2
                                              c0,0,1.6-15.5-0.6-15.5c-2.2,0-21.8,3.2-21.8,3.2l-0.3,8.8l-23.5,0.3l-5.3,14.6l-13.6,11.1l16.7,34.7L2005.2,685.2z"></path>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="66" data-tooltip="tooltip" data-placement="top" title="HABITASA"> -->
                                    <polygon class="st1" points="2030.8,691.8 2065.4,697.2 2060.5,718.3 2067.1,732.8 2063.6,736.6 2002,730 2005.2,704.7 
                                             1999.5,700.5 2030.5,704.4 "></polygon>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="20" data-tooltip="tooltip" data-placement="top" title="CADEIA VELHA">
                                        <path class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 20); ?>" d="M2161.8,592.9v-10.1l32.8-20.2l4.4-3.1c0,0,12.3,2.2,16.4-5.1s16.4-19.9,16.4-19.9l10.9-17.8l2.2-1.9l4,3.7
                                              l1.1,14.1l4.7,4.2l4.4-3.1l-0.2-5.2l4.9-7.8l6.8-2.2l6.1,2.8l0.5,17.1l-24.7,24.9l-38.7,61.5l-36.1,51.9l-42.2,39.3l-13.2,10.8
                                              l-28.1,12l-30.9,5.6l-66.7,10.8l-27.5,6.3l-15.2-37.9l109.6,12.9l3.5-3.8l-6.6-14.5l4.9-21.2l-34.6-5.4l-0.3,12.6l-31-4l-8.2-6.1
                                              l13.9-9.2l18.9-18.6l49.3-18.3h13.6l36.6-13.3l30.9-31.9L2161.8,592.9z"></path>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="66" data-tooltip="tooltip" data-placement="top" title="PAPOUCO"> -->
                                    <path class="st1" d="M1807.7,750.9l2.7,14l7.9-2.2l14.4-1.4c0,0,11.4-1.1,15.1,0.2c3.6,1.2,6.8,3.4,6.8,3.4l5,11.6l0.9,11.3
                                          l20.5-8.8l-25.2-50.3L1807.7,750.9z"></path>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="38" data-tooltip="tooltip" data-placement="top" title="DOM GIOCONDO">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 38); ?>" points="1784.8,705.1 1790.5,726.4 1807.7,750.9 1855.9,728.7 1853,722.7 1850.5,711.7 1810.8,708.8 "></polygon>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="66" data-tooltip="tooltip" data-placement="top" title="IPASE"> -->
                                    <polygon class="st1" points="1862.8,661.2 1862.8,684.8 1878.4,714.9 1909.9,718.6 1890.4,672.5 1887.4,661.2 "></polygon>
                                    <!-- </a> -->
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="66" data-tooltip="tooltip" data-placement="top" title="CASA NOVA"> -->
                                    <polygon class="st1" points="2001.4,611.9 2013.2,610.3 2046,599.1 2055.1,596 2036.8,569.1 2012.8,567.1 2006.3,565.4 
                                             2003.8,560.2 1995.5,564 1998,600.8 2001.4,601.3 "></polygon>
                                    <!-- </a> -->
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="0" data-tooltip="tooltip" data-placement="top" title="São Francisco"> -->
                                    <polygon class="st1" points="2049,606.4 2047.2,607.5 2047.5,615.1 2063.9,605.9 2065.2,634 2039.6,637.2 2039,621.7 2017.2,624.8 
                                             2016.9,633.7 1993.4,634 2001.4,611.9 2013.2,610.3 2046,599.1 "></polygon>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="92" data-tooltip="tooltip" data-placement="top" title="TROPICAL">
                                        <path class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 92); ?>" d="M2040.7,565.3c0,0-4.7,2.6-3.9,3.7c0.8,1.2,18.3,26.9,18.3,26.9l-9.1,3.1l3,7.3l51.3-31.8l8.2,3.5l38.8-36.6
                                              l-8.1-16.5l3.9-7.7l-0.2-5.2l-4.6-3.6l-8,9.2l-16.5,0.5l-0.7-23.1l-11.7,0.4l-8-3.1l-2.7,6.4l-9.7,1.1l-0.7,7.2l12.2,0.7l-0.5,6.2
                                              l-16.8,9.9l-10,4.2l-11.3,3.7l-8.6,8.8l0.7,12L2040.7,565.3z"></path>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="66" data-tooltip="tooltip" data-placement="top" title="ADALBERTO ARAGÃO"> -->
                                    <polygon class="st1" points="2179.8,518.4 2156.2,552.2 2120.2,586.9 2154.6,603.1 2161.8,592.9 2161.8,582.8 2199.1,559.6 
                                             2191.2,558 2184.3,555.8 2181.7,554.9 2180.8,549.7 2188,540.5 2192.8,525.4 2201,522.9 2200.7,512.5 2190.8,514.8 "></polygon>
                                    <!-- </a> -->
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="66" data-tooltip="tooltip" data-placement="top" title="BAIXADA DA COLINA"> -->
                                    <polygon class="st1" points="1971.3,556.9 1956.2,560.2 1949.8,560.2 1962.3,590.9 1970.6,620.7 2001.4,611.9 2001.4,601.3 
                                             1998,600.8 1995.5,564 2003.8,560.2 2001.7,552.4 1997.3,549.4 1990.5,549.5 1986.6,552.5 1984.7,558.9 1984.9,566.3 1983.1,568.1 
                                             1978.3,564.1 "></polygon>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="111" data-tooltip="tooltip" data-placement="top" title="GUIOMARD SANTOS">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 111); ?>" points="1869.8,553.5 1877,586.1 1907.9,573.3 1905.8,579.6 1926,588 1929.4,593.3 1946,593.3 1961.5,589.2 
                                                 1949.8,560.2 1935.9,554.3 1937,552 1919.9,545.1 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="19" data-tooltip="tooltip" data-placement="top" title="BOSQUE">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 19); ?>" points="1878.4,714.9 1850.5,711.7 1784.8,705.1 1783.8,703.1 1768.9,705.8 1753.9,699.2 1748.7,637.9 
                                                 1786.6,636.3 1753.8,583.3 1771.3,569.3 1776.4,575.5 1782,575.7 1784.8,570.5 1779.1,565 1778.7,551.6 1762.7,551.6 1753.6,531.8 
                                                 1758.9,510.8 1752.4,489.6 1797.3,465.6 1798.4,476.8 1810.4,477.1 1815.4,494.9 1824.8,494.6 1837.4,486.8 1841,479.6 
                                                 1840.2,472.5 1848,471.6 1851,457.2 1872.8,458 1891.7,466.3 1895,471.4 1904.6,471.4 1908,475.3 1908,481.5 1905.8,484.7 
                                                 1896.1,485.6 1895,490.1 1895.7,494.9 1901,499 1905,498.2 1913.6,490.8 1917.9,485.7 1923.8,483.2 1924.1,492.2 1926.5,498.1 
                                                 1932.3,498.7 1939.2,494.9 1941.8,487.9 1945.1,482.2 1950.6,479.4 1955.3,483.7 1955,494.9 1955.3,513.7 1957.5,526.1 
                                                 1960.9,534.8 1967,537.6 1970.5,539.7 1973,545.8 1971.3,556.9 1954,560.2 1949.8,560.2 1935.9,554.3 1937,552 1919.9,545.1 
                                                 1869.8,553.5 1877,586.1 1907.9,573.3 1905.8,579.6 1926,588 1929.4,593.3 1946,593.3 1961.5,589.2 1970.6,620.7 1950.9,626.4 
                                                 1944.9,617.9 1924.4,620.1 1917,625.6 1908.6,615.1 1860.6,638.1 1862.8,661.2 1862.8,684.8 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="98" data-tooltip="tooltip" data-placement="top" title="VILA IVONETE">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 98); ?>" points="1752.4,489.6 1797.3,465.6 1796.2,454.5 1784.8,451.9 1780.1,442.4 1792.7,438.9 1792.5,433 
                                                 1779.6,429.6 1769.5,415.8 1770.5,408.3 1774.4,406 1771,394.2 1761.3,395.2 1742.1,407 1741.6,413.7 1736.3,411.6 1736.7,406 
                                                 1735.8,394.5 1717.9,399.5 1710.4,391.9 1708.6,388 1695,386.9 1686.6,392.8 1692.7,397 1699,428.9 1706.6,455.4 1707.2,475.6 
                                                 1677.1,482.8 1677.1,492.4 1680.6,499.3 1681.6,506.6 1684.7,510.9 1701.9,508.9 1718.1,500.2 1736.7,494.4 1744,488.2 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="15" data-tooltip="tooltip" data-placement="top" title="BOA ESPERANCA">
                                        <path class="st1" d="M1751.5,488l-6.3-0.2l-8.5,6.6l-17.3,5.1l-17.5,9.4l-16.7,1.6l-12.8,10.8v18.5l-3.4,8.6l6.3,2.9l1.8-9.5
                                              c0,0,24.1,7.2,25,6.7c0.8-0.4,22.5-20.6,22.5-20.6l27.5-12.2l2.3,5.9l2.7-3.3l2.2-7.9l-4-8.1L1751.5,488z"></path>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="63" data-tooltip="tooltip" data-placement="top" title="NOVA ESTACAO">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 63); ?>" points="1685.2,511.4 1681.6,506.6 1680.2,497.3 1677.8,492.9 1677.1,483.8 1673.2,475.2 1622.3,489.3 
                                                 1620.2,611.2 1634,600.4 1632.6,584.4 1661.6,580.4 1669,583.6 1689.2,563.4 1675.5,561.3 1675.3,551.4 1669,548.4 1672.4,539.8 
                                                 1672.4,521.3 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="100" data-tooltip="tooltip" data-placement="top" title="WALDEMAR MACIEL">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 100); ?>" points="1724.6,528 1752.1,515.8 1754.5,521.7 1754,531.5 1755.8,541.1 1763.6,551.2 1778.2,551.9 
                                                 1778.2,564.2 1784.1,569.6 1782.4,575.3 1776.4,575.5 1771.7,568.9 1765.5,570.1 1761.4,575.9 1755.5,576.1 1754.1,581.8 
                                                 1750,581.8 "></polygon>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="66" data-tooltip="tooltip" data-placement="top" title="JARDIM MANOEL JULIAO"> -->
                                    <polygon class="st1" points="1724.6,528 1744,569 1731.5,576.4 1689.2,563.4 1675.5,561.3 1675.3,551.4 1677.1,541.9 1702,548.6 "></polygon>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="56" data-tooltip="tooltip" data-placement="top" title="ISAURA PARENTE">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 56); ?>" points="1620.2,611.2 1620.2,620.4 1653.6,630.7 1682,635.8 1748.7,637.9 1786.6,636.3 1754.1,581.8 
                                                 1750,581.8 1744,569 1731.5,576.4 1689.2,563.4 1669,583.6 1661.6,580.4 1632.6,584.4 1634,600.4 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="42" data-tooltip="tooltip" data-placement="top" title="ESTACAO EXPERIMENTAL">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 42); ?>" points="1567.7,604.4 1565.6,665.7 1577.2,665.3 1606,675.6 1619.9,675.1 1618.2,683.4 1609.8,687.4 
                                                 1605.9,695 1605.4,706.1 1632.1,707.6 1632.8,682.9 1645.6,682.5 1667,687.1 1709.7,693.5 1731,694 1738.4,699.7 1755.6,699.1 
                                                 1748.7,637.9 1682,635.8 1654.7,630.9 1620.2,620.4 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="58" data-tooltip="tooltip" data-placement="top" title="MANOEL JULIAO">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 58); ?>" points="1622.3,489.3 1597.6,496.4 1601.2,499.6 1570.3,516.8 1559.9,516.8 1567.7,604.4 1620.2,620.4 "></polygon>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="45" data-tooltip="tooltip" data-placement="top" title="IOLANDA"> -->
                                    <polygon class="st1" points="1559.9,516.8 1552.6,515.8 1523.5,528 1522.3,548.8 1514.7,594.9 1514.7,620.4 1567.7,604.4 "></polygon>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="45" data-tooltip="tooltip" data-placement="top" title="GERALDO FLEMING">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 45); ?>" points="1601.2,499.6 1597.6,496.4 1577.6,501.7 1559.9,503.6 1502,500 1503.1,511.6 1497.6,519.6 
                                                 1485.2,522.7 1470.9,538.7 1522.3,548.8 1523.5,528 1552.6,515.8 1559.9,516.8 1570.3,516.8 "></polygon>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="45" data-tooltip="tooltip" data-placement="top" title="PAZ"> -->
                                    <polygon class="st1" points="1417.6,474.1 1417.6,505.9 1453.2,519.4 1475.1,515.8 1489.5,521.6 1497.6,519.6 1503.1,511.6 
                                             1502,500 1509.4,489.3 1526.1,478.6 1522.4,475 1522.2,469.8 1506,470.3 1505.5,453.9 1495.4,452.6 1481.4,447.9 1486.8,441.2 
                                             1470.9,446.1 1455.9,457.1 1445.4,459.4 "></polygon>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="84" data-tooltip="tooltip" data-placement="top" title="SANTA QUITERIA">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 84); ?>" points="1706.9,475.9 1706.6,455.4 1699.5,430.5 1664.4,436.4 1673.2,475.2 1677.1,483.8 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="36" data-tooltip="tooltip" data-placement="top" title="CONQUISTA">
                                        <path class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 36); ?>" d="M1699.5,430.5l-7.9-33.6l-6-4.4l-11.9-3.7l-16.5-1l-10.2-3.9l-3.2-7.3l-8.7-1.5l-0.1,10.7l-2.6,11.3l-10.1-1.2
                                              l-7.1-9l-6.1-2.1l-5.8-5.5l-16.9,0.3l-6.5,5.1l-9.3,7.5l-9.7,2.5l-6.2,5.3l4.7,5.5l7.2,2.4l0.8,14l-5.6,1.8l-5,9.4l-2.4,9.9l2.5,7.6
                                              c0,0,0.7,3.2-0.1,5.3s-2.7,5.7-2.7,5.7l-8,2.8l-7.9-1.9l-0.2-6.7l-9.4,0.2l0.1,13.5l-2.9,9l-16.7,10.7L1502,500l57.9,3.6l17.7-1.9
                                              l20-5.3l75.6-21.3l-8.8-38.7L1699.5,430.5z"></path>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="45" data-tooltip="tooltip" data-placement="top" title="MARIANA"> -->
                                    <polygon class="st1" points="1417.6,505.9 1414.7,515.8 1389.8,548.8 1387.3,556 1391.7,604.4 1405.1,605.2 1409.6,604.7 
                                             1442.3,619 1442.3,546.3 1447.3,548.8 1457,546.9 1471.9,537.7 1485.2,522.7 1486.8,520.5 1475.1,515.8 1453.2,519.4 "></polygon>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="34" data-tooltip="tooltip" data-placement="top" title="TUCUMA">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 34); ?>" points="1351.7,494.9 1301.8,460 1301.8,455.4 1265.6,455.4 1256.2,428.6 1250.5,428.6 1243.5,426.1 
                                                 1232.4,422.1 1224.4,424.2 1210.3,467.4 1223.8,491.9 1269.1,528.5 1283.9,547.5 1295,566.5 1300.5,579.8 1335.3,553.5 1336.2,532 
                                                 1342.9,530.7 1350.3,507.4 1353.7,509 1355.9,504.4 1352.8,500.9 "></polygon>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="45" data-tooltip="tooltip" data-placement="top" title="JARDIM BRASIL"> -->
                                    <polygon class="st1" points="1414.7,456 1405.1,447.6 1374.1,454.7 1376.6,461.7 1357,467.8 1359.7,472.6 1349.2,479.6 
                                             1357.2,484.4 1351.7,494.9 1352.8,500.9 1355.9,504.4 1353.7,509 1402.6,531.9 1414.7,515.8 1417.6,505.9 1417.6,474.1 "></polygon>
                                    <!-- </a> -->
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="45" data-tooltip="tooltip" data-placement="top" title="RUI LINO"> -->
                                    <polygon class="st1" points="1245.8,420.4 1251.7,418.3 1286.1,386.9 1321.4,424 1352.8,425.9 1374.1,454.7 1376.6,461.7 
                                             1357,467.8 1360.6,473.5 1349.2,479.6 1357.2,484.4 1351.7,494.9 1301.8,460 1301.8,455.4 1265.6,455.4 1256.2,428.6 1250.5,428.6 
                                             1243.5,426.1 "></polygon>
                                    <!-- </a> -->
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="45" data-tooltip="tooltip" data-placement="top" title="JOAFRA"> -->
                                    <polygon class="st1" points="1406.7,362.7 1349.2,384.8 1320.3,422.8 1322.1,425.1 1352.8,425.9 1374.1,454.7 1405.1,447.6 
                                             1414.7,456 1417.6,474.1 1445.4,459.4 1455.9,457.1 1470.9,446.1 1486.8,441.2 1479.7,439.8 1481.4,434.6 1481.6,427.7 1476.6,428 
                                             1472,423.7 1465.8,425.2 1459.9,431.4 1453.4,427.3 1452.1,420.5 1459.5,418.7 1463.1,413.4 1457.7,408.3 1450.2,406.3 
                                             1438.7,400.7 1437.9,387.5 1432.2,381.6 1426.3,384.8 1426.5,391.5 1411.8,402.4 1403.1,400.6 1407.3,392.9 1401.3,390.9 
                                             1401.5,385.6 1407.4,382.4 1407.2,375 1400,375.4 1394.8,378.5 1389.9,376.2 1393.6,371.7 1402.1,370.1 1407.7,367.2 "></polygon>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="59" data-tooltip="tooltip" data-placement="top" title="MOCINHA MAGALHAES">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 59); ?>" points="1237.2,362.7 1227.7,386.9 1257.9,412.7 1286.1,386.9 1320.3,422.8 1350,384.5 1406.7,362.7 
                                                 1406.7,357.7 1402.2,354.8 1398.6,354.5 1394.2,359.1 1386.8,360.1 1379.9,358.9 1374.5,360.3 1366,367.4 1354.1,353.5 
                                                 1343.6,354.6 1332.1,349 1329.6,340.9 1318.6,339.9 1315.5,333.3 1312.7,330.2 1304.4,327.5 1304.6,337.5 1296.1,327.8 
                                                 1287.8,327.3 1284.2,332.6 1277,337.9 1260.6,331.3 1263.3,319.3 1262.3,311.9 1256.2,309.8 1249.8,310.6 1247.7,323.5 
                                                 1231.6,327.6 1224.1,329.2 1237.2,352.6 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="37" data-tooltip="tooltip" data-placement="top" title="" data-original-title="DISTRITO INDUSTRIAL">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 37); ?>" points="1214.1,343.8 1200.9,351.7 1192,353.5 1177,354 1159.8,356.8 1152.3,353.3 1154.4,348.7 1153.5,344.3 
                                                 1149.5,339.2 1145.7,338.5 1142.9,342.4 1141.5,347.7 1137.9,352.2 1133.4,350.9 1129.3,335.3 1144.7,331.2 1138.6,325.2 
                                                 1136.1,319.6 1131.5,312.2 1110.3,312.9 1081.6,324.8 1057.2,325.9 1034.1,319.8 1016.4,308.6 1010.1,319.2 1010.1,324.4 
                                                 1010.6,331.8 1018.3,338.1 1145.4,436.3 1156.7,438.1 1223.8,491.9 1210.3,467.4 1224.4,424.2 1232.4,422.1 1243.5,426.1 
                                                 1245.8,420.4 1251.7,418.3 1257.9,412.7 1228.6,386.9 1237.2,362.7 1237.2,352.6 1224.1,329.2 1212.9,344.5 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="35" data-tooltip="tooltip" data-placement="top" title="UNIVERSITARIO">
                                        <path class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 35); ?>" d="M1003.1,338.5l-6-1.3l-1-5.9l-2.4-4.4l-3.8,1.4l-4.2,6.3l-6.9,1.6l-3.3,6.2l-5.8,5.4l-6-2.1l-3.9-5.1l1.1-5.9
                                              l0-6.8l-5.4-3.2l-5.6-2.3l-2.1-6.2l3.4-4.1l1.5-7.9l-2.4-5.2h-8.6l-5.5,4.2l-3.5,8.3l-10.5,0.3l-5.2,2.4l-0.7,3.8l3.2,6.6l0.4,14.1
                                              l-4.3,5.4l-15.4,8.3l-9.7,1.5l-9.1-2.7l-32.9,1l-4.9,4.5l-4.8,5.7l-7,6l0.2,6.7l3.3,8.8l5.5,8.7l7.7,7.2c0,0,12,9.9,12.6,10.3
                                              s17.7,22.7,17.7,22.7l14.1,20.7l15.7,22.7l5.6,7.3l10.8,7.1l12.3,3.6l16.5,8.2l16.6,1.7l7.6,5l9.7-0.3l-0.6-17.9l3.7-2l7.4-6.2
                                              l8.6-8.8l60-0.4l12.7-1.9l1.9-12.7l12.3-18.2l7.8-7.8l7.1,1.4l9.4,1.8l7.4,4.1l11.5,1.1l6.9-6.9l-132.9-103l-2.3,4.3L1003.1,338.5z"></path>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="33" data-tooltip="tooltip" data-placement="top" title="TANGARA">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 33); ?>" points="1503.7,624.8 1481.8,628.6 1482.9,631.4 1479.9,646.2 1475.1,658.3 1473.9,667.4 1471.3,680.6 
                                                 1465,696.3 1475.6,699.1 1557.1,707.6 1562.6,700.1 1565.6,665.7 1567.7,604.4 1514.7,620.4 "></polygon>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="33" data-tooltip="tooltip" data-placement="top" title="PAULO CESAR DE OLIVEIRA"> -->
                                    <polygon class="st1" points="1498.5,588.8 1503.7,624.8 1481.8,628.6 1471.9,588 "></polygon>
                                    <!-- </a> -->
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="33" data-tooltip="tooltip" data-placement="top" title="ENGENHEIROS"> -->
                                    <polygon class="st1" points="1512.2,589.1 1498.5,588.8 1503.7,624.8 1514.7,620.4 1515.6,589.6 "></polygon>
                                    <!-- </a> -->
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="33" data-tooltip="tooltip" data-placement="top" title="JARDIM DE ALAH"> -->
                                    <polygon class="st1" points="1480.8,625.8 1475.7,623.3 1464.2,624.6 1449.7,620.7 1431.7,614 1409.6,604.7 1405.1,605.2 
                                             1402.3,608.9 1393.6,621 1389.3,624.3 1379.5,630.1 1371.3,634.5 1364.8,637.1 1363,638.3 1364.8,665.3 1368.8,677.3 1375.5,678.5 
                                             1401.2,665.3 1411.5,685.1 1409.8,701.9 1424.4,697.7 1451.5,653.3 1475.7,658.4 1480.9,643.2 1482.9,631.4 "></polygon>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="48" data-tooltip="tooltip" data-placement="top" title="JARDIM EUROPA">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 48); ?>" points="1324.2,633.5 1409.7,804.2 1425.3,800.8 1426.9,782 1432.8,777.2 1431.6,766 1427.4,752 1429.1,736.3 
                                                 1450.7,735.1 1477,734 1475.6,699.1 1465,696.4 1471.3,680.6 1475.7,658.4 1451.5,653.3 1424.4,697.7 1409.8,701.9 1411.5,685.1 
                                                 1401.2,665.3 1375.5,678.5 1368.8,677.3 1364.8,665.3 1363,638.3 1353.1,638.8 1347.3,635.9 1346.1,627.8 1342.5,624.8 
                                                 1332.3,626.9 "></polygon>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="33" data-tooltip="tooltip" data-placement="top" title="PEDRO ROSENO"> -->
                                    <polygon class="st1" points="1322.5,658.3 1242.9,665.3 1290.9,701.9 1309.1,671.9 1336,657.1 "></polygon>
                                    <!-- </a> -->
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="33" data-tooltip="tooltip" data-placement="top" title="IPE"> -->
                                    <polygon class="st1" points="1141.2,667.5 1225.8,665.3 1256.8,664.1 1336,657.1 1324.2,633.5 1332.3,626.9 1325.5,624.2 
                                             1306.4,591.7 1303.7,586.4 1261.1,609.9 1245.6,616.9 1218.5,623.7 1202.1,626.4 1195.4,629.6 1164.7,630.6 1117.5,608.2 
                                             1050.9,655.8 1079,684.5 1118.8,684.5 1136.5,688 "></polygon>
                                    <!-- </a> -->
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="33" data-tooltip="tooltip" data-placement="top" title="JARDIM UNIVERSITARIO"> -->
                                    <polygon class="st1" points="1112.1,604.7 1093.7,588.9 1090.6,586 1088.2,579.7 1078.4,581.9 1026.5,624.8 1050.9,655.8 
                                             1117.5,608.2 "></polygon>
                                    <!-- </a> -->
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="33" data-tooltip="tooltip" data-placement="top" title="VILA TIRADENTES"> -->
                                    <polygon class="st1" points="1282.7,886.7 1330.3,885.6 1356.7,884.1 1357.4,801.9 1339.2,799.5 1279.8,803.8 1273.8,804.2 "></polygon>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="100" data-tooltip="tooltip" data-placement="top" title="" data-original-title="WADEMAR MACIEL">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 100); ?>" points="1086.6,948.6 1086.1,933.7 1099.2,931 1136.5,931 1168.7,917.5 1262.3,887.9 1282.7,886.7 
                                                 1273.8,804.2 1259.6,804.2 1231.2,818.1 1220.2,818.4 1203.1,812.4 1191.1,808.9 1177.2,808.9 1144.4,827.9 1136.5,827.9 
                                                 1123.5,823.2 1112.5,829.8 1093.8,864.5 1081.2,868.9 1078.1,948.6 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="21" data-tooltip="tooltip" data-placement="top" title="" data-original-title="CALAFATE">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 21); ?>" points="864.9,918.2 934.1,849.1 963.4,885.7 1010.5,886.6 1070.9,954.1 1078.1,948.6 1081.2,868.9 
                                                 1093.8,864.5 1112.5,829.8 1123.5,823.2 1097.9,808.7 1088.2,808.6 1037,827.9 977.6,820.6 941.6,822.5 946.4,793.3 806.5,802.9 
                                                 816.7,912.9 854.9,913.2 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="136" data-tooltip="tooltip" data-placement="top" title="" data-original-title="ILSON RIBEIRO">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 136); ?>" points="872.3,924.4 872.8,925.8 880.4,946.3 876.3,954.4 880,974.3 917,966.5 946.7,927.4 959.3,918.2 
                                                 949.5,903.1 926.5,912.8 915.8,896.4 901.5,881.6 864.9,918.2 "></polygon>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="33" data-tooltip="tooltip" data-placement="top" title="LAELIA ALCANTARA"> -->
                                    <polygon class="st1" points="930.6,1040.7 937.3,1040.5 962.2,1027.5 1058.9,963.7 1070.9,954.1 1010.5,886.6 963.4,885.7 
                                             934.1,849.1 901.5,881.6 915.8,896.4 926.5,912.8 949.5,903.1 959.3,918.2 946.7,927.4 917,966.5 880,974.3 889.6,986.4 
                                             899.3,997.4 920.3,1023.1 "></polygon>
                                    <!-- </a> -->
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="33" data-tooltip="tooltip" data-placement="top" title="HABITAR BRASIL"> -->
                                    <polygon class="st1" points="1428.4,736.6 1427.4,752.2 1431.6,766 1432.8,777.4 1426.2,782.8 1425.3,800.8 1422.6,806.1 
                                             1435.8,806.1 1444.7,763.5 1451,763.2 1453.5,735 "></polygon>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="41" data-tooltip="tooltip" data-placement="top" title="ESPERANÇA">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 41); ?>" points="1455.4,809.6 1571,819.7 1579.5,786.8 1600,774.2 1580.5,751.5 1477,734 1453.5,735 1451,763.2 
                                                 1444.7,763.5 1435.8,806.1 "></polygon>
                                    </a>

                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="33" data-tooltip="tooltip" data-placement="top" title="DOCA FURTADO"> -->
                                    <polygon class="st1" points="1565.9,665.7 1562.6,700.1 1557.1,707.6 1605.1,706.2 1605.5,695 1609.8,687.4 1617.9,683.4 
                                             1619.9,675.1 1605.7,675.6 1594.9,671.1 1576.8,665.3 "></polygon>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="50" data-tooltip="tooltip" data-placement="top" title="JARDIM NAZLE">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 50); ?>" points="1631.9,707.6 1632.8,682.9 1645.9,682.5 1712.2,694.6 1730.7,694 1738.4,699.7 1754.9,699.2 
                                                 1767.8,705.5 1783.5,705 1788.1,714.9 1769.9,722.1 1671.4,713.6 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="2" data-tooltip="tooltip" data-placement="top" title="ABRAO ALAB">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 2); ?>" points="1747.5,746.6 1754.8,746.6 1789.5,726.4 1789.5,719.1 1788.1,714.9 1769.9,722.1 1671.4,713.6 
                                                 1669.5,742.9 "></polygon>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="49" data-tooltip="tooltip" data-placement="top" title="IVETE VARGAS"> -->
                                    <polygon class="st1" points="1718.5,787.8 1736.1,772.6 1745.8,758.7 1745.8,747.1 1669.5,742.9 1697.3,788.1 1714,796.9 "></polygon>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="96" data-tooltip="tooltip" data-placement="top" title="VILA BETEL">
                                        <path class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 96); ?>" d="M1448.6,851.4c0,0,36.4,1.3,37.3,1.1c0.8-0.2,20.8-4.2,20.8-4.2l11.3,28l30.4-19.3l5.2-10.2l12.3-14.4l9.3-6
                                              l-4.2-6.6l-115.6-10.1L1448.6,851.4z"></path>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="70" data-tooltip="tooltip" data-placement="top" title="PREVENTORIO">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 70); ?>" points="1810.5,764.9 1810.1,752.2 1795.2,731.9 1789.5,727.3 1760.5,742.7 1745.4,746.8 1745.4,759.2 
                                                 1736.9,772 1717.9,787.5 1714.3,794.4 1714.1,802.2 1734.7,814.3 1741.7,826.5 1759,833.7 1766.2,814 1774.7,801.5 1783.4,786.6 
                                                 1790.6,776.7 1803.2,770.3 "></polygon>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="49" data-tooltip="tooltip" data-placement="top" title="VOLTA SECA"> -->
                                    <polygon class="st1" points="1704.4,824.1 1706,837.1 1710.6,847.8 1721.3,846.8 1748.9,873.4 1751.4,855.4 1759,833.7 
                                             1741.7,826.5 1734.7,814.3 1714.1,802.2 "></polygon>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="65" data-tooltip="tooltip" data-placement="top" title="PALHEIRAL">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 65); ?>" points="1672.8,831.1 1665.3,882.2 1693.5,888.2 1722.9,879.5 1710.6,847.8 1706,837.1 1705.1,830 "></polygon>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="61" data-tooltip="tooltip" data-placement="top" title="PISTA"> -->
                                    <polygon class="st1" points="1746.1,939.1 1722.9,879.5 1693.5,888.2 1671.6,912.4 1676.6,929.6 1713.9,965.4 "></polygon>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="11" data-tooltip="tooltip" data-placement="top" title="BAHIA VELHA">
                                        <path class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 11); ?>" d="M1622.6,905.3l41.1,14.2l7.9-7.1l5.1,17.2l13.3,12.8c0,0-13.4,7-14.1,6.8s-56.4,4.1-56.4,4.1l-11.8,3.4l-0.4-2
                                              l-12.8-1.1l-0.4-23.2l19.5-5.9L1622.6,905.3z"></path>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="3" data-tooltip="tooltip" data-placement="top" title="AEROPORTO VELHO">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 3); ?>" points="1853.9,952.1 1846.9,946.3 1834,940.8 1787,926.1 1771,914.5 1759,903.3 1751.3,884.5 1748.9,873.4 
                                                 1721.3,846.8 1710.6,847.8 1746.1,939.1 1760.3,976.6 1761.7,983.4 1760.5,988.5 1750.4,997.9 1754.2,1005.7 1804,1029.5 
                                                 1823,1007.4 1829.9,999.1 1846.2,983.3 1854.4,969.9 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="9" data-tooltip="tooltip" data-placement="top" title="AYRTON SENA">
                                        <path class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 9); ?>" d="M1765.4,1070.4c0,0-49.7,35.2-51.8,33.8s-33.9-39.1-33.9-39.1l70.6-67.1l3.8,7.7l49.9,23.8L1765.4,1070.4z"></path>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="61" data-tooltip="tooltip" data-placement="top" title="GLORIA"> -->
                                    <polygon class="st1" points="1692.4,983.6 1726.1,1021 1760.5,988.5 1761.7,983.4 1760.3,976.6 1746.1,939.1 "></polygon>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="17" data-tooltip="tooltip" data-placement="top" title="BOA UNIÃO">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 17); ?>" points="1686.3,974.5 1692.4,983.6 1726.1,1021 1679.7,1065.1 1664.6,1072.2 1616.4,1012.4 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="88" data-tooltip="tooltip" data-placement="top" title="" data-original-title="SOBRAL">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 88); ?>" points="1562.4,1010 1571.1,1030.5 1618.7,1090.5 1595.7,1100.4 1613.8,1131.2 1629.4,1148.4 1641.4,1142.7 
                                                 1646.1,1150.7 1661.3,1140.9 1681.7,1124.8 1713.6,1105.3 1679.7,1065.1 1664.6,1072.2 1616.4,1012.4 1591.3,986.3 1585.4,989.9 
                                                 1568.9,1006.6 "></polygon>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="61" data-tooltip="tooltip" data-placement="top" title="JOAO PAULO II"> -->
                                    <polygon class="st1" points="1542.3,1000.9 1515.1,1014.7 1523.9,1033.3 1534,1042.3 1545.6,1048 1568.6,1093.3 1579.1,1106.7 
                                             1595.7,1100.4 1618.7,1090.5 1571.1,1030.5 1562.4,1010 1556.9,1012.3 "></polygon>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="68" data-tooltip="tooltip" data-placement="top" title="PLACIDO DE CASTRO">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 68); ?>" points="1471.3,1041.7 1494.4,1140.4 1579.1,1106.7 1568.6,1093.3 1545.6,1048 1534,1042.3 1523.9,1033.3 
                                                 1515.1,1014.7 1481.8,1032.7 "></polygon>
                                    </a>
                                    <!--<a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="52" data-tooltip="tooltip" data-placement="top" title="JOÃO EDUARDO">-->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="143" data-tooltip="tooltip" data-placement="top" title="JOAO EDURADO II">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 143); ?>" points="1603,866.1 1594.1,894.2 1578.6,890.4 1584.6,871 1551,863.3 1568.5,900.2 1577.5,913.9 1594.3,922.3 
                                                 1594.1,930.4 1613.6,924.4 1622.6,905.3 1663.7,919.5 1671.6,912.4 1693.5,888.2 1665.3,882.2 1642.7,872.4 1630,867.7 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="52" data-tooltip="tooltip" data-placement="top" title="JOÃO EDUARDO I">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 52); ?>" points="1581.5,825.7 1575.3,825.7 1566.4,832.5 1553.5,846.8 1547.9,856.7 1551,863.3 1584.6,871 
                                                 1578.6,890.4 1594.1,894.2 1603,866.1 1630,867.7 1665.3,882.2 1672.8,831.1 1587.4,834.1 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="27" data-tooltip="tooltip" data-placement="top" title="CIDADE NOVA">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 27); ?>" points="1848.5,778.7 1843.8,774.4 1837,772.3 1825.6,772.7 1818.3,772.9 1812.4,776.1 1807.2,776.2 
                                                 1795.5,786.3 1790.1,794.8 1782.3,806.6 1777.3,816.2 1772.3,828.1 1768.3,840 1764.8,851 1761.1,862.9 1760.8,873.7 1762.6,882.7 
                                                 1766.6,890 1774,900.1 1783.2,907.7 1797,917.1 1807.7,867.2 1861.7,839.4 1857.8,834.3 1855.2,826.1 1850.6,798 1851.1,791.3 
                                                 1848.6,783.9 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="71" data-tooltip="tooltip" data-placement="top" title="QUINZE">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 71); ?>" points="1875.4,847.9 1861.7,839.4 1807.7,867.2 1797,917.1 1805.1,922.3 1812.8,924 1828.5,927.5 
                                                 1846.1,936.6 1856.2,936.3 1864.2,921.9 1883.5,895.6 1868.8,876.4 1876.2,872.2 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="87" data-tooltip="tooltip" data-placement="top" title="6 DE AGOSTO">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 87); ?>" points="2087.4,747.5 2054.4,753.4 2034.8,757 2008.6,762.3 1981,770.1 1965.8,779.4 1953.6,785.7 
                                                 1941.5,798.2 1931.1,810.5 1921.6,821.3 1913.6,830.3 1885.9,847.5 1875.4,847.9 1876.2,872.2 1868.8,876.4 1883.5,895.6 
                                                 1887.5,900 1903.3,891.6 1893.2,872.6 1940.4,849.5 1962.5,843.2 1991.9,849.7 2014,846.5 2018.5,862.5 2042.7,863.8 2013.4,790.7 
                                                 2088.8,752.8 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="91" data-tooltip="tooltip" data-placement="top" title="TRIANGULO VELHO">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 91); ?>" points="1870.9,942.6 1914.5,959.3 1925.2,974.3 2006.3,975.4 2042.7,863.8 2018.5,862.5 2014,846.5 
                                                 1991.9,849.7 1962.5,843.2 1940.4,849.5 1893.2,872.6 1903.3,891.6 1887.5,900 1883.5,895.6 1864.2,921.9 1856.2,936.3 
                                                 1866.7,938.6 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="162" data-tooltip="tooltip" data-placement="top" title="TRIANGULO NOVO">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 162); ?>" points="1863.7,975.4 1884.4,988.8 1893.6,982.1 1919.1,1010.3 1923.3,1027.5 1930,1025.7 1925.2,974.3 
                                                 1914.5,959.3 1870.9,942.6 1871.6,964.1 1869.5,970.1 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="28" data-tooltip="tooltip" data-placement="top" title="COMARA">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 28); ?>" points="1957.2,1132.4 2006.3,975.4 1925.2,974.3 1930,1025.7 1938.3,1099.6 1941,1122.3 1943.3,1126.5 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="90" data-tooltip="tooltip" data-placement="top" title="" data-original-title="TAQUARI">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 90); ?>" points="1853.6,989.5 1837.7,1008.4 1815.3,1032.6 1802.3,1046 1779.5,1069.1 1768,1080 1743.9,1096.9 
                                                 1714.4,1115.9 1687.8,1133.5 1666.8,1149 1655.4,1158.2 1681.6,1212 1759.3,1322.1 1784.2,1279.4 1797.2,1237.3 1796.2,1218.9 
                                                 1943.3,1126.5 1941,1122.3 1930,1025.7 1923.3,1027.5 1919.1,1010.3 1893.6,982.1 1884.4,988.8 1863.7,975.4 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="7" data-tooltip="tooltip" data-placement="top" title="AREAL">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 7); ?>" points="2030.3,1044 2006.3,975.4 1957.2,1132.4 1987.1,1140 2042.5,1202.6 2039,1186.3 2033.4,1176 
                                                 2030,1167.5 2027.5,1157.6 2027.2,1145.1 2024.3,1125.6 2040.9,1102.7 2036.1,1096.2 2037.5,1068.5 2042.3,1054.9 2047.8,1051.8 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="22" data-tooltip="tooltip" data-placement="top" title="CANAA">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 22); ?>" points="2062.3,1044.8 2081.3,1033.6 2093.1,1025.2 2095.9,1018.4 2091.1,1009.6 2090.8,999.9 2108.6,991.2 
                                                 2108.5,965.8 2116.5,957.4 2118.5,946.1 2118.8,933.5 2123.6,918.4 2123.5,909.3 2124.4,904.8 2127.5,898.9 2119,892.4 2118.5,876 
                                                 2129.5,868.3 2133.5,852.5 2130.1,838.5 2132.5,822 2138,806.9 2132.2,795.3 2127.3,792.8 2117.3,791.1 2109.6,786.3 2100.2,779.2 
                                                 2098.3,768.9 2103.7,763.9 2106.4,754.1 2104.4,744.4 2087.4,747.5 2088.8,752.8 2013.4,790.7 2042.7,863.8 2006.3,975.4 
                                                 2030.3,1044 2047.8,1051.8 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="78" data-tooltip="tooltip" data-placement="top" title="RECANTO DOS BURITIS">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 78); ?>" points="2107.1,1049 2103,1029.9 2093.1,1025 2075.3,1037 2042.3,1054.9 2037.5,1068.5 2036.4,1085.7 
                                                 2074.3,1107.6 2105.2,1105.1 2135.5,1072.8 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="82" data-tooltip="tooltip" data-placement="top" title="SANTA INES">
                                        <path class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 82); ?>" d="M2040.6,1103.8l-16.3,21.8l2.6,17.5l0.6,13.9c0,0,4.3,17.9,5.6,17.9c1.2,0,6.1,12.2,6.1,12.2l3.4,15.3l5.3,6.2
                                              l129.6-6.7l-2.1-40.2l-10.7-28.6l-4.6-18.1l13.7-4.2l-38.1-38.1l-30.3,32.2l-30.9,2.5l-37.8-21.9l-0.3,10.5L2040.6,1103.8z"></path>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="14" data-tooltip="tooltip" data-placement="top" title="BELO JARDIM I">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 14); ?>" points="2262.6,999.9 2119.9,1041.3 2108.3,1050 2135.5,1072.8 2173.6,1110.9 2159.9,1115.2 2164.6,1133.3 
                                                 2175.3,1161.9 2177.4,1202 2286.5,1203.4 2280.8,1033.7 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="15" data-tooltip="tooltip" data-placement="top" title="BELO JARDIM II">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 15); ?>" points="2420.9,958.7 2262.6,999.9 2280.8,1033.7 2286.5,1203.4 2585.1,1200.7 2578.7,1086.4 2565.1,1081.6 
                                                 2560.8,1041.5 "></polygon>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="61" data-tooltip="tooltip" data-placement="top" title="ROSA LINDA"> -->
                                    <path class="st1" d="M2273.6,1418.7l15.6,0.4l41.4,28.5c0,0,58.7-3.7,61.5-2.7c2.8,1,17.4,6.1,17.4,6.1l17.6-135.2h185.7
                                          c0,0-0.6-117.8-5.1-117.7c-4.4,0-22.6,2.7-22.6,2.7l-298.6,2.6L2273.6,1418.7z"></path>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="85" data-tooltip="tooltip" data-placement="top" title="SANTO AFONSO">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 85); ?>" points="2225.6,1398.7 2250.8,1418.3 2273.6,1418.7 2286.5,1203.4 2179.2,1202.4 2179.5,1221.3 2213.1,1264.9 
                                                 2215.1,1276.1 2189.5,1328.5 2217.5,1363.4 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="81" data-tooltip="tooltip" data-placement="top" title="SANTA CECILIA">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 81); ?>" points="2981.6,1126.9 2994.9,1334.4 3067.8,1389.4 3304.7,1376.1 3304.7,1144 3044.2,1162 3045.1,1126.9 "></polygon>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="61" data-tooltip="tooltip" data-placement="top" title="SANTA HELENA"> -->
                                    <polygon class="st1" points="2179.5,1202.1 2077.4,1208.1 2048.7,1209.8 2051,1231.1 2053.9,1285.4 2050.4,1295.6 2051,1332 
                                             2051.9,1430.4 2153.3,1418.7 2251,1418 2225.4,1399 2217.8,1363.2 2189.7,1328.3 2214.8,1276.3 2213.1,1264.9 2179.3,1219.8 "></polygon>
                                    <!-- </a> -->
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="61" data-tooltip="tooltip" data-placement="top" title="VILA DA AMIZADE"> -->
                                    <polygon class="st1" points="1918.5,1339.1 1913.7,1470.5 2051.6,1469.3 2051.9,1430.4 2051,1332 2043.4,1337.6 "></polygon>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="16" data-tooltip="tooltip" data-placement="top" title="" data-original-title="BENFICA">
                                        <path class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 16); ?>" d="M2274.9,1701.7l-112.3-71.9l-56,106l-41.4,73.3l68.2,79.6l12,36l94.7,103.6l31.6-101.1l34.7,2.5
                                              c0,0-15.8,24.6-10.7,27.2c5.1,2.5,62.5,55.6,62.5,55.6l57.5,4.4l106.7,15.2l114.3,36l10.7-56.2v-145.9l-18.3-63.8l-140.8,10.7
                                              l-19.6-3.2l-97.9-79.6L2274.9,1701.7z"></path>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="83" data-tooltip="tooltip" data-placement="top" title="" data-original-title="SANTA MARIA">
                                        <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="61" data-tooltip="tooltip" data-placement="top" title="ITUCUMA"> -->
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 83); ?>" points="2410.9,1451 2392.1,1444.9 2330.6,1447.6 2289.2,1419.1 2251,1418 2153.3,1418.7 2155.8,1467.4 
                                                 2173.8,1537.2 2193.1,1571.9 2162.5,1629.8 2274.9,1701.7 2370.9,1730.1 2468.8,1809.7 2488.4,1812.8 2629.2,1802.1 2680.4,1768.4 
                                                 2755.2,1710.7 2724,1702.8 2698.4,1696.1 2673.4,1689.6 2642.5,1681.5 2618.1,1646.5 2595.2,1609.2 2569.7,1583.2 2561.1,1552 
                                                 2558.1,1525.4 2565,1507.3 2591.9,1506.4 2613.8,1444.8 2565.4,1408.4 2540.7,1402.2 2531.4,1408.7 2523.6,1414.2 2508.4,1428.8 
                                                 2497.6,1444 2480.7,1453.1 2453,1464.3 2437.6,1469 2410,1466.7 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="86" data-tooltip="tooltip" data-placement="top" title="SAO FRANCISCO">
                                        <path class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 86); ?>" d="M2001.4,552.9l2.9,9l3.4,4.1l32,1.5l6-14.5l-0.4-12.6l9-9.1l14.8-5.7c0,0,6.4-3.7,8.5-3.2s14.3-8.2,14.3-8.2
                                              l0.6-6.4l-12.2-0.7l0.7-7.2l9.6-0.7l2.9-7.2l-15-16.2l-7.6-3.4l-30.7,16.8l-86.7-33.9l-28.2-12.6l-34.7-26.1l-6.1,4.6l-1.1,7.8
                                              l-25.5,6.1l-26.7,18.6l2.8,9.3l6.2,9.7l8.9-1.1l1.9-14.4l21.7,1l17.6,7.1l5.4,7.1l8.9-1.1l3.1,2.9l0.9,2.9l-1.1,5.2l-2.5,3.5
                                              l-8.9-0.1l-0.6,7.8l2.2,3.7l6.3,1.6l8.8-7.1l5-6l5.9-2.5l0.8,3.6l-0.5,5.3l2.5,5.9l6.5,0.6l5.5-2.8l4.2-10l3.5-4.8l4.4-1.6l4.6,4.2
                                              l-0.5,6.2l1.7,31.8l4.5,13.1l8,3.3l2.7,3.8l0.9,4.4l-1.4,6.8l0.1,3.7l11.5,10.7l2.1-3.6l0.4-7.7l3-5.7l6.7-1.8L2001.4,552.9z"></path>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="61" data-tooltip="tooltip" data-placement="top" title="OSCAR PASSOS"> -->
                                    <polygon class="st1" points="1964.4,492.1 1969.6,494.9 1981.4,503.2 2032.4,523.6 2035.7,517.9 2035.7,498.3 1965.6,470.9 "></polygon>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="67" data-tooltip="tooltip" data-placement="top" title="PLACAS">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 67); ?>" points="1882.5,392.8 1839.5,329.3 1826.1,336.1 1811.2,340.3 1789.7,345.8 1789.9,351.9 1793.1,357.7 
                                                 1795.8,370.3 1793.9,383 1791.1,390.7 1791.5,402.3 1801.6,414.3 1827.3,444 1831.1,453.7 1857.8,435.2 1883.3,429.1 1884.4,421.3 
                                                 1897.4,412 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="132" data-tooltip="tooltip" data-placement="top" title="VITORIA">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 132); ?>" points="1927.9,354.9 1882.5,392.8 1897.4,412 1890.5,416.6 1925.2,442.7 2040.2,489.3 2070.9,472.4 
                                                 2068.8,405.1 1971.9,365.1 1964.4,374.5 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="25" data-tooltip="tooltip" data-placement="top" title="CHICO MENDES">
                                        <path class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 25); ?>" d="M1985.9,354.9c0,0-5.4-18.1-6.3-18.4s-49.6-40.1-49.6-40.1l-6,8.8l-46.1-24.6l6-12l-14.1-8l-6.1,28.8l-9.4,19.9
                                              l-14.8,20l43,63.5l45.5-37.9l36.4,19.6l7.6-9.5l8.2,3.4L1985.9,354.9z"></path>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="61" data-tooltip="tooltip" data-placement="top" title="OURICURI"> -->
                                    <polygon class="st1" points="1989.1,259.2 1958.5,270.5 1946.2,270.5 1927.2,260.7 1887.1,256.3 1869.8,260.7 1883.9,268.6 
                                             1877.9,280.6 1924,305.3 1930,296.4 1965.4,325.2 1973.6,318.5 1995.4,290.8 "></polygon>
                                    <!-- </a> -->
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="61" data-tooltip="tooltip" data-placement="top" title="JAGUAR"> -->
                                    <polygon class="st1" points="1999.4,219.2 1873,208 1874,235.9 1869.8,260.7 1887.1,256.3 1927.2,260.7 1946.2,270.5 1958.5,270.5 
                                             1989.1,259.2 2005.2,256.3 2005.5,225 "></polygon>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="6" data-tooltip="tooltip" data-placement="top" title="" data-original-title="APOLONIO SALES">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 6); ?>" points="1964,72 1904.5,71.9 1867.8,74.9 1784.5,77.7 1771.5,76.6 1772.3,82.6 1781.7,92.7 1778.1,100.3 
                                                 1769.4,106.5 1763.6,120.9 1753.4,169.1 1752.3,182.3 1761.5,188.4 1791.9,193.5 1797.9,201 1815.1,201.6 1829.7,198.4 
                                                 1851.4,186.3 1860.4,185.2 1873,208 1999.4,219.2 1997.5,160.4 1971.5,144.8 1969.5,79.3 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="40" data-tooltip="tooltip" data-placement="top" title="ELDORADO">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 40); ?>" points="2063.9,223.1 2005.5,225 2005.2,256.3 1989.1,259.2 1995.4,290.8 1973.6,318.5 1965.4,325.2 
                                                 1979.6,336.5 1985.9,354.9 1980.2,368.5 2068.8,405.1 2074,389.3 2068.4,370.6 2109.8,346.3 2099.5,330.8 2099.7,312.2 
                                                 2112.1,301.5 2111.2,294 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="4" data-tooltip="tooltip" data-placement="top" title="ALTO ALEGRE">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 4); ?>" points="1769.6,45.5 1639.1,49.7 1646.7,164.6 1635.7,234.9 1635.7,256.3 1645,276.6 1670.7,303.2 
                                                 1679.5,247.6 1749,195.4 1752.3,182.3 1753.4,169.1 1763.6,120.9 1769.4,106.5 1778.1,100.3 1781.7,92.7 1772.3,82.6 1771.5,76.6 
                                                 "></polygon>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="61" data-tooltip="tooltip" data-placement="top" title="PARQUES DOS SABIAS"> -->
                                    <polygon class="st1" points="1752.3,201.4 1839.5,329.3 1855.7,306.2 1863.7,289.5 1869.8,260.7 1874,239.2 1873,208 1860.4,185.2 
                                             1851.4,186.3 1829.7,198.4 1815.1,201.6 1797.9,201 1791.9,193.5 1761.5,188.4 1752.3,182.3 1749,195.4 "></polygon>
                                    <!--  </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="101" data-tooltip="tooltip" data-placement="top" title="XAVIER MAIA">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 101); ?>" points="1762.7,287.3 1775,279.4 1819.8,336.8 1839.5,329.3 1749,195.4 1732.2,208 1756.4,234.8 1752.1,256.3 
                                                 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="29" data-tooltip="tooltip" data-placement="top" title="ADALBERTO SENA">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 29); ?>" points="1722.3,303.2 1733.6,260.7 1752.1,256.3 1756.4,234.8 1732.2,208 1679.5,247.6 1674.9,276.9 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="134" data-tooltip="tooltip" data-placement="top" title="WANDERLEY DANTAS">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 134); ?>" points="1710.3,346.9 1747.2,342.8 1789.9,351.9 1789.7,345.8 1819.8,336.8 1775,279.4 1762.7,287.3 
                                                 1752.1,256.3 1733.6,260.7 1722.3,303.2 1674.9,276.9 1670.7,303.2 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="73" data-tooltip="tooltip" data-placement="top" title="RAIMUNDO MELO">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 73); ?>" points="1706.2,368.4 1710.3,381.1 1710.3,388.6 1716.9,398.6 1735.8,394.5 1736.9,404.2 1736.3,411.6 
                                                 1741.6,413.7 1742.1,407 1761.3,395.2 1771,394.2 1774.4,406 1769.3,409.1 1769.4,414.8 1779,428.7 1792.5,433 1792.7,438.9 
                                                 1780.1,442.4 1784.3,450.9 1796.2,454.5 1798.4,476.8 1809.7,476.8 1815.4,494.9 1822.8,494.4 1831.1,491.6 1838.4,485.9 
                                                 1841.2,479.1 1840.1,472.7 1833.9,463 1827.3,444 1791.5,402.3 1791.1,390.7 1793.9,383 1795.8,370.3 1793.1,357.7 1789.9,351.9 
                                                 1747.2,342.8 1710.3,346.9 "></polygon>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="61" data-tooltip="tooltip" data-placement="top" title="VILA NOVA"> -->
                                    <polygon class="st1" points="1610,385.2 1614.9,387.2 1621.8,395.9 1632.4,397.1 1635.4,385.5 1634.7,375.4 1643.8,376.6 
                                             1647.3,383.6 1656.9,388.1 1672.2,389 1686.6,392.8 1695,386.9 1710.3,388.6 1710.3,381.1 1706.2,368.4 1710.3,346.9 1670.7,303.2 
                                             "></polygon>
                                    <!-- </a> -->
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="30" data-tooltip="tooltip" data-placement="top" title="BELA VISTA">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 30); ?>" points="1548.3,316.3 1566.1,393.5 1571.7,391.7 1586.5,379.6 1603.4,379.3 1610,385.2 1670.7,303.2 
                                                 1645,276.6 1639.7,276.6 1588.2,308.7 1580.5,313.9 1568.9,318.5 1560.7,319.2 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="124" data-tooltip="tooltip" data-placement="top" title="" data-original-title="DEFESA CIVIL">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 124); ?>" points="1578.4,245.1 1535.9,250.2 1531,276.6 1503.6,301.5 1548.3,316.3 1560.7,319.2 1568.9,318.5 
                                                 1580.5,313.9 1611.2,294.4 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="89" data-tooltip="tooltip" data-placement="top" title="TANCREDO NEVES">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 89); ?>" points="1530.3,209.9 1535.9,250.2 1578.4,245.1 1611.2,294.4 1639.7,276.6 1645,276.6 1635.7,256.3 
                                                 1635.7,234.9 1643.9,182.3 1626.9,178.9 1546.4,196.9 1548.3,204.5 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="60" data-tooltip="tooltip" data-placement="top" title="MONTANHES">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 60); ?>" points="1498.4,53.6 1453.2,107.3 1546.4,196.9 1626.9,178.9 1643.9,182.3 1646.7,164.6 1639.1,49.7 "></polygon>
                                    </a>
                                    <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="53" data-tooltip="tooltip" data-placement="top" title="JORGE FELIX LAVOCAT">
                                        <polygon class="<?= carregar_cor_bairro($reg_22, $reg_15, $reg_30, $reg_40, 53); ?>" points="1366.1,186.8 1376.6,174.3 1453.2,107.3 1546.4,196.9 1548.3,204.5 1530.3,209.9 1535.9,250.2 
                                                 1531,276.6 1503.6,301.5 1414.7,276.6 1402.1,266.1 1394.8,245.1 1406.5,232 1370.5,200.1 "></polygon>
                                    </a>
                                    <!-- <a data-toggle="modal" data-target="#modal_1" onclick="carregar_bairro(this)" style="cursor: pointer" id="61" data-tooltip="tooltip" data-placement="top" title="IRINEU SERRA"> -->
                                    <polygon class="st1" points="1343,125.5 1317.8,133.1 1251.3,145 1168.4,160.1 1103,171.9 1103.3,179.1 1120.4,189.7 1169.3,211.7 
                                             1227.4,237.7 1231.5,251.3 1229.1,255.2 1229.1,270.8 1243.5,275.5 1243.7,281 1231,317.6 1231.6,327.6 1247.7,323.5 1249.5,310.8 
                                             1256.5,309.6 1262.5,313 1263.3,319.3 1260.9,330.1 1276.5,336.7 1284.2,332.6 1287.5,327.5 1294.5,327.7 1304.6,338.1 
                                             1303.3,327.7 1312.3,330.4 1318.6,339.9 1328.8,339.9 1333,349.3 1343.1,354.7 1354.1,353.5 1366,367.4 1371.1,364.6 1374.1,360.5 
                                             1381.2,358.6 1386,359.9 1394.2,359.1 1398.6,354.5 1404.2,356 1406.8,365.9 1399.6,370.5 1393.1,371.8 1387.9,375.6 1393.4,377.6 
                                             1400.4,375.2 1408.2,375.8 1407.4,381.1 1401.5,385.6 1401.7,390.8 1407.3,392.9 1403.5,400.4 1410.4,402.1 1426,391.7 
                                             1426.3,384.8 1432.2,381.6 1437.2,386.3 1438.3,401 1449.7,406.5 1456.3,408 1462.7,413.6 1459.5,418.7 1451.7,420.6 1453.8,427.1 
                                             1459.5,431.6 1464.9,426.1 1472,423.7 1475.3,427.4 1481.2,427.9 1481.3,433.3 1479.3,439.9 1486.8,441.2 1481,448.1 1499.1,453.2 
                                             1505.5,453.9 1506,470.3 1522.2,469.8 1522.4,475 1525.4,481.1 1529.3,469.5 1528.9,456.1 1538.3,455.9 1538.3,462.1 1545.7,464.3 
                                             1553.8,462.8 1557,456.1 1556.9,450.9 1554.4,444.3 1556.6,434.4 1562.4,423.7 1567.6,422.1 1567.1,407.9 1561.1,406.6 
                                             1555.6,400.8 1560.2,395.5 1566.1,393.5 1548.3,316.3 1503.6,301.5 1414.7,276.6 1402.1,266.1 1394.8,245.1 1406.5,232 
                                             1370.5,200.1 1366.1,186.8 1374.3,177 1367.8,175.5 1355.6,144.5 "></polygon>
                                    <!-- </a> -->
                                    </svg>

                                </div>
                            </div>

                        </div>
                        <!-- Fim do Corpo do Site -->

                        <!-- Modal -->
                        <div class="modal fade modal-info" id="modal_1" role="dialog">
                            <div class="modal-dialog modal_bairro" role="document">
                                <div id="carregar_dados_div" class="modal-content"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include("template/layout/dashboard/rodape.php");
?>

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/apuracao/mapa_bairro.js"></script>