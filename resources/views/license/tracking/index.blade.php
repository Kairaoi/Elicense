@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="elegant-heading">Species Tracking</h1>
        <a href="{{ route('license.trackings.create') }}" class="btn btn-secondary elegant-back-btn">
            <i class="fas fa-plus"></i> Add New Tracking
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="trackingTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Species</th>
                        <th>Agent</th>
                        <th>Island</th>
                        <th>Year</th>
                        <th>Quota Allocated (kg)</th>
                        <th>Quota Used (kg)</th>
                        <th>Remaining Quota (kg)</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<style>
    .container { 
        padding-top: 20px; 
    }
    
    .elegant-heading { 
        margin-bottom: 0;
        font-size: 1.75rem;
        font-weight: 600;
    }
    
    .elegant-back-btn { 
        margin-left: 10px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: none;
        border-radius: 0.5rem;
    }

    .card-body {
        padding: 1.5rem;
    }
    
    /* DataTable Styling */
    .table {
        margin-bottom: 0;
    }
    
    .table thead th {
        border-top: none;
        background-color: #f8f9fa;
        font-weight: 600;
        font-size: 0.875rem;
    }

    /* Status badge styles */
    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }

    .status-active { 
        background-color: #d1e7dd; 
        color: #0f5132; 
    }
    
    .status-inactive { 
        background-color: #f8d7da; 
        color: #842029; 
    }

    /* Action button styles */
    .action-btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .dropdown-menu {
        padding: 0.5rem 0;
        margin: 0;
        font-size: 0.875rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .dropdown-item {
        padding: 0.5rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .dropdown-item i {
        width: 1rem;
        text-align: center;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    var table = $('#trackingTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('license.trackings.datatables') }}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            { data: 'id' },
            { 
                data: 'species.name',
                render: function(data) {
                    return data || 'N/A';
                }
            },
            {
                data: 'agent',
                render: function(data) {
                    return data ? `${data.first_name} ${data.last_name}` : 'N/A';
                }
            },
            {
                data: 'island.name',
                render: function(data) {
                    return data || 'N/A';
                }
            },
            { data: 'year' },
            { 
                data: 'quota_allocated',
                render: function(data) {
                    return parseFloat(data).toFixed(2);
                }
            },
            { 
                data: 'quota_used',
                render: function(data) {
                    return parseFloat(data).toFixed(2);
                }
            },
            { 
                data: 'remaining_quota',
                render: function(data) {
                    return parseFloat(data).toFixed(2);
                }
            },
            {
                data: null,
                render: function(data) {
                    let status = data.remaining_quota > 0 ? 'active' : 'inactive';
                    let statusText = status.charAt(0).toUpperCase() + status.slice(1);
                    return `<span class="status-badge status-${status}">${statusText}</span>`;
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data) {
                    return `
                        <div class="dropdown">
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('license.trackings.edit', '') }}/${data.id}">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a class="dropdown-item" href="{{ route('license.trackings.show', '') }}/${data.id}">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                        </div>
                    `;
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 10,
        responsive: true,
        drawCallback: function() {
            $('[data-bs-toggle="dropdown"]').dropdown();
        }
    });
});
</script>
@endpush