@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Role Details</h1>
    
    <div class="mb-3">
        <strong>Name:</strong> {{ $role->name }}
    </div>

    <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-warning">Edit Role</a>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Back to Roles</a>
</div>
@endsection
