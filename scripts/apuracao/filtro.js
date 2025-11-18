//------------------------------------------------------------------------------
$(document).ready(function () {

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    $('#tabela_1').DataTable({
        dom: 'Bfrtip',
       "paging": false, // Desativa a paginação
        buttons: [
            'copy', // Mantém o botão de copiar
            'excel', // Mantém o botão de exportar para Excel
            'pdf', // Mantém o botão de exportar para PDF
            'colvis' // Mantém o botão de visibilidade de colunas
        ],
        initComplete: function () {
            // Ajustar o campo de busca (search box)
            $('.dt-search').css({
                'float': 'right',
                'margin': '0px 0px 30px'
            });

            // Ajustar a área dos botões de exportação
            $('.dt-buttons').css({
                'margin-bottom': '-40px'
            });

            // Ajustar a área de informações (info)
            $('#tabela_1_info').css({
                'margin-top': '15px'
            });

            // Ajustar a área de paginação (paging)
            $('#tabela_1_paginate').css({
                'margin-top': '15px'
            });

            // Cursor 'pointer' para os headers (tr > th)
            $('#tabela_1 thead th').css({
                'cursor': 'pointer'
            });
        }
    });
});