@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 20px;">
        <h1 class="elegant-heading">Equipment Rentals Registry</h1>
        <a href="{{ route('pfps.equipment_rentals.create') }}" class="btn btn-secondary elegant-back-btn">Add New Rental</a>
    </div>

    <!-- DataTable Component -->
    <table id="equipmentRentalsTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Equipment Type</th>
                <th>Rental Fee</th>
                <th>Currency</th>
                <th>Rental Date</th>
                <th>Return Date</th>
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
        var editRoute = "{{ route('pfps.equipment_rentals.edit', ':id') }}";

        var table = $('#equipmentRentalsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pfps.equipment_rentals.datatables') }}", // Updated route for equipment rentals
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [
                { data: 'rental_id', name: 'rental_id' },
                { data: 'equipment_type', name: 'equipment_type' },
                { data: 'rental_fee', name: 'rental_fee' },
                { data: 'currency', name: 'currency' },
                { data: 'rental_date', name: 'rental_date' },
                { data: 'return_date', name: 'return_date' },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group" role="group">
                                <a href="${editRoute.replace(':id', row.rental_id)}" class="btn btn-primary btn-sm">Edit</a>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${row.rental_id}">Delete</button>
                            </div>
                        `;
                    }
                }
            ],
            pageLength: 10,
            responsive: true
        });

        // Handle Delete button clicks
        $('#equipmentRentalsTable tbody').on('click', '.delete-btn', function(e) {
            e.preventDefault();
            var rentalId = $(this).data('id');
            if (confirm('Are you sure you want to delete this rental?')) {
                $.ajax({
                    url: "{{ route('pfps.equipment_rentals.destroy', ':id') }}".replace(':id', rentalId),
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        table.ajax.reload();
                        alert('Equipment rental deleted successfully!');
                    },
                    error: function(error) {
                        alert('Error deleting rental.');
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
