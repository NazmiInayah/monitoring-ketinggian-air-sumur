<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Monitoring Ketinggian Air Sumur') }}</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link rel="icon" type="image/png" href="{{ asset('monairsu/public/img/logo-kpspams.png') }}">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRkIbggVph5FhAB6AA6MOGBHRm5y0uC1knBGeox7D" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <style>
            body {
                background-color: rgb(58,124,165);
            }
            .container {
                background-color: rgb(58,124,165);
            }
            .card {
                background-color: white;
            }
            .navbar {
                background-color: #6395ec; /* Navbar background color */
            }
            .navbar-nav .nav-link {
                color: white; /* Default text color */
            }
            .navbar-nav .nav-link:hover {
                color: #e0e0e0; /* Text color on hover */
                background-color: #4a77d1; /* Background color on hover */
                border-radius: 4px; /* Optional: rounded corners */
            }
            .navbar-nav .nav-link.active {
                color: #fff; /* Text color for active link */
                background-color: #4a77d1; /* Background color for active link */
            }
        </style>
    </head>
    
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/">PAM Sagara</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/about">Tentang Alat</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/dashboard">Monitoring</a>
                </li>
                @if (Auth::check())
                <li class="nav-item">
                    <a class="nav-link" href="/history">Riwayat</a>
                </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link" href="/contact">Layanan</a>
                </li>
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                @endguest
                @auth
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="GET" style="display: none;">
                            @csrf
                        </form>
                    </li>
                @endauth
            </ul>
        </div>
    </nav>

    <div class="container" style="text-align:center; margin-top:10px;">
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
