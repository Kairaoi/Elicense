@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 20px;">
        <h1 class="elegant-heading">Fishing Durations Registry</h1>
        <a href="{{ route('pfps.durations.create') }}" class="btn btn-secondary elegant-back-btn">Add New Duration</a>
    </div>

    <!-- DataTable Component -->
    <table id="durationsTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Duration Name</th>
                <th>Fee Amount</th>
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
        var editRoute = "{{ route('pfps.durations.edit', ':id') }}";

        var table = $('#durationsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pfps.durations.datatables') }}", // Updated route for durations
                type: 'GET', // Use GET or POST based on your route settings
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [
                { data: 'duration_id', name: 'duration_id' },
                { data: 'duration_name', name: 'duration_name' },
                { data: 'fee_amount', name: 'fee_amount' },  // Fee Amount column
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        // Use duration_id for the Edit link
                        return `
                            <div class="btn-group" role="group">
                                <a href="${editRoute.replace(':id', row.duration_id)}" class="btn btn-primary btn-sm">Edit</a>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${row.duration_id}">Delete</button>
                            </div>
                        `;
                    }
                }
            ],
            pageLength: 10,
            responsive: true
        });

        // Handle Delete button clicks
        $('#durationsTable tbody').on('click', '.delete-btn', function(e) {
            e.preventDefault();
            var durationId = $(this).data('id');
            if (confirm('Are you sure you want to delete this duration?')) {
                $.ajax({
                    url: "{{ route('pfps.durations.destroy', ':id') }}".replace(':id', durationId),
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        table.ajax.reload();
                        alert('Duration deleted successfully!');
                    },
                    error: function(error) {
                        alert('Error deleting duration.');
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
