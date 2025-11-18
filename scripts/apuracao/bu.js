//------------------------------------------------------------------------------------------------------
//Cadastrar
$(document).ready(function () {

    $('select').each(function () {
        $(this).select2();
        $('.select2').attr('style', 'width: 100%');
    });

    $("select#zona").change(function () {
        $("select#secao").html('<option value="0">Carregando...</option>');
        $.post(PORTAL_URL + "combo/secao_apuracao_2024.php",
                {zona: $(this).val()},
                function (valor) {
                    $("select#secao").html(valor);
                    $("select#secao").select2();
                });
    });

    $("select#secao").change(function () {

        var zona = $("select#zona").val();

        $.post(PORTAL_URL + "combo/local_2024.php",
                {secao: $(this).val(), zona: zona},
                function (valor) {
                    $("div#div_local").html(valor);
                });
    });
});
//----------------------------------------------------------------------------------------
//Enviando as Informações Coletadas ao Banco de Dados
$('form#formulario').submit(function () {

    var zona = $('select#zona').val();
    var secao = $('select#secao').val();
    var local = $('div#div_local').html();
    var brancos = $('input#brancos').val();
    var nulos = $('input#nulos').val();

    // Obter todos os inputs de votos
    let candidato = document.querySelectorAll('input[name="candidato[]"]');
    let candidatoArray = [];

    // Iterar sobre os inputs e coletar os valores
    candidato.forEach(function (input) {
        candidatoArray.push(input.value);
    });

    // Obter todos os inputs de votos
    let votos = document.querySelectorAll('input[name="votos[]"]');
    let votosArray = [];

    // Iterar sobre os inputs e coletar os valores
    votos.forEach(function (input) {
        votosArray.push(input.value);
    });
    
    // Obter todos os inputs de votos
    let partidos = document.querySelectorAll('input[name="partidos[]"]');
    let partidosArray = [];

    // Iterar sobre os inputs e coletar os valores
    partidos.forEach(function (input) {
        partidosArray.push(input.value);
    });

    if (zona == "" || secao == "" || brancos == "" || nulos == "") {
        swal({
            title: "Salvando as Informações",
            html: "Informações não foram todas preenchidas corretamente!",
            type: "error",
            showCancelButton: false,
            confirmButtonColor: "#8CD4F5",
            confirmButtonText: "OK",
            closeOnConfirm: false
        });
        $("#submit").attr("disabled", false);
        return false;
    } else {
        $.post(PORTAL_URL + "dao/apuracao/bu", {partidos: partidosArray, candidato: candidatoArray, votos: votosArray, brancos: brancos, nulos: nulos, local: local, zona: zona, secao: secao}, function (data) {
            if (isNaN(data)) {
                swal({
                    title: "Apuração",
                    html: data,
                    type: "error",
                    showCancelButton: false,
                    confirmButtonColor: "#8CD4F5",
                    confirmButtonText: "OK",
                    closeOnConfirm: false
                });
                $("#submit").attr("disabled", false);
                return false;
            } else {
                swal({
                    title: "Apuração",
                    text: "Ação realizada com sucesso!",
                    type: "success",
                    confirmButtonClass: "btn btn-success",
                    confirmButtonText: "Ok"
                }).then(function () {
                    setTimeout("location.href='" + PORTAL_URL + "view/apuracao/bu'", 1);
                });
            }
        }
        , "html");
        return false;
    }
});
//----------------------------------------------------------------------------------------