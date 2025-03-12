@extends('layouts.app')

@section('styles')
<style>
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
</style>
@endsection

@section('content')
<div class="container">
    <div class="page-title">
        <h1>Edit License</h1>
    </div>

    <form action="{{ route('license.licenses.update', $license->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="applicant_id" class="form-label">Applicant</label>
            <select name="applicant_id" id="applicant_id" class="form-select" required>
                <option value="">Select Applicant</option>
                @foreach ($applicants as $id => $name)
                    <option value="{{ $id }}" {{ $license->applicant_id == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label for="license_type_id" class="form-label">License Type</label>
            <select name="license_type_id" id="license_type_id" class="form-select" required>
                <option value="">Select License Type</option>
                @foreach ($licenseTypes as $id => $name)
                    <option value="{{ $id }}" {{ $license->license_type_id == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="form-label">Select Islands</label>
            <div class="row">
                @foreach ($islands as $id => $name)
                    <div class="col-md-4 mb-2">
                        <div class="form-check">
                            <input class="form-check-input island-checkbox" type="checkbox" name="islands[]" value="{{ $id }}" id="island_{{ $id }}" {{ in_array($id, $selectedIslands) ? 'checked' : '' }}>
                            <label class="form-check-label" for="island_{{ $id }}">{{ $name }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div id="species-quotas-container" class="mt-4" style="display: none;"></div>

        <button type="submit" class="btn btn-primary mt-4">Update License</button>
        <a href="{{ route('license.licenses.index') }}" class="btn btn-secondary ml-2">Cancel</a>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const licenseTypeSelect = document.getElementById('license_type_id');
    const islandCheckboxes = document.querySelectorAll('.island-checkbox');
    const speciesQuotasContainer = document.getElementById('species-quotas-container');
    
    const availableQuotas = @json($availableQuotas);
    const speciesByLicenseType = @json($speciesByLicenseType);
    const existingQuotas = @json($existingQuotas ?? []);
    
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
                
                // Get existing quota value if it exists
                const existingValue = existingQuotas[species.id]?.[islandId] || '';
                
                html += `
                    <tr>
                        <td>${document.getElementById('island_' + islandId).nextElementSibling.textContent}</td>
                        <td>${availableQuota}</td>
                        <td>
                            <input type="number" name="species_quota[${species.id}][${islandId}]" 
                            class="form-control" min="0" max="${availableQuota}" 
                            value="${existingValue}">
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
    islandCheckboxes.forEach(checkbox => checkbox.addEventListener('change', updateSpeciesQuotas));
    
    // Important: Trigger the function on page load to show existing values
    if (licenseTypeSelect.value) {
        updateSpeciesQuotas();
    }
});
</script>
@endpush