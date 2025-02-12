@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 20px;">
        <h1 class="elegant-heading">Organizations Registry</h1>
        <a href="{{ route('pfps.organizations.create') }}" class="btn btn-secondary elegant-back-btn">Add New Organization</a>
    </div>

    <!-- DataTable Component -->
    <table id="organizationsTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Organization Name</th>
                
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
        var editRoute = "{{ route('pfps.organizations.edit', ':id') }}";

        var table = $('#organizationsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pfps.organizations.datatables') }}",
                type: 'GET', // Use GET or POST based on your route settings
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [
    { data: 'organization_id', name: 'organization_id' }, // Correctly referring to organization_id
    { data: 'organization_name', name: 'organization_name' }, // Correctly referring to organization_name
    
    {
        data: null,
        orderable: false,
        render: function(data, type, row) {
            // Use organization_id instead of country_id for the Edit link
            return `
                <div class="btn-group" role="group">
                    <a href="${editRoute.replace(':id', row.organization_id)}" class="btn btn-primary btn-sm">Edit</a>
                    <button class="btn btn-danger btn-sm delete-btn" data-id="${row.organization_id}">Delete</button>
                </div>
            `;
        }
    }
],

            pageLength: 10,
            responsive: true
        });

        // Handle Delete button clicks
        $('#organizationsTable tbody').on('click', '.delete-btn', function(e) {
            e.preventDefault();
            var organizationId = $(this).data('id');
            if (confirm('Are you sure you want to delete this organization?')) {
                $.ajax({
                    url: "{{ route('pfps.organizations.destroy', ':id') }}".replace(':id', organizationId),
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        table.ajax.reload();
                        alert('Organization deleted successfully!');
                    },
                    error: function(error) {
                        alert('Error deleting organization.');
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
