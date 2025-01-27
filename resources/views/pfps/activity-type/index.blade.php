@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 20px;">
        <h1 class="elegant-heading">Activity Types Registry</h1>
        <a href="{{ route('pfps.activity-types.create') }}" class="btn btn-secondary elegant-back-btn">Add New Activity Type</a>
    </div>

    <!-- DataTable Component -->
    <table id="activityTypesTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Activity Name</th>
                <th>Category</th>
                <th>Requirements</th>
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
        var editRoute = "{{ route('pfps.activity-types.edit', ':id') }}";

        var table = $('#activityTypesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pfps.activity-types.datatables') }}",
                type: 'GET', // Use GET or POST based on your route settings
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [
                { data: 'activity_type_id' },
                { data: 'activity_name' },
                { 
                    data: 'category.category_name', 
                    defaultContent: 'No category' // Handle cases where the category is null
                },
                { data: 'requirements' },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group" role="group">
                                <a href="${editRoute.replace(':id', row.activity_type_id)}" class="btn btn-primary btn-sm">Edit</a>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${row.activity_type_id}">Delete</button>
                            </div>
                        `;
                    }
                }
            ],
            pageLength: 10,
            responsive: true
        });

        // Handle Delete button clicks
        $('#activityTypesTable tbody').on('click', '.delete-btn', function(e) {
            e.preventDefault();
            var activityTypeId = $(this).data('id');
            if (confirm('Are you sure you want to delete this activity type?')) {
                $.ajax({
                    url: "{{ route('pfps.activity-types.destroy', ':id') }}".replace(':id', activityTypeId),
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        table.ajax.reload();
                        alert('Activity type deleted successfully!');
                    },
                    error: function(error) {
                        alert('Error deleting activity type.');
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
