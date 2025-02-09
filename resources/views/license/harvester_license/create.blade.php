@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h1>Create Harvester License</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('harvester.licenses.store') }}" method="POST" id="harvesterLicenseForm">
                @csrf

                <div class="row">
                    <!-- Applicant Selection -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="harvester_applicant_id">Harvester Applicant</label>
                            <select name="harvester_applicant_id" id="harvester_applicant_id" class="form-control" required>
                                <option value="">Select Applicant</option>
                                @foreach($applicants as $applicant)
                                    <option value="{{ $applicant->id }}" data-is-group="{{ $applicant->is_group ? '1' : '0' }}">
                                        {{ $applicant->name }} ({{ $applicant->is_group ? 'Group' : 'Individual' }})
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
                                    <option value="{{ $id }}">{{ $island }}</option>
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
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('license_type_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Species Selection (CheckBoxes) -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Species</label>
                            <div id="speciesContainer">
                                <!-- Checkboxes will be added dynamically here -->
                            </div>
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
                            <input type="number" name="fee" id="fee" class="form-control" step="0.01" readonly>
                            <small class="text-muted">Fee is automatically calculated based on applicant type</small>
                            @error('fee')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Payment Receipt -->
                <div class="form-group mt-3">
                    <label for="payment_receipt_no">Payment Receipt No.</label>
                    <input type="text" name="payment_receipt_no" id="payment_receipt_no" class="form-control" required>
                    @error('payment_receipt_no')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Group Members Section -->
                <div id="groupMembersSection" style="display: none;" class="mt-4">
                    <h3>Group Members</h3>
                    <small class="text-muted">Maximum 5 members allowed</small>

                    <div id="groupMembersContainer"></div>

                    <button type="button" class="btn btn-secondary mb-3" id="addMemberBtn">
                        Add Member
                    </button>
                </div>

                <!-- Form Buttons -->
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Create License</button>
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
    const speciesContainer = document.getElementById('speciesContainer');
    let memberCount = 0;

    // Store species data by license type
    const speciesByLicenseType = @json($speciesByLicenseType);

    // Handle license type change
    licenseTypeSelect.addEventListener('change', function() {
        const licenseTypeId = this.value;
        speciesContainer.innerHTML = '';

        if (licenseTypeId && speciesByLicenseType[licenseTypeId]) {
            speciesByLicenseType[licenseTypeId].forEach(function(species) {
                const checkbox = document.createElement('div');
                checkbox.classList.add('form-check');
                checkbox.innerHTML = `
                    <input class="form-check-input" type="checkbox" name="species[]" value="${species.id}" id="species_${species.id}">
                    <label class="form-check-label" for="species_${species.id}">${species.name}</label>
                `;
                speciesContainer.appendChild(checkbox);
            });
        }
    });

    // Handle applicant selection change
    applicantSelect.addEventListener('change', function() {
        const selected = applicantSelect.options[applicantSelect.selectedIndex];
        const isGroup = selected.dataset.isGroup === '1';

        feeInput.value = isGroup ? '500.00' : '25.00';
        groupSection.style.display = isGroup ? 'block' : 'none';

        if (!isGroup) {
            container.innerHTML = '';
            memberCount = 0;
        }
    });

    // Add Group Member
    function addMemberField() {
        if (memberCount >= 5) {
            alert('Maximum 5 members allowed');
            return;
        }

        const memberDiv = document.createElement('div');
        memberDiv.classList.add('group-member', 'border', 'p-3', 'mb-3');
        memberDiv.innerHTML = `
            <div class="form-group">
                <label>Member Name</label>
                <input type="text" name="group_members[${memberCount}][name]" class="form-control" required>
            </div>
            <div class="form-group mt-2">
                <label>National ID</label>
                <input type="text" name="group_members[${memberCount}][national_id]" class="form-control" required>
            </div>
            <button type="button" class="btn btn-danger btn-sm mt-2 remove-member">Remove</button>
        `;

        container.appendChild(memberDiv);
        memberCount++;
    }

    addMemberBtn.addEventListener('click', addMemberField);

    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-member')) {
            e.target.closest('.group-member').remove();
            memberCount--;
        }
    });
});
</script>
@endpush
