@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit License</h1>

    <form action="{{ route('license.licenses.update', $license->id) }}" method="POST">
        @csrf
        @method('PUT')

        <input type="hidden" name="applicant_id" value="{{ $applicant->id }}">

        <div class="mb-3">
            <label class="form-label">Applicant</label>
            <p>{{ $applicant->first_name }} {{ $applicant->last_name }} ({{ $applicant->email }})</p>
        </div>

        <div class="mb-3">
            <label for="license_type_id" class="form-label">License Type</label>
            <select name="license_type_id" id="license_type_id" class="form-select" required>
                <option value="">Select License Type</option>
                @foreach ($licenseTypes as $id => $licenseType)
                    <option value="{{ $id }}" {{ $license->license_type_id == $id ? 'selected' : '' }}>
                        {{ $licenseType }}
                    </option>
                @endforeach
            </select>
            @error('license_type_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <h5>Species Information</h5>
        <div id="species-container">
            @foreach ($license->licenseItems as $index => $item)
                <div class="mb-3 species-entry">
                    <label for="species_{{ $index }}" class="form-label">Species</label>
                    <select name="species[{{ $index }}][id]" class="form-select species-select" required>
                        <option value="">Select Species</option>
                        @foreach ($speciesList as $id => $species)
                            <option value="{{ $id }}" {{ $item->species_id == $id ? 'selected' : '' }}>
                                {{ $species }}
                            </option>
                        @endforeach
                    </select>

                    <label for="requested_quota_{{ $index }}" class="form-label mt-2">Requested Quota</label>
                    <input type="number" name="species[{{ $index }}][requested_quota]" class="form-control" min="0" step="0.01" value="{{ $item->requested_quota }}" required>
                    <button type="button" class="btn btn-danger mt-2 remove-species">Remove</button>
                </div>
            @endforeach
        </div>

        <button type="button" class="btn btn-primary mt-3" id="add-species">Add Another Species</button>

        <button type="submit" class="btn btn-success mt-4">Update License</button>
    </form>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let speciesIndex = {{ count($license->licenseItems) }};

        document.getElementById('add-species').addEventListener('click', function() {
            const speciesContainer = document.getElementById('species-container');
            const newSpeciesEntry = document.createElement('div');
            newSpeciesEntry.className = 'mb-3 species-entry';
            newSpeciesEntry.innerHTML = `
                <label for="species_${speciesIndex}" class="form-label">Species</label>
                <select name="species[${speciesIndex}][id]" class="form-select species-select" required>
                    <option value="">Select Species</option>
                    @foreach($speciesList as $id => $species)
                        <option value="{{ $id }}">{{ $species }}</option>
                    @endforeach
                </select>
                <label for="requested_quota_${speciesIndex}" class="form-label mt-2">Requested Quota</label>
                <input type="number" name="species[${speciesIndex}][requested_quota]" class="form-control" min="0" step="0.01" required>
                <button type="button" class="btn btn-danger mt-2 remove-species">Remove</button>
            `;
            speciesContainer.appendChild(newSpeciesEntry);
            speciesIndex++;
        });

        document.getElementById('species-container').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-species')) {
                e.target.closest('.species-entry').remove();
            }
        });
    });
</script>
@endpush
