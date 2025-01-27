@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="//cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<style>
    /* Add spacing to push content down */
    .content-wrapper {
        min-height: 100vh;
        padding: 20px 0 100px 0; /* Add padding bottom */
    }
    
    /* Card styling */
    .card {
        margin-bottom: 30px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    /* Table container spacing */
    .table-responsive {
        margin-top: 20px;
        margin-bottom: 20px;
    }
    
    /* Add some dummy space */
    .spacer {
        height: 50px;
    }
    
    /* Table styling */
    #login-logs-table {
        margin-bottom: 30px;
    }
    
    /* Footer spacing */
    .footer-spacer {
        height: 100px;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="container">
        <!-- Title Section -->
        <div class="row mb-4">
            <div class="col-12">
                <h2>System Administration</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Login Logs</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Login Logs</h3>
            </div>
            <div class="card-body">
                <!-- Summary Section -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5>Total Logins</h5>
                                <p class="h3">{{ $totalLogins ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5>Successful Logins</h5>
                                <p class="h3">{{ $successfulLogins ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5>Failed Attempts</h5>
                                <p class="h3">{{ $failedLogins ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="login-logs-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>IP Address</th>
                                <th>Login Time</th>
                                <th>Logout Time</th>
                                <th>Status</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <!-- Additional Information Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Login Activity Information</h5>
                        <p>This section displays all user login activities including successful and failed attempts.</p>
                        <p>Use this information to monitor system access and identify potential security issues.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add space at bottom -->
        <div class="footer-spacer"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="//cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#login-logs-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.login-logs.datatables') }}",
        columns: [
            {data: 'user_name', name: 'user_name'},
            {data: 'ip_address', name: 'ip_address'},
            {data: 'login_at', name: 'login_at'},
            {data: 'logout_at', name: 'logout_at'},
            {data: 'status', name: 'status'},
            {data: 'notes', name: 'notes'}
        ],
        pageLength: 10, // Show 10 entries per page
        order: [[2, 'desc']], // Sort by login time by default
    });
});
</script>
@endpush