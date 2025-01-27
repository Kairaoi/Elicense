@extends('layouts.app')

@section('content')
<div class="container mt-5"> <!-- Added mt-5 for top margin -->
    <h1>Create Export Declaration</h1>

    <form action="{{ route('export.declarations.store') }}" method="POST">
        @csrf
        
        {{-- Export Declaration Fields --}}
        <div class="card mb-4">
            <div class="card-header">
                Export Declaration Details
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="applicant_id">Applicant</label>
                    <select name="applicant_id" id="applicant_id" class="form-control @error('applicant_id') is-invalid @enderror">
                        <option value="">Select Applicant</option>
                        @foreach($applicants as $id => $applicant)
                            <option value="{{ $id }}" {{ old('applicant_id') == $id ? 'selected' : '' }}>
                                {{ $applicant }} 
                            </option>
                        @endforeach
                    </select>
                    @error('applicant_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="shipment_date">Shipment Date</label>
                    <input type="date" name="shipment_date" id="shipment_date" class="form-control @error('shipment_date') is-invalid @enderror" value="{{ old('shipment_date') }}">
                    @error('shipment_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="export_destination">Export Destination</label>
                    <input type="text" name="export_destination" id="export_destination" class="form-control @error('export_destination') is-invalid @enderror" value="{{ old('export_destination') }}" placeholder="Enter export destination">
                    @error('export_destination')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Species Fields -->
        <div id="species-container">
            <div class="species-row row mb-3">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="species_id_1">Species</label>
                        <select name="species[1][species_id]" id="species_id_1" class="form-control species-select">
                            <option value="">Select Species</option>
                            @foreach($speciesList as $id => $speciesInfo)
                                @php
                                    preg_match('/Unit Price: (\d+\.\d+)/', $speciesInfo, $matches);
                                    $unitPrice = $matches[1] ?? '0.00';
                                @endphp
                                <option value="{{ $id }}" data-unit-price="{{ $unitPrice }}">{{ $speciesInfo }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="volume_kg_1">Volume (kg)</label>
                        <input type="number" name="species[1][volume_kg]" id="volume_kg_1" class="form-control" step="0.01" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="under_size_volume_kg_1">Under-size Volume (kg)</label>
                        <input type="number" name="species[1][under_size_volume_kg]" id="under_size_volume_kg_1" class="form-control" step="0.01" value="0" />
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-species">X</button>
                </div>
                <input type="hidden" name="species[1][unit_price]" id="unit_price_1" />
            </div>
        </div>

        <!-- Add Species Button -->
        <button type="button" id="add-species-btn" class="btn btn-primary mt-2">Add More Species</button>

        <button type="submit" class="btn btn-success mt-4">Submit</button>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let speciesCount = 1;
    let applicantSpecies = @json($speciesList);

    $(document).ready(function() {
        $('#applicant_id').change(function() {
            let applicantId = $(this).val();
            if(applicantId) {
                $.ajax({
                    url: '{{ route("export.get-species-for-applicant") }}',
                    type: 'GET',
                    data: { applicant_id: applicantId },
                    success: function(data) {
                        applicantSpecies = data;
                        $('#species-container').empty();
                        speciesCount = 0;
                        addSpeciesRow();
                    }
                });
            } else {
                $('#species-container').empty();
                speciesCount = 0;
            }
        });

        $('#add-species-btn').click(addSpeciesRow);
        addSpeciesSelectListener();

        // Event delegation for remove button
        $('#species-container').on('click', '.remove-species', function() {
            if ($('.species-row').length > 1) {
                $(this).closest('.species-row').remove();
            } else {
                alert('You must have at least one species.');
            }
        });
    });

    function addSpeciesRow() {
        speciesCount++;
        let row = `
            <div class="species-row row mb-3">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="species_id_${speciesCount}">Species</label>
                        <select name="species[${speciesCount}][species_id]" id="species_id_${speciesCount}" class="form-control species-select">
                            <option value="">Select Species</option>
                            ${Object.entries(applicantSpecies).map(([id, info]) => `
                                <option value="${id}" data-unit-price="${extractUnitPrice(info)}">${info}</option>
                            `).join('')}
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="volume_kg_${speciesCount}">Volume (kg)</label>
                        <input type="number" name="species[${speciesCount}][volume_kg]" id="volume_kg_${speciesCount}" class="form-control" step="0.01" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="under_size_volume_kg_${speciesCount}">Under-size Volume (kg)</label>
                        <input type="number" name="species[${speciesCount}][under_size_volume_kg]" id="under_size_volume_kg_${speciesCount}" class="form-control" step="0.01" value="0" />
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-species">X</button>
                </div>
                <input type="hidden" name="species[${speciesCount}][unit_price]" id="unit_price_${speciesCount}" />
            </div>`;

        $('#species-container').append(row);
        addSpeciesSelectListener();
    }

    function addSpeciesSelectListener() {
        $('.species-select').off('change').on('change', function() {
            let unitPrice = $(this).find(':selected').data('unit-price');
            let rowIndex = this.id.split('_').pop();
            $(`#unit_price_${rowIndex}`).val(unitPrice);
        });
    }

    function extractUnitPrice(info) {
        let match = info.match(/Unit Price: (\d+(\.\d+)?)/);
        return match ? match[1] : '0.00';
    }
</script>
@endpush
