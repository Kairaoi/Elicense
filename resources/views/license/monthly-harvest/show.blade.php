@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Monthly Harvest Details</h5>
            <a href="{{ route('license.monthly-harvests.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th width="200">License Item</th>
                    <td>{{ $monthlyHarvest->licenseItem->species->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Applicant</th>
                    <td>{{ $monthlyHarvest->applicant->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Island</th>
                    <td>{{ $monthlyHarvest->island->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Year</th>
                    <td>{{ $monthlyHarvest->year }}</td>
                </tr>
                <tr>
                    <th>Month</th>
                    <td>{{ date('F', mktime(0, 0, 0, $monthlyHarvest->month, 1)) }}</td>
                </tr>
                <tr>
                    <th>Quantity monthlyHarvested</th>
                    <td>{{ number_format($monthlyHarvest->quantity_monthlyHarvested, 2) }} kg</td>
                </tr>
                <tr>
                    <th>Remaining Quota</th>
                    <td>{{ $monthlyHarvest->remaining_quota ? number_format($monthlyHarvest->remaining_quota, 2) . ' kg' : 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Notes</th>
                    <td>{{ $monthlyHarvest->notes ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td>{{ $monthlyHarvest->created_at->format('d/m/Y H:i:s') }}</td>
                </tr>
                <tr>
                    <th>Updated At</th>
                    <td>{{ $monthlyHarvest->updated_at->format('d/m/Y H:i:s') }}</td>
                </tr>
                <tr>
                    <th>Created By</th>
                    <td>{{ $monthlyHarvest->creator->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Updated By</th>
                    <td>{{ $monthlyHarvest->updater->name ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection