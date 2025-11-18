//----------------------------------------------------------------------------------------
ordenar_div_candidatos();
//----------------------------------------------------------------------------------------
$('a#link_rio_branco').addClass('active');
$('a#link_rio_branco').attr('href', PORTAL_URL + 'comparacao.php');
//----------------------------------------------------------------------------------------
$('li#li_filtros').addClass('active');
$('a#a_comparacao').addClass('active');
//----------------------------------------------------------------------------------------
$('#check_22').change(function () {
    $('#gov_22').toggle();
});
//----------------------------------------------------------------------------------------
$('#check_30').change(function () {
    $('#gov_30').toggle();
});
//----------------------------------------------------------------------------------------
$('#check_15').change(function () {
    $('#gov_15').toggle();
});
//----------------------------------------------------------------------------------------
$('#check_40').change(function () {
    $('#gov_40').toggle();
});
//----------------------------------------------------------------------------------------
$('#check_brancos').change(function () {
    $('#gov_brancos').toggle();
});
//----------------------------------------------------------------------------------------
$('#check_nulos').change(function () {
    $('#gov_nulos').toggle();
});
//----------------------------------------------------------------------------------------
$('#check_agrupar').change(function () {

    $('#div_candidatos').toggle();

    $('#gov_agrupados').toggle();
});

//----------------------------------------------------------------------------------------
$('input.check_candidatos').change(function () {

    var qtd = 0;

    $('input.check_candidatos').each(function () {

        if ($(this).is(':checked') && $(this).parents('label').find('input#check_agrupar').html() == undefined) {
            qtd++;
        }
    });

    if (qtd > 1) {
        $('input#check_agrupar').parents('tfoot').show();
    } else {
        $('input#check_agrupar').parents('tfoot').hide();
        $('input#check_agrupar').prop('checked', false);
        $('#div_candidatos').show();
        $('#gov_agrupados').hide();
    }

    var votos_22 = Number($('b#votos_22').html().replace(/\D+/g, ''));
    var votos_30 = Number($('b#votos_30').html().replace(/\D+/g, ''));
    var votos_15 = Number($('b#votos_15').html().replace(/\D+/g, ''));
    var votos_40 = Number($('b#votos_40').html().replace(/\D+/g, ''));
    
    var votos_brancos = Number($('b#votos_brancos').html().replace(/\D+/g, ''));
    var votos_nulos = Number($('b#votos_nulos').html().replace(/\D+/g, ''));
    var votos_agrupados = 0;
    var votos_agrupados_porc = 0;
    var numeros_agrupados = "";

    var soma_total = votos_22;

    if ($('div#gov_30').attr('style') != 'display: none;') {
        soma_total += Number(votos_30);
        votos_agrupados += Number(votos_30);
        numeros_agrupados += "30, ";
    }

    if ($('div#gov_15').attr('style') != 'display: none;') {
        soma_total += Number(votos_15);
        votos_agrupados += Number(votos_15);
        numeros_agrupados += "15, ";
    }

    if ($('div#gov_40').attr('style') != 'display: none;') {
        soma_total += Number(votos_40);
        votos_agrupados += Number(votos_40);
        numeros_agrupados += "40, ";
    }

    if ($('div#gov_brancos').attr('style') != 'display: none;') {
        soma_total += Number(votos_brancos);
        votos_agrupados += Number(votos_brancos);
        numeros_agrupados += "brancos, ";
    }

    if ($('div#gov_nulos').attr('style') != 'display: none;') {
        soma_total += Number(votos_nulos);
        votos_agrupados += Number(votos_nulos);
        numeros_agrupados += "nulos, ";
    }
//MOSTRANDO A PORCENTAGEM NA TELA-----------------------------------------------
    votos_22 = ((100 / soma_total) * votos_22);

    $('div#porc_22').html(fdec(votos_22) + "%");
    $('div#gov_22').find('div.bar').attr('style', 'width: ' + votos_22 + '%');

    if ($('div#gov_30').attr('style') != 'display: none;') {
        votos_30 = ((100 / soma_total) * votos_30);

        $('div#porc_30').html(fdec(votos_30) + "%");
        $('div#gov_30').find('div.bar').attr('style', 'width: ' + votos_30 + '%');
    }

    if ($('div#gov_15').attr('style') != 'display: none;') {
        votos_15 = ((100 / soma_total) * votos_15);

        $('div#porc_15').html(fdec(votos_15) + "%");
        $('div#gov_15').find('div.bar').attr('style', 'width: ' + votos_15 + '%');
    }

    if ($('div#gov_40').attr('style') != 'display: none;') {
        votos_40 = ((100 / soma_total) * votos_40);

        $('div#porc_40').html(fdec(votos_40) + "%");
        $('div#gov_40').find('div.bar').attr('style', 'width: ' + votos_40 + '%');
    }

    if ($('div#gov_brancos').attr('style') != 'display: none;') {
        votos_brancos = ((100 / soma_total) * votos_brancos);

        $('div#porc_brancos').html(fdec(votos_brancos) + "%");
        $('div#gov_brancos').find('div.bar').attr('style', 'width: ' + votos_brancos + '%');
    }

    if ($('div#gov_nulos').attr('style') != 'display: none;') {
        votos_nulos = ((100 / soma_total) * votos_nulos);

        $('div#porc_nulos').html(fdec(votos_nulos) + "%");
        $('div#gov_nulos').find('div.bar').attr('style', 'width: ' + votos_nulos + '%');
    }

    if ($('div#gov_agrupados').attr('style') != 'display: none;') {
        votos_agrupados_porc = ((100 / soma_total) * votos_agrupados);
        $('b#votos_agrupados').html(votos_agrupados + " votos");
        $('div#porc_agrupados').html(fdec(votos_agrupados_porc) + "%");
        $('div#gov_agrupados').find('div.bar').attr('style', 'width: ' + votos_agrupados_porc + '%');

        numeros_agrupados = numeros_agrupados.substring(0, (numeros_agrupados.length - 2));

        $('div#gov_agrupados').find('span#agrupado_numeros').html(numeros_agrupados);
    } else {
        ordenar_div_candidatos();
    }

});
//----------------------------------------------------------------------------------------
function fdec(numero) {

    var formato = null;
    switch (formato) {
        case null:
            if (numero != 0)
                numero = number_format(numero, 2, ',', '.');
            else
                numero = '0,00';
            break;
    }
    return numero;
}
//------------------------------------------------------------------------------------------------------
function number_format(number, decimals, dec_point, thousands_sep) {
// * example 1: number_format(1234.56);
// * returns 1: '1,235'
// * example 2: number_format(1234.56, 2, ',', ' ');
// * returns 2: '1 234,56'
// * example 3: number_format(1234.5678, 2, '.', '');
// * returns 3: '1234.57'
// * example 4: number_format(67, 2, ',', '.');
// * returns 4: '67,00'
// * example 5: number_format(1000);
// * returns 5: '1,000'
// * example 6: number_format(67.311, 2);
// * returns 6: '67.31'
// * example 7: number_format(1000.55, 1);
// * returns 7: '1,000.6'
// * example 8: number_format(67000, 5, ',', '.');
// * returns 8: '67.000,00000'
// * example 9: number_format(0.9, 0);
// * returns 9: '1'
// * example 10: number_format('1.20', 2);
// * returns 10: '1.20'
// * example 11: number_format('1.20', 4);
// * returns 11: '1.2000'
// * example 12: number_format('1.2000', 3);
// * returns 12: '1.200'
// * example 13: number_format('1 000,50', 2, '.', ' ');
// * returns 13: '100 050.00'
// Strip all characters but numerical ones.
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}
//------------------------------------------------------------------------------------------------------
function ordenar_div_candidatos() {
    var items = $('div#div_candidatos').find("div.candidates");

    var numericallyOrderedDivs = items.sort(function (a, b) {
        if ($(a).find("b.votos_candidatos").text().replace(/[^0-9]/g, '') != '' && $(b).find("b.votos_candidatos").text().replace(/[^0-9]/g, '') != '' &&
                $(a).find("b.votos_candidatos").attr('style') != 'display: none;' && $(b).find("b.votos_candidatos").attr('style') != 'display: none;') {

            var num1 = $(a).find("b.votos_candidatos").text().replace(/[^0-9]/g, '') < 10 ? "0" + $(a).find("b.votos_candidatos").text().replace(/[^0-9]/g, '') : $(a).find("b.votos_candidatos").text().replace(/[^0-9]/g, '');
            var num2 = $(b).find("b.votos_candidatos").text().replace(/[^0-9]/g, '') < 10 ? "0" + $(b).find("b.votos_candidatos").text().replace(/[^0-9]/g, '') : $(b).find("b.votos_candidatos").text().replace(/[^0-9]/g, '');

            return Number(num1) < Number(num2);
        }
    });

    $("div#div_candidatos").html(numericallyOrderedDivs);
}
//------------------------------------------------------------------------------------------------------