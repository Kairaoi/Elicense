@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Species Island Quota</h2>

    <form action="{{ route('species-island-quotas.quota.update', $speciesIslandQuota->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="species_id">Species</label>
            <select class="form-control" name="species_id" id="species_id" required>
                <option value="">Select Species</option>
                @foreach($species as $id => $speciesItem)
                    <option value="{{ $id }}" {{ $speciesIslandQuota->species_id == $id ? 'selected' : '' }}>
                        {{ $speciesItem }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="island_id">Island</label>
            <select class="form-control" name="island_id" id="island_id" required>
                <option value="">Select Island</option>
                @foreach($islands as $id => $island)
                    <option value="{{ $id }}" {{ $speciesIslandQuota->island_id == $id ? 'selected' : '' }}>
                        {{ $island }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="island_quota">Island Quota</label>
            <input type="number" class="form-control" name="island_quota" id="island_quota" value="{{ $speciesIslandQuota->island_quota }}" required min="0">
        </div>

        <div class="form-group">
            <label for="remaining_quota">Remaining Quota</label>
            <input type="number" class="form-control" name="remaining_quota" id="remaining_quota" value="{{ $speciesIslandQuota->remaining_quota }}" required min="0">
        </div>

        <div class="form-group">
            <label for="year">Year</label>
            <input type="number" class="form-control" name="year" id="year" value="{{ $speciesIslandQuota->year }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
