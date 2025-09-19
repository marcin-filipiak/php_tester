
var oTable;

$(document).ready(function() {
        
/*Tabela z danymi*/


        oTable = $('#datatable').dataTable(
                {
                //http://datatables.net/usage/options#sDom    
                "sDom":'<l<t>ip>',
                //tlumaczenia
                "oLanguage": {
                    "sProcessing" : "Proszę czekać...",
                    "sLengthMenu": "Wyświetl _MENU_ wpisów na stronie",
                    "sZeroRecords": "Brak wyników wyszukiwania",
                    "sInfo": "Wyświetlono _START_ do _END_ z _TOTAL_ wpisów",
                    "sInfoEmpty": "Wyświetlono 0 wpisów",
                    "sInfoFiltered": "(filtrowanie z _MAX_ wpisów)",
                    "sSearch": "Szukaj:",
                    "oPaginate":{
                        "sFirst" : "Pierwsza",
                        "sPrevious" : "Poprzednia",
                        "sNext": "Następna",
                        "sLast" : "Ostatnia"
                    }
                }
        });
        
        //filtrowanie
        $('#IDiacritic').keyup( function() { oTable.fnDraw(); } );
                
        //tip
        $(".help").tipTip({maxWidth: "auto", edgeOffset: 10});
        
        //widget kalendarza
        $('.date').simpleDatepicker();
        
});