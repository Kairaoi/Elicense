@extends('layouts.app')

@section('content')
<div class="container mt-5" style="margin-top: 100px;"> <!-- Added inline margin-top for more spacing -->
    <h1 class="elegant-heading">Add New Harvester Applicant</h1>

    <form action="{{ route('harvester.applicants.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" name="first_name" id="first_name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" name="last_name" id="last_name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="text" name="phone_number" id="phone_number" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="is_group">Is Group?</label>
            <select name="is_group" id="is_group" class="form-control" required>
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>

        <div class="form-group">
            <label for="group_size">Group Size (if applicable)</label>
            <input type="number" name="group_size" id="group_size" class="form-control" min="1" value="1" placeholder="Enter group size">
        </div>

        <div class="form-group">
            <label for="national_id">National ID</label>
            <input type="text" name="national_id" id="national_id" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Create Applicant</button>
    </form>
</div>
@endsection
