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
    let routes = {
        'edit': "{{ route('license.licenses.edit', ':id') }}",
        'invoice': "{{ route('license.licenses.invoice', ':id') }}",
        'showIssueForm': "{{ route('license.licenses.showIssueForm', ':id') }}",
        'download': "{{ route('license.licenses.download', ':id') }}",
        'revoke': "{{ route('license.licenses.revoke', ':id') }}",
        'downloadRevoked': "{{ route('license.licenses.download-revoked', ':id') }}"
    };

    function route(name, id) {
        return routes[name].replace(':id', id);
    }

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
                    return `$${parseFloat(data).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                }
            },
            {
    data: 'status',
    render: function(data) {
        let formattedStatus = data.replace('_', ' ').toLowerCase().split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');

        let statusClass = 'status-' + data.toLowerCase().replace(/\s/g, '-');

        return `<span class="status-badge ${statusClass}">${formattedStatus}</span>`;
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
                <li><a class="dropdown-item" href="${route('edit', row.id)}">
                    <i class="fas fa-edit"></i> Edit
                </a></li>
                <li><a class="dropdown-item" href="${route('invoice', row.id)}">
                    <i class="fas fa-file-invoice"></i> Invoice
                </a></li>
            `;
        } 
        else if (row.status.toLowerCase() === 'reviewed') {
            buttons += `
                <li><a class="dropdown-item" href="${route('edit', row.id)}">
                    <i class="fas fa-edit"></i> Edit
                </a></li>
                <li><a class="dropdown-item" href="${route('showIssueForm', row.id)}" 
                    onclick="return confirmAction('Are you sure you want to issue this license?')">
                    <i class="fas fa-check-circle"></i> Issue License
                </a></li>
            `;
        }
        else if (row.status.toLowerCase() === 'license_issued') {
            buttons += `
                <li><a class="dropdown-item" href="${route('download', row.id)}">
                    <i class="fas fa-download"></i> Download
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="revokeLicense(${row.id}); return false;">
                    <i class="fas fa-ban"></i> Revoke
                </a></li>
            `;
        }
        else if (row.status.toLowerCase() === 'license_revoked') {
            buttons += `
                <li><a class="dropdown-item" href="${route('downloadRevoked', row.id)}">
                    <i class="fas fa-download"></i> Download Revoked License
                </a></li>
            `;
        }

        return buttons;
    }

    window.confirmAction = function(message) {
        return confirm(message);
    }

    window.revokeLicense = function(licenseId) {
        const reason = prompt('Please enter the reason for revoking this license:');
        if (!reason || reason.trim() === '') return;

        if (confirmAction('Are you sure you want to revoke this license? This action cannot be undone.')) {
            $.ajax({
                url: route('revoke', licenseId),
                type: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify({ revocation_reason: reason.trim() }),
                dataType: 'json',
                success: function(response) {
                    alert('License has been revoked successfully');
                    table.ajax.reload();

                    if (response.download_url) {
                        if (confirm('Would you like to download the revoked license document?')) {
                            window.location.href = response.download_url;
                        }
                    }
                },
                error: function(xhr) {
                    alert('Error revoking license: ' + (xhr.responseJSON?.message || 'Unknown error occurred'));
                }
            });
        }
    }
});
</script>
@endpush
