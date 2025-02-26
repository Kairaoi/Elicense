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
                <th>Vat</th>
                <th>Sub Total </th>
                <th>Total Fee Inc Vat</th>
                <th>Status</th>
                <th>Issue Date</th>
                <th>Expiry Date</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

@push('styles')
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
</style>
@endpush

@push('scripts')
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
                render: function(data) {
                    return data ? data : "License number will be shown when your license is paid.";
                }
            },
            { 
                data: 'license_type_name'
            },
            { 
                data: 'vat_amount',
                render: function(data, type, row) {
                    const currency = getCurrency(row.license_type_name);
                    return formatCurrency(data, currency);
                }
            },
            { 
                data: 'total_fee',
                render: function(data, type, row) {
                    const currency = getCurrency(row.license_type_name);
                    return formatCurrency(data, currency);
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    let total = parseFloat(data.total_fee) + parseFloat(data.vat_amount);
                    const currency = getCurrency(row.license_type_name);
                    return formatCurrency(total, currency);
                },
                title: "Total Amount (Incl. VAT)"
            },
            { 
                data: 'status',
                render: function(data) {
                    let statusClass = 'status-' + data.toLowerCase();
                    let statusText = data.replace('_', ' ').toUpperCase();
                    return `<span class="status-badge ${statusClass}">${statusText}</span>`;
                }
            },
            { 
                data: 'issue_date',
                render: function(data) {
                    return data ? new Date(data).toLocaleDateString() : 'N/A';
                }
            },
            { 
                data: 'expiry_date',
                render: function(data) {
                    return data ? new Date(data).toLocaleDateString() : 'N/A';
                }
            }
        ],
        pageLength: 10,
        responsive: true,
        order: [[0, 'desc']]
    });
});

// Helper function to format the currency with prefix (AUD$ or USD$)
function formatCurrency(amount, currency) {
    const currencySymbol = currency === 'USD' ? 'USD$' : 'AUD$';
    return `${currencySymbol}${parseFloat(amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
}

// Helper function to determine the currency based on the license type
function getCurrency(licenseType) {
    if (licenseType === 'Export License for Lobster' || licenseType === 'Export License for Petfish') {
        return 'USD'; // USD for Export License for Lobster and Petfish
    }
    return 'AUD'; // Default to AUD for other license types
}
</script>
@endpush
