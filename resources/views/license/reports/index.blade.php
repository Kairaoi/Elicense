@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10"> <!-- Increased width for better balance -->
            <div class="card shadow">
                <div class="card-header bg-primary text-white py-3">
                    <h4 class="mb-0 text-center"><i class="fas fa-chart-bar me-2"></i>Reports</h4>
                </div>
                <div class="card-body p-4">
                    {{-- Report Group Dropdown --}}
                    <div class="row justify-content-center mb-4">
                        <div class="col-md-8 col-lg-6"> <!-- Centered dropdown -->
                            <select id="reportSelector" class="form-select form-select-lg">
                                <option value="">Select a Report</option>
                                @foreach($reports as $report)
                                    <option value="report-{{ $report->id }}">{{ $report->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Report Cards --}}
                    <div class="row justify-content-center">
                        @foreach($reports as $report)
                            <div class="col-12 col-lg-8 mb-4 report-card" id="report-{{ $report->id }}" style="display: none;">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-header bg-light py-3">
                                        <h5 class="mb-0 text-center">{{ $report->name }}</h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <p class="text-muted text-center mb-4">{{ $report->description }}</p>
                                        
                                        @php
                                            $parameters = is_array($report->parameters) ? $report->parameters : [];
                                        @endphp
                                        
                                        @if(!empty($parameters))
                                            <form action="{{ route('reports.run', $report->id) }}" method="GET" class="mt-3">
                                                @csrf
                                                <div class="row justify-content-center">
                                                    @foreach($parameters as $param)
                                                        <div class="col-md-8 mb-3">
                                                            <label class="form-label">{{ ucfirst(str_replace('_', ' ', $param)) }}</label>
                                                            @if(is_string($param) && str_contains(strtolower($param), 'date'))
                                                                <input type="date" class="form-control" name="{{ $param }}" required>
                                                            @else
                                                                <input type="text" class="form-control" name="{{ $param }}" required>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="text-center mt-4">
                                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                                        <i class="fas fa-play me-2"></i>Run Report
                                                    </button>
                                                </div>
                                            </form>
                                        @else
                                            <div class="text-center">
                                                <a href="{{ route('reports.run', $report->id) }}" class="btn btn-primary btn-lg px-5">
                                                    <i class="fas fa-play me-2"></i>Run Report
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 15px;
    }
    .card-header {
        border-radius: 15px 15px 0 0 !important;
    }
    .form-select {
        border-radius: 10px;
        padding: 12px;
    }
    .btn {
        border-radius: 10px;
        padding: 12px 30px;
    }
    .form-control {
        border-radius: 10px;
        padding: 12px;
    }
    .shadow {
        box-shadow: 0 0 30px rgba(0,0,0,0.1) !important;
    }
    .shadow-sm {
        box-shadow: 0 5px 15px rgba(0,0,0,0.05) !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const reportSelector = document.getElementById('reportSelector');
    const reportCards = document.querySelectorAll('.report-card');

    // Hide all report cards initially
    reportCards.forEach(card => card.style.display = 'none');

    reportSelector.addEventListener('change', function() {
        // Hide all report cards
        reportCards.forEach(card => card.style.display = 'none');

        // Show selected report card with fade effect
        const selectedReport = document.getElementById(this.value);
        if (selectedReport) {
            selectedReport.style.display = 'block';
            selectedReport.style.opacity = 0;
            setTimeout(() => {
                selectedReport.style.transition = 'opacity 0.3s ease-in-out';
                selectedReport.style.opacity = 1;
            }, 10);
        }
    });
});
</script>
@endpush
@endsection