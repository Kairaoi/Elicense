@extends('layouts.app')

@section('title', 'Edit Monthly Harvest')

@push('styles')
<style>
    .quota-info {
        background-color: #f8f9fa;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .quota-info .label {
        font-weight: 500;
        color: #6c757d;
    }

    .quota-info .value {
        font-size: 1em;
        font-weight: 600;
    }

    .required-field::after {
        content: " *";
        color: red;
    }

    .quota-item, .harvest-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
        gap: 10px;
    }

    .quota-item .species-name, .harvest-item .species-name {
        font-weight: 600;
        flex: 2;
        font-size: 1em;
    }

    .quota-item .quota-value, .harvest-item .quantity-input {
        font-size: 1em;
        flex: 1;
        text-align: right;
    }

    .quota-item .quota-value {
        color: #28a745;
    }

    .harvest-item .quantity-input input {
        width: 100%;
        padding: 8px;
        font-size: 0.95em;
        text-align: right;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        font-size: 0.95em;
    }

    .form-control {
        font-size: 0.95em;
    }

    .btn {
        font-size: 0.95em;
        padding: 8px 16px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Monthly Harvest</h5>
                    <a href="{{ route('license.monthly-harvests.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('license.monthly-harvests.update', $harvest->id) }}" method="POST" id="harvest-form">
                        @csrf
                        @method('PUT')

                        <!-- Applicant Selection -->
                        <div class="form-group mb-3">
                            <label for="applicant_id" class="form-label required-field">Applicant</label>
                            <select name="applicant_id" id="applicant_id" class="form-control @error('applicant_id') is-invalid @enderror" required>
                                <option value="">Select Applicant</option>
                                @foreach($applicants as $id => $name)
                                    <option value="{{ $id }}" {{ old('applicant_id', $harvest->applicant_id) == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('applicant_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Island Selection -->
                        <div class="form-group mb-3">
                            <label for="island_id" class="form-label required-field">Island</label>
                            <select name="island_id" id="island_id" class="form-control @error('island_id') is-invalid @enderror" required>
                                <option value="">Select Island</option>
                                @foreach($islands as $id => $name)
                                    <option value="{{ $id }}" {{ old('island_id', $harvest->island_id) == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('island_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- License Items and Harvest Quantity -->
                        <div class="form-group mb-3">
                            <label class="form-label required-field">License Items and Harvest Quantity</label>
                            <div id="license-items-container">
                                <!-- License items will be loaded here via AJAX -->
                            </div>
                        </div>

                        <!-- Year and Month Selection -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="year" class="form-label required-field">Year</label>
                                    <select name="year" id="year" class="form-control @error('year') is-invalid @enderror" required>
                                        <option value="">Select Year</option>
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" {{ old('year', $harvest->year) == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="month" class="form-label required-field">Month</label>
                                    <select name="month" id="month" class="form-control @error('month') is-invalid @enderror" required>
                                        <option value="">Select Month</option>
                                        @foreach($months as $value => $label)
                                            <option value="{{ $value }}" {{ old('month', $harvest->month) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('month')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="form-group mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" 
                                    id="notes" 
                                    class="form-control @error('notes') is-invalid @enderror"
                                    rows="3"
                                    maxlength="1000">{{ old('notes', $harvest->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Maximum 1000 characters</small>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <i class="fas fa-save"></i> Update Harvest
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
    function loadQuota() {
        const applicantId = $('#applicant_id').val();
        const islandId = $('#island_id').val();

        if (applicantId && islandId) {
            const url = "{{ route('license.licenses.getLicenseItems') }}";

            $.get(url, { applicant_id: applicantId, island_id: islandId })
                .done(function(response) {
                    console.log('API Response:', response);

                    if (response.success && response.items.length > 0) {
                        let licenseItemsHtml = '';

                        response.items.forEach(function(item) {
                            // Get the existing harvested quantity if any
                            const existingQuantity = {{ json_encode((array)old('harvested_quantity', $harvest->getHarvestedQuantities())) }};
                            const quantity = existingQuantity[item.id] || '';

                            licenseItemsHtml += `
                                <div class="harvest-item">
                                    <div class="species-name">${item.species_name}</div>
                                    <div class="quota-value">
                                        Requested: ${item.requested_quota} kg | Available: ${item.remaining_quota} kg
                                    </div>
                                    <div class="quantity-input">
                                        <input type="number"
                                            name="harvested_quantity[${item.id}]"
                                            id="harvested_quantity_${item.id}"
                                            class="form-control @error('harvested_quantity.${item.id}') is-invalid @enderror"
                                            step="0.01"
                                            min="0"
                                            value="${quantity}"
                                            required>
                                        @error('harvested_quantity.${item.id}')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <input type="hidden" name="license_item_id[${item.id}]" value="${item.id}" />
                                </div>
                            `;
                        });

                        $('#license-items-container').html(licenseItemsHtml);
                    } else {
                        $('#license-items-container').html('No quota information available for this selection.');
                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error:', jqXHR.responseText);
                    $('#license-items-container').html('An error occurred while loading data.');
                });
        } else {
            $('#license-items-container').html('Please select an applicant and island.');
        }
    }

    // Load quota initially if values are present
    if ($('#applicant_id').val() && $('#island_id').val()) {
        loadQuota();
    }

    // Trigger quota loading when applicant or island is changed
    $('#applicant_id, #island_id').change(loadQuota);
});
</script>
@endpush