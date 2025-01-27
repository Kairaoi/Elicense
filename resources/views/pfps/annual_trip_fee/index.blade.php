@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 20px;">
        <h1 class="elegant-heading">Annual Trip Fees Registry</h1>
        <a href="{{ route('pfps.annual_trip_fees.create') }}" class="btn btn-secondary elegant-back-btn">Add New Fee</a>
    </div>

    <!-- DataTable Component -->
    <table id="annualTripFeesTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Amount</th>
                <th>Currency</th>
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
        // Pass the route URL to JavaScript for generating edit links dynamically
        var editRoute = "{{ route('pfps.annual_trip_fees.edit', ':id') }}";

        var table = $('#annualTripFeesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pfps.annual_trip_fees.datatables') }}", // Updated route for annual_trip_fees
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [
                { data: 'fee_id', name: 'fee_id' },
                { data: 'amount', name: 'amount' },
                { data: 'currency', name: 'currency' },
                { data: 'year', name: 'year' },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group" role="group">
                                <a href="${editRoute.replace(':id', row.fee_id)}" class="btn btn-primary btn-sm">Edit</a>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${row.fee_id}">Delete</button>
                            </div>
                        `;
                    }
                }
            ],
            pageLength: 10,
            responsive: true
        });

        // Handle Delete button clicks
        $('#annualTripFeesTable tbody').on('click', '.delete-btn', function(e) {
            e.preventDefault();
            var feeId = $(this).data('id');
            if (confirm('Are you sure you want to delete this fee?')) {
                $.ajax({
                    url: "{{ route('pfps.annual_trip_fees.destroy', ':id') }}".replace(':id', feeId),
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        table.ajax.reload();
                        alert('Fee deleted successfully!');
                    },
                    error: function(error) {
                        alert('Error deleting fee.');
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

    main {
        padding-top: 60px;
    }

    .btn-sm {
        margin: 2px;
    }

    .table td {
        vertical-align: middle;
    }

    .btn-group {
        display: flex;
        gap: 2px;
    }
</style>
@endpush
