@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 20px;">
        <h1 class="elegant-heading">Visitors Registry</h1>
        <a href="{{ route('pfps.visitors.create') }}" class="btn btn-secondary elegant-back-btn">Add New Visitor</a>
    </div>

    <!-- DataTable Component -->
    <table id="visitorsTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Gender</th>
                <th>Lodge</th>
                <th>Passport Number</th>
                <th>Country</th>
                <th>Organization</th>
                <th>Arrival Date</th>
                <th>Departure Date</th>
                <th>Emergency Contact</th>
                <th>Certification Number</th>
                <th>Certification Type</th>
                <th>Certification Expiry</th>
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
        var editRoute = "{{ route('pfps.visitors.edit', ':id') }}";

        var table = $('#visitorsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pfps.visitors.datatables') }}", // Route to fetch visitors
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataSrc: function(json) {
                    return json.data; // Ensure to return the 'data' array from the response
                }
            },
            columns: [
                { data: 'visitor_id', name: 'visitor_id' },
                { data: function(data) { return data.first_name + ' ' + data.last_name; }, name: 'full_name' },
                { data: 'gender', name: 'gender' },
                { data: 'lodge_name', name: 'lodge_name' },
                { data: 'passport_number', name: 'passport_number' },
                { data: 'country_name', name: 'country_name' },
                { data: 'organization_name', name: 'organization_name' },
                { data: 'arrival_date', name: 'arrival_date' },
                { data: 'departure_date', name: 'departure_date' },
                { data: 'emergency_contact', name: 'emergency_contact' },
                { data: 'certification_number', name: 'certification_number' },
                { data: 'certification_type', name: 'certification_type' },
                { data: 'certification_expiry', name: 'certification_expiry' },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group" role="group">
                                <a href="${editRoute.replace(':id', row.visitor_id)}" class="btn btn-primary btn-sm">Edit</a>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${row.visitor_id}">Delete</button>
                            </div>
                        `;
                    }
                }
            ],
            pageLength: 10,
            responsive: true
        });

        // Handle Delete button clicks
        $('#visitorsTable tbody').on('click', '.delete-btn', function(e) {
            e.preventDefault();
            var visitorId = $(this).data('id');
            if (confirm('Are you sure you want to delete this visitor?')) {
                $.ajax({
                    url: "{{ route('pfps.visitors.destroy', ':id') }}".replace(':id', visitorId),
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        table.ajax.reload();
                        alert('Visitor deleted successfully!');
                    },
                    error: function(error) {
                        alert('Error deleting visitor.');
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
