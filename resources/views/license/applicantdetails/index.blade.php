@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="elegant-heading">Applicant Licenses</h1>
    </div>

    <table id="applicantLicensesTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>License Number</th>
                <th>License Type</th>
                <th>Vat</th>
                <th>Total Fee</th>
                <th>Total Amount Inc Vat</th>
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
                data: 'license_type_id', 
                render: function(data) {
                    let licenseTypes = {
                        1: "Export License",
                        2: "Import License",
                        3: "Fishing License"
                    };
                    return licenseTypes[data] || "Unknown";
                }
            },
            { 
                data: 'vat_amount',
                render: function(data) {
                    return `$${parseFloat(data).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                }
            },
            { 
                data: 'total_fee',
                render: function(data) {
                    return `$${parseFloat(data).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                }
            },
            { 
                data: null,
                render: function(data) {
                    let total = parseFloat(data.total_fee) + parseFloat(data.vat_amount);
                    return `$${total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
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


</script>
@endpush
