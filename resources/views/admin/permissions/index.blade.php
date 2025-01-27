@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 20px;">
        <h1 class="elegant-heading">Permissions Management</h1>
        <a href="{{ route('admin.permissions.create') }}" class="btn btn-secondary elegant-back-btn">Add New Permission</a>
    </div>

    <!-- DataTable Component -->
    <x-datatable
        id="permissionsTable"
        :columns="[    
            ['title' => 'Name', 'data' => 'name', 'name' => 'name']
        ]"
        ajaxUrl="{{ route('admin.permissions.datatables') }}"
        actions="true"
        editRoute="{{ route('admin.permissions.edit', ['permission' => '__id__']) }}"
        deleteRoute="{{ route('admin.permissions.destroy', ['permission' => '__id__']) }}"
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
    }

    .elegant-back-btn {
        margin-left: 10px;
    }

    main {
        padding-top: 60px; /* Adjust based on your navbar height */
    }
</style>
@endpush
