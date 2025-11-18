<?php
include("template/layout/dashboard/topo.php");
?>

<?php
if (!ver_nivel(1)) {
    msg('Você não possui permissão para acessar essa área.');
    url(PORTAL_URL . 'view/admin/dashboard');
}
?>

<table id="tabela_1" class="table table-sm table-nowrap table-striped table-bordered display">
    <thead>
        <tr>
            <th scope="col" class="text-center">ZONA</th>
            <th scope="col" class="text-center">SEÇÃO</th>
            <th scope="col" class="text-center">AGREGADAS</th>
            <th scope="col" class="text-center">COD LOCAL</th>
            <th scope="col" class="text-center">LOCAL</th>
            <th scope="col" class="text-center">BAIRRO</th>
            <th scope="col" class="text-center">REGIONAL</th>
            <th scope="col" class="text-center">LATLONG</th>
            <th scope="col" class="text-center">CARGO</th>
            <th scope="col" class="text-center">CANDIDATO</th>
            <th scope="col" class="text-center">PARTIDO</th>
            <th scope="col" class="text-center">TIPO</th>
            <th scope="col" class="text-center">VOTOS</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $result = $db->prepare("SELECT sr.nome AS REGIONAL, r.QT_VOTOS, r.NR_ZONA, r.NR_SECAO, lv.NM_BAIRRO, lv.NM_LOCAL_VOTACAO, lv.GEOLOCALIZACAO, 
                                c.NM_URNA_CANDIDATO, r.DS_TIPO_VOTAVEL, lv.NR_LOCAL_VOTACAO, p.SG_PARTIDO    
                                FROM 2024_resultado_tse AS r 
                                INNER JOIN 2024_secoes AS s ON s.NR_ZONA = r.NR_ZONA AND s.NR_SECAO = r.NR_SECAO
                                INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID
                                INNER JOIN sys_regionais AS sr ON sr.id = lv.REGIONAL_ID 
                                LEFT JOIN 2024_candidatos AS c ON c.NR_CANDIDATO = r.NR_VOTAVEL AND c.CD_CARGO = 11 AND c.ANO_ELEICAO = 2024 AND c.SG_UE = 01392 
                                LEFT JOIN sys_partidos AS p ON p.NR_PARTIDO = r.NR_PARTIDO 
                                WHERE r.DS_CARGO_PERGUNTA = 'Prefeito' AND r.ANO_ELEICAO = 2024 AND r.CD_MUNICIPIO = 1392  
                                GROUP BY r.ID");
        $result->execute();
        while ($regionais = $result->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <tr>
                <td class="col-1 text-center"><?= $regionais['NR_ZONA']; ?></td>
                <td class="col-1 text-center"><?= $regionais['NR_SECAO']; ?></td>
                <td class="col-1 text-center"><?= removePrimeiraVirgula(retornar_agregacao($regionais['NR_ZONA'], $regionais['NR_SECAO'])); ?></td>
                <td class="col-1 text-center"><?= $regionais['NR_LOCAL_VOTACAO']; ?></td>
                <td class="col-1 text-center"><?= $regionais['NM_LOCAL_VOTACAO']; ?></td>
                <td class="col-1 text-center"><?= $regionais['NM_BAIRRO']; ?></td>
                <td class="col-1 text-center"><?= $regionais['REGIONAL']; ?></td>
                <td class="col-1 text-center"><?= $regionais['GEOLOCALIZACAO']; ?></td>
                <td class="col-1 text-center">PREFEITO</td>
                <td class="col-1 text-center"><?= $regionais['NM_URNA_CANDIDATO']; ?></td>
                <td class="col-1 text-center"><?= $regionais['SG_PARTIDO']; ?></td>
                <td class="col-1 text-center"><?= $regionais['DS_TIPO_VOTAVEL']; ?></td>
                <td class="col-1 text-center"><?= $regionais['QT_VOTOS']; ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>

<?php
include("template/layout/dashboard/rodape.php");
?>

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/fiscal/distribuicao.js"></script>