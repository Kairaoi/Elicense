@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 20px;">
        <h1 class="elegant-heading">Fishing Spots Registry</h1>
        <a href="{{ route('pfps.activity_sites.create') }}" class="btn btn-secondary elegant-back-btn">Add New Fishing Spot</a>
    </div>

    <!-- DataTable Component -->
    <table id="activitySitesTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Site Name</th>
                <th>Location</th>
                <th>Category</th>
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
        var editRoute = "{{ route('pfps.activity_sites.edit', ':id') }}";

        var table = $('#activitySitesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pfps.activity_sites.datatables') }}", // Updated route for activity sites
                type: 'GET', // Use GET or POST based on your route settings
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [
                { data: 'site_id', name: 'site_id' },
                { data: 'site_name', name: 'site_name' },
                { data: 'location', name: 'location' },  // Location column
                { data: 'category.category_name', name: 'category.category_name' },  // Category name from category relation
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        // Use site_id for the Edit link
                        return `
                            <div class="btn-group" role="group">
                                <a href="${editRoute.replace(':id', row.site_id)}" class="btn btn-primary btn-sm">Edit</a>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${row.site_id}">Delete</button>
                            </div>
                        `;
                    }
                }
            ],
            pageLength: 10,
            responsive: true
        });

        // Handle Delete button clicks
        $('#activitySitesTable tbody').on('click', '.delete-btn', function(e) {
            e.preventDefault();
            var siteId = $(this).data('id');
            if (confirm('Are you sure you want to delete this fishing spot?')) {
                $.ajax({
                    url: "{{ route('pfps.activity_sites.destroy', ':id') }}".replace(':id', siteId),
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        table.ajax.reload();
                        alert('Fishing spot deleted successfully!');
                    },
                    error: function(error) {
                        alert('Error deleting fishing spot.');
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
