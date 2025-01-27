@extends('layouts.app')

@section('content')
<<div class="container" style="margin-top: 100px;"> <!-- Add margin here -->
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 20px;">
        <h1 class="elegant-heading">Island Information</h1>
        <a href="{{ route('reference.islands.create') }}" class="btn btn-secondary elegant-back-btn">Add New Island</a>
    </div>

    <!-- DataTable Component -->
    <x-datatable
        id="islandsTable"
        :columns="[ 
            ['title' => 'ID', 'data' => 'id', 'name' => 'id'],
            ['title' => 'Name', 'data' => 'name', 'name' => 'name'],
           
        ]"
        ajaxUrl="{{ route('reference.islands.datatables') }}"
        actions="true"
        editRoute="{{ route('reference.islands.edit', ['island' => '__id__']) }}"
        showRoute="{{ route('reference.islands.show', ['island' => '__id__']) }}"
        deleteRoute="{{ route('reference.islands.destroy', ['island' => '__id__']) }}"
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