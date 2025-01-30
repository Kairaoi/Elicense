@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="elegant-heading">Monthly Harvests</h1>
        <a href="{{ route('license.monthly-harvests.create') }}" class="btn btn-secondary elegant-back-btn">Add New Harvest</a>
    </div>

    <table id="harvestsTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Species Name</th>
                <th>Applicant Name</th>
                <th>Island</th>
                <th>Year</th>
                <th>Month</th>
                <th>Quantity Harvested</th>
                <th>Remaining Quota</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

@push('scripts')
<!-- DataTables and Buttons CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

<script>
$(document).ready(function() {
    $('#harvestsTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ route('license.monthly-harvests.datatables') }}",
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    },
    columns: [
        { data: 'id' },
        { data: 'license_item.species.name', name: 'licenseItem.species.name' },
        { 
            data: 'applicant',
            render: function(data) {
                return `${data.first_name} ${data.last_name}`;
            },
            name: 'applicant.first_name'
        },
        { data: 'island.name', name: 'island.name' },
        { data: 'year' },
        { 
            data: 'month',
            render: function(data) {
                const monthNames = ["January", "February", "March", "April", "May", "June", 
                                  "July", "August", "September", "October", "November", "December"];
                return monthNames[data - 1];
            }
        },
        { 
            data: 'quantity_harvested',
            render: function(data) {
                return parseFloat(data).toFixed(2) + ' kg';
            }
        },
        { 
            data: 'remaining_quota',
            render: function(data) {
                return parseFloat(data).toFixed(2) + ' kg';
            }
        },
        {
            data: null,
            orderable: false,
            searchable: false, // Exclude from search
            exportable: false, // Exclude from export
            render: function(data, type, row) {
                return `
                    <div class="dropdown">
                        <button class="btn btn-secondary btn-sm action-btn dropdown-toggle" type="button" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                            Actions
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="${route('license.monthly-harvests.edit', row.id)}">
                                <i class="fas fa-edit"></i> Edit
                            </a></li>
                            <li><a class="dropdown-item" href="${route('license.monthly-harvests.show', row.id)}">
                                <i class="fas fa-eye"></i> View Details
                            </a></li>
                        </ul>
                    </div>
                `;
            }
        }
    ],
    pageLength: 10,
    responsive: true,
    order: [[0, 'desc']],
    dom: 'Bfrtip',
    buttons: [
        {
            extend: 'excelHtml5',
            text: 'Export to Excel',
            className: 'btn btn-success btn-sm',
            exportOptions: {
                columns: ':not(:last-child)' // Exclude the last column (Actions)
            }
        },
        {
            extend: 'pdfHtml5',
            text: 'Export to PDF',
            className: 'btn btn-danger btn-sm',
            exportOptions: {
                columns: ':not(:last-child)' // Exclude the last column (Actions)
            }
        },
        {
            extend: 'csvHtml5',
            text: 'Export to CSV',
            className: 'btn btn-info btn-sm',
            exportOptions: {
                columns: ':not(:last-child)' // Exclude the last column (Actions)
            }
        }
    ]
});


    function route(name, params) {
        return {
            'license.monthly-harvests.edit': "{{ route('license.monthly-harvests.edit', ':id') }}".replace(':id', params),
            'license.monthly-harvests.show': "{{ route('license.monthly-harvests.show', ':id') }}".replace(':id', params)
        }[name];
    }
});
</script>
@endpush
