@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit License Type</h1>

    <form action="{{ route('reference.licenses_types.update', $licenseType->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $licenseType->name) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update License Type</button>
        <a href="{{ route('reference.licenses_types.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
