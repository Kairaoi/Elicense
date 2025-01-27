@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 20px;">
        <h1 class="elegant-heading">Harvester Licenses Registry</h1>
        <a href="{{ route('harvester.licenses.create') }}" class="btn btn-secondary elegant-back-btn">Add New License</a>
    </div>

    <!-- DataTable Component -->
    <table id="licensesTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Applicant Name</th>
                <th>Island</th>
                <th>Fee</th>
                <th>Issue Date</th>
                <th>Expiry Date</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#licensesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('harvester.licenses.datatables') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [
                { data: 'id' },
                { data: 'applicant_name' },
                { data: 'island_name' },
                { 
                    data: 'fee',
                    render: function(data, type, row) {
                        return '$' + parseFloat(data).toFixed(2);
                    }
                },
                { 
                    data: 'issue_date',
                    render: function(data, type, row) {
                        return data ? new Date(data).toISOString().split('T')[0] : 'N/A';
                    }
                },
                { 
                    data: 'expiry_date',
                    render: function(data, type, row) {
                        return data ? new Date(data).toISOString().split('T')[0] : 'N/A';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        var actions = `
                            <div class="btn-group" role="group">
                                <a href="${route('harvester.licenses.edit', row.id)}" class="btn btn-primary btn-sm">Edit</a>
                                <a href="${route('harvester.licenses.show', row.id)}" class="btn btn-secondary btn-sm">View</a>
                                <a href="${route('harvester.licenses.issue', row.id)}" class="btn btn-warning btn-sm">Issue</a>
                                <a href="#" class="btn btn-danger btn-sm delete-btn" data-id="${row.id}">Delete</a>
                            </div>
                        `;

                        if (row.license_status === 'license_issued') {
                            actions = `
                                <div class="btn-group" role="group">
                                    <a href="${route('harvester.licenses.edit', row.id)}" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="${route('harvester.licenses.show', row.id)}" class="btn btn-secondary btn-sm">View</a>
                                    <a href="${route('harvester.licenses.download', row.id)}" class="btn btn-success btn-sm">Download</a>
                                    <a href="${route('harvester.licenses.issue', row.id)}" class="btn btn-warning btn-sm">Issue</a>
                                    <a href="#" class="btn btn-danger btn-sm delete-btn" data-id="${row.id}">Delete</a>
                                </div>
                            `;
                        }

                        return actions;
                    }
                }
            ],
            pageLength: 10,
            responsive: true
        });

        $('#licensesTable tbody').on('click', '.delete-btn', function(e) {
            e.preventDefault();
            var licenseId = $(this).data('id');
            if (confirm('Are you sure you want to delete this license?')) {
                $.ajax({
                    url: "{{ route('harvester.licenses.destroy', ['license' => '__id__']) }}".replace('__id__', licenseId),
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        table.ajax.reload();
                        alert('License deleted successfully.');
                    },
                    error: function(response) {
                        alert('An error occurred while trying to delete the license.');
                    }
                });
            }
        });
    });

    function route(name, params) {
        var routes = {
            'harvester.licenses.edit': "{{ route('harvester.licenses.edit', ':id') }}",
            'harvester.licenses.show': "{{ route('harvester.licenses.show', ':id') }}",
            'harvester.licenses.download': "{{ route('harvester.licenses.download', ':id') }}",
            'harvester.licenses.issue': "{{ route('harvester.licenses.issue', ':id') }}",
            'harvester.licenses.destroy': "{{ route('harvester.licenses.destroy', ':id') }}"
        };
        return routes[name].replace(':id', params);
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
