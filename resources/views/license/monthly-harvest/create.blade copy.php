@extends('layouts.app')

@section('title', 'Record Monthly Harvest')

@push('styles')
<style>
    .quota-info {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .quota-info .label {
        font-weight: 500;
        color: #6c757d;
    }

    .quota-info .value {
        font-size: 1.1em;
        font-weight: 600;
    }

    .required-field::after {
        content: " *";
        color: red;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Record Monthly Harvest</h5>
                    <a href="{{ route('license.monthly-harvests.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('license.monthly-harvests.store') }}" method="POST" id="harvest-form">
                        @csrf

                        <!-- Agent Selection -->
                        <div class="form-group mb-3">
                            <label for="agent_id" class="form-label required-field">Agent</label>
                            <select name="agent_id" id="agent_id" class="form-control @error('agent_id') is-invalid @enderror" required>
                                <option value="">Select Agent</option>
                                @foreach($agents as $id => $name)
                                    <option value="{{ $id }}" {{ old('agent_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('agent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Species Selection -->
<div class="form-group mb-3">
    <label for="species" class="form-label required-field">Species and Quantity Harvested</label>
    <div id="species-selection">
        <div class="d-flex mb-2">
            <select name="species[]" 
                    class="form-control species-select me-2 @error('species.*') is-invalid @enderror" 
                    required>
                <option value="">Select Species</option>
                @foreach($speciesTrackings as $id => $name)
                    <option value="{{ $id }}" {{ old('species') && in_array($id, old('species', [])) ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            <input type="number" 
                   name="quantities[]" 
                   class="form-control quantity-input @error('quantities.*') is-invalid @enderror" 
                   placeholder="Quantity (kg)" 
                   min="0" 
                   step="0.01" 
                   required>
            <button type="button" class="btn btn-danger remove-species"><i class="fas fa-trash-alt"></i></button>
        </div>
    </div>
    <button type="button" id="add-species" class="btn btn-success btn-sm">
        <i class="fas fa-plus"></i> Add Species
    </button>
    @error('species.*')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    @error('quantities.*')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


                        <!-- Quota Information -->
                        <div id="quota-info" class="quota-info" style="display: none;">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="label">Species</div>
                                    <div class="value" id="species-name">-</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="label">Total Quota</div>
                                    <div class="value" id="total-quota">-</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="label">Remaining Quota</div>
                                    <div class="value" id="remaining-quota">-</div>
                                </div>
                            </div>
                        </div>

                        <!-- Month Selection -->
                        <div class="form-group mb-3">
                            <label for="month" class="form-label required-field">Month</label>
                            <select name="month" id="month" class="form-control @error('month') is-invalid @enderror" required>
                                <option value="">Select Month</option>
                                @foreach($months as $value => $label)
                                    <option value="{{ $value }}" {{ old('month') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('month')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Quantity Harvested -->
                        <div class="form-group mb-3">
                            <label for="quantity_harvested" class="form-label required-field">Quantity Harvested (kg)</label>
                            <input type="number" 
                                   name="quantity_harvested" 
                                   id="quantity_harvested" 
                                   class="form-control @error('quantity_harvested') is-invalid @enderror"
                                   value="{{ old('quantity_harvested') }}"
                                   required
                                   min="0"
                                   step="0.01">
                            @error('quantity_harvested')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="form-group mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" 
                                      id="notes" 
                                      class="form-control @error('notes') is-invalid @enderror"
                                      rows="3"
                                      maxlength="1000">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Maximum 1000 characters</small>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                                <i class="fas fa-save"></i> Record Harvest
                            </button>
                            <a href="{{ route('license.monthly-harvests.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const speciesSelectionContainer = document.getElementById('species-selection');
    const addSpeciesButton = document.getElementById('add-species');

    // Function to add a new row for species and quantity
    function addSpeciesRow() {
        const row = document.createElement('div');
        row.classList.add('d-flex', 'mb-2');
        row.innerHTML = `
            <select name="species[]" 
                    class="form-control species-select me-2" 
                    required>
                <option value="">Select Species</option>
                @foreach($speciesTrackings as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
            <input type="number" 
                   name="quantities[]" 
                   class="form-control quantity-input" 
                   placeholder="Quantity (kg)" 
                   min="0" 
                   step="0.01" 
                   required>
            <button type="button" class="btn btn-danger remove-species"><i class="fas fa-trash-alt"></i></button>
        `;
        speciesSelectionContainer.appendChild(row);
        attachRemoveListener(row.querySelector('.remove-species'));
    }

    // Function to attach remove event listener
    function attachRemoveListener(button) {
        button.addEventListener('click', function () {
            button.parentElement.remove();
        });
    }

    // Add event listener to the Add Species button
    addSpeciesButton.addEventListener('click', addSpeciesRow);

    // Attach listeners to initial remove buttons
    document.querySelectorAll('.remove-species').forEach(attachRemoveListener);
});

</script>
@endpush
