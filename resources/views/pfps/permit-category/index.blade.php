@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 20px;">
        <h1 class="elegant-heading">Permit Categories Registry</h1>
        <a href="{{ route('pfps.permit-categories.create') }}" class="btn btn-secondary elegant-back-btn">Add New Permit Category</a>
    </div>

    <!-- DataTable Component -->
    <table id="permitCategoriesTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Category Name</th>
                <th>Description</th>
                <th>Base Fee</th>
                <th>Certification Required</th>
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
        var editRoute = "{{ route('pfps.permit-categories.edit', ':id') }}";

        var table = $('#permitCategoriesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pfps.permit-categories.datatables') }}", // Updated route for permit categories
                type: 'GET', // Use GET or POST based on your route settings
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [
                { data: 'category_id', name: 'category_id' },
                { data: 'category_name', name: 'category_name' },
                { data: 'description', name: 'description' },
                { data: 'base_fee', name: 'base_fee' },
                {
                    data: 'requires_certification', 
                    name: 'requires_certification',
                    render: function(data, type, row) {
                        return data ? 'Yes' : 'No';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        // Use category_id for the Edit link
                        return `
                            <div class="btn-group" role="group">
                                <a href="${editRoute.replace(':id', row.category_id)}" class="btn btn-primary btn-sm">Edit</a>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${row.category_id}">Delete</button>
                            </div>
                        `;
                    }
                }
            ],
            pageLength: 10,
            responsive: true
        });

        // Handle Delete button clicks
        $('#permitCategoriesTable tbody').on('click', '.delete-btn', function(e) {
            e.preventDefault();
            var categoryId = $(this).data('id');
            if (confirm('Are you sure you want to delete this permit category?')) {
                $.ajax({
                    url: "{{ route('pfps.permit-categories.destroy', ':id') }}".replace(':id', categoryId),
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        table.ajax.reload();
                        alert('Permit category deleted successfully!');
                    },
                    error: function(error) {
                        alert('Error deleting permit category.');
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
