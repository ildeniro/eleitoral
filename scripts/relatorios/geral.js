$(document).ready(function () {

    //COMBO ESTADO E MUNICIPIO
    $("select#tipo").change(function () {

        var ano = $("#ano").val();

        $("select#numero").html('<option value="0">Carregando...</option>');
        $.post(PORTAL_URL + "combo/tipo_candidato.php",
                {tipo: $(this).val(), ano: ano},
                function (valor) {
                    $("select#numero").html(valor);
                    $("select#numero").select2();
                });
    });

    $("select#ano").change(function () {
        $("form#form_usuario").submit();
    });

    $("select#numero").change(function () {
        $("#div_loader_2").show();
        $("form#form_usuario").submit();
    });

    new DataTable('#example', {
        "pageLength": 50,
        layout: {
            topStart: {
                buttons: ['copy', 'excel', 'pdf', 'colvis']
            }
        }
    });

    new DataTable('#example2', {
        "pageLength": 50,
        layout: {
            topStart: {
                buttons: ['copy', 'excel', 'pdf', 'colvis']
            }
        }
    });

    new DataTable('#example3', {
        "pageLength": 90,
        layout: {
            topStart: {
                buttons: ['copy', 'excel', 'pdf', 'colvis']
            }
        }
    });
});


window.onload = function () {
    $("#div_loader_2").hide();
};