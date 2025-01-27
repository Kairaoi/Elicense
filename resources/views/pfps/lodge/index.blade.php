@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 20px;">
        <h1 class="elegant-heading">Lodges Registry</h1>
        <a href="{{ route('pfps.lodges.create') }}" class="btn btn-secondary elegant-back-btn">Add New Lodge</a>
    </div>

    <!-- DataTable Component -->
    <table id="lodgesTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Lodge Name</th>
                <th>Location</th>
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
        var editRoute = "{{ route('pfps.lodges.edit', ':id') }}";

        var table = $('#lodgesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pfps.lodges.datatables') }}", // Updated route for lodges
                type: 'GET', // Use GET or POST based on your route settings
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [
                { data: 'lodge_id', name: 'lodge_id' },
                { data: 'lodge_name', name: 'lodge_name' },
                { data: 'location', name: 'location' },  // Location column
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        // Use lodge_id for the Edit link
                        return `
                            <div class="btn-group" role="group">
                                <a href="${editRoute.replace(':id', row.lodge_id)}" class="btn btn-primary btn-sm">Edit</a>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${row.lodge_id}">Delete</button>
                            </div>
                        `;
                    }
                }
            ],
            pageLength: 10,
            responsive: true
        });

        // Handle Delete button clicks
        $('#lodgesTable tbody').on('click', '.delete-btn', function(e) {
            e.preventDefault();
            var lodgeId = $(this).data('id');
            if (confirm('Are you sure you want to delete this lodge?')) {
                $.ajax({
                    url: "{{ route('pfps.lodges.destroy', ':id') }}".replace(':id', lodgeId),
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        table.ajax.reload();
                        alert('Lodge deleted successfully!');
                    },
                    error: function(error) {
                        alert('Error deleting lodge.');
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
