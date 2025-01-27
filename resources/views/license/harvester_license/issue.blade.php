@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Issue License</h4>
                </div>
                
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('harvester.licenses.issue', $license) }}" method="POST">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label for="issue_date">Issue Date</label>
                            <input type="date" 
                                name="issue_date" 
                                id="issue_date" 
                                class="form-control @error('issue_date') is-invalid @enderror"
                                value="{{ old('issue_date', date('Y-m-d')) }}"
                                required>
                            @error('issue_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="expiry_date">Expiry Date</label>
                            <input type="date" 
                                name="expiry_date" 
                                id="expiry_date" 
                                class="form-control @error('expiry_date') is-invalid @enderror"
                                value="{{ old('expiry_date', date('Y-m-d', strtotime('+1 year'))) }}"
                                required>
                            @error('expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Display License Details -->
                        <div class="mb-3">
                            <h5>License Details</h5>
                            <p><strong>Applicant:</strong> {{ $license->harvesterApplicant->name ?? 'N/A' }}</p>
                            <p><strong>Island:</strong> {{ $license->island->name ?? 'N/A' }}</p>
                            <p><strong>License Type:</strong> {{ $license->licenseType->name ?? 'N/A' }}</p>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Issue License</button>
                            <a href="{{ route('harvester.licenses.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set minimum date for issue_date to today
        const issueDate = document.getElementById('issue_date');
        const expiryDate = document.getElementById('expiry_date');
        
        const today = new Date().toISOString().split('T')[0];
        issueDate.setAttribute('min', today);
        
        // Update expiry date min value when issue date changes
        issueDate.addEventListener('change', function() {
            expiryDate.setAttribute('min', this.value);
        });
    });
</script>
@endpush
@endsection