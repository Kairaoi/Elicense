@extends('layouts.app')

@section('title', 'Create Species Tracking')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .species-row {
        background-color: #f8f9fa;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    
    .remove-species {
        color: #dc3545;
        cursor: pointer;
    }
    
    .add-species-btn {
        margin-bottom: 20px;
    }
    
    .select2-container .select2-selection--single {
        height: 38px;
        line-height: 38px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create New Species Tracking</h5>
                    <a href="{{ route('license.trackings.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>

                <div class="card-body p-4">
                    <form id="tracking-form" action="{{ route('license.trackings.store') }}" method="POST">
                        @csrf

                        <!-- Common Fields -->
                        <div class="row mb-4">
                            <!-- Agent Selection -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="agent_id" class="form-label required-field">Agent</label>
                                    <select class="form-control select2" name="agent_id" id="agent_id" required>
                                        <option value="">Select Agent</option>
                                        @foreach($agents as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="agent_id-error"></div>
                                </div>
                            </div>

                            <!-- Island Selection -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="island_id" class="form-label required-field">Island</label>
                                    <select class="form-control select2" name="island_id" id="island_id" required>
                                        <option value="">Select Island</option>
                                        @foreach($islands as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="island_id-error"></div>
                                </div>
                            </div>

                            <!-- Year Selection -->
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
                                    <div class="invalid-feedback" id="year-error"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Species Rows Container -->
                        <div id="species-container">
                            <!-- Species rows will be added here -->
                        </div>

                        <!-- Add Species Button -->
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
                                    <a href="{{ route('license.trackings.index') }}" class="btn btn-secondary">
                                        Cancel
                                    </a>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <div class="col-md-5">
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
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label class="form-label required-field">Quota Allocated (kg)</label>
                        <input type="number" 
                               class="form-control" 
                               name="species[${rowCounter}][quota_allocated]" 
                               required 
                               min="0" 
                               step="0.01">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" 
                                class="btn btn-danger btn-block" 
                                onclick="removeSpeciesRow(${rowCounter})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', rowHtml);
    initializeSelect2(`#species-row-${rowCounter} .species-select`);
    rowCounter++;
}

function removeSpeciesRow(rowId) {
    const row = document.getElementById(`species-row-${rowId}`);
    row.remove();
}

$(document).ready(function() {
    // Initialize Select2 for static selects
    $('.select2').select2({
        width: '100%',
        placeholder: 'Select an option',
        allowClear: true
    });

    // Add initial species row
    addSpeciesRow();

    // Form submission handling
    $('#tracking-form').on('submit', function(e) {
        e.preventDefault();

        // Reset error states
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').empty();

        // Validate at least one species
        if ($('.species-row').length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please add at least one species'
            });
            return;
        }

        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalBtnText = submitBtn.html();
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop('disabled', true);

        // Submit form
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message || 'Species tracking created successfully',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = "{{ route('license.trackings.index') }}";
                });
            },
            error: function(xhr) {
                submitBtn.html(originalBtnText).prop('disabled', false);

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function(key) {
                        const input = $(`[name="${key}"]`);
                        input.addClass('is-invalid')
                            .siblings('.invalid-feedback')
                            .text(errors[key][0]);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON.message || 'An error occurred while creating the record'
                    });
                }
            }
        });
    });
});
</script>
@endpush