@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Activity Type</h1>

    <form action="{{ route('pfps.activity-types.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="category_id">Category</label>
            <select name="category_id" id="category_id" class="form-control" required>
                <option value="">Select Category</option>
                @foreach($categories as $id => $category)
                    <option value="{{ $id }}">{{ $category }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="activity_name">Activity Name</label>
            <input type="text" name="activity_name" id="activity_name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="requirements">Requirements</label>
            <textarea name="requirements" id="requirements" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Create Activity Type</button>
        <a href="{{ route('pfps.activity-types.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
