@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="elegant-heading">Create New Island</h1>
        <a href="{{ route('reference.islands.index') }}" class="btn btn-secondary elegant-back-btn">Back to List</a>
    </div>

    <form action="{{ route('reference.islands.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">Island Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Create Island</button>
        </div>
    </form>
</div>
@endsection
