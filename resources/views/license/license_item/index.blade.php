@extends('layouts.app')

@section('content')
<<div class="container" style="margin-top: 100px;"> <!-- Add margin here -->
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 20px;">
        <h1 class="elegant-heading">License Types</h1>
        <a href="{{ route('license.licenses_types.create') }}" class="btn btn-secondary elegant-back-btn">Add New License Type</a>
    </div>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="text-center" style="display: none;">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- DataTable Component -->
    <x-datatable
    id="licenseTypesTable"
    :columns="[
        ['title' => 'ID', 'data' => 'id', 'name' => 'id'],
        ['title' => 'Name', 'data' => 'name', 'name' => 'name'],
       
    ]"
    ajaxUrl="{{ route('license.licenses_types.datatables') }}"
    actions="true"
    editRoute="{{ route('license.licenses_types.edit', ['licenses_type' => '__id__']) }}"
    showRoute="{{ route('license.licenses_types.show', ['licenses_type' => '__id__']) }}"
    deleteRoute="{{ route('license.licenses_types.destroy', ['licenses_type' => '__id__']) }}"
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