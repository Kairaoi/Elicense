@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="elegant-heading">Licenses Registry</h1>
        <a href="{{ route('license.licenses.create2') }}" class="btn btn-secondary elegant-back-btn">Add New License</a>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="filterForm" class="row g-3">
                <div class="col-md-3">
                    <label for="licenseType" class="form-label">License Type</label>
                    <select id="licenseType" name="license_type" class="form-control">
                        <option value="">All</option>
                        @foreach($licenseTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="">All</option>
                        <option value="pending">Pending</option>
                        <option value="reviewed">Reviewed</option>
                        <option value="license_issued">Issued</option>
                        <option value="license_revoked">Revoked</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="applicant" class="form-label">Applicant Name</label>
                    <input type="text" id="applicant" name="applicant" class="form-control" placeholder="Search applicant">
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <button type="button" id="resetFilter" class="btn btn-secondary">Reset</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Licenses Table -->
    <table id="licensesTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Applicant Name</th>
                <th>License Type</th>
                <th>Total Fee</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

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

    var table = $('#licensesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('license.licenses.datatables') }}",
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: function(d) {
                d.license_type = $('#licenseType').val();
                d.status = $('#status').val();
                d.applicant = $('#applicant').val();
            }
        },
        columns: [
            { data: 'id' },
            { data: 'full_name' },
            { data: 'license_type_name' },
            { data: 'total_fee', render: function(data, type, row) {
                let currencySymbol = 'AUD';
                if (row.license_type_name === 'Export License for Seacucumber' || row.license_type_name === 'Export License for Lobster') {
                    currencySymbol = 'US';
                }
                return `${currencySymbol} ${parseFloat(data).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            }},
            { data: 'status', render: function(data) {
                let formattedStatus = data.replace('_', ' ').toLowerCase().split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');

                let statusClass = 'status-' + data.toLowerCase().replace(/\s/g, '-');
                return `<span class="status-badge ${statusClass}">${formattedStatus}</span>`;
            }},
            { data: null, orderable: false, render: function(data, type, row) {
                return `<div class="dropdown">
                    <button class="btn btn-secondary btn-sm action-btn dropdown-toggle" type="button" 
                            data-bs-toggle="dropdown" aria-expanded="false">
                        Actions
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        ${getActionButtons(row)}
                    </ul>
                </div>`;
            }}
        ],
        pageLength: 10,
        responsive: true,
        order: [[0, 'desc']]
    });

    // Filter functionality
    $('#filterForm').submit(function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    // Reset filter
    $('#resetFilter').click(function() {
        $('#licenseType, #status, #applicant').val('');
        table.ajax.reload();
    });

    function getActionButtons(row) {
        let buttons = '';

        if (row.status.toLowerCase() === 'pending') {
            buttons += `
                <li><a class="dropdown-item" href="${routes.edit.replace(':id', row.id)}">
                    <i class="fas fa-edit"></i> Edit
                </a></li>
                <li><a class="dropdown-item" href="${routes.invoice.replace(':id', row.id)}">
                    <i class="fas fa-file-invoice"></i> Invoice
                </a></li>
            `;
        } 
        else if (row.status.toLowerCase() === 'reviewed') {
            buttons += `
                <li><a class="dropdown-item" href="${routes.edit.replace(':id', row.id)}">
                    <i class="fas fa-edit"></i> Edit
                </a></li>
                <li><a class="dropdown-item" href="${routes.showIssueForm.replace(':id', row.id)}" 
                    onclick="return confirmAction('Are you sure you want to issue this license?')">
                    <i class="fas fa-check-circle"></i> Issue License
                </a></li>
            `;
        }
        else if (row.status.toLowerCase() === 'license_issued') {
            buttons += `
                <li><a class="dropdown-item" href="${routes.download.replace(':id', row.id)}">
                    <i class="fas fa-download"></i> Download
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="revokeLicense(${row.id}); return false;">
                    <i class="fas fa-ban"></i> Revoke
                </a></li>
            `;
        }
        else if (row.status.toLowerCase() === 'license_revoked') {
            buttons += `
                <li><a class="dropdown-item" href="${routes.downloadRevoked.replace(':id', row.id)}">
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
                url: routes.revoke.replace(':id', licenseId),
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
