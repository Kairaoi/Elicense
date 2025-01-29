@extends('layouts.app')

@section('title', 'Create Species Tracking')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .species-row {
        background-color: #f8f9fa;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 10px;
        border: 1px solid #ced4da;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .remove-species {
        color: #dc3545;
        cursor: pointer;
    }

    .add-species-btn {
        margin-bottom: 25px;
    }

    .select2-container .select2-selection--single {
        height: 40px;
        line-height: 40px;
    }

    .form-header {
        background-color: #007bff;
        color: #fff;
        padding: 15px;
        border-radius: 5px;
    }

    .form-header h5 {
        margin: 0;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create New Species Tracking</h5>
                    <a href="{{ route('license.trackings.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <form id="tracking-form" action="{{ route('license.trackings.store') }}" method="POST">
                        @csrf

                        <!-- General Information -->
                        <div class="form-header mb-4">
                            <h5>General Information</h5>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="agent_id" class="form-label required-field">Agent</label>
                                    <select class="form-control select2" name="agent_id" id="agent_id" required>
                                        <option value="">Select Agent</option>
                                        @foreach($agents as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('agent_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="island_id" class="form-label required-field">Island</label>
                                    <select class="form-control select2" name="island_id" id="island_id" required>
                                        <option value="">Select Island</option>
                                        @foreach($islands as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('island_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="year" class="form-label required-field">Year</label>
                                    <select class="form-control" name="year" id="year" required>
                                        <option value="">Select Year</option>
                                        @for($y = date('Y') - 5; $y <= date('Y') + 5; $y++)
                                            <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>
                                                {{ $y }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('year')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Species Rows -->
                        <div id="species-container">
                            <!-- Species rows will be dynamically added here -->
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <button type="button" class="btn btn-success add-species-btn" onclick="addSpeciesRow()">
                                    <i class="fas fa-plus"></i> Add Species
                                </button>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Create Tracking
                                    </button>
                                    <a href="{{ route('license.trackings.index') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
let rowCounter = 0;

function initializeSelect2(element) {
    $(element).select2({
        width: '100%',
        placeholder: 'Select Species',
        allowClear: true
    });
}

function addSpeciesRow() {
    const container = document.getElementById('species-container');
    const rowHtml = `
        <div class="species-row" id="species-row-${rowCounter}">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="form-label required-field">Species</label>
                        <select class="form-control species-select" 
                                name="species[${rowCounter}][species_id]" 
                                required>
                            <option value="">Select Species</option>
                            @foreach($species as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="button" 
                            class="btn btn-danger w-100" 
                            onclick="removeSpeciesRow(${rowCounter})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', rowHtml);
    initializeSelect2(`#species-row-${rowCounter} .species-select`);
    rowCounter++;
}

function removeSpeciesRow(rowId) {
    document.getElementById(`species-row-${rowId}`).remove();
}

$(document).ready(function() {
    $('.select2').select2({
        width: '100%',
        placeholder: 'Select an option',
        allowClear: true
    });

    addSpeciesRow();
});
</script>
@endpush
