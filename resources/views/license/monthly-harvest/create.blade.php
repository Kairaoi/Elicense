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

                        <!-- Island Selection -->
                        <div class="form-group mb-3">
                            <label for="island_id" class="form-label required-field">Island</label>
                            <select name="island_id" id="island_id" class="form-control @error('island_id') is-invalid @enderror" required>
                                <option value="">Select Island</option>
                                @foreach($islands as $id => $name)
                                    <option value="{{ $id }}" {{ old('island_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('island_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Year Selection -->
                        <div class="form-group mb-3">
                            <label for="year" class="form-label required-field">Year</label>
                            <select name="year" id="year" class="form-control @error('year') is-invalid @enderror" required>
                                <option value="">Select Year</option>
                                @foreach($years as $value => $label)
                                    <option value="{{ $value }}" {{ old('year') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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

                        <!-- Species Selection with Quantity -->
                 
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
            <button type="button" class="btn btn-danger remove-species">
                <i class="fas fa-trash-alt"></i>
            </button>
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
$(document).ready(function() {
    // Function to update the species dropdown based on selected agent and island
    function updateSpeciesDropdown() {
        var agentId = $('#agent_id').val();
        var islandId = $('#island_id').val();
        
        // Clear existing species options
        $('#species-selection').empty();

        // Only make the request if both agent and island are selected
        if (agentId && islandId) {
            $.ajax({
                url: '{{ route("license.getSpecies") }}',
                method: 'GET',
                data: {
                    agent_id: agentId,
                    island_id: islandId
                },
                success: function(response) {
                    if (response.success && response.species.length > 0) {
                        // Add initial species row
                        addSpeciesRow(response.species);
                        
                        // Enable the add species button
                        $('#add-species').prop('disabled', false);
                        
                        // Store species data for later use
                        window.availableSpecies = response.species;
                    } else {
                        $('#species-selection').html(
                            '<div class="alert alert-info">No species available for the selected agent and island.</div>'
                        );
                        $('#add-species').prop('disabled', true);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching species data:', error);
                    $('#species-selection').html(
                        '<div class="alert alert-danger">Error loading species data. Please try again.</div>'
                    );
                    $('#add-species').prop('disabled', true);
                }
            });
        } else {
            $('#add-species').prop('disabled', true);
        }
    }

    // Function to add a new species row
    function addSpeciesRow(speciesData) {
        var row = `
            <div class="d-flex mb-2">
                <select name="species[]" class="form-control species-select me-2" required>
                    <option value="">Select Species</option>
                    ${speciesData.map(species => `
                        <option value="${species.id}">
                            ${species.name} (Remaining: ${species.remaining_quota} kg)
                        </option>
                    `).join('')}
                </select>
                <input type="number" 
                       name="quantities[]" 
                       class="form-control quantity-input" 
                       placeholder="Quantity (kg)" 
                       min="0" 
                       step="0.01" 
                       required>
                <button type="button" class="btn btn-danger remove-species ms-2">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        `;
        $('#species-selection').append(row);
    }

    // Event handlers
    $('#agent_id, #island_id').change(updateSpeciesDropdown);
    
    $('#add-species').click(function() {
        if (window.availableSpecies) {
            addSpeciesRow(window.availableSpecies);
        }
    });

    // Remove species row
    $('#species-selection').on('click', '.remove-species', function() {
        $(this).closest('.d-flex').remove();
    });

    // Initial load
    updateSpeciesDropdown();
});
$(document).ready(function() {
    // Existing code remains the same...

    // Add this function to check if the form is valid
    function checkFormValidity() {
        var isValid = true;
        
        // Check if agent is selected
        if (!$('#agent_id').val()) {
            isValid = false;
        }
        
        // Check if island is selected
        if (!$('#island_id').val()) {
            isValid = false;
        }
        
        // Check if year is selected
        if (!$('#year').val()) {
            isValid = false;
        }
        
        // Check if month is selected
        if (!$('#month').val()) {
            isValid = false;
        }
        
        // Check if at least one species is selected and has a quantity
        var hasValidSpecies = false;
        $('#species-selection .d-flex').each(function() {
            var speciesSelect = $(this).find('select[name="species[]"]').val();
            var quantityInput = $(this).find('input[name="quantities[]"]').val();
            
            if (speciesSelect && quantityInput && quantityInput > 0) {
                hasValidSpecies = true;
            }
        });
        
        if (!hasValidSpecies) {
            isValid = false;
        }
        
        // Enable or disable submit button based on form validity
        $('#submit-btn').prop('disabled', !isValid);
    }

    // Add event listeners for form fields
    $('#agent_id, #island_id, #year, #month').on('change', checkFormValidity);
    
    // Monitor species selection changes
    $('#species-selection').on('change', 'select, input', checkFormValidity);
    $('#species-selection').on('keyup', 'input', checkFormValidity);
    
    // Add to your existing addSpeciesRow function
    function addSpeciesRow(speciesData) {
        // Existing row creation code...
        
        // After adding the row, check form validity
        checkFormValidity();
    }
    
    // Add to your existing remove species click handler
    $('#species-selection').on('click', '.remove-species', function() {
        $(this).closest('.d-flex').remove();
        checkFormValidity();  // Check validity after removing
    });

    // Initial check
    checkFormValidity();
});

</script>


@endpush