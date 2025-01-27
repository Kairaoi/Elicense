@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Permission Details</h1>
    
    <div class="mb-3">
        <strong>Name:</strong> {{ $permission->name }}
    </div>

    <a href="{{ route('admin.permissions.edit', $permission->id) }}" class="btn btn-warning">Edit Permission</a>
    <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">Back to Permissions</a>
</div>
@endsection
