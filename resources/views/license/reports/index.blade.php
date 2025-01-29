@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card glass-effect shadow-lg border-0">
                <div class="card-header gradient-bg text-white text-center py-4">
                    <h3 class="mb-0"><i class="fas fa-chart-line me-2"></i> Company Reports</h3>
                </div>
                <div class="card-body p-5">
                    {{-- Report Group Dropdown --}}
                    <div class="row justify-content-center mb-5">
                        <div class="col-md-8 col-lg-6">
                            <select id="reportSelector" class="form-select form-select-lg elegant-dropdown">
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
                                <div class="card shadow-sm border-0 rounded-4 hover-lift">
                                    <div class="card-header soft-bg text-center py-3">
                                        <h5 class="mb-0">{{ $report->name }}</h5>
                                    </div>
                                    <div class="card-body p-4 text-center">
                                        <p class="text-muted mb-4">{{ $report->description }}</p>
                                        
                                        @php
                                            $parameters = is_array($report->parameters) ? $report->parameters : [];
                                        @endphp
                                        
                                        @if(!empty($parameters))
                                            <form action="{{ route('reports.run', $report->id) }}" method="GET">
                                                @csrf
                                                <div class="row justify-content-center">
                                                    @foreach($parameters as $param)
                                                        <div class="col-md-8 mb-3">
                                                            <label class="form-label">{{ ucfirst(str_replace('_', ' ', $param)) }}</label>
                                                            @if(is_string($param) && str_contains(strtolower($param), 'date'))
                                                                <input type="date" class="form-control input-rounded" name="{{ $param }}" required>
                                                            @else
                                                                <input type="text" class="form-control input-rounded" name="{{ $param }}" required>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="text-center mt-4">
                                                    <button type="submit" class="btn btn-gradient btn-lg px-5">
                                                        <i class="fas fa-play me-2"></i>Run Report
                                                    </button>
                                                </div>
                                            </form>
                                        @else
                                            <div class="text-center">
                                                <a href="{{ route('reports.run', $report->id) }}" class="btn btn-gradient btn-lg px-5">
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
    /* Stunning Glassmorphism Effect */
    .glass-effect {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border-radius: 20px;
    }

    /* Gradient Header */
    .gradient-bg {
        background: linear-gradient(135deg, #007bff, #6610f2);
        border-radius: 20px 20px 0 0;
    }

    /* Soft Background */
    .soft-bg {
        background: rgba(0, 123, 255, 0.1);
        border-radius: 15px 15px 0 0;
    }

    /* Elegant Dropdown */
    .elegant-dropdown {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 12px;
        transition: 0.3s ease-in-out;
    }
    .elegant-dropdown:hover {
        background: #e9ecef;
    }

    /* Smooth Buttons */
    .btn-gradient {
        background: linear-gradient(to right, #007bff, #6610f2);
        border: none;
        border-radius: 12px;
        padding: 12px 30px;
        color: #fff;
        font-weight: bold;
        transition: 0.3s ease-in-out;
    }
    .btn-gradient:hover {
        background: linear-gradient(to right, #6610f2, #007bff);
        transform: scale(1.05);
    }

    /* Input Fields */
    .input-rounded {
        border-radius: 12px;
        padding: 12px;
    }

    /* Hover Lift Effect */
    .hover-lift {
        transition: all 0.3s ease-in-out;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const reportSelector = document.getElementById('reportSelector');
    const reportCards = document.querySelectorAll('.report-card');

    reportCards.forEach(card => card.style.display = 'none');

    reportSelector.addEventListener('change', function() {
        reportCards.forEach(card => card.style.display = 'none');

        const selectedReport = document.getElementById(this.value);
        if (selectedReport) {
            selectedReport.style.display = 'block';
            selectedReport.style.opacity = 0;
            setTimeout(() => {
                selectedReport.style.transition = 'opacity 0.5s ease-in-out';
                selectedReport.style.opacity = 1;
            }, 10);
        }
    });
});
</script>
@endpush
@endsection
