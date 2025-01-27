@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 20px;">
        <h1 class="elegant-heading">Species Island Quota Registry</h1>
        <a href="{{ route('species-island-quotas.quota.create') }}" class="btn btn-secondary elegant-back-btn">Add New Quota</a>
    </div>

    <!-- DataTable Component -->
    <table id="quotaTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Species</th>
                <th>Island</th>
                <th>Island Quota</th>
                <th>Remaining Quota</th>
                <th>Year</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#quotaTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('species-island-quotas.quota.datatables') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [
                { data: 'id' },
                { data: 'species_name' },
                { data: 'island_name' },
                { 
                    data: 'island_quota',
                    render: function(data, type, row) {
                        return parseFloat(data).toFixed(2) + ' kg';
                    }
                },
                { 
                    data: 'remaining_quota',
                    render: function(data, type, row) {
                        // Add color coding based on remaining quota
                        let percentage = (parseFloat(data) / parseFloat(row.island_quota)) * 100;
                        let color = percentage > 50 ? 'text-success' : 
                                  percentage > 25 ? 'text-warning' : 'text-danger';
                        return `<span class="${color}">${parseFloat(data).toFixed(2)} kg</span>`;
                    }
                },
                { data: 'year' },
                {
    data: null,
    orderable: false,
    render: function(data, type, row) {
        return `
            <div class="btn-group" role="group">
                <a href="{{ url('species-island-quotas/quota') }}/${row.id}/edit" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ url('species-island-quotas/quota') }}/${row.id}" class="btn btn-info btn-sm">
                    <i class="fas fa-eye"></i> View
                </a>
                <button class="btn btn-danger btn-sm delete-quota" data-id="${row.id}">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        `;
    }
}
            ],
            order: [[5, 'desc'], [1, 'asc']], // Sort by year desc, then species name
            pageLength: 10,
            responsive: true
        });

        // Delete handler
        $('#quotaTable').on('click', '.delete-quota', function() {
    let id = $(this).data('id');
    
    if (confirm('Are you sure you want to delete this quota record?')) {
        $.ajax({
            url: `{{ url('species-island-quotas/quota') }}/${id}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                table.ajax.reload();
                toastr.success('Quota record deleted successfully');
            },
            error: function(error) {
                toastr.error('Error deleting quota record');
                console.error('Deletion error:', error);
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

    .btn-group {
        display: flex;
        gap: 2px;
    }

    .table td {
        vertical-align: middle;
    }

    /* Add color coding for quota levels */
    .text-success { color: #28a745; }
    .text-warning { color: #ffc107; }
    .text-danger { color: #dc3545; }
</style>
@endpush