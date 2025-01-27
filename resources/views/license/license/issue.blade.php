@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Issue License</h2>
    <form action="{{ route('license.licenses.issue', ['license' => $license->id]) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="issue_date">Issue Date</label>
            <input type="date" name="issue_date" id="issue_date" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="expiry_date">Expiry Date</label>
            <input type="date" name="expiry_date" id="expiry_date" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Issue License</button>
    </form>
</div>
@endsection