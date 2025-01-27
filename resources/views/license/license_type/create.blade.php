@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create License Type</h1>

    <form action="{{ route('reference.licenses_types.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Create License Type</button>
        <a href="{{ route('reference.licenses_types.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
