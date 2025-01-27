@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4" style="margin-top: 20px;">
        <h1 class="elegant-heading">Roles Management</h1>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-secondary elegant-back-btn mt-3 mt-md-0">Add New Role</a>
    </div>

    <!-- DataTable Component -->
    <x-datatable
        id="rolesTable"
        :columns="[    
            ['title' => 'ID', 'data' => 'id', 'name' => 'id'],
            ['title' => 'Name', 'data' => 'name', 'name' => 'name'],
          
            ['title' => 'Created At', 'data' => 'created_at', 'name' => 'created_at'],
            ['title' => 'Permissions', 'data' => 'permissions', 'name' => 'permissions']
        ]"
        ajaxUrl="{{ route('admin.roles.datatables') }}"
        actions="true"
        editRoute="{{ route('admin.roles.edit', ['role' => '__id__']) }}"
        showRoute="{{ route('admin.roles.show', ['role' => '__id__']) }}"
        deleteRoute="{{ route('admin.roles.destroy', ['role' => '__id__']) }}"
    />
</div>
@endsection

@push('styles')
<style>
    /* Add these styles */
    .container {
        padding-top: 20px;
    }
    
    .elegant-heading {
        margin-bottom: 0;
        font-size: 2rem; /* Responsive font size */
    }

    .elegant-back-btn {
        margin-left: 10px;
    }

    main {
        padding-top: 60px; /* Adjust based on your navbar height */
    }

    @media (max-width: 768px) {
        .elegant-heading {
            font-size: 1.5rem; /* Smaller font size on mobile */
        }
        
        .elegant-back-btn {
            width: 100%; /* Full-width button on small screens */
            margin-top: 10px; /* Margin on top for spacing */
        }
    }

    /* Make DataTable responsive */
    .dataTables_wrapper {
        width: 100%;
        overflow-x: auto; /* Allow horizontal scrolling */
    }
</style>
@endpush