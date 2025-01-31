@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="elegant-heading">Licenses Registry</h1>
        <a href="{{ route('license.licenses.create2') }}" class="btn btn-secondary elegant-back-btn">Add New License</a>
    </div>

    <table id="licensesTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Applicant Name</th>
                <th>License Type Name</th>
                <th>Total Fee</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

@push('styles')
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

    .status-pending { background-color: #ffeeba; color: #856404; }
    .status-reviewed { background-color: #b8daff; color: #004085; }
    .status-issued { background-color: #c3e6cb; color: #155724; }
    .status-revoked { background-color: #f5c6cb; color: #721c24; }

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
            url: "{{ route('license.licenses.datatables') }}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            { data: 'id' },
            { data: 'full_name' },
            { data: 'license_type_name' },
            { 
                data: 'total_fee',
                render: function(data) {
                    return '$' + parseFloat(data).toFixed(2);
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    let statusClass = 'status-' + data.toLowerCase();
                    let statusText = data.replace('_', ' ').charAt(0).toUpperCase() + 
                                   data.slice(1).toLowerCase();
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
    let buttons = '';
    
    if (row.status.toLowerCase() === 'pending') {
        buttons += `
            <li><a class="dropdown-item" href="${route('license.licenses.edit', row.id)}">
                <i class="fas fa-edit"></i> Edit
            </a></li>
            <li><a class="dropdown-item" href="${route('license.licenses.invoice', row.id)}">
                <i class="fas fa-file-invoice"></i> Invoice
            </a></li>
        `;
    } 
    else if (row.status.toLowerCase() === 'reviewed') {
        buttons += `
            <li><a class="dropdown-item" href="${route('license.licenses.edit', row.id)}">
                <i class="fas fa-edit"></i> Edit
            </a></li>
            <li><a class="dropdown-item" href="${route('license.licenses.showIssueForm', row.id)}" 
                onclick="return confirmAction('Are you sure you want to issue this license?')">
                <i class="fas fa-check-circle"></i> Issue License
            </a></li>
        `;
    }
    else if (row.status.toLowerCase() === 'license_issued') {
        buttons += `
            <li><a class="dropdown-item" href="${route('license.licenses.download', row.id)}">
                <i class="fas fa-download"></i> Download
            </a></li>
            <li><a class="dropdown-item" href="#" onclick="revokeLicense(${row.id}); return false;">
                <i class="fas fa-ban"></i> Revoke
            </a></li>
        `;
    }
    else if (row.status.toLowerCase() === 'license_revoked') {
        buttons += `
            <li>
                <form action="${route('license.licenses.revoke', row.id)}" method="POST" class="dropdown-item">
                    <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                    <input type="hidden" name="_method" value="PUT">
                    <button type="submit" class="btn btn-link p-0" onclick="return confirmAction('Are you sure you want to revoke this license?')">
                        <i class="fas fa-ban"></i> Revoke License
                    </button>
                </form>
            </li>
        `;
    }
    
    return buttons;
}

    window.confirmAction = function(message) {
        return confirm(message);
    }

    window.revokeLicense = function(licenseId) {
    // Show prompt for reason
    const reason = prompt('Please enter the reason for revoking this license:');
    
    // If user cancels or enters empty reason, exit
    if (!reason || reason.trim() === '') {
        return;
    }

    // Confirm the action
    if (confirmAction('Are you sure you want to revoke this license? This action cannot be undone.')) {
        // Make sure we have the CSRF token
        const token = $('meta[name="csrf-token"]').attr('content');
        if (!token) {
            alert('CSRF token not found. Please refresh the page and try again.');
            return;
        }

        // Send the AJAX request
        $.ajax({
            url: route('license.licenses.revoke', licenseId),
            type: 'PUT',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            data: JSON.stringify({
                revocation_reason: reason.trim()
            }),
            dataType: 'json',
            success: function(response) {
                alert('License has been revoked successfully');
                table.ajax.reload();
                
                // If there's a download URL in the response, offer to download
                if (response.download_url) {
                    if (confirm('Would you like to download the revoked license document?')) {
                        window.location.href = response.download_url;
                    }
                }
            },
            error: function(xhr) {
                // Handle validation errors specifically
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = 'Validation failed:\n';
                    Object.keys(errors).forEach(key => {
                        errorMessage += `${errors[key]}\n`;
                    });
                    alert(errorMessage);
                } else {
                    alert('Error revoking license: ' + (xhr.responseJSON?.message || 'Unknown error occurred'));
                }
            }
        });
    }
}
});

function route(name, params) {
    return {
        'license.licenses.edit': "{{ route('license.licenses.edit', ':id') }}".replace(':id', params),
        'license.licenses.invoice': "{{ route('license.licenses.invoice', ':id') }}".replace(':id', params),
        'license.licenses.showIssueForm': "{{ route('license.licenses.showIssueForm', ':id') }}".replace(':id', params),
        'license.licenses.download': "{{ route('license.licenses.download', ':id') }}".replace(':id', params),
        'license.licenses.revoke': "{{ route('license.licenses.revoke', ':id') }}".replace(':id', params),
        'license.licenses.download-revoked': "{{ route('license.licenses.download-revoked', ':id') }}".replace(':id', params)
    }[name];
}

</script>
@endpush