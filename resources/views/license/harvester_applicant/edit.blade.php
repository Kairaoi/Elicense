@extends('layouts.app')

@section('content')
<div class="container mt-5" style="margin-top: 100px;"> <!-- Added inline margin-top for more spacing -->
    <h1 class="elegant-heading">Edit Harvester Applicant</h1>

    <form action="{{ route('harvester.applicants.update', $applicant->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name', $applicant->first_name) }}" required>
        </div>

        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name', $applicant->last_name) }}" required>
        </div>

        <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="text" name="phone_number" id="phone_number" class="form-control" value="{{ old('phone_number', $applicant->phone_number) }}" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $applicant->email) }}" required>
        </div>

        <div class="form-group">
            <label for="is_group">Is Group?</label>
            <select name="is_group" id="is_group" class="form-control" required>
                <option value="0" {{ $applicant->is_group == 0 ? 'selected' : '' }}>No</option>
                <option value="1" {{ $applicant->is_group == 1 ? 'selected' : '' }}>Yes</option>
            </select>
        </div>

        <div class="form-group">
            <label for="group_size">Group Size (if applicable)</label>
            <input type="number" name="group_size" id="group_size" class="form-control" min="1" value="{{ old('group_size', $applicant->group_size) }}" placeholder="Enter group size">
        </div>

        <div class="form-group">
            <label for="national_id">National ID</label>
            <input type="text" name="national_id" id="national_id" class="form-control" value="{{ old('national_id', $applicant->national_id) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Applicant</button>
    </form>
</div>
@endsection
