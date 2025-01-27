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
</style>
@endsection

@section('content')
<div class="container">
    <!-- Page Title -->
    <div class="page-title">
        <h1>
            Select Your License Type</h1>
    </div>
    
    <!-- License Form -->
    <form id="licenseForm" method="POST" action="{{ route('license.licenses.store') }}">
        @csrf
        <input type="hidden" name="applicant_id" value="{{ $applicant->id }}">

        <!-- Applicant Information -->
        <div class="mb-4">
            <label class="form-label">Applicant</label>
            <p>{{ $applicant->first_name }} {{ $applicant->last_name }} ({{ $applicant->email }})</p>
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

        <!-- License Type Selection -->
        <div class="mb-4">
            <label for="license_type_id" class="form-label">Islands</label>
            <select name="license_type_id" id="license_type_id" class="form-select" required>
                <option value="">Select Islands</option>
                @foreach ($islands as $id => $island)
                    <option value="{{ $id }}" {{ old('island_id') == $id ? 'selected' : '' }}>
                        {{ $island }}
                    </option>
                @endforeach
            </select>
            @error('island_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Species Information -->
        <h5 class="form-section-title">Species Information</h5>
        <div id="species-container" class="mb-4">
            <!-- Species entries will be dynamically added here -->
        </div>

        <button type="button" class="btn btn-custom-primary mt-3" id="add-species" style="display:none;">Add Species</button>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-custom-primary mt-4">Submit License Application</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const licenseTypeSelect = document.getElementById('license_type_id');
        const speciesContainer = document.getElementById('species-container');
        const addSpeciesButton = document.getElementById('add-species');
        let speciesIndex = 0;

        // Using the species list grouped by license type
        const allSpeciesByLicenseType = @json($speciesByLicenseType);

        // Add total fee display elements after the species container
        const totalFeeDisplay = document.createElement('div');
        totalFeeDisplay.className = 'row mt-4';
        totalFeeDisplay.innerHTML = `
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Subtotal:</strong>
                            <span id="subtotal">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <strong>VAT (12.5%):</strong>
                            <span id="vat">$0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total Amount:</strong>
                            <span id="total" class="text-primary h5">$0.00</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
        speciesContainer.after(totalFeeDisplay);

        // Add hidden input fields for the form submission
        const hiddenInputs = document.createElement('div');
        hiddenInputs.innerHTML = `
            <input type="hidden" name="subtotal" id="subtotal-input">
            <input type="hidden" name="vat_amount" id="vat-input">
            <input type="hidden" name="total_amount" id="total-input">
        `;
        speciesContainer.after(hiddenInputs);

        function calculateTotals() {
            let subtotal = 0;
            const VAT_RATE = 0.125; // 12.5%

            // Calculate subtotal from all species entries
            document.querySelectorAll('.species-entry').forEach(entry => {
                const speciesSelect = entry.querySelector('.species-select');
                const quotaInput = entry.querySelector('input[type="number"]');
                
                if (speciesSelect.value && quotaInput.value) {
                    const selectedSpecies = allSpeciesByLicenseType[licenseTypeSelect.value]
                        .find(s => s.id == speciesSelect.value);
                    
                    if (selectedSpecies) {
                        subtotal += selectedSpecies.unit_price * parseFloat(quotaInput.value);
                    }
                }
            });

            const vatAmount = subtotal * VAT_RATE;
            const totalAmount = subtotal + vatAmount;

            // Update display
            document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
            document.getElementById('vat').textContent = `$${vatAmount.toFixed(2)}`;
            document.getElementById('total').textContent = `$${totalAmount.toFixed(2)}`;

            // Update hidden inputs
            document.getElementById('subtotal-input').value = subtotal.toFixed(2);
            document.getElementById('vat-input').value = vatAmount.toFixed(2);
            document.getElementById('total-input').value = totalAmount.toFixed(2);
        }

        function updateSpeciesOptions() {
            const selectedLicenseTypeId = licenseTypeSelect.value;
            speciesContainer.innerHTML = '';
            addSpeciesButton.style.display = selectedLicenseTypeId ? 'block' : 'none';

            if (selectedLicenseTypeId && allSpeciesByLicenseType[selectedLicenseTypeId]) {
                addSpeciesEntry();
            }
            calculateTotals();
        }

        function addSpeciesEntry() {
            const selectedLicenseTypeId = licenseTypeSelect.value;

            if (!allSpeciesByLicenseType[selectedLicenseTypeId]) {
                return;
            }

            const newSpeciesEntry = document.createElement('div');
            newSpeciesEntry.className = 'row mb-3 species-entry';
            newSpeciesEntry.innerHTML = `
                <div class="col-md-5">
                    <label for="species_${speciesIndex}" class="form-label">Species</label>
                    <select name="species[${speciesIndex}][id]" class="form-select species-select" required>
                        <option value="">Select Species</option>
                        ${allSpeciesByLicenseType[selectedLicenseTypeId].map(species => `
                            <option value="${species.id}" data-price="${species.unit_price}">${species.name} ($${species.unit_price}/unit)</option>
                        `).join('')}
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="requested_quota_${speciesIndex}" class="form-label">Requested Quota</label>
                    <input type="number" name="species[${speciesIndex}][requested_quota]" class="form-control quota-input" min="0" step="0.01" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Unit Price</label>
                    <div class="unit-price">$0.00</div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn remove-species">Remove</button>
                </div>
            `;
            speciesContainer.appendChild(newSpeciesEntry);

            // Add event listeners for the new entry
            const newSelect = newSpeciesEntry.querySelector('.species-select');
            const newQuotaInput = newSpeciesEntry.querySelector('.quota-input');

            newSelect.addEventListener('change', function() {
                calculateTotals();
                updateUnitPrice(this);
            });

            newQuotaInput.addEventListener('input', calculateTotals);

            speciesIndex++;
        }

        function updateUnitPrice(select) {
            const unitPriceDiv = select.closest('.species-entry').querySelector('.unit-price');
            const selectedOption = select.options[select.selectedIndex];
            const price = selectedOption.dataset.price || '0.00';
            unitPriceDiv.textContent = `$${price}`;
        }

        licenseTypeSelect.addEventListener('change', updateSpeciesOptions);
        addSpeciesButton.addEventListener('click', addSpeciesEntry);

        speciesContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-species')) {
                e.target.closest('.species-entry').remove();
                calculateTotals();
            }
        });

        // Initial setup
        updateSpeciesOptions();
    });
</script>
@endpush
