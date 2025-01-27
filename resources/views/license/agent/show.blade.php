@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Agent Details</h1>
    <table class="table table-bordered">
        <tr>
            <th>First Name</th>
            <td>{{ $agent->first_name }}</td>
        </tr>
        <tr>
            <th>Last Name</th>
            <td>{{ $agent->last_name }}</td>
        </tr>
        <tr>
            <th>Phone Number</th>
            <td>{{ $agent->phone_number }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $agent->email }}</td>
        </tr>
        <tr>
            <th>Applicant</th>
            <td>{{ $agent->applicant->name }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ ucfirst($agent->status) }}</td>
        </tr>
        <tr>
            <th>Start Date</th>
            <td>{{ $agent->start_date }}</td>
        </tr>
        <tr>
            <th>End Date</th>
            <td>{{ $agent->end_date ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Notes</th>
            <td>{{ $agent->notes ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Created By</th>
            <td>{{ $agent->creator->name }}</td>
        </tr>
        <tr>
            <th>Updated By</th>
            <td>{{ $agent->updater->name ?? 'N/A' }}</td>
        </tr>
    </table>

    <a href="{{ route('license.agents.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection
