@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="elegant-heading">Agents Registry</h1>
        <a href="{{ route('license.agents.create') }}" class="btn btn-secondary elegant-back-btn">Add New Agent</a>
    </div>

    <table id="licensesTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Agent Name</th>
                <th>Company</th>
                <th>Phone Number</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

@push('styles')
<!-- Keep existing styles -->
<style>
    .container { padding-top: 20px; }
    .elegant-heading { margin-bottom: 0; }
    .elegant-back-btn { margin-left: 10px; }
    
    /* Dropdown styles */
    .dropdown-menu {
        min-width: 8rem;
        padding: 0.5rem 0;
        margin: 0;
        font-size: 0.875rem;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        padding: 0.5rem 1rem;
        clear: both;
        font-weight: 400;
        color: #212529;
        text-align: inherit;
        text-decoration: none;
        white-space: nowrap;
        background-color: transparent;
        border: 0;
    }

    .dropdown-item i {
        margin-right: 0.5rem;
        width: 1rem;
        text-align: center;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    /* Status badge styles */
    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .status-active { background-color: #c3e6cb; color: #155724; }
    .status-inactive { background-color: #f5c6cb; color: #721c24; }

    /* Action button styles */
    .action-btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#licensesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('license.agents.datatables') }}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            { data: 'id' },
            { 
                data: null,
                render: function(data) {
                    return data.first_name + ' ' + data.last_name;
                }
            },
            { 
                data: 'applicant',
                render: function(data) {
                    return data.company_name;
                }
            },
            { data: 'phone_number' },
            { 
                data: 'status',
                render: function(data) {
                    let statusClass = 'status-' + data.toLowerCase();
                    let statusText = data.charAt(0).toUpperCase() + data.slice(1).toLowerCase();
                    return `<span class="status-badge ${statusClass}">${statusText}</span>`;
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <div class="dropdown">
                            <button class="btn btn-secondary btn-sm action-btn dropdown-toggle" type="button" 
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                Actions
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                ${getActionButtons(row)}
                            </ul>
                        </div>
                    `;
                }
            }
        ],
        pageLength: 10,
        responsive: true,
        order: [[0, 'desc']]
    });

    function getActionButtons(row) {
        return `
            <li><a class="dropdown-item" href="${route('license.agents.edit', row.id)}">
                <i class="fas fa-edit"></i> Edit
            </a></li>
            <li><a class="dropdown-item" href="${route('license.agents.show', row.id)}">
                <i class="fas fa-eye"></i> View Details
            </a></li>
        `;
    }
});

function route(name, params) {
    return {
        'license.agents.edit': "{{ route('license.agents.edit', ':id') }}".replace(':id', params),
        'license.agents.show': "{{ route('license.agents.show', ':id') }}".replace(':id', params)
    }[name];
}
</script>
@endpush