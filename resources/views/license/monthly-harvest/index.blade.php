@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<style>
    .filter-card {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .filter-header {
        background-color: #e9ecef;
        padding: 10px 15px;
        border-bottom: 1px solid #dee2e6;
        border-radius: 8px 8px 0 0;
    }

    .filter-body {
        padding: 15px;
    }

    .elegant-heading {
        color: #2c3e50;
        font-weight: 600;
    }

    .btn-filter {
        background-color: #3498db;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
    }

    .btn-filter:hover {
        background-color: #2980b9;
    }

    .btn-reset {
        background-color: #95a5a6;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
    }

    .btn-reset:hover {
        background-color: #7f8c8d;
    }

    .table-container {
        background-color: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .action-btn {
        min-width: 100px;
    }
</style>
@endsection

@section('content')
<div class="container" style="margin-top: 100px;">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="elegant-heading">Monthly Harvests</h1>
        <a href="{{ route('license.monthly-harvests.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Harvest
        </a>
    </div>

    <!-- Filters Section -->
    <div class="card filter-card">
        <div class="filter-header">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Filters</h5>
        </div>
        <div class="filter-body">
            <form id="filter-form">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="island-filter" class="form-label">Island</label>
                        <select id="island-filter" class="form-select">
                            <option value="">All Islands</option>
                            @foreach($islands as $island)
                                <option value="{{ $island->name }}">{{ $island->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="month-filter" class="form-label">Month</label>
                        <select id="month-filter" class="form-select">
                            <option value="">All Months</option>
                            @foreach(range(1, 12) as $month)
                                <option value="{{ $month }}">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="species-filter" class="form-label">Species</label>
                        <select id="species-filter" class="form-select">
                            <option value="">All Species</option>
                            @foreach($species as $specie)
                                <option value="{{ $specie->name }}">{{ $specie->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="applicant-filter" class="form-label">Applicant Name</label>
                        <input type="text" id="applicant-filter" class="form-control" placeholder="Search applicant...">
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-reset" id="reset-filters">
                            <i class="fas fa-undo"></i> Reset Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Section -->
    <div class="table-container">
        <table id="harvestsTable" class="table table-striped table-hover">
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
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this harvest record?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- DataTables Scripts -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#harvestsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('license.monthly-harvests.datatables') }}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                // Add filter values to the request
                d.island = $('#island-filter').val();
                d.month = $('#month-filter').val();
                d.species = $('#species-filter').val();
                d.applicant = $('#applicant-filter').val();

                // Log the filter values
                console.log('Filter Values:', {
                    island: d.island,
                    month: d.month,
                    species: d.species,
                    applicant: d.applicant
                });

                return d;
            },
            error: function(xhr, error, thrown) {
                console.error('DataTables Ajax Error:', {
                    xhr: xhr,
                    error: error,
                    thrown: thrown
                });
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
                searchable: false,
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
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger delete-harvest" href="#" data-id="${row.id}">
                                    <i class="fas fa-trash"></i> Delete
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
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'csvHtml5',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-info btn-sm',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }
        ]
    });

    // Filter event handlers
    $('#island-filter, #month-filter, #species-filter').on('change', function() {
        table.draw();
    });

    // Debounced applicant filter
    var applicantTimer;
    $('#applicant-filter').on('keyup', function() {
        clearTimeout(applicantTimer);
        applicantTimer = setTimeout(function() {
            table.draw();
        }, 500);
    });

    // Reset filters
    $('#reset-filters').on('click', function() {
        $('#island-filter, #month-filter, #species-filter').val('');
        $('#applicant-filter').val('');
        table.draw();
    });

    // Delete functionality
    var deleteId;
    $(document).on('click', '.delete-harvest', function(e) {
        e.preventDefault();
        deleteId = $(this).data('id');
        $('#deleteModal').modal('show');
    });

    $('#confirmDelete').on('click', function() {
        $.ajax({
            url: route('license.monthly-harvests.destroy', deleteId),
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#deleteModal').modal('hide');
                table.draw();
                // Show success message
                toastr.success('Harvest record deleted successfully');
            },
            error: function(error) {
                // Show error message
                toastr.error('Error deleting harvest record');
            }
        });
    });

    function route(name, params) {
        return {
            'license.monthly-harvests.edit': "{{ route('license.monthly-harvests.edit', ':id') }}".replace(':id', params),
            'license.monthly-harvests.show': "{{ route('license.monthly-harvests.show', ':id') }}".replace(':id', params),
            'license.monthly-harvests.destroy': "{{ route('license.monthly-harvests.destroy', ':id') }}".replace(':id', params)
        }[name];
    }
});
</script>
@endpush