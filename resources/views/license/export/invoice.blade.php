@push('styles')
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        line-height: 1.4;
    }

    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 15px;
    }

    .destination {
        margin-bottom: 10px;
        padding: 8px;
        background-color: #ebf4ff;
        border-left: 4px solid #2c5282;
    }

    .species-list {
        margin: 20px 0;
    }

    .species-item {
        margin: 10px 0;
        padding: 5px 0;
    }

    .underline {
        border-bottom: 1px solid #000;
        padding-bottom: 2px;
        min-width: 200px;
        display: inline-block;
    }

    .totals {
        margin-top: 20px;
    }

    .totals p {
        margin: 10px 0;
    }
</style>
@endpush

@extends('layouts.app')

@section('title', 'Export Details')

@section('content')
    <div class="destination">
        {{ $declaration->export_destination }}
    </div>

    <div class="species-list">
        @foreach($declaration->species as $index => $species)
            <div class="species-item">
                {{ $index + 1 }}. <span class="underline">{{ $species->species->common_name }} - {{ number_format($species->volume_kg, 2) }}kg</span>
            </div>
        @endforeach
        @for($i = count($declaration->species); $i < 5; $i++)
            <div class="species-item">
                {{ $i + 1 }}. <span class="underline">&nbsp;</span>
            </div>
        @endfor
    </div>

    <div class="totals">
        <p>Total weight: <span class="underline">{{ number_format($declaration->species->sum('volume_kg'), 2) }} kg</span></p>
        <p>Total Cost: <span class="underline">${{ number_format($declaration->total_license_fee, 2) }}</span></p>
    </div>
@endsection