@extends('layouts.app')

@section('title', 'Export Details')

@push('styles')
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        line-height: 1.5;
        background-color: #f5f8fa;
        color: #333;
    }

    .container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 30px;
    }

    /* Added more top margin to push content down */
    .main-content {
        margin-top: 150px;
    }

    .export-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        margin-bottom: 30px;
    }

    .export-header {
        position: relative;
    }

    .company-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 25px 30px; /* Increased padding */
        border-bottom: 1px solid #eaeaea;
    }

    .company-logo {
        height: 60px;
        width: auto;
    }

    .company-info {
        text-align: right;
    }

    .company-name {
        font-size: 1.4rem;
        font-weight: 700;
        color: #333;
        margin: 0;
    }

    .company-details {
        color: #666;
        font-size: 0.9rem;
        margin: 8px 0 0 0; /* Increased top margin */
    }

    .export-title-section {
        background-color: #0056b3;
        background-image: linear-gradient(135deg, #0056b3, #007bff);
        color: white;
        padding: 30px; /* Increased padding */
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .export-title-left {
        flex: 1;
    }

    .export-title {
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 0 0 8px 0; /* Increased bottom margin */
        opacity: 0.9;
    }

    .destination {
        font-size: 1.8rem;
        font-weight: 600;
        margin: 0;
        line-height: 1.3; /* Added line height for better spacing */
    }

    .export-title-right {
        text-align: right;
    }

    .export-reference {
        font-size: 0.85rem;
        opacity: 0.9;
        margin-bottom: 8px; /* Increased margin */
    }

    .export-date {
        font-size: 1.1rem;
        font-weight: 500;
    }

    .export-body {
        padding: 35px; /* Increased padding */
    }

    .section-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #0056b3;
        margin: 0 0 25px 0; /* Increased bottom margin */
        padding-bottom: 12px; /* Increased padding */
        border-bottom: 2px solid #eaeaea;
    }

    .species-list {
        margin-bottom: 35px; /* Increased margin */
    }

    .species-header {
        display: flex;
        justify-content: space-between;
        font-weight: 600;
        color: #555;
        padding-bottom: 12px; /* Increased padding */
        border-bottom: 2px solid #eaeaea;
        font-size: 0.9rem;
        text-transform: uppercase;
    }

    .species-header-item {
        flex: 1;
    }

    .species-header-item:first-child {
        flex: 0 0 40px;
    }

    .species-header-item:last-child {
        text-align: right;
        flex: 0 0 100px;
    }

    .species-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 0; /* Increased padding */
        border-bottom: 1px solid #eaeaea;
    }

    .species-item:last-child {
        border-bottom: none;
    }

    .species-item-number {
        flex: 0 0 40px;
        font-weight: 500;
        color: #666;
    }

    .species-item-name {
        flex: 1;
        font-weight: 500;
    }

    .species-item-weight {
        flex: 0 0 100px;
        text-align: right;
        color: #555;
        font-weight: 500;
    }

    .underline {
        border-bottom: 1px solid #aaa;
        padding-bottom: 3px;
        display: inline-block;
        min-width: 150px;
    }

    .empty-row .species-item-name {
        height: 24px;
    }

    .totals {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 25px 30px; /* Increased padding */
        margin-top: 25px; /* Increased margin */
        border-left: 4px solid #007bff;
    }

    .totals p {
        display: flex;
        justify-content: space-between;
        font-size: 1.1rem;
        margin: 12px 0; /* Increased margin */
        align-items: center;
    }

    .totals p strong {
        font-weight: 600;
        color: #444;
    }

    .totals .value {
        font-weight: 600;
        color: #0056b3;
    }

    .totals .total-cost {
        font-size: 1.3rem;
        margin-top: 20px; /* Increased margin */
        padding-top: 20px; /* Increased padding */
        border-top: 1px solid #ddd;
    }

    .totals .total-cost .value {
        color: #0056b3;
        font-weight: 700;
    }

    .footer {
        text-align: center;
        margin-top: 25px; /* Increased margin */
        font-size: 0.85rem;
        color: #777;
        padding: 15px 0; /* Added padding */
    }

    @media print {
        body {
            background-color: white;
        }
        
        .export-card {
            box-shadow: none;
            border: 1px solid #ddd;
        }
        
        .container {
            padding: 0;
        }
        
        /* Keep content visible when printing */
        .main-content {
            margin-top: 20px;
        }
    }

    @media (max-width: 768px) {
        .container {
            padding: 15px;
        }
        
        /* Adjusted for mobile */
        .main-content {
            margin-top: 100px;
        }
        
        .company-section {
            padding: 20px;
            flex-direction: column;
            align-items: flex-start;
        }
        
        .company-info {
            text-align: left;
            margin-top: 15px;
        }
        
        .export-title-section {
            padding: 25px;
            flex-direction: column;
            align-items: flex-start;
        }
        
        .export-title-right {
            text-align: left;
            margin-top: 20px;
        }
        
        .destination {
            font-size: 1.4rem;
        }
        
        .export-body {
            padding: 25px;
        }
        
        .species-item {
            font-size: 0.9rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="main-content">
        <div class="export-card">
            <div class="export-header">
                <!-- Company Information Section -->
                <div class="company-section">
                    <div class="company-logo-container">
                        <!-- If you have a logo, uncomment this line -->
                        <!-- <img src="{{ asset('images/logo.png') }}" alt="Company Logo" class="company-logo"> -->
                        <h2 class="company-name">Fisheries Department</h2>
                    </div>
                    <div class="company-info">
                        <h3 class="company-name">{{ $declaration->applicant->company_name ?? 'Company Name' }}</h3>
                        <p class="company-details">License #: {{ $declaration->license->license_number ?? 'N/A' }}</p>
                    </div>
                </div>
                
                <!-- Export Destination Section -->
                <div class="export-title-section">
                    <div class="export-title-left">
                        <h3 class="export-title">Export Destination</h3>
                        <h2 class="destination">{{ $declaration->export_destination }}</h2>
                    </div>
                    <div class="export-title-right">
                        <p class="export-reference">Reference: EXP-{{ $declaration->id }}</p>
                        <p class="export-date">{{ $declaration->shipment_date->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="export-body">
                <h4 class="section-title">Export Species</h4>
                
                <div class="species-list">
                    <div class="species-header">
                        <div class="species-header-item">#</div>
                        <div class="species-header-item">Species</div>
                        <div class="species-header-item">Weight</div>
                    </div>

                    @foreach($declaration->species as $index => $exportSpecies)
                        <div class="species-item">
                            <div class="species-item-number">{{ $index + 1 }}.</div>
                            <div class="species-item-name">{{ optional($exportSpecies->species)->name ?? 'N/A' }}</div>
                            <div class="species-item-weight">{{ number_format($exportSpecies->volume_kg, 2) }} kg</div>
                        </div>
                    @endforeach

                    @for($i = count($declaration->species); $i < 5; $i++)
                        <div class="species-item empty-row">
                            <div class="species-item-number">{{ $i + 1 }}.</div>
                            <div class="species-item-name underline">&nbsp;</div>
                            <div class="species-item-weight">&nbsp;</div>
                        </div>
                    @endfor
                </div>

                <div class="totals">
                    <p>
                        <strong>Total weight:</strong>
                        <span class="value">{{ number_format($declaration->species->sum('volume_kg'), 2) }} kg</span>
                    </p>
                    <p class="total-cost">
                        <strong>Total Cost:</strong>
                        <span class="value">${{ number_format($declaration->total_license_fee, 2) }}</span>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="footer">
            This is an official export declaration document. Please retain for your records.
        </div>
    </div>
</div>
@endsection