@extends('layouts.app')

@section('styles')
<style>
    /* Custom styling */
    .form-control, .form-select {
        padding: 0.75rem 1rem;
        border-radius: 10px;
        border: 2px solid #e0e0e0;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    .page-title {
        background: linear-gradient(45deg, #4e73df, #2e59d9);
        color: #fff;
        padding: 40px 0;
        text-align: center;
        border-radius: 15px;
        margin-bottom: 40px;
    }

    .page-title h1 {
        font-size: 2.5rem;
        font-weight: bold;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
    }

    .form-section-title {
        font-size: 1.5rem;
        color: #2c3e50;
        font-weight: bold;
        padding-bottom: 10px;
        border-bottom: 2px solid #4e73df;
        margin-bottom: 25px;
    }

    .btn-custom-primary {
        background: #4e73df;
        border: none;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-custom-primary:hover {
        background: #2e59d9;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(78, 115, 223, 0.3);
    }

    .btn-custom-secondary {
        background: #858796;
        border: none;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-custom-secondary:hover {
        background: #717384;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(133, 135, 150, 0.3);
    }

    .species-entry .remove-species {
        background-color: #e74a3b;
        border: none;
        color: white;
        border-radius: 5px;
        padding: 8px 16px;
        font-weight: 600;
        transition: background 0.3s ease;
    }

    .species-entry .remove-species:hover {
        background-color: #c0392b;
    }

    /* Tooltip styles */
    .tooltip-inner {
        background-color: #4e73df;
    }
</style>
@endsection

@section('content')
<div class="container">
    <!-- Page Title -->
    <div class="page-title">
        <h1>Select Your License Type</h1>
    </div>

    <!-- License Form -->
    <form id="licenseForm" method="POST" action="{{ route('license.licenses.store2') }}">
        @csrf

        <!-- Applicant Information -->
        <div class="mb-4">
            <label for="applicant_id" class="form-label">Select Applicant</label>
            <select name="applicant_id" id="applicant_id" class="form-select" required>
                <option value="">Select Applicant</option>
                @foreach ($applicants as $id => $applicant)
                    <option value="{{ $id }}" {{ old('applicant_id') == $id ? 'selected' : '' }}>
                        {{ $applicant }}
                    </option>
                @endforeach
            </select>
            @error('applicant_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- License Type Selection -->
        <div class="mb-4">
            <label for="license_type_id" class="form-label">License Type</label>
            <select name="license_type_id" id="license_type_id" class="form-select" required>
                <option value="">Select License Type</option>
                @foreach ($licenseTypes as $id => $licenseType)
                    <option value="{{ $id }}" {{ old('license_type_id') == $id ? 'selected' : '' }}>
                        {{ $licenseType }}
                    </option>
                @endforeach
            </select>
            @error('license_type_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Island Selection -->
        <div class="mb-4">
            <label class="form-label">Select Islands</label>
            <div class="row">
                @foreach ($islands as $id => $name)
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input class="form-check-input island-checkbox" type="checkbox" 
                               name="selected_islands[]" value="{{ $id }}" 
                               id="island_{{ $id }}">
                        <label class="form-check-label" for="island_{{ $id }}">
                            {{ $name }}
                        </label>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Species and Quotas Section -->
        <div id="species-quotas-container" class="mt-4" style="display: none;">
            <!-- This will be populated dynamically -->
        </div>

        <!-- Hidden Inputs for Totals -->
        <input type="hidden" name="subtotal" id="subtotal-input">
        <input type="hidden" name="vat_amount" id="vat-input">
        <input type="hidden" name="total_amount" id="total-input">

        <!-- Submit Button -->
        <button type="submit" class="btn btn-custom-primary mt-4">Submit License Application</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const licenseTypeSelect = document.getElementById('license_type_id');
    const islandCheckboxes = document.querySelectorAll('.island-checkbox');
    const speciesQuotasContainer = document.getElementById('species-quotas-container');
    
    // Data from the backend
    const availableQuotas = @json($availableQuotas);
    const speciesByLicenseType = @json($speciesByLicenseType);

    // Check if availableQuotas has data
    if (Object.keys(availableQuotas).length === 0) {
        // If no quotas available, show message
        speciesQuotasContainer.innerHTML = '<div class="alert alert-warning">No quotas available at this time.</div>';
        speciesQuotasContainer.style.display = 'block';
        
        // Optionally disable form elements
        licenseTypeSelect.disabled = true;
        islandCheckboxes.forEach(checkbox => checkbox.disabled = true);
        return;
    }

    function updateSpeciesQuotas() {
        const selectedLicenseType = licenseTypeSelect.value;
        const selectedIslands = Array.from(document.querySelectorAll('.island-checkbox:checked'))
            .map(cb => cb.value);

        if (!selectedLicenseType || selectedIslands.length === 0) {
            speciesQuotasContainer.style.display = 'none';
            return;
        }

        speciesQuotasContainer.style.display = 'block';
        let html = '<h4>Species and Quotas</h4>';

        const species = speciesByLicenseType[selectedLicenseType] || [];

        species.forEach(species => {
            html += `
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">${species.name}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Island</th>
                                        <th>Available Quota (kg)</th>
                                        <th>Requested Quota (kg)</th>
                                    </tr>
                                </thead>
                                <tbody>`;
            
            selectedIslands.forEach(islandId => {
                const availableQuota = availableQuotas[species.id]?.[islandId] || 0;
                html += `
                    <tr>
                        <td>${document.getElementById('island_' + islandId).nextElementSibling.textContent}</td>
                        <td>${availableQuota}</td>
                        <td>
                            <input type="number" 
                                   name="quotas[${species.id}][${islandId}]" 
                                   class="form-control quota-input" 
                                   min="0" 
                                   max="${availableQuota}"
                                   step="0.01">
                        </td>
                    </tr>`;
            });

            html += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>`;
        });

        speciesQuotasContainer.innerHTML = html;
    }

    licenseTypeSelect.addEventListener('change', updateSpeciesQuotas);
    islandCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSpeciesQuotas);
    });
});
</script>
@endpush
