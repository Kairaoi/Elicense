@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    /* Font Import */
    @import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Roboto:wght@300;400;500;700&display=swap');

    /* General Styles */
    body {
        font-family: 'Roboto', sans-serif;
        background: linear-gradient(135deg, #eef2f3 0%, #dfe4e7 100%);
        margin: 0;
        padding: 0;
        color: #333;
        overflow-x: hidden;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    /* Header Section */
    .header {
        background: linear-gradient(145deg, #0d3c61, #144f77);
        color: #fff;
        text-align: center;
        padding: 50px 20px;
        border-radius: 15px;
        margin-bottom: 40px;
        position: relative;
    }

    .header:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('/images/pattern-bg.svg') no-repeat center/cover;
        opacity: 0.1;
        z-index: 1;
    }

    .header .logo {
        width: 100px;
        height: auto;
        margin-bottom: 20px;
        filter: drop-shadow(0 5px 10px rgba(0, 0, 0, 0.3));
    }

    .header h1 {
        font-family: 'DM Serif Display', serif;
        font-size: 2.5rem;
        z-index: 2;
        position: relative;
        margin-bottom: 15px;
    }

    .header .subtitle {
        font-size: 1.2rem;
        font-weight: 300;
        color: #e0e8ed;
        z-index: 2;
        position: relative;
    }

    /* Button Styles */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 25px;
        font-weight: 500;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        background: #ffd700;
        color: #0d3c61;
        text-decoration: none;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
        gap: 10px;
    }

    .btn:hover {
        background: transparent;
        color: #ffd700;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.25);
        transform: translateY(-2px);
    }

    /* Section Styling */
    .section {
        background: #fff;
        margin-bottom: 40px;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }

    .section:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 6px;
        height: 100%;
        background: linear-gradient(to bottom, #ffd700, #ff7f00);
    }

    .section h2 {
        font-family: 'DM Serif Display', serif;
        font-size: 1.8rem;
        margin-bottom: 15px;
        color: #144f77;
    }

    .section p {
        color: #555;
        line-height: 1.6;
        font-size: 1rem;
        margin-bottom: 20px;
    }

    .section .btn-container {
        text-align: right;
    }

    .icon {
        font-size: 2.5rem;
        color: #144f77;
        margin-bottom: 10px;
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        .header h1 {
            font-size: 2rem;
        }

        .header .subtitle {
            font-size: 1rem;
        }

        .section h2 {
            font-size: 1.5rem;
        }

        .btn-container {
            text-align: center;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <!-- Header Section -->
    <div class="header">
    <img src="http://localhost/fisherylicense/public/images/logos.png" alt="Official Seal" class="logo">
    <h1>Online Licensing System</h1>
    <p class="subtitle">Coastal Fisheries Division | Ministry of Fisheries and Ocean Resources</p>
</div>

    <!-- Section 1 -->
    <div class="section">
        <i class="fas fa-file-signature icon"></i>
        <h2>License Application Process</h2>
        <p>Streamline your fishing license applications with our efficient and user-friendly system, ensuring compliance with national regulations and sustainability standards.</p>
        <div class="btn-container">
            <a href="{{ route('license.applicants.create') }}" class="btn">
                <i class="fas fa-arrow-right"></i> Start Application
            </a>
        </div>
    </div>

    <!-- Section 2 -->
    <div class="section">
        <i class="fas fa-headset icon"></i>
        <h2>Support Services</h2>
        <p>Our support team is available to guide you through the licensing process, addressing documentation requirements, regulatory queries, and technical assistance.</p>
        <div class="btn-container">
            <a href="#contact" class="btn">
                <i class="fas fa-envelope"></i> Contact Us
            </a>
        </div>
    </div>
</div>
@endsection