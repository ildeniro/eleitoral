$(document).ready(function () {

    $('#form_login').submit(function () {
        $.ajax({
            type: "POST",
            url: PORTAL_URL + 'portaldofiscal/autenticar',
            data: $('#form_login').serialize(),
            cache: false,
            success: function (obj) {
                obj = JSON.parse(obj);
                if (obj.msg == 'success') {
                    setTimeout("location.href='" + PORTAL_URL + "portaldofiscal/dashboard'", 1);
                } else if (obj.msg == 'error') {
                    swal({
                        title: "Autenticação",
                        html: obj.retorno,
                        type: "error",
                        showCancelButton: false,
                        confirmButtonColor: "#8CD4F5",
                        confirmButtonText: "OK",
                        closeOnConfirm: false
                    });
                    return false;
                }
            },
            error: function (obj) {
                swal({
                    title: "Autenticação",
                    html: obj.retorno,
                    type: "error",
                    showCancelButton: false,
                    confirmButtonColor: "#8CD4F5",
                    confirmButtonText: "OK",
                    closeOnConfirm: false
                });
                return false;
            }
        });
        return false;
    });
});