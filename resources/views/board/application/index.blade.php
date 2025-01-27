@extends('layouts.app')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Poppins:wght@300;400;600&family=Raleway:wght@400;700&display=swap');
    /* Global font and background settings */
    body {
        font-family: 'Poppins', sans-serif;
        color: #333;
    }

    /* Central container styling */
    .main-panel {
        background-color: #ffffff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 40px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Header styles */
    .main-panel h1.display-4 {
        font-size: 2.2rem;
        color: #007bff;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .main-panel p.lead {
        color: #6c757d;
        font-size: 1rem;
        margin-bottom: 30px;
    }

    /* Navigation pills styling */
    .nav-pills .nav-link {
        color: #007bff;
        background-color: #f8f9fa;
        border-radius: 50px;
        padding: 8px 18px;
        font-weight: 500;
        transition: all 0.3s;
    }

    .nav-pills .nav-link.active {
        background-color: #007bff;
        color: #fff;
    }

    /* Card styling */
    .card {
        background: #f9f9f9;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
        margin-top: 20px;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
    }

    .card-title {
        font-size: 1.4rem;
        color: #007bff;
    }

    .card-text {
        color: #6c757d;
        font-size: 1rem;
    }

    /* Button styling */
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        border-radius: 30px;
        padding: 8px 20px;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }

    /* Icon styling */
    .bi {
        font-size: 1.1rem;
        margin-right: 6px;
        vertical-align: middle;
    }
</style>
@endsection

@section('content')
<div class="container my-5">
    <!-- Header -->
    <div class="text-center mb-5">
        <h1 class="display-4">Applicant Registry</h1>
        <p class="lead">Manage your  licenses and track important compliance and quota details.</p>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills mb-4 justify-content-center">
        @foreach ([ 
            ['id' => 'applicants', 'title' => 'Applicants', 'icon' => 'bi bi-person'],
            
        ] as $tab)
            <li class="nav-item">
                <a class="nav-link {{ $loop->first ? 'active' : '' }}" 
                   id="{{ $tab['id'] }}-tab" 
                   data-bs-toggle="tab" 
                   href="#{{ $tab['id'] }}" 
                   role="tab" 
                   aria-controls="{{ $tab['id'] }}">
                    <i class="{{ $tab['icon'] }}"></i> {{ $tab['title'] }}
                </a>
            </li>
        @endforeach
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content">
        @foreach ([ 
            ['id' => 'applicants', 'title' => 'Applicants', 'description' => 'View and manage sea cucumber export license applicants.', 'route' => 'applicant.applicants.index'],
          
        ] as $tab)
            <div class="tab-pane fade @if($loop->first) show active @endif" id="{{ $tab['id'] }}" role="tabpanel">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <h3 class="card-title mb-3">{{ $tab['title'] }}</h3>
                        <p class="card-text">{{ $tab['description'] }}</p>
                        <a href="{{ route($tab['route']) }}" class="btn btn-primary">Go to {{ $tab['title'] }}</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
