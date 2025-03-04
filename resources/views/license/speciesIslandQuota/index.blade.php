@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 20px;">
        <h1 class="elegant-heading">Species Island Quota Registry</h1>
        <a href="{{ route('species-island-quotas.quota.create') }}" class="btn btn-secondary elegant-back-btn">Add New Quota</a>
    </div>

    <!-- Export Buttons - Kaitin bwai aikai -->
    <div class="mb-3 export-buttons">
        <button class="btn btn-success me-2" id="exportExcel"><i class="fas fa-file-excel"></i> Export to Excel</button>
        <button class="btn btn-primary me-2" id="exportCsv"><i class="fas fa-file-csv"></i> Export to CSV</button>
        <button class="btn btn-danger" id="exportPdf"><i class="fas fa-file-pdf"></i> Export to PDF</button>
    </div>

    <!-- DataTable Component -->
    <table id="quotaTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Species</th>
                <th>Island</th>
                <th>Island Quota</th>
                <th>Remaining Quota</th>
                <th>Year</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

@push('styles')
<!-- DataTables Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
@endpush

@push('scripts')
<!-- DataTables and Buttons JS -->
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    // Katea te table n namakin ke karekean ao kamaraurau
    var table = $('#quotaTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('species-island-quotas.quota.datatables') }}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            { data: 'id' },
            { data: 'species_name' },
            { data: 'island_name' },
            { 
                data: 'island_quota',
                render: function(data) {
                    return parseFloat(data).toFixed(2) + ' kg';
                }
            },
            { 
                data: 'remaining_quota',
                render: function(data, type, row) {
                    let percentage = (parseFloat(data) / parseFloat(row.island_quota)) * 100;
                    let color = percentage > 50 ? 'text-success' : 
                                percentage > 25 ? 'text-warning' : 'text-danger';
                    return `<span class="${color}">${parseFloat(data).toFixed(2)} kg</span>`;
                }
            },
            { data: 'year' },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `  
                        <div class="btn-group">
                            <a href="{{ url('species-island-quotas/quota') }}/${row.id}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </div>
                    `;
                }
            }
        ],
        order: [[5, 'desc'], [1, 'asc']],
        pageLength: 10,
        responsive: true
    });
    
    // Katea taian custom buttons n rorooko ni katoonga taian export functions
    $('#exportExcel').on('click', function() {
        // Create a new invisible button with DataTables excelHtml5 extension
        var excelButton = $('<button></button>')
            .addClass('buttons-excel buttons-html5')
            .hide();
        
        // Add to the DOM
        $('.export-buttons').append(excelButton);
        
        // Use DataTables API to create and trigger the Excel export
        new $.fn.dataTable.Buttons(table, {
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Species Island Quotas'
                }
            ]
        });
        
        // Trigger click
        setTimeout(function() {
            $('.buttons-excel.buttons-html5').trigger('click');
            excelButton.remove();
        }, 100);
    });
    
    $('#exportCsv').on('click', function() {
        // Create a new invisible button with DataTables csvHtml5 extension
        var csvButton = $('<button></button>')
            .addClass('buttons-csv buttons-html5')
            .hide();
        
        // Add to the DOM
        $('.export-buttons').append(csvButton);
        
        // Use DataTables API to create and trigger the CSV export
        new $.fn.dataTable.Buttons(table, {
            buttons: [
                {
                    extend: 'csvHtml5',
                    title: 'Species Island Quotas'
                }
            ]
        });
        
        // Trigger click
        setTimeout(function() {
            $('.buttons-csv.buttons-html5').trigger('click');
            csvButton.remove();
        }, 100);
    });
    
    $('#exportPdf').on('click', function() {
        // Create a new invisible button with DataTables pdfHtml5 extension
        var pdfButton = $('<button></button>')
            .addClass('buttons-pdf buttons-html5')
            .hide();
        
        // Add to the DOM
        $('.export-buttons').append(pdfButton);
        
        // Use DataTables API to create and trigger the PDF export
        new $.fn.dataTable.Buttons(table, {
            buttons: [
                {
                    extend: 'pdfHtml5',
                    title: 'Species Island Quotas',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    customize: function(doc) {
                        doc.content[1].table.widths = ['10%', '20%', '20%', '15%', '15%', '10%', '10%'];
                    }
                }
            ]
        });
        
        // Trigger click
        setTimeout(function() {
            $('.buttons-pdf.buttons-html5').trigger('click');
            pdfButton.remove();
        }, 100);
    });
});
</script>
@endpush