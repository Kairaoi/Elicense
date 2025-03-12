@extends('layouts.app')

@section('content')
<div class="container">
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
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
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
            { data: 'island_quota' },
            { data: 'remaining_quota' },
            { data: 'year' },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <div class="btn-group">
                            <a href="{{ url('quotas') }}/${row.id}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </div>
                    `;
                }
            }
        ],
        
// DataTables export options
dom: 'Bfrtip', // This enables the button controls
buttons: [
    {
        extend: 'excelHtml5',
        title: 'Species Island Quotas',
        exportOptions: {
            columns: ':visible:not(:last-child)', // Exclude the last column (Actions)
            modifier: {
                search: 'none', // Export all records, ignoring search filter
                order: 'applied', // Keep current order
                page: 'all' // Export all pages
            }
        }
    },
    {
        extend: 'csvHtml5',
        title: 'Species Island Quotas',
        exportOptions: {
            columns: ':visible:not(:last-child)', // Exclude the last column (Actions)
            modifier: {
                search: 'none', // Export all records, ignoring search filter
                order: 'applied', // Keep current order
                page: 'all' // Export all pages
            }
        }
    },
    {
        extend: 'pdfHtml5',
        title: 'Species Island Quotas',
        orientation: 'landscape',
        pageSize: 'A4',
        exportOptions: {
            columns: ':visible:not(:last-child)', // Exclude the last column (Actions)
            modifier: {
                search: 'none', // Export all records, ignoring search filter
                order: 'applied', // Keep current order
                page: 'all' // Export all pages
            }
        },
        customize: function(doc) {
            doc.content[1].table.widths = ['10%', '20%', '20%', '15%', '15%', '10%'];
        }
    }
]


    });
});
</script>
</script>
@endpush
