// ============================================================== 
// Gross Profit Margin
// ============================================================== 
$('input#pesquisar').keyup(function () {

    var pesquisa = $(this).val().replace("'", "´");

    var url = PORTAL_URL + "ajax/pesquisa_partidos.php?pesquisa=" + pesquisa;
    ajax(url);

});
//------------------------------------------------------------------------------
var apuradas = $("input#apuradas").val();
var nao_apuradas = $("input#nao_apuradas").val();

Morris.Donut({
    element: 'morris_gross',

    data: [
        {value: apuradas, label: 'Apuradas'},
        {value: nao_apuradas, label: 'Não Apuradas'}

    ],

    labelColor: '#5969ff',

    colors: [
        '#5969ff',
        '#a8b0ff'

    ],

    formatter: function (x) {
        return x + "%"
    },
    resize: true

});
// ============================================================== 