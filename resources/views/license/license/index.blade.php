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
                        <option value="expired">Expired</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="applicant" class="form-label">Applicant Name | Company Name</label>
                    <input type="text" id="applicant" name="applicant" class="form-control" placeholder="Search applicant">
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <button type="button" id="resetFilter" class="btn btn-secondary">Reset</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Expiring Licenses Alert -->
    <div id="expiringLicensesAlert" class="alert alert-warning d-none mb-4">
        <h5><i class="fas fa-exclamation-triangle"></i> Attention: Licenses Expiring Soon</h5>
        <p class="mb-1">The following licenses will expire within the next 5 days:</p>
        <ul id="expiringLicensesList" class="mb-0"></ul>
    </div>

    <!-- Licenses Table -->
    <table id="licensesTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Applicant Name</th>
                <th>License Type</th>
                <th>Fee</th>
                <th>Issue Date</th>
                <th>Expiry Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

<style>
    /* Status Badge Styles */
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
        margin: 0.1rem;
    }
    .status-expired {
        background-color: #dc3545;
        color: white;
    }
    .status-license-issued {
        background-color: #28a745;
        color: white;
    }
    .status-pending {
        background-color: #ffc107;
        color: #212529;
    }
    .status-reviewed {
        background-color: #17a2b8;
        color: white;
    }
    .status-license-revoked {
        background-color: #6c757d;
        color: white;
    }
    .status-critical {
        background-color: #dc3545;
        color: white;
    }
    .status-warning {
        background-color: #fd7e14;
        color: white;
    }
    .status-notice {
        background-color: #6f42c1;
        color: white;
    }
    
    /* Expiry countdown styling */
    .expiry-countdown {
        display: block;
        margin-top: 5px;
        font-weight: bold;
    }
    .countdown-critical {
        color: #dc3545;
    }
    .countdown-warning {
        color: #fd7e14;
    }
    .countdown-notice {
        color: #6f42c1;
    }
    
    /* Action button styles */
    .action-btn {
        white-space: nowrap;
    }
    
    /* Table responsive fixes */
    #licensesTable {
        width: 100% !important;
    }
</style>
@endpush

@push('scripts')
<!-- DataTables JS & Extensions -->
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    let routes = {
        'edit': "{{ route('license.licenses.edit', ':id') }}",
        'invoice': "{{ route('license.licenses.invoice', ':id') }}",
        'showIssueForm': "{{ route('license.licenses.showIssueForm', ':id') }}",
        'download': "{{ route('license.licenses.download', ':id') }}",
        'revoke': "{{ route('license.licenses.revoke', ':id') }}",
        'downloadRevoked': "{{ route('license.licenses.download-revoked', ':id') }}",
    };

    // Keep track of expiring licenses
    let expiringLicenses = [];

    var table = $('#licensesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('license.licenses.datatables') }}",
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: function(d) {
                // Send additional filter parameters
                d.license_type = $('#licenseType').val();
                d.status = $('#status').val();
                d.applicant = $('#applicant').val();
            }
        },
        columns: [
            { data: 'id' },
            { data: 'applicant_name' },
            { data: 'license_type_name' },
            { 
                data: 'total_fee', 
                render: function(data, type, row) {
                    // Ensure all currencies are AUD$
                    let currencySymbol = 'AUD$';

                    // For export, return plain data
                    if (type === 'export') {
                        return currencySymbol + parseFloat(data).toFixed(2);
                    }
                    
                    // For display, format the number
                    return `${currencySymbol}${parseFloat(data).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                }
            },
            { 
                data: 'issue_date',
                render: function(data, type, row) {
                    if (!data) return 'Not issued';
                    
                    // Format date
                    const date = new Date(data);
                    return date.toLocaleDateString();
                }
            },
            { 
                data: 'expiry_date',
                render: function(data, type, row) {
                    if (!data) return 'N/A';
                    
                    if (type === 'export') {
                        return new Date(data).toLocaleDateString();
                    }
                    
                    const expiryDate = new Date(data);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    expiryDate.setHours(0, 0, 0, 0);
                    
                    // Check if expired
                    if (expiryDate < today) {
                        return `<span class="text-danger">${expiryDate.toLocaleDateString()} (Expired)</span>`;
                    }
                    
                    // Calculate days until expiry
                    const diffTime = expiryDate - today;
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    
                    let countdownClass = '';
                    
                    if (diffDays <= 5) {
                        countdownClass = 'countdown-critical';
                    } else if (diffDays <= 15) {
                        countdownClass = 'countdown-warning';
                    } else if (diffDays <= 30) {
                        countdownClass = 'countdown-notice';
                    }
                    
                    let result = expiryDate.toLocaleDateString();
                    
                    if (diffDays <= 30) {
                        result += `<span class="expiry-countdown ${countdownClass}">
                            ${diffDays} day${diffDays !== 1 ? 's' : ''} remaining
                        </span>`;
                    }
                    
                    return result;
                }
            },
            { 
                data: 'status', 
                render: function(data, type, row) {
                    // Check if license has expired
                    const isExpired = isLicenseExpired(row.expiry_date);
                    
                    // For export, return plain status
                    if (type === 'export') {
                        if (isExpired && row.status === 'license_issued') {
                            return 'Expired';
                        }
                        return data.replace('_', ' ').toLowerCase().split(' ')
                            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                            .join(' ');
                    }
                    
                    // If license is expired, show expired status
                    if (isExpired && row.status === 'license_issued') {
                        return '<span class="status-badge status-expired">Expired</span>';
                    }
                    
                    // Format with badge
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
                    // Don't include actions column in exports
                    if (type === 'export') {
                        return '';
                    }
                    
                    // Check if license has expired
                    const isExpired = isLicenseExpired(row.expiry_date);
                    
                    // If license is expired but still shows as issued, update the row status for action buttons
                    if (isExpired && row.status === 'license_issued') {
                        row.status = 'expired';
                    }
                    
                    return `<div class="dropdown">
                        <button class="btn btn-secondary btn-sm action-btn dropdown-toggle" type="button" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                            Actions
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            ${getActionButtons(row)}
                        </ul>
                    </div>`;
                }
            }
        ],
        pageLength: 10,
        responsive: true,
        order: [[0, 'desc']],
        
        // Add export buttons
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex"B><"d-flex"f>>t<"d-flex justify-content-between"ip>',
        buttons: [
            {
                extend: 'collection',
                text: '<i class="fas fa-download"></i> Export',
                className: 'btn btn-outline-primary mr-2',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6] // Export all columns except actions
                        },
                        title: 'Licenses Registry - ' + new Date().toLocaleDateString(),
                        className: 'dropdown-item'
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        },
                        title: 'Licenses Registry - ' + new Date().toLocaleDateString(),
                        className: 'dropdown-item'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        },
                        title: 'Licenses Registry - ' + new Date().toLocaleDateString(),
                        className: 'dropdown-item',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        customize: function(doc) {
                            // Add styling to PDF
                            doc.styles.tableHeader.fontSize = 12;
                            doc.styles.tableHeader.bold = true;
                            doc.styles.tableHeader.color = '#333';
                            doc.styles.tableHeader.fillColor = '#f3f3f3';
                            doc.styles.tableHeader.alignment = 'center';
                            
                            // Add footer with date and page numbers
                            doc.footer = function(currentPage, pageCount) {
                                return {
                                    columns: [
                                        { text: 'Generated on: ' + new Date().toLocaleDateString(), alignment: 'left', margin: [20, 0] },
                                        { text: 'Page ' + currentPage.toString() + ' of ' + pageCount, alignment: 'right', margin: [0, 0, 20, 0] }
                                    ],
                                    margin: [20, 0]
                                };
                            };
                        }
                    }
                ]
            }
        ],
        "drawCallback": function(settings) {
            // After table is drawn, check for expiring licenses
            checkExpiringLicenses();
        }
    });

    // Function to check if license is expired
    function isLicenseExpired(expiryDate) {
        if (!expiryDate) return false;
        
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Set to beginning of day for accurate comparison
        
        const expiry = new Date(expiryDate);
        expiry.setHours(0, 0, 0, 0);
        
        return expiry < today;
    }
    
    // Function to calculate days until expiry
    function getDaysUntilExpiry(expiryDate) {
        if (!expiryDate) return null;
        
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Set to beginning of day for accurate comparison
        
        const expiry = new Date(expiryDate);
        expiry.setHours(0, 0, 0, 0);
        
        // Calculate difference in milliseconds and convert to days
        const diffTime = expiry - today;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        return diffDays;
    }
    
    // Function to check for licenses expiring soon and display alert
    function checkExpiringLicenses() {
        expiringLicenses = [];
        
        // Check each row for expiring licenses
        table.rows().data().each(function(row) {
            if (row.status === 'license_issued' && row.expiry_date) {
                const daysUntilExpiry = getDaysUntilExpiry(row.expiry_date);
                
                if (daysUntilExpiry !== null && daysUntilExpiry > 0 && daysUntilExpiry <= 5) {
                    expiringLicenses.push({
                        id: row.id,
                        name: row.applicant_name,
                        licenseType: row.license_type_name,
                        expiry: new Date(row.expiry_date).toLocaleDateString(),
                        daysLeft: daysUntilExpiry
                    });
                }
            }
        });
        
        // Update the expiring licenses alert
        if (expiringLicenses.length > 0) {
            const listHtml = expiringLicenses.map(license => 
                `<li><strong>${license.name}</strong> - ${license.licenseType} - <span class="text-danger">${license.daysLeft} day${license.daysLeft !== 1 ? 's' : ''} remaining</span></li>`
            ).join('');
            
            $('#expiringLicensesList').html(listHtml);
            $('#expiringLicensesAlert').removeClass('d-none');
        } else {
            $('#expiringLicensesAlert').addClass('d-none');
        }
    }

    // Filter functionality
    $('#filterForm').submit(function(e) {
        e.preventDefault(); // Prevent default form submission
        table.ajax.reload(); // Reload table with the updated filter data
    });

    // Reset filter
    $('#resetFilter').click(function() {
        $('#licenseType, #status, #applicant').val(''); // Reset filter fields
        table.ajax.reload(); // Reload table without filters
    });

    function getActionButtons(row) {
        let buttons = '';
        
        // Handle expired licenses
        if (row.status === 'expired') {
            buttons += `
                <li><a class="dropdown-item" href="${routes.download.replace(':id', row.id)}">
                    <i class="fas fa-download"></i> Download Expired License
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="renewLicense(${row.id}); return false;">
                    <i class="fas fa-sync"></i> Renew License
                </a></li>
            `;
        }
        else if (row.status.toLowerCase() === 'pending') {
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
    
    // Optional: Add renewal functionality - you'll need to implement this on your backend
    window.renewLicense = function(licenseId) {
        if (confirmAction('Are you sure you want to start the renewal process for this license?')) {
            // Redirect to renewal form or send AJAX request to start renewal
            window.location.href = "{{ route('license.licenses.create2') }}?renew=" + licenseId;
        }
    }
    
    // Auto-refresh daily to check for newly expired licenses
    setTimeout(function() {
        table.ajax.reload();
    }, 24 * 60 * 60 * 1000); // 24 hours
});
</script>
@endpush