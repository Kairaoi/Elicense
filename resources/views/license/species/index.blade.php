@extends('layouts.app')

@section('content')
<<div class="container" style="margin-top: 100px;"> <!-- Add margin here -->
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 20px;">
        <h1 class="elegant-heading">Species</h1>
        <a href="{{ route('reference.species.create') }}" class="btn btn-secondary elegant-back-btn">Add New Species</a>
    </div>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="text-center" style="display: none;">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- DataTable Component -->
    <x-datatable
        id="speciesTable"
        :columns="[
            ['title' => 'ID', 'data' => 'id', 'name' => 'id'],
            ['title' => 'Name', 'data' => 'name', 'name' => 'name'],
            ['title' => 'License Type ID', 'data' => 'license_type_id', 'name' => 'license_type_id'],
            ['title' => 'Quota', 'data' => 'quota', 'name' => 'quota'],
             ['title' => 'Year', 'data' => 'year', 'name' => 'year'],
            ['title' => 'Unit Price', 'data' => 'unit_price', 'name' => 'unit_price'],
            
           
        ]"
        ajaxUrl="{{ route('reference.speices.datatables') }}"
        actions="true"
        editRoute="{{ route('reference.species.edit', ['species' => '__id__']) }}"
        showRoute="{{ route('reference.species.show', ['species' => '__id__']) }}"
        deleteRoute="{{ route('reference.species.destroy', ['species' => '__id__']) }}"
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