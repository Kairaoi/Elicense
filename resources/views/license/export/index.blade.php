@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 20px;">
        <h1 class="elegant-heading">Export Declarations</h1>
        <a href="{{ route('export.declarations.create') }}" class="btn btn-secondary elegant-back-btn">Add New Declaration</a>
    </div>

    <!-- DataTable Component -->
    <table id="declarationsTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Applicant</th>
                <th>Company Name</th>
                <th>License Type</th>
                <th>Shipment Date</th>
                <th>Destination</th>
                <th>Total Fee</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#declarationsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('export.declarations.datatables') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [
                { data: 'id' },
                { 
                    data: null,
                    render: function(data, type, row) {
                        return `${row.first_name} ${row.last_name}`;
                    }
                },
                { data: 'company_name' },
                { data: 'license_type' },
                { data: 'shipment_date' },
                { data: 'export_destination' },
                { 
                    data: 'total_license_fee',
                    render: function(data, type, row) {
                        return '$' + parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group" role="group">
                                <a href="${route('export.declarations.edit', row.id)}" class="btn btn-primary btn-sm">Edit</a>
                                <a href="${route('export.declarations.show', row.id)}" class="btn btn-secondary btn-sm">View</a>
                            </div>
                        `;
                    }
                }
            ],
            pageLength: 10,
            responsive: true
        });
    });

    // Helper function to generate URLs dynamically
    function route(name, params) {
        return {
            'export.declarations.edit': "{{ route('export.declarations.edit', ':id') }}".replace(':id', params),
            'export.declarations.show': "{{ route('export.declarations.show', ':id') }}".replace(':id', params)
        }[name];
    }
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
