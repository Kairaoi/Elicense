@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit License</h1>

        <form action="{{ route('license.licenses.update', $license->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="applicant_id">Applicant</label>
                <select name="applicant_id" id="applicant_id" class="form-control">
                    @foreach ($applicants as $id => $name)
                        <option value="{{ $id }}" {{ $license->applicant_id == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                @error('applicant_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="license_type_id">License Type</label>
                <select name="license_type_id" id="license_type_id" class="form-control">
                    @foreach ($licenseTypes as $id => $name)
                        <option value="{{ $id }}" {{ $license->license_type_id == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                @error('license_type_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            

            <h4>Quotas</h4>
            @foreach ($speciesByLicenseType as $licenseTypeId => $species)
                <div class="form-group">
                    <label>{{ $licenseTypes[$licenseTypeId] }}</label>

                    @foreach ($species as $sp)
                        <div class="form-row">
                            <div class="col-md-6">
                                <label for="quotas[{{ $sp->id }}]">{{ $sp->name }}</label>
                                @foreach ($islands as $islandId => $islandName)
                                    <div class="form-group">
                                        <label for="quotas[{{ $sp->id }}][{{ $islandId }}]">
                                            {{ $islandName }} (Available Quota: 
                                            {{ $availableQuotas[$sp->id][$islandId] ?? 0 }})
                                        </label>
                                        <input type="number" name="quotas[{{ $sp->id }}][{{ $islandId }}]" 
                                               class="form-control" value="{{ old('quotas')[$sp->id][$islandId] ?? 0 }}">
                                        @error("quotas.{$sp->id}.{$islandId}")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
@endsection
