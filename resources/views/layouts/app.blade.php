<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Coastal Fisheries Online Licensing System') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
     <!-- Add these CSS and JS files for phone input -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

    <style>
        /* Flexbox to keep footer at the bottom */
        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
            
        }
        .d-flex-wrapper {
            display: flex;
            flex-direction: column;
            flex: 1;
        }
        .flex-grow-content {
            flex: 1;
        }
    </style>

    <!-- Custom Styles -->
    <style>
        /* Navbar Brand Styles */
        .navbar-brand {
            font-family: 'Roboto', sans-serif;
            font-weight: bold;
            font-size: 1.75rem;
            color: #ffffff;
            background: linear-gradient(45deg, #1a73e8, #ff5722);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 3px 3px 6px rgba(0,0,0,0.5);
            position: relative;
            overflow: hidden;
            padding: 0.5rem;
            display: flex;
            align-items: center;
        }

        .navbar-brand::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: #ff5722;
            transform: scaleX(0);
            transform-origin: 0 0;
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover::after {
            transform: scaleX(1);
        }

        .navbar-brand img {
            width: 2rem;
            height: auto;
            margin-right: 0.5rem;
        }

        /* Dropdown Menu Styles */
        .dropdown-menu {
            padding: 0.5rem 0;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .dropdown-item {
            padding: 0.75rem 1.5rem;
            font-size: 0.9rem;
            transition: background-color 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #1a73e8;
        }

        .dropdown-item:active {
            background-color: #e9ecef;
        }

        .dropdown-item:not(:last-child) {
            border-bottom: 1px solid #eee;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            text-align: center;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .modal h2 {
            color: #4CAF50;
            margin-bottom: 15px;
        }

        .modal p {
            margin-bottom: 20px;
        }

        .modal button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .modal button:hover {
            background-color: #45a049;
        }
        main {
    padding-top: 80px !important; /* Add extra space for fixed navbar */
}

footer {
    position: relative;
    z-index: 1000;
}

footer * {
    position: relative;
    z-index: 1001;
}

.footer-content {
    background-color: rgba(33, 37, 41, 1); /* Te color ae ti te arona ma bg-dark */
}
    </style>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm fixed-top">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            {{ config('app.name', 'E-License') }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto"></ul>
            <ul class="navbar-nav ms-auto">
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @endif
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                @else
                <li class="nav-item dropdown">
    <a id="navbarSettings" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Settings
    </a>
    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarSettings">
        {{-- System Administration --}}
        @if(Auth::user()->hasRole('sys.admin'))
            <a class="dropdown-item" href="{{ route('admin.dashboard') }}">User Management</a>
            <a class="dropdown-item" href="{{ route('reference.boards.index') }}">Reference</a>
            <a class="dropdown-item" href="{{ route('admin.login-logs') }}">User login logs</a>  
        @endif

        {{-- License Administration --}}
        @if(Auth::user()->hasRole('lic.admin'))
            <a class="dropdown-item" href="{{ route('applicant.boards.index') }}">Applications</a>
            <a class="dropdown-item" href="{{ route('license.boards.index') }}">License</a>
            <a class="dropdown-item" href="{{ route('export.boards.index') }}">Consignment</a>
            
            <a class="dropdown-item" href="{{ route('harvester.boards.index') }}">Harvester</a>
            <a class="dropdown-item" href="{{ route('species-island-quotas.quota.index') }}">Island Quota</a>
            <a class="dropdown-item" href="{{ route('pfps.boards.index') }}">Please Fishing Permt</a>
            <a class="dropdown-item" href="{{ route('license.board.index') }}">Managing Agent Activities</a>
             
            <a class="dropdown-item" href="{{ route('reports.index') }}">Report</a>
        @endif

        {{-- License User --}}
        @if(Auth::user()->hasRole('lic.user'))
            <a class="dropdown-item" href="{{ route('applicant.boards.index') }}">Applications</a>
            <a class="dropdown-item" href="{{ route('license.boards.index') }}">License</a>
            <a class="dropdown-item" href="{{ route('export.boards.index') }}">Consignment</a>
        @endif

        {{-- View All Access --}}
        @if(Auth::user()->hasRole('lic.view.all'))
            <a class="dropdown-item" href="{{ route('applicant.boards.index') }}">Applications</a>
            <a class="dropdown-item" href="{{ route('license.boards.index') }}">License</a>
            <a class="dropdown-item" href="{{ route('export.boards.index') }}">Consignment</a>
            
            <a class="dropdown-item" href="{{ route('harvester.boards.index') }}">Harvester</a>
            
        @endif 

        {{-- Viewer Access --}}
        @if(Auth::user()->hasRole('lic.viewer'))
            <a class="dropdown-item" href="{{ route('applicant.boards.index') }}">Applications</a>
            <a class="dropdown-item" href="{{ route('license.boards.index') }}">License</a>
            <a class="dropdown-item" href="{{ route('export.boards.index') }}">Consignment</a>
        @endif

        @if(Auth::user()->hasRole('applicant'))
            <a class="dropdown-item" href="{{ route('license.licenses.index') }}">My Licenses</a>
        @endif

    </div>
</li>
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

{{--0@auth
            @include('partials.header')
        @endauth--}}

        <main class="py-4 flex-grow-content">
            @yield('content')
        </main>
        @yield('styles')
    </div>

 
        @include('partials.footer')
   
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
    @stack('scripts')

    @if(session('showThankYouModal'))
    <div id="thankYouModal" class="modal">
        <div class="modal-content">
            <h2>Thank You!</h2>
            <p>Your application has been submitted successfully.</p>
            <button onclick="closeModal()">Close</button>
        </div>
    </div>
    @if(session('showDuplicateApplicantModal'))
    <script>
        window.onload = function() {
            var duplicateModal = new bootstrap.Modal(document.getElementById('duplicateApplicantModal'));
            duplicateModal.show();
        };
    </script>
@endif

    <script>
        window.onload = function() {
            document.getElementById('thankYouModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('thankYouModal').style.display = 'none';
        }
    </script>
    @endif
     <!-- Duplicate Applicant Modal -->
     @if(session('showDuplicateApplicantModal'))
<div id="duplicateApplicantModal" class="modal">
    <div class="modal-content">
        <h2>Duplicate Application</h2>
        <p>Thank you! Your name and email are already registered in the system.</p>
        <button onclick="closeDuplicateApplicantModal()">Close</button>
    </div>
</div>
<script>
    window.onload = function() {
    document.getElementById('duplicateApplicantModal').style.display = 'block';
};

function closeDuplicateApplicantModal() {
    document.getElementById('duplicateApplicantModal').style.display = 'none';
}

    }
</script>
@endif

@if(session('xml_download'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const xmlContent = atob("{{ session('xml_download') }}");
        const blob = new Blob([xmlContent], {type: 'application/xml'});
        const link = document.createElement('a');
        link.href = window.URL.createObjectURL(blob);
        link.download = 'applicant_credentials.xml';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
</script>
@endif

</body>
</html>