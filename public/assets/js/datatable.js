$(document).ready(function(){
    $('.sortTable').DataTable({
        "dom": 'Bfrtipl',
        "buttons": ['csv'],
        "paging":   true,
        "info":     true,
        "searching":   true,
        "lengthMenu": [ [20, 50, 100, -1], [20, 50, 100, "Todos"] ],
        "stateSave": true,
        "language": {
            decimal: ",",
            search:         "Buscar",
            lengthMenu:    "Exibindo _MENU_ registros",
            info:           "Registros de _START_ a _END_. Total de _TOTAL_",
            emptyTable:     "Sem dados registrados",
            infoEmpty:      "",
            paginate: {
                first:      "Primeiro",
                previous:   "Anterior",
                next:       "Próximo",
                last:       "Último"
            },
        }
    });
})