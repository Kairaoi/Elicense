@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 20px;">
        <h1 class="elegant-heading">Applicants Registry</h1>
        <a href="{{ route('harvester.applicants.create') }}" class="btn btn-secondary elegant-back-btn">Add New Applicant</a>
    </div>

    <!-- DataTable Component -->
    <table id="applicantsTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Phone Number</th>
                <th>Email</th>
                <th>National ID</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        var table = $('#applicantsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('harvester.applicants.datatables') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [
                { data: 'id' },
                { 
                    data: null, 
                    render: function (data, type, row) {
                        return row.first_name + ' ' + row.last_name;
                    } 
                },
                { data: 'phone_number' },
                { data: 'email' },
                { data: 'national_id' },
                {
                    data: null,
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                            <div class="btn-group" role="group">
                                <a href="${route('harvester.applicants.edit', row.id)}" class="btn btn-primary btn-sm">Edit</a>
                                <a href="${route('harvester.applicants.show', row.id)}" class="btn btn-info btn-sm">View</a>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${row.id}">Delete</button>
                            </div>
                        `;
                    }
                }
            ],
            pageLength: 10,
            responsive: true
        });

        // Handle Delete button clicks
        $('#applicantsTable tbody').on('click', '.delete-btn', function (e) {
            e.preventDefault();
            var rowId = $(this).data('id');
            var deleteUrl = "{{ route('harvester.applicants.destroy', ['applicant' => '__id__']) }}".replace('__id__', rowId);
            if (confirm('Are you sure you want to delete this applicant?')) {
                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        table.ajax.reload(null, false); // Reload DataTable
                        alert(response.message || 'Applicant deleted successfully.');
                    },
                    error: function () {
                        alert('An error occurred while deleting the applicant.');
                    }
                });
            }
        });
    });

    // Helper function to generate URLs
    function route(name, param) {
        return {
            'harvester.applicants.edit': "{{ route('harvester.applicants.edit', ['applicant' => '__id__']) }}".replace('__id__', param),
            'harvester.applicants.show': "{{ route('harvester.applicants.show', ['applicant' => '__id__']) }}".replace('__id__', param),
            'harvester.applicants.destroy': "{{ route('harvester.applicants.destroy', ['applicant' => '__id__']) }}".replace('__id__', param),
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
