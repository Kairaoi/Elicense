@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 20px;">
        <h1 class="elegant-heading">Target Species Registry</h1>
        <a href="{{ route('pfps.target_species.create') }}" class="btn btn-secondary elegant-back-btn">Add New Species</a>
    </div>

    <!-- DataTable Component -->
    <table id="targetSpeciesTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Species Name</th>
                <th>Species Category</th>
                <th>Description</th>
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
        var editRoute = "{{ route('pfps.target_species.edit', ':id') }}";

        var table = $('#targetSpeciesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pfps.target_species.datatables') }}", // Updated route for target species
                type: 'GET', // Use GET or POST based on your route settings
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [
                { data: 'species_id', name: 'species_id' },
                { data: 'species_name', name: 'species_name' },
                { data: 'species_category', name: 'species_category' },  // Species Category column
                { data: 'description', name: 'description' },  // Description column
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        // Use species_id for the Edit link
                        return `
                            <div class="btn-group" role="group">
                                <a href="${editRoute.replace(':id', row.species_id)}" class="btn btn-primary btn-sm">Edit</a>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${row.species_id}">Delete</button>
                            </div>
                        `;
                    }
                }
            ],
            pageLength: 10,
            responsive: true
        });

        // Handle Delete button clicks
        $('#targetSpeciesTable tbody').on('click', '.delete-btn', function(e) {
            e.preventDefault();
            var speciesId = $(this).data('id');
            if (confirm('Are you sure you want to delete this species?')) {
                $.ajax({
                    url: "{{ route('pfps.target_species.destroy', ':id') }}".replace(':id', speciesId),
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        table.ajax.reload();
                        alert('Species deleted successfully!');
                    },
                    error: function(error) {
                        alert('Error deleting species.');
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
