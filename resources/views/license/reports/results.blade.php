@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Chart Card -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Graph View</h4>
            <div class="btn-group">
                <button type="button" class="btn btn-light btn-sm" onclick="changeChartType('column')">Column</button>
                <button type="button" class="btn btn-light btn-sm" onclick="changeChartType('line')">Line</button>
                <button type="button" class="btn btn-light btn-sm" onclick="changeChartType('bar')">Bar</button>
                <button type="button" class="btn btn-light btn-sm" onclick="changeChartType('pie')">Pie</button>
                <button type="button" class="btn btn-light btn-sm" onclick="changeChartType('donut')">Donut</button>
            </div>
        </div>
        <div class="card-body">
            <div id="chartContainer" style="min-height: 400px;"></div>
        </div>
    </div>

    <!-- Table Card (Your existing code) -->
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-table me-2"></i>Report Results</h4>
            <a href="{{ route('reports.index') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left me-2"></i>Back to Reports
            </a>
        </div>
        <div class="card-body">
            @if(!empty($results))
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                @if(count($results) > 0)
                                    @foreach((array)$results[0] as $key => $value)
                                        <th>{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                                    @endforeach
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $row)
                                <tr>
                                    @foreach((array)$row as $value)
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>Akea results aika a reke.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        border-radius: 0.5rem;
    }
    
    .card-header {
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
    }
    
    .table th {
        font-weight: 600;
    }
    
    .btn {
        border-radius: 0.25rem;
    }
    
    .form-control {
        border-radius: 0.25rem;
    }
    
    .alert {
        border-radius: 0.25rem;
    }
</style>
@endpush

@push('scripts')
<!-- Add Highcharts library -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prepare data for the chart
    const categories = [];
    const seriesData = [];
    
    @if(!empty($results))
        @foreach($results as $row)
            categories.push("{{ array_values((array)$row)[0] }}");
            seriesData.push({{ array_values((array)$row)[1] ?? 0 }});
        @endforeach
    @endif

    // Initialize the chart
    Highcharts.chart('chartContainer', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Report Results'
        },
        xAxis: {
            categories: categories,
            title: {
                text: '{{ !empty($results) ? array_keys((array)$results[0])[0] : "" }}'
            }
        },
        yAxis: {
            title: {
                text: '{{ !empty($results) ? array_keys((array)$results[0])[1] : "Value" }}'
            }
        },
        series: [{
            name: 'Values',
            data: seriesData
        }],
        credits: {
            enabled: false
        },
        plotOptions: {
            column: {
                borderRadius: 5
            }
        },
        colors: ['#0d6efd']
    });
});

// Function to change chart type
function changeChartType(type) {
    const chart = Highcharts.charts[0];
    chart.update({
        chart: {
            type: type
        }
    });
}
</script>
@endpush