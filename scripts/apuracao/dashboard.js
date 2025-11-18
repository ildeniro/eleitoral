//----------------------------------------------------------------------------------------
ordenar_div_candidatos();
//----------------------------------------------------------------------------------------
$('li#li_dashboard').addClass('active');
//----------------------------------------------------------------------------------------
$('a#link_rio_branco').addClass('active');
$('a#link_rio_branco').attr('href', PORTAL_URL + 'dashboard');
$('a#link_cruzeiro_do_sul').attr('href', PORTAL_URL + 'dashboard_c');
//----------------------------------------------------------------------------------------
$('input#reg_todos').change(function () {

    var obj = this;

    if ($(obj).is(':checked')) {
        $('input.check_enviar').each(function () {
            $(this).prop('checked', true);
        });
    } else {
        $('input.check_enviar').each(function () {
            $(this).prop('checked', false);
        });
    }

    $('form#form_check').submit();

});
//----------------------------------------------------------------------------------------
$('input.check_enviar').change(function () {
    $('form#form_check').submit();
});
//----------------------------------------------------------------------------------------
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