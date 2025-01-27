@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Activity Type</h1>

    <form action="{{ route('pfps.activity-types.update', $activityType->activity_type_id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="category_id">Category</label>
            <select name="category_id" id="category_id" class="form-control" required>
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->category_id }}" 
                        {{ old('category_id', $activityType->category_id) == $category->category_id ? 'selected' : '' }}>
                        {{ $category->category_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="activity_name">Activity Name</label>
            <input type="text" name="activity_name" id="activity_name" class="form-control" 
                   value="{{ old('activity_name', $activityType->activity_name) }}" required>
        </div>

        <div class="form-group">
            <label for="requirements">Requirements</label>
            <textarea name="requirements" id="requirements" class="form-control">{{ old('requirements', $activityType->requirements) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update Activity Type</button>
        <a href="{{ route('pfps.activity-types.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
