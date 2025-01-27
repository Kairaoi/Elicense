@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Activity Log for {{ $applicant->first_name }} {{ $applicant->last_name }}</h4>
                </div>
                <div class="card-body">
                    @if($activities->isEmpty())
                        <div class="alert alert-info text-center">
                            No activity logs found for this applicant.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col">Date/Time</th>
                                        <th scope="col">Action</th>
                                        <th scope="col">User</th>
                                        <th scope="col">Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activities as $activity)
                                    <tr>
                                        <td>{{ $activity->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td>{{ ucfirst($activity->description) }}</td>
                                        <td>
                                            @if($activity->causer)
                                                {{ $activity->causer->name }}
                                            @else
                                                <span class="text-muted">System</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($activity->properties->has('old') && $activity->properties->has('new'))
                                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#changes-{{ $activity->id }}">
                                                    View Changes
                                                </button>

                                                <!-- Modal -->
                                                <div class="modal fade" id="changes-{{ $activity->id }}" tabindex="-1" aria-labelledby="changesLabel-{{ $activity->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="changesLabel-{{ $activity->id }}">Changes</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h6 class="fw-bold">Old Values:</h6>
                                                                <pre class="bg-light p-3 rounded">{{ json_encode($activity->properties['old'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                                                <h6 class="fw-bold">New Values:</h6>
                                                                <pre class="bg-light p-3 rounded">{{ json_encode($activity->properties['new'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">No details available</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
