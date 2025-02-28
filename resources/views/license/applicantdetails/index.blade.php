@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="elegant-heading">Applicant Licenses</h1>
        <a href="{{ route('applicantdetails.applicantdetails.create') }}" class="btn btn-secondary elegant-back-btn">Add New License</a>
    </div>

    <table id="applicantLicensesTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>License Number</th>
                <th>License Type</th>
                <th>Sub Total</th>
                <th>Status</th>
                <th>Issue Date</th>
                <th>Expiry Date</th>
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
    .container { padding-top: 20px; }
    .elegant-heading { margin-bottom: 0; }
    
    /* Status badge styles */
    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .status-license_issued { background-color: #c3e6cb; color: #155724; }
    .status-pending { background-color: #ffeeba; color: #856404; }
    .status-revoked { background-color: #f5c6cb; color: #721c24; }
    
    /* Export button styling */
    .dt-buttons .btn {
        margin-right: 5px;
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
    $('#applicantLicensesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('applicantdetails.applicants.datatables') }}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            { data: 'id' },
            { 
                data: 'license_number',
                render: function(data, type) {
                    if (type === 'export') {
                        return data ? data : "Pending Payment";
                    }
                    return data ? data : "License issued upon payment.";
                }
            },
            { 
                data: 'license_type_name'
            },
            { 
                data: 'total_fee',
                render: function(data, type, row) {
                    const currency = getCurrency(row.license_type_name);
                    if (type === 'export') {
                        return currency + parseFloat(data).toFixed(2);
                    }
                    return formatCurrency(data, currency);
                }
            },
            { 
                data: 'status',
                render: function(data, type) {
                    if (type === 'export') {
                        return data.replace('_', ' ').toUpperCase();
                    }
                    let statusClass = 'status-' + data.toLowerCase();
                    let statusText = data.replace('_', ' ').toUpperCase();
                    return `<span class="status-badge ${statusClass}">${statusText}</span>`;
                }
            },
            { 
                data: 'issue_date',
                render: function(data, type) {
                    if (type === 'export') {
                        return data || 'N/A';
                    }
                    return data ? new Date(data).toLocaleDateString() : 'N/A';
                }
            },
            { 
                data: 'expiry_date',
                render: function(data, type) {
                    if (type === 'export') {
                        return data || 'N/A';
                    }
                    return data ? new Date(data).toLocaleDateString() : 'N/A';
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
                            columns: [0, 1, 2, 3, 4, 5, 6, 7] // Export all columns except VAT and Total Amount
                        },
                        title: 'Applicant Licenses - ' + new Date().toLocaleDateString(),
                        className: 'dropdown-item'
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        },
                        title: 'Applicant Licenses - ' + new Date().toLocaleDateString(),
                        className: 'dropdown-item'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        },
                        title: 'Applicant Licenses - ' + new Date().toLocaleDateString(),
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
        ]
    });
});

// Helper function to format the currency with prefix (AUD$ or USD$)
function formatCurrency(amount, currency) {
    return `${currency}${parseFloat(amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
}

// Helper function to determine the currency based on the license type
function getCurrency(licenseType) {
    if (licenseType === 'Export License for Lobster' || licenseType === 'Export License for Petfish') {
        return 'USD$'; // USD for Export License for Lobster and Petfish
    }
    return 'AUD$'; // Default to AUD for other license types
}
</script>
@endpush
