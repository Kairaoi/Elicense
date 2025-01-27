@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 20px;">
        <h1 class="elegant-heading">Countries Registry</h1>
        <a href="{{ route('pfps.countries.create') }}" class="btn btn-secondary elegant-back-btn">Add New Country</a>
    </div>

    <!-- DataTable Component -->
    <table id="countriesTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Country Name</th>
                <th>ISO Code</th>               
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
        var editRoute = "{{ route('pfps.countries.edit', ':id') }}";

        var table = $('#countriesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pfps.countries.datatables') }}",
                type: 'GET', // Use GET or POST based on your route settings
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [
                { data: 'country_id' },
                { data: 'country_name' },
                { data: 'iso_code' },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group" role="group">
                                <a href="${editRoute.replace(':id', row.country_id)}" class="btn btn-primary btn-sm">Edit</a>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${row.country_id}">Delete</button>
                            </div>
                        `;
                    }
                }
            ],
            pageLength: 10,
            responsive: true
        });

        // Handle Delete button clicks
        $('#countriesTable tbody').on('click', '.delete-btn', function(e) {
            e.preventDefault();
            var countryId = $(this).data('id');
            if (confirm('Are you sure you want to delete this country?')) {
                $.ajax({
                    url: "{{ route('pfps.countries.destroy', ':id') }}".replace(':id', countryId),
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        table.ajax.reload();
                        alert('Country deleted successfully!');
                    },
                    error: function(error) {
                        alert('Error deleting country.');
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
