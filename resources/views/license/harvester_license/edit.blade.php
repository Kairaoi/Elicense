@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h1>Edit Harvester License</h1>
        </div>
        <div class="card-body">
        <form action="{{ route('harvester.licenses.update', $harvesterLicense->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Applicant Selection -->
                    <div class="col-md-6">
                    <div class="form-group">
            <label for="harvester_applicant_id">Applicant</label>
            <select name="harvester_applicant_id" id="harvester_applicant_id" class="form-control" required>
                <option value="">Select Applicant</option>
                @foreach($applicants as $applicant)
                    <option value="{{ $applicant->id }}" {{ $harvesterLicense->harvester_applicant_id == $applicant->id ? 'selected' : '' }}>
                        {{ $applicant->name }}
                    </option>
                @endforeach
            </select>
            @error('harvester_applicant_id')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
                    </div>

                    <!-- Island Selection -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="island_id">Island</label>
                            <select name="island_id" id="island_id" class="form-control" required>
                                <option value="">Select Island</option>
                                @foreach($islands as $id => $island)
                                    <option value="{{ $id }}" {{ $harvesterLicense->island_id == $id ? 'selected' : '' }}>{{ $island }}</option>
                                @endforeach
                            </select>
                            @error('island_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <!-- License Type Selection -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="license_type">License Type</label>
                            <select name="license_type_id" id="license_type" class="form-control" required>
                                <option value="">Select License Type</option>
                                @foreach($licenseTypes as $id => $name)
                                    <option value="{{ $id }}" {{ $harvesterLicense->license_type_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('license_type_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Species Selection -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="species">Species</label>
                            <select name="species[]" id="species" class="form-control" multiple required style="height: 150px;">
                                @if($harvesterLicense->species)
                                    @foreach($harvesterLicense->species as $species)
                                        <option value="{{ $species->id }}" selected>{{ $species->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <small class="text-muted">Hold Ctrl/Cmd to select multiple species</small>
                            @error('species')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <!-- Fee -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fee">Fee ($)</label>
                            <input type="number" name="fee" id="fee" class="form-control" step="0.01" readonly value="{{ $harvesterLicense->fee }}">
                            @error('fee')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Issue Date -->
                    <!-- <div class="col-md-4">
                        <div class="form-group">
                            <label for="issue_date">Issue Date</label>
                            <input type="date" name="issue_date" id="issue_date" class="form-control" required value="{{ $harvesterLicense->issue_date }}">
                            @error('issue_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div> -->

                    <!-- Expiry Date -->
                    <!-- <div class="col-md-4">
                        <div class="form-group">
                            <label for="expiry_date">Expiry Date</label>
                            <input type="date" name="expiry_date" id="expiry_date" class="form-control" required value="{{ $harvesterLicense->expiry_date }}">
                            @error('expiry_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div> -->
                </div>

                <!-- Payment Receipt -->
                <div class="form-group mt-3">
                    <label for="payment_receipt_no">Payment Receipt No.</label>
                    <input type="text" name="payment_receipt_no" id="payment_receipt_no" class="form-control" required value="{{ $harvesterLicense->payment_receipt_no }}">
                    @error('payment_receipt_no')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Form Buttons -->
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update License</button>
                    <a href="{{ route('harvester.licenses.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const applicantSelect = document.getElementById('harvester_applicant_id');
    const feeInput = document.getElementById('fee');
    const groupSection = document.getElementById('groupMembersSection');
    const addMemberBtn = document.getElementById('addMemberBtn');
    const container = document.getElementById('groupMembersContainer');
    const licenseTypeSelect = document.getElementById('license_type');
    const speciesSelect = document.getElementById('species');
    let memberCount = 1;

    // Store species data by license type
    const speciesByLicenseType = @json($speciesByLicenseType);

    // Handle license type change
    licenseTypeSelect.addEventListener('change', function() {
        const licenseTypeId = this.value;
        
        // Clear current options
        speciesSelect.innerHTML = '';
        
        // If license type selected, populate species
        if (licenseTypeId && speciesByLicenseType[licenseTypeId]) {
            speciesByLicenseType[licenseTypeId].forEach(function(species) {
                const option = new Option(species.name, species.id);
                speciesSelect.add(option);
            });
        }
    });

    // Handle applicant selection change
    applicantSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const isGroup = selected.dataset.isGroup === '1';
        
        feeInput.value = isGroup ? '500.00' : '25.00';
        groupSection.style.display = isGroup ? 'block' : 'none';
        
        if (!isGroup) {
            // Clear group members section for individual applications
            container.innerHTML = '';
            memberCount = 0;
        } else if (container.children.length === 0) {
            // Add first member field for group applications
            addMemberField();
        }
    });

    function addMemberField() {
        if (memberCount >= 5) {
            alert('Maximum 5 members allowed');
            return;
        }

        const template = `
            <div class="group-member border p-3 mb-3">
                <div class="form-group">
                    <label>Member Name</label>
                    <input type="text" name="group_members[${memberCount}][name]" class="form-control" placeholder="Full Name" ${memberCount === 0 ? 'required' : ''}>
                </div>
                <div class="form-group mt-2">
                    <label>National ID</label>
                    <input type="text" name="group_members[${memberCount}][national_id]" class="form-control" placeholder="National ID" ${memberCount === 0 ? 'required' : ''}>
                </div>
                ${memberCount > 0 ? '<button type="button" class="btn btn-danger btn-sm mt-2 remove-member">Remove</button>' : ''}
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', template);
        memberCount++;
    }

    // Add Member button handler
    addMemberBtn.addEventListener('click', addMemberField);

    // Remove member handler
    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-member')) {
            e.target.closest('.group-member').remove();
            memberCount--;
        }
    });

    // Form submission handler
    document.getElementById('harvesterLicenseForm').addEventListener('submit', function(event) {
        // Validate applicant selection
        const selected = applicantSelect.options[applicantSelect.selectedIndex];
        const isGroup = selected.dataset.isGroup === '1';
        
        if (isGroup) {
            const members = document.querySelectorAll('.group-member');
            if (members.length === 0) {
                event.preventDefault();
                alert('Please add at least one group member');
                return;
            }

            // Validate member fields
            let isValid = true;
            members.forEach((member, index) => {
                const nameInput = member.querySelector('input[name$="[name]"]');
                const idInput = member.querySelector('input[name$="[national_id]"]');
                
                if (!nameInput.value.trim() || !idInput.value.trim()) {
                    isValid = false;
                }
            });

            if (!isValid) {
                event.preventDefault();
                alert('Please fill in all member details');
                return;
            }
        }

        // Validate species selection
        const selectedSpecies = Array.from(speciesSelect.selectedOptions);
        if (selectedSpecies.length === 0) {
            event.preventDefault();
            alert('Please select at least one species');
            return;
        }
    });
});
</script>
@endpush
