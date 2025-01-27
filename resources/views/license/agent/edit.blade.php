@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Agent</h1>
    <form action="{{ route('license.agents.update', $agent->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" name="first_name" id="first_name" class="form-control" 
                   value="{{ old('first_name', $agent->first_name) }}" required>
        </div>

        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" name="last_name" id="last_name" class="form-control" 
                   value="{{ old('last_name', $agent->last_name) }}" required>
        </div>

        <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="text" name="phone_number" id="phone_number" class="form-control" 
                   value="{{ old('phone_number', $agent->phone_number) }}" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" 
                   value="{{ old('email', $agent->email) }}" required>
        </div>

        <div class="form-group">
            <label for="applicant_id">Applicant</label>
            <select name="applicant_id" id="applicant_id" class="form-control" required>
                @foreach($applicants as $id => $name)
                    <option value="{{ $id }}" {{ old('applicant_id', $agent->applicant_id) == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="active" {{ old('status', $agent->status) === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $agent->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div class="form-group">
            <label for="start_date">Start Date</label>
            <input type="date" name="start_date" id="start_date" class="form-control" 
                   value="{{ old('start_date', $agent->start_date) }}" required>
        </div>

        <div class="form-group">
            <label for="end_date">End Date</label>
            <input type="date" name="end_date" id="end_date" class="form-control" 
                   value="{{ old('end_date', $agent->end_date) }}">
        </div>

        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea name="notes" id="notes" class="form-control">{{ old('notes', $agent->notes) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
