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
            <th scope="col" class="text-center">ENDEREÇO LOCAL</th>
            <th scope="col" class="text-center">TELEFONE LOCAL</th>

            <th scope="col" class="text-center">SUPERVISOR_NOME</th>
            <th scope="col" class="text-center">SUPERVISOR TELEFONE</th>

            <th scope="col" class="text-center">ZONA</th>
            <th scope="col" class="text-center">SECAO (Uma Principal + agrega(s)</th>
            <th scope="col" class="text-center">FISCAL 1</th>
            <th scope="col" class="text-center">FISCAL 1 - TELEFONE</th>
            <th scope="col" class="text-center">FISCAL 2</th>
            <th scope="col" class="text-center">FISCAL 2 - TELEFONE</th>
            <th scope="col" class="text-center">QTD FISCAIS (somar FISCAL 1 + FISCAL 2)</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $result = $db->prepare("SELECT s.LOCAL_VOTACAO_ID, r.nome AS REGIONAL, v.NM_BAIRRO AS BAIRRO, v.NM_LOCAL_VOTACAO AS NOME_LOCAL, v.DS_ENDERECO, v.NR_TELEFONE_LOCAL AS TELEFONE, v.NR_ZONA AS ZONA, s.NR_SECAO AS SECAO, SUM(s.QT_ELEITOR_AGREGADO) AS APTOS
                                FROM 2024_locais_votacao AS v 
                                INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = v.ID 
                                INNER JOIN sys_regionais AS r on r.id = v.REGIONAL_ID
                                WHERE s.TIPO = 'Principal'
                                GROUP BY s.id");
        $result->execute();
        while ($locais = $result->fetch(PDO::FETCH_ASSOC)) {

            $fiscal_1 = "";
            $fiscal_2 = "";

            $qtd = 0;

            $fiscal_1 = vf_secao_fiscal($locais['ZONA'], $locais['SECAO'], 5, 1, "nome");
            $fiscal_2 = vf_secao_fiscal($locais['ZONA'], $locais['SECAO'], 5, 2, "nome");

            if ($fiscal_1 != "") {
                $qtd++;
            }

            if ($fiscal_2 != "") {
                $qtd++;
            }
            ?>
            <tr>
                <td><?= $locais['REGIONAL']; ?></td>
                <td><?= $locais['BAIRRO']; ?></td>
                <td><?= $locais['NOME_LOCAL']; ?></td>
                <td><?= $locais['DS_ENDERECO']; ?></td>
                <td><?= $locais['TELEFONE'] != NULL && $locais['TELEFONE'] != "" && strlen($locais['TELEFONE']) >= 9 ? formatarTelefone($locais['TELEFONE']) : ""; ?></td>
                <td><?= contato_supervisor($locais['LOCAL_VOTACAO_ID'], $locais['ZONA'], $locais['SECAO'], "nome"); ?></td>
                <td><?= contato_supervisor($locais['LOCAL_VOTACAO_ID'], $locais['ZONA'], $locais['SECAO'], "contato"); ?></td>
                <td><?= $locais['ZONA']; ?></td>
                <td><?= retorna_secoes($locais['LOCAL_VOTACAO_ID']); ?><?= retornar_agregacao($locais['ZONA'], $locais['SECAO']); ?></td>
                <td><?= $fiscal_1; ?></td>
                <td><?= vf_secao_fiscal($locais['ZONA'], $locais['SECAO'], 5, 1, "celular"); ?></td>
                <td><?= $fiscal_2; ?></td>
                <td><?= vf_secao_fiscal($locais['ZONA'], $locais['SECAO'], 5, 2, "celular"); ?></td>
                <td><?= $qtd; ?></td>
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