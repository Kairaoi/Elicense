@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 20px;">
        <h1 class="elegant-heading">Visitor Application Registry</h1>
        <a href="{{ route('pfps.visitor-applications.create') }}" class="btn btn-secondary elegant-back-btn">Add New Application</a>
    </div>

    <!-- DataTable Component -->
    <table id="applicationsTable" class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Passport</th>
                <th>Category</th>
                <th>Activity</th>
                <th>Duration</th>
                <th>Status</th>
                <th>Application Date</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        console.log('DataTable initialization starting...');
        
        var editRoute = "{{ route('pfps.visitor-applications.edit', ':id') }}";

        var table = $('#applicationsTable').DataTable({
            processing: true,
            serverSide: false, // Changed to false since we're getting complete data
            ajax: {
                url: '/fisherylicense/public/pfps/visitor-applications/datatables',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [
                {
                    data: 'visitor',
                    render: function(data, type, row) {
                        return `${data.first_name} ${data.last_name}`;
                    }
                },
                { 
                    data: 'visitor.passport_number'
                },
                { 
                    data: 'category.category_name'
                },
                { 
                    data: 'activity_type.activity_name'
                },
                { 
                    data: 'duration.duration_name'
                },
                {
                    data: 'status',
                    render: function(data) {
                        let badgeClass = data === 'approved' ? 'bg-success' : 'bg-danger';
                        return `<span class="badge ${badgeClass}">${data}</span>`;
                    }
                },
                { 
                    data: 'application_date'
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group" role="group">
                                <a href="${editRoute.replace(':id', row.application_id)}" class="btn btn-primary btn-sm">Edit</a>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${row.application_id}">Delete</button>
                            </div>
                        `;
                    }
                }
            ],
            pageLength: 10,
            responsive: true,
            language: {
                emptyTable: 'No visitor applications available',
                zeroRecords: 'No matching records found'
            }
        });

        // Handle Delete button clicks
       // Handle Delete button clicks
$('#applicationsTable').on('click', '.delete-btn', function(e) {
    e.preventDefault();
    var applicationId = $(this).data('id');
    
    if (confirm('Are you sure you want to delete this application?')) {
        $.ajax({
            url: "{{ route('pfps.visitor-applications.destroy', ':id') }}".replace(':id', applicationId),
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Reload the table after deletion
                table.ajax.reload();
                // Notify user about successful deletion
                alert('Application deleted successfully!');
            },
            error: function(error) {
                // Handle error case
                alert('Error deleting application.');
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