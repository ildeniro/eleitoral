//------------------------------------------------------------------------------
$(document).ready(function () {
    $('select').each(function () {
        $(this).select2();
        $('.select2').attr('style', 'width: 100%');
    });

    $("form#form_comparacao").submit(function () {

        var ano1 = $("#ano1").val();
        var ano2 = $("#ano2").val();

        if (ano2 == ano1) {
            swal({
                title: "Formulário de Comparação",
                html: "O ano 1 não pode ser igual ao ano 2",
                type: "error",
                showCancelButton: false,
                confirmButtonColor: "#8CD4F5",
                confirmButtonText: "OK",
                closeOnConfirm: false
            });
            return false;
        }

    });

});
//------------------------------------------------------------------------------
