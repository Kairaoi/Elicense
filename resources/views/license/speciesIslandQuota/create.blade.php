@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create Species Island Quota</h2>

    <form action="{{ route('species-island-quotas.quota.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="species_id">Species</label>
            <select class="form-control" name="species_id" id="species_id" required>
                <option value="">Select Species</option>
                @foreach($species as $id => $speciesItem)
                    <option value="{{ $id }}">{{ $speciesItem }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="island_id">Island</label>
            <select class="form-control" name="island_id" id="island_id" required>
                <option value="">Select Island</option>
                @foreach($islands as $id => $island)
                    <option value="{{ $id }}">{{ $island }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="island_quota">Island Quota</label>
            <input type="number" class="form-control" name="island_quota" id="island_quota" required min="0">
        </div>

        

        <div class="form-group">
            <label for="year">Year</label>
            <input type="number" class="form-control" name="year" id="year" required>
        </div>

        <button type="submit" class="btn btn-primary">Create</button>
    </form>
</div>
@endsection
