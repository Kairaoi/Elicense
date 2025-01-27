@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 20px;">
        <h1 class="elegant-heading">Permits Registry</h1>
        <a href="{{ route('pfps.permits.generate') }}" class="btn btn-secondary elegant-back-btn">Add New Permit</a>
    </div>

    <!-- DataTable Component -->
    <table id="permitsTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Permit Number</th>
                <th>Application ID</th>
                <th>Invoice ID</th>
                <th>Issue Date</th>
                <th>Expiry Date</th>
                <th>Permit Type</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Pass the route URL to JavaScript for generating edit links dynamically
        var editRoute = "{{ route('pfps.permits.edit', ':id') }}";

        var table = $('#permitsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pfps.permits.datatables') }}", // Updated route for permits
                type: 'GET', // Use GET or POST based on your route settings
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [
                { data: 'permit_id', name: 'permit_id' },
                { data: 'permit_number', name: 'permit_number' },
                { data: 'application_id', name: 'application_id' },  // Application ID column
                { data: 'invoice_id', name: 'invoice_id' },  // Invoice ID column
                { data: 'issue_date', name: 'issue_date' },  // Issue Date column
                { data: 'expiry_date', name: 'expiry_date' },  // Expiry Date column
                { data: 'permit_type', name: 'permit_type' },  // Permit Type column
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        // Use permit_id for the Edit link
                        return `
                            <div class="btn-group" role="group">
                                <a href="${editRoute.replace(':id', row.permit_id)}" class="btn btn-primary btn-sm">Edit</a>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${row.permit_id}">Delete</button>
                            </div>
                        `;
                    }
                }
            ],
            pageLength: 10,
            responsive: true
        });

        // Handle Delete button clicks
        $('#permitsTable tbody').on('click', '.delete-btn', function(e) {
            e.preventDefault();
            var permitId = $(this).data('id');
            if (confirm('Are you sure you want to delete this permit?')) {
                $.ajax({
                    url: "{{ route('pfps.permits.destroy', ':id') }}".replace(':id', permitId),
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        table.ajax.reload();
                        alert('Permit deleted successfully!');
                    },
                    error: function(error) {
                        alert('Error deleting permit.');
                    }
                });
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    .container {
        padding-top: 20px;
    }

    .elegant-heading {
        margin-bottom: 0;
    }

    .elegant-back-btn {
        margin-left: 10px;
    }

    main {
        padding-top: 60px;
    }

    .btn-sm {
        margin: 2px;
    }

    .table td {
        vertical-align: middle;
    }

    .btn-group {
        display: flex;
        gap: 2px;
    }
</style>
@endpush
