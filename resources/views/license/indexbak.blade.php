@extends('layouts.app')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Poppins:wght@300;400;600&family=Raleway:wght@400;700&display=swap');

    body {
        font-family: 'Poppins', sans-serif;
        background: radial-gradient(circle, #00416A, #E4E5E6);
        color: #333;
        overflow-x: hidden;
    }

    .container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 60px 30px;
        position: relative;
        z-index: 1;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .text-center {
        margin-bottom: 30px;
    }

    h1.display-4 {
        font-family: 'Orbitron', sans-serif;
        font-size: 3rem;
        color: #007bff;
        font-weight: 700;
    }

    .lead {
        font-size: 1.25rem;
        color: #6c757d;
    }

    .nav-pills .nav-link {
        border-radius: 50px;
        padding: 12px 20px;
        margin: 5px;
        color: #007bff;
        background: #e9ecef;
        border: 1px solid #ced4da;
        transition: background 0.3s, color 0.3s;
    }

    .nav-pills .nav-link:hover, .nav-pills .nav-link.active {
        background: #007bff;
        color: #ffffff;
    }

    .card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        transition: box-shadow 0.3s;
    }

    .card:hover {
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .card-body {
        padding: 20px;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        border-radius: 30px;
        padding: 12px 24px;
        font-weight: 600;
        transition: background-color 0.3s, border-color 0.3s;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }

    .bi {
        font-size: 1.5rem;
        margin-right: 8px;
    }
</style>
@endsection

@section('content')
<div class="container my-5">
    <!-- Header -->
    <div class="text-center mb-5">
        <h1 class="display-4">Coastal License Applications</h1>
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
            ['id' => 'applicants', 'title' => 'Applicants', 'description' => 'View and manage sea cucumber export license applicants.', 'route' => 'license.applicants.index'],
          
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
