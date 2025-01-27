@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="elegant-heading">Monthly Harvests</h1>
        <a href="{{ route('license.monthly-harvests.create') }}" class="btn btn-secondary elegant-back-btn">Add New Harvest</a>
    </div>

    <table id="harvestsTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Species Name</th>
                <th>Agent Name</th>
                <th>Month</th>
                <th>Quantity Harvested</th>
                <th>Notes</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#harvestsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('license.monthly-harvests.datatables') }}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            { data: 'id' },
            { 
                data: 'species_tracking.species.name',
                name: 'species_tracking.species.name'
            },
            { 
                data: 'agent',
                render: function(data) {
                    return `${data.first_name} ${data.last_name}`; // Combine first and last names
                },
                name: 'agent.first_name'
            },
            { 
                data: 'month',
                render: function(data) {
                    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                    return monthNames[data - 1]; // Convert numeric month to name
                }
            },
            { data: 'quantity_harvested' },
            { data: 'notes', defaultContent: 'N/A' },
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
                                <li><a class="dropdown-item" href="${route('license.monthly-harvests.edit', row.id)}">
                                    <i class="fas fa-edit"></i> Edit
                                </a></li>
                                <li><a class="dropdown-item" href="${route('license.monthly-harvests.show', row.id)}">
                                    <i class="fas fa-eye"></i> View Details
                                </a></li>
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

    function route(name, params) {
        return {
            'license.monthly-harvests.edit': "{{ route('license.monthly-harvests.edit', ':id') }}".replace(':id', params),
            'license.monthly-harvests.show': "{{ route('license.monthly-harvests.show', ':id') }}".replace(':id', params)
        }[name];
    }
});
</script>
@endpush
