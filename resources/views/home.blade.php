@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    /* Font Import */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap');

    /* Reset & General Styles */
    *, *::before, *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: #f8fafc;
        color: #1a1a1a;
        line-height: 1.6;
        overflow-x: hidden;
    }

    .container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 2rem;
    }

    /* Header Section */
    .header {
        background: linear-gradient(135deg, #003366 0%, #004d99 100%);
        position: relative;
        padding: 4rem 2rem;
        border-radius: 20px;
        margin-bottom: 3rem;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 51, 102, 0.15);
    }

    .header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
        z-index: 1;
    }

    .header-content {
        position: relative;
        z-index: 2;
        text-align: center;
    }

    .logo {
        width: 120px;
        height: auto;
        margin-bottom: 1.5rem;
        filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        transition: transform 0.3s ease;
    }

    .logo:hover {
        transform: scale(1.05);
    }

    .header h1 {
        font-family: 'Playfair Display', serif;
        font-size: 3rem;
        color: #ffffff;
        margin-bottom: 1rem;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .subtitle {
        font-size: 1.25rem;
        color: rgba(255, 255, 255, 0.9);
        font-weight: 300;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Section Styling */
    .section {
        background: #ffffff;
        border-radius: 16px;
        padding: 2.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid rgba(0, 51, 102, 0.1);
        position: relative;
        overflow: hidden;
    }

    .section:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }

    .section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(to bottom, #ffd700, #ff9900);
        border-radius: 4px 0 0 4px;
    }

    .icon {
        font-size: 2.5rem;
        background: linear-gradient(135deg, #ffd700, #ff9900);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 1rem;
    }

    .section h2 {
        font-family: 'Playfair Display', serif;
        font-size: 2rem;
        color: #003366;
        margin-bottom: 1rem;
    }

    .section p {
        color: #4a5568;
        margin-bottom: 1.5rem;
        font-size: 1.1rem;
        line-height: 1.8;
    }

    /* Button Styles */
    .btn-container {
        text-align: right;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.875rem 1.75rem;
        font-weight: 500;
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #003366;
        background: linear-gradient(135deg, #ffd700, #ff9900);
        border-radius: 12px;
        text-decoration: none;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border: none;
        cursor: pointer;
    }

    .btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #ff9900, #ffd700);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 153, 0, 0.3);
    }

    .btn:hover::before {
        opacity: 1;
    }

    .btn i {
        position: relative;
        z-index: 1;
        transition: transform 0.3s ease;
    }

    .btn:hover i {
        transform: translateX(4px);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .container {
            padding: 1rem;
        }

        .header {
            padding: 3rem 1.5rem;
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 2.25rem;
        }

        .subtitle {
            font-size: 1.1rem;
        }

        .section {
            padding: 2rem;
        }

        .section h2 {
            font-size: 1.75rem;
        }

        .btn-container {
            text-align: center;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }

    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .header, .section {
        animation: fadeIn 0.6s ease-out forwards;
    }

    .section:nth-child(2) {
        animation-delay: 0.2s;
    }

    .section:nth-child(3) {
        animation-delay: 0.4s;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="header">
        <div class="header-content">
            <img src="http://localhost/fisherylicense/public/images/logos.png" alt="Official Seal" class="logo">
            <h1>Online Licensing System</h1>
            <p class="subtitle">Coastal Fisheries Division | Ministry of Fisheries and Ocean Resources</p>
        </div>
    </div>

    <div class="section">
        <i class="fas fa-file-signature icon"></i>
        <h2>License Application Process</h2>
        <p>Streamline your fishing license applications with our efficient and user-friendly system, ensuring compliance with national regulations and sustainability standards.</p>
        <div class="btn-container">
            <a href="{{ route('license.applicants.create') }}" class="btn">
                Start Application <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="section">
        <i class="fas fa-headset icon"></i>
        <h2>Support Services</h2>
        <p>Our support team is available to guide you through the licensing process, addressing documentation requirements, regulatory queries, and technical assistance.</p>
        <div class="btn-container">
            <a href="#contact" class="btn">
                Contact Us <i class="fas fa-envelope"></i>
            </a>
        </div>
    </div>
</div>
@endsection