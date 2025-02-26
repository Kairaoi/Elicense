@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 20px;">
        <h1 class="elegant-heading">Applicants Registry</h1>
        
        <!-- Pending Applications Count Badge -->
        <span id="pendingCount" class="badge bg-danger" style="font-size: 1rem; display: none;">
            <span id="pendingCountText">0 New Pending Applications</span>
        </span>

        <a href="{{ route('license.applicants.create') }}" class="btn btn-secondary elegant-back-btn">Add New Applicant</a>
    </div>

    <!-- DataTable Component -->
    <table id="applicantsTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Company Name</th>
                <th>Phone Number</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#applicantsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
    url: "{{ route('applicant.applicants.datatables') }}",
    type: 'POST',
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    complete: function(response) {
        var data = response.responseJSON;
        if (data && data.pendingCount > 0) {
            $('#pendingCountText').text(data.pendingCount + ' New Pending Application' + (data.pendingCount > 1 ? 's' : ''));
            $('#pendingCount').show();
        } else {
            $('#pendingCount').hide();
        }
    }
},
            columns: [
                { data: 'id' },
                { data: 'full_name', render: function(data, type, row) {
                    return row.first_name + ' ' + row.last_name;
                }},
                { data: 'company_name' },
                { data: 'phone_number' },
                { data: 'email' },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group" role="group">
                                <a href="${route('applicant.applicants.edit', row.id)}" class="btn btn-primary btn-sm">Edit</a>
                                <a href="${route('applicant.applicants.show', row.id)}" class="btn btn-info btn-sm">Review</a>
                                <a href="${route('applicant.applicants.activity-log', row.id)}" class="btn btn-info btn-sm">Activity Log</a>
                                <a href="${route('applicant.applicants.pdf', row.id)}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </a>
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
        $('#applicantsTable tbody').on('click', '.delete-btn', function(e) {
            e.preventDefault();
            var applicantId = $(this).data('id');
            if (confirm('Are you sure you want to delete this applicant?')) {
                $.ajax({
                    url: route('applicant.applicants.destroy', applicantId),
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        table.ajax.reload();
                        alert('Applicant deleted successfully!');
                    },
                    error: function(error) {
                        alert('Error deleting applicant.');
                    }
                });
            }
        });
    });

    // Helper function to generate URLs
    function route(name, param) {
        return {
            'applicant.applicants.edit': "{{ route('applicant.applicants.edit', ':id') }}".replace(':id', param),
            'applicant.applicants.show': "{{ route('applicant.applicants.show', ':id') }}".replace(':id', param),
            'applicant.applicants.destroy': "{{ route('applicant.applicants.destroy', ':id') }}".replace(':id', param),
            'applicant.applicants.activity-log': "{{ route('applicant.applicants.activity-log', ':id') }}".replace(':id', param),
            'applicant.applicants.pdf': "{{ route('applicant.applicants.pdf', ':id') }}".replace(':id', param)
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

    #pendingCount {
        background-color: #dc3545;
        color: white;
        font-size: 1.2rem;
        font-weight: bold;
        padding: 5px 10px;
        margin-left: 10px;
        border-radius: 5px;
    }
</style>
@endpush