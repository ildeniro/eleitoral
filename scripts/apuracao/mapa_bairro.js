$(document).ready(function () {
    $('body').tooltip({
        selector: "[data-tooltip=tooltip]",
        container: "body"
    });
});


function carregar_bairro(obj) {

    var id = $(obj).attr('id');

    $.post(PORTAL_URL + "combo/carregar_bairro.php",
            {id: id},
            function (valor) {
                $("div#carregar_dados_div").html(valor);
            });
}

function enviar_bairros() {
    var reg_22 = $("#reg_22:checked").val() == 1 ? 1 : 0;
    var reg_15 = $("#reg_15:checked").val() == 1 ? 1 : 0;
    var reg_30 = $("#reg_30:checked").val() == 1 ? 1 : 0;
    var reg_40 = $("#reg_40:checked").val() == 1 ? 1 : 0;
    
    $("input#guard_22").val(reg_22);
    $("input#guard_15").val(reg_15);
    $("input#guard_30").val(reg_30);
    $("input#guard_40").val(reg_40);

    $("form#form_check").submit();
}