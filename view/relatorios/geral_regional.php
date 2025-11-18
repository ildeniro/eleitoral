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
            <th scope="col" class="text-center">NOME_VOLUNTARIO</th>
            <th scope="col" class="text-center">CPF</th>
            <th scope="col" class="text-center">DATA DE NASCIMENTO</th>
            <th scope="col" class="text-center">TELEFONE</th>
            <th scope="col" class="text-center">SEXO</th>
            <th scope="col" class="text-center">LOCAL_VOTACAO</th>
            <th scope="col" class="text-center">ZONA</th>
            <th scope="col" class="text-center">SECAO</th>
            <th scope="col" class="text-center">TIPO (FISCAL OU SUPERV)</th>
            <th scope="col" class="text-center">CONDICAO FISICA</th>
            <th scope="col" class="text-center">INDICACAO</th>
            <th scope="col" class="text-center">BAIRRO_VOLUNTARIO</th>
            <th scope="col" class="text-center">REGIONA_VOLUNTARIO</th>
            <th scope="col" class="text-center">SITUACAO</th>
            <th scope="col" class="text-center">TREINAMENTO</th>
            <th scope="col" class="text-center">DISTRIBUIDO</th>
            <th scope="col" class="text-center">D_LOCAL_VOTACAO</th>
            <th scope="col" class="text-center">D_ZONA</th>
            <th scope="col" class="text-center">D_SECAO</th>
            <th scope="col" class="text-center">D_REGIONAL</th>
            <th scope="col" class="text-center">D_BAIRRO</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $result = $db->prepare("SELECT v.id, v.nome AS voluntario, v.cpf, v.nascimento, v.celular, v.sexo, v.zona, v.secao_numero, sf.nome AS tipo, v.deficiencia,
                                b.NM_BAIRRO, b.REGIONAL_NOME, v.status, v.tipo_cancelamento, v.treinamento, v.zona_2, v.secao_numero_2,
                                lv.NM_LOCAL_VOTACAO 
                                FROM 2024_voluntarios AS v
                                LEFT JOIN 2024_secoes AS s ON s.NR_ZONA = v.zona_2 AND s.NR_SECAO = v.secao_numero_2 
                                LEFT JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID 
                                INNER JOIN sys_funcoes AS sf ON sf.id = v.funcao_id 
                                INNER JOIN bsc_bairros AS b ON b.ID = v.bsc_bairros_id 
                                WHERE 1
                                GROUP BY v.id");
        $result->execute();
        while ($geral = $result->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <tr>
                <td><?= ctexto($geral['voluntario'], "mai") ?></td>
                <td><?= $geral['cpf']; ?></td>
                <td><?= obterDataBRTimestamp($geral['nascimento']); ?></td>
                <td><?= $geral['celular']; ?></td>
                <td><?= $geral['sexo'] == 1 ? "Masculino" : ($geral['sexo'] == 2 ? "Feminino" : "Outros"); ?></td>
                <td><?= $geral['NM_LOCAL_VOTACAO']; ?></td>
                <td><?= $geral['zona']; ?></td>
                <td><?= $geral['secao_numero']; ?></td>
                <td><?= $geral['tipo']; ?></td>
                <td><?= $geral['deficiencia'] == 1 ? "Deficiente" : ($geral['deficiencia'] == 2 ? "Mobilidade Reduzida" : "Não Deficiente"); ?></td>
                <td><?= vf_indicacao($geral['id']); ?></td>
                <td><?= $geral['NM_BAIRRO']; ?></td>
                <td><?= $geral['REGIONAL_NOME']; ?></td>
                <td><?= status_voluntario_nome($geral['status']); ?></td>
                <td><?= $geral['treinamento'] == 1 ? "SIM" : "NÃO"; ?></td>
                <td><?= $geral['zona_2'] != "" && $geral['secao_numero_2'] != "" ? "SIM" : "NÃO"; ?></td>
                <td><?= carregar_local_votacao($geral['zona_2'], $geral['secao_numero_2']); ?></td>
                <td><?= $geral['zona_2']; ?></td>
                <td><?= $geral['secao_numero_2']; ?></td>
                <td><?= carregar_regional_votacao($geral['zona_2'], $geral['secao_numero_2']); ?></td>
                <td><?= carregar_bairro_votacao($geral['zona_2'], $geral['secao_numero_2']); ?></td>
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