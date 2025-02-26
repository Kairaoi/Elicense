@extends('layouts.app')

@section('title', 'Export Details')

@push('styles')
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; /* Modern font */
        margin: 20px;
        line-height: 1.5;
        background-color: #f9f9f9; /* Light background for contrast */
    }

    .container {
        max-width: 1200px; /* Increased max-width to allow more space */
        margin: 0 auto;
        padding: 0 30px; /* Increased padding to give more space around content */
    }

    .destination {
        background-color: #007bff; /* Blue background for destination */
        color: white;
        font-size: 1.5rem;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-weight: bold;
        text-align: center;
    }

    .species-list {
        background-color: #ffffff; /* White background for species list */
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
        margin-bottom: 20px;
    }

    .species-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #e0e0e0; /* Light gray border for each species */
    }

    .species-item:last-child {
        border-bottom: none; /* Remove border from last item */
    }

    .underline {
        border-bottom: 1px solid #000;
        padding-bottom: 2px;
        display: inline-block;
        min-width: 150px; /* Adjust to ensure it doesn't look cramped */
    }

    .totals {
        background-color: #f8f9fa; /* Light background for totals section */
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
    }

    .totals p {
        font-size: 1.1rem;
        margin: 10px 0;
        font-weight: bold;
    }

    .totals .underline {
        font-weight: normal; /* Make the totals' underline less bold */
    }

    /* Responsive design for smaller screens */
    @media (max-width: 768px) {
        .destination {
            font-size: 1.2rem; /* Slightly smaller font for mobile */
        }

        .species-item {
            font-size: 0.9rem; /* Reduce font size for species list */
        }
    }
</style>
@endpush

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="destination">
        {{ $declaration->export_destination }}
    </div>

    <div class="species-list">
        @foreach($declaration->species as $index => $species)
            <div class="species-item">
                <span>{{ $index + 1 }}. {{ $species->species->common_name }}</span>
                <span class="underline">{{ number_format($species->volume_kg, 2) }}kg</span>
            </div>
        @endforeach
        @for($i = count($declaration->species); $i < 5; $i++)
            <div class="species-item">
                <span>{{ $i + 1 }}.</span>
                <span class="underline">&nbsp;</span>
            </div>
        @endfor
    </div>

    <div class="totals">
        <p>Total weight: <span class="underline">{{ number_format($declaration->species->sum('volume_kg'), 2) }} kg</span></p>
        <p>Total Cost: <span class="underline">${{ number_format($declaration->total_license_fee, 2) }}</span></p>
    </div>
</div>
@endsection
