@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="elegant-heading">Applicants Registry</h1>
        
        <span id="pendingCount" class="badge bg-danger" style="font-size: 1rem; display: none;">
            <i class="fas fa-clock"></i>
            <span id="pendingCountText">0 New Pending Applications</span>
        </span>

        <a href="{{ route('license.applicants.create') }}" class="btn btn-secondary">Add New Applicant</a>
    </div>

    <div class="mb-3">
        <select id="status-filter" class="form-select">
            <option value="">All Status</option>
            <option value="Pending">Pending</option>
            <option value="Active">Active</option>
        </select>
    </div>

    <table id="applicantsTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Company Name</th>
                <th>Phone Number</th>
                <th>Email</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

@push('styles')
<style>
    .badge-status {
        padding: 8px 12px;
        border-radius: 15px;
        font-weight: 500;
        display: inline-block;
        min-width: 100px;
        text-align: center;
    }
    
    .badge-pending {
        background-color: #ffc107;
        color: #000;
    }
    
    .badge-active {
        background-color: #28a745;
        color: #fff;
    }

    #pendingCount {
        background-color: #dc3545;
        color: white;
        font-size: 1.2rem;
        font-weight: bold;
        padding: 8px 15px;
        margin-left: 10px;
        border-radius: 20px;
        transition: all 0.3s ease;
    }

    .pulse-animation {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.05); opacity: 0.8; }
        100% { transform: scale(1); opacity: 1; }
    }

    .btn-group .btn {
        margin-right: 5px;
    }
</style>
@endpush

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
            }
        },
        columns: [
            { data: 'id' },
            { 
                data: 'full_name',
                render: function(data, type, row) {
                    return row.first_name + ' ' + row.last_name;
                }
            },
            { data: 'company_name' },
            { data: 'phone_number' },
            { data: 'email' },
            { 
                data: 'status',
                render: function(data, type, row) {
                    if (row.has_pending_license) {
                        return '<span class="badge-status badge-pending">Pending</span>';
                    }
                    return '<span class="badge-status badge-active">Active</span>';
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <div class="btn-group">
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
        order: [[0, 'desc']],
        pageLength: 10,
        responsive: true
    });

    function updatePendingCount(count) {
        if (count > 0) {
            $('#pendingCountText').text(count + ' New Pending Application' + (count > 1 ? 's' : ''));
            $('#pendingCount').show().addClass('pulse-animation');
        } else {
            $('#pendingCount').hide().removeClass('pulse-animation');
        }
    }

    // Status filter
    $('#status-filter').on('change', function() {
        table.column(5).search($(this).val()).draw();
    });

    // Delete functionality
    $('#applicantsTable').on('click', '.delete-btn', function(e) {
        e.preventDefault();
        var applicantId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this applicant?')) {
            $.ajax({
                url: route('applicant.applicants.destroy', applicantId),
                type: 'DELETE',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
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

    // Auto refresh pending count
    setInterval(function() {
        $.get("{{ route('applicant.applicants.pending-count') }}", function(data) {
            updatePendingCount(data.pendingCount);
        });
    }, 30000);
});

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