@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">License Application</h2>

    <form method="POST" action="{{ route('applicantdetails.applicantdetails.store') }}">
        @csrf
        <!-- Applicant Information -->
        <div class="mb-4">
            <label class="form-label">Applicant</label>
            @if(!empty($applicants))
                @php
                    // Get the single applicant from the array (since we only have one now)
                    $applicantId = array_key_first($applicants);
                    $applicantName = reset($applicants);
                @endphp
                <p>{{ $applicantName }}</p>
                <!-- Hidden input to pass the applicant ID -->
                <input type="hidden" name="applicant_id" value="{{ $applicantId }}">
            @else
                <p class="text-danger">No applicant profile found. Please create a profile first.</p>
            @endif
        </div>

        <!-- License Type Selection -->
        <div class="mb-4">
            <label for="license_type_id" class="form-label">License Type</label>
            <select name="license_type_id" id="license_type_id" class="form-select" required {{ empty($applicants) ? 'disabled' : '' }}>
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
                               id="island_{{ $id }}" {{ empty($applicants) ? 'disabled' : '' }}>
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
        <button type="submit" class="btn btn-primary mt-4" {{ empty($applicants) ? 'disabled' : '' }}>Submit License Application</button>
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