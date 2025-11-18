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
            <th scope="col" class="text-center">REGIONAL</th>
            <th scope="col" class="text-center">BAIRRO|VILA|RAMAL</th>
            <th scope="col" class="text-center">LOCAL DE VOTAÇÃO</th>
            <th scope="col" class="text-center">ENDEREÇO</th>
            <th scope="col" class="text-center">TELEFONE</th>
            <th scope="col" class="text-center">ZONA</th>
            <th scope="col" class="text-center">SEÇÃO</th>
            <th scope="col" class="text-center">TOTAL DE APTOS A VOTAR</th>
            <th scope="col" class="text-center">QTD DE URNAS</th>
            <th scope="col" class="text-center">QTD TOTAL DE FISCALIZAÇÃO</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $result = $db->prepare("SELECT s.LOCAL_VOTACAO_ID, r.nome AS REGIONAL, v.NM_BAIRRO AS BAIRRO, v.NM_LOCAL_VOTACAO AS NOME_LOCAL, v.DS_ENDERECO, v.NR_TELEFONE_LOCAL AS TELEFONE, v.NR_ZONA AS ZONA, s.NR_SECAO AS SECAO, SUM(s.QT_ELEITOR_AGREGADO) AS APTOS
                                FROM 2024_locais_votacao AS v 
                                INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = v.ID 
                                INNER JOIN sys_regionais AS r on r.id = v.REGIONAL_ID
                                WHERE s.TIPO = 'Principal'
                                GROUP BY v.ID;");
        $result->execute();
        while ($locais = $result->fetch(PDO::FETCH_ASSOC)) {
            
            $qtd_urnas = qtd_urnas_local($locais['LOCAL_VOTACAO_ID']);
            
            ?>
            <tr>
                <td><?= $locais['REGIONAL']; ?></td>
                <td><?= $locais['BAIRRO']; ?></td>
                <td><?= $locais['NOME_LOCAL']; ?></td>
                <td><?= $locais['DS_ENDERECO']; ?></td>
                <td><?= $locais['TELEFONE'] != NULL && $locais['TELEFONE'] != "" && strlen($locais['TELEFONE']) >= 9 ? formatarTelefone($locais['TELEFONE']) : ""; ?></td>
                <td><?= $locais['ZONA']; ?></td>
                <td><?= retorna_secoes($locais['LOCAL_VOTACAO_ID']); ?><?= retornar_agregacao($locais['ZONA'], $locais['SECAO']); ?></td>
                <td><?= $locais['APTOS']; ?></td>
                <td><?= $qtd_urnas; ?></td>
                <td><?= $qtd_urnas > 2 ? (($qtd_urnas * 2) + 1) : ($qtd_urnas * 2); ?></td>
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