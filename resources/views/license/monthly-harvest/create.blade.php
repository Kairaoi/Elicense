@extends('layouts.app')

@section('title', 'Record Monthly Harvest')

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
        gap: 10px; /* Add some space between elements */
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
        color: #28a745; /* Green color for available quota */
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
                    <h5 class="mb-0">Record Monthly Harvest</h5>
                    <a href="{{ route('license.monthly-harvests.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('license.monthly-harvests.store') }}" method="POST" id="harvest-form">
                        @csrf

                        <!-- Applicant Selection -->
                        <div class="form-group mb-3">
                            <label for="applicant_id" class="form-label required-field">Applicant</label>
                            <select name="applicant_id" id="applicant_id" class="form-control @error('applicant_id') is-invalid @enderror" required>
                                <option value="">Select Applicant</option>
                                @foreach($applicants as $id => $name)
                                    <option value="{{ $id }}" {{ old('applicant_id') == $id ? 'selected' : '' }}>
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
                                    <option value="{{ $id }}" {{ old('island_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('island_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!<!-- License Type Selection -->
<div class="form-group">
    <label for="license_type_id">License Type</label>
    <select name="license_type_id" id="license_type_id" class="form-control" required>
        <option value="">Select License Type</option>
        @foreach($licenseTypes as $id => $name)
            <option value="{{ $id }}">{{ $name }}</option>
        @endforeach
    </select>
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
                                            <option value="{{ $year }}" {{ old('year') == $year ? 'selected' : '' }}>
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
                                            <option value="{{ $value }}" {{ old('month') == $value ? 'selected' : '' }}>
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
                                    maxlength="1000">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Maximum 1000 characters</small>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" id="submit-btn">
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
// Wait for document ready
$(document).ready(function() {
    // Function to clear and disable license type selection
    function resetLicenseType() {
        $('#license_type_id').val('').prop('disabled', true);
        clearLicenseItems();
    }

    // Function to clear license items
    function clearLicenseItems() {
        $('#license-items-container').empty();
        // Or however you're displaying the license items
    }

    // Handle applicant selection
    $('#applicant_id').on('change', function() {
        if (!$(this).val()) {
            resetLicenseType();
            return;
        }
        
        const applicantId = $(this).val();
        
        // Enable license type selection when applicant is selected
        $('#license_type_id').prop('disabled', false);
        
        // Clear previous selections
        $('#license_type_id').val('');
        clearLicenseItems();
    });

    // Handle island selection
    $('#island_id').on('change', function() {
        if (!$(this).val()) {
            clearLicenseItems();
            return;
        }
        
        // If all required fields are selected, fetch license items
        const licenseTypeId = $('#license_type_id').val();
        if (licenseTypeId) {
            fetchLicenseItems();
        }
    });

    // Handle license type selection
    $('#license_type_id').on('change', function() {
        if (!$(this).val()) {
            clearLicenseItems();
            return;
        }
        
        fetchLicenseItems();
    });

    // Function to fetch license items
    function fetchLicenseItems() {
        const applicantId = $('#applicant_id').val();
        const islandId = $('#island_id').val();
        const licenseTypeId = $('#license_type_id').val();
        
        if (!applicantId || !islandId || !licenseTypeId) {
            return;
        }

        $.ajax({
            url: '{{ route("license.licenses.getLicenseItems") }}',
            data: {
                applicant_id: applicantId,
                island_id: islandId,
                license_type_id: licenseTypeId
            },
            success: function(response) {
                if (response.success) {
                    updateLicenseItemsTable(response.items);
                } else {
                    alert(response.message || 'No items found');
                }
            },
            error: function(xhr) {
                alert('Error fetching license items');
                console.error(xhr);
            }
        });
    }

    // Function to update the license items table
    function updateLicenseItemsTable(items) {
        const container = $('#license-items-container');
        container.empty();

        if (items.length === 0) {
            container.html('<p>No license items found</p>');
            return;
        }

        let html = `
            <table class="table">
                <thead>
                    <tr>
                        <th>Species</th>
                        <th>Requested Quota</th>
                        <th>Remaining Quota</th>
                        <th>License Number</th>
                        <th>Quantity Harvested</th>
                    </tr>
                </thead>
                <tbody>
        `;

        items.forEach((item) => {
            html += `
                <tr>
                    <td>${item.species_name}</td>
                    <td>${item.requested_quota}</td>
                    <td>${item.remaining_quota}</td>
                    <td>${item.license_number}</td>
                    <td>
                        <input type="number" 
                               name="harvested_quantity[${item.id}]" 
                               class="form-control"
                               min="0"
                               max="${item.remaining_quota}"
                               required>
                        <input type="hidden" 
                               name="license_item_id[${item.id}]" 
                               value="${item.id}">
                    </td>
                </tr>
            `;
        });

        html += '</tbody></table>';
        container.html(html);
    }
});
</script>
@endpush