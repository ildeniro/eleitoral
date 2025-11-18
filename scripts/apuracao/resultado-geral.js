$("body").addClass('sidebar-icon-only');

$(document).ready(function () {
    setTimeout(function () {
        document.location.reload(true);
    }, 60000);
});
//----------------------------------------------------------------------------------------
ordenar_div_candidatos();
//----------------------------------------------------------------------------------------
$('li#li_candidatos').addClass('active');
//----------------------------------------------------------------------------------------
$('a#link_rio_branco').addClass('active');
$('a#link_rio_branco').attr('href', PORTAL_URL + 'candidatos.php');
$('a#link_cruzeiro_do_sul').attr('href', PORTAL_URL + 'candidatos_c.php');
//----------------------------------------------------------------------------------------
function ordenar_div_candidatos() {
    var items = $('div#div_candidatos').find("div.candidates");

    var numericallyOrderedDivs = items.sort(function (a, b) {
        if ($(a).find("b.votos_candidatos").text().replace(/[^0-9]/g, '') != '' && $(b).find("b.votos_candidatos").text().replace(/[^0-9]/g, '') != '' &&
                $(a).find("b.votos_candidatos").attr('style') != 'display: none;' && $(b).find("b.votos_candidatos").attr('style') != 'display: none;') {

            var num1 = $(a).find("b.votos_candidatos").text().replace(/[^0-9]/g, '') < 10 ? "0" + $(a).find("b.votos_candidatos").text().replace(/[^0-9]/g, '') : $(a).find("b.votos_candidatos").text().replace(/[^0-9]/g, '');
            var num2 = $(b).find("b.votos_candidatos").text().replace(/[^0-9]/g, '') < 10 ? "0" + $(b).find("b.votos_candidatos").text().replace(/[^0-9]/g, '') : $(b).find("b.votos_candidatos").text().replace(/[^0-9]/g, '');

            return num1 < num2;
        }
    });

    $("div#div_candidatos").html(numericallyOrderedDivs);
}
//------------------------------------------------------------------------------------------------------