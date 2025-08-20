<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PTBA - Plan de Travail Annuel')</title>

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .main-content {
            min-height: 100vh;
        }

        .card-stats {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .table-responsive {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .badge-reforme {
            font-size: 0.75rem;
            margin-right: 3px;
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3">
                    <h4 class="text-white">🇨🇮 PTBA 2023</h4>
                    <p class="text-light small">{{ auth()->user()->structure->nom_structure }}</p>
                </div>

                <nav class="nav flex-column">
                    <a class="nav-link text-white {{ request()->routeIs('dashboard') ? 'active bg-white bg-opacity-25' : '' }}"
                        href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
                    </a>

                    <a class="nav-link text-white {{ request()->routeIs('activites.*') ? 'active bg-white bg-opacity-25' : '' }}"
                        href="{{ route('activites.index') }}">
                        <i class="fas fa-tasks me-2"></i> Activités
                    </a>

                    <a class="nav-link text-white {{ request()->routeIs('realisations.*') ? 'active bg-white bg-opacity-25' : '' }}"
                        href="{{ route('realisations.index') }}">
                        <i class="fas fa-chart-line me-2"></i> Réalisations
                    </a>

                    <a class="nav-link text-white {{ request()->routeIs('rapports.*') ? 'active bg-white bg-opacity-25' : '' }}"
                        href="{{ route('rapports.index') }}">
                        <i class="fas fa-file-alt me-2"></i> Rapports
                    </a>

                    @if(auth()->user()->isAdmin())
                    <a class="nav-link text-white" href="#">
                        <i class="fas fa-cog me-2"></i> Administration
                    </a>
                    @endif
                </nav>

                <div class="mt-auto p-3">
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle w-100" type="button"
                            data-bs-toggle="dropdown">
                            {{ auth()->user()->nom_complet }}
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">
                                    <i class="fas fa-user me-2"></i> Profil
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Header -->
                <header class="bg-white border-bottom p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1 class="h3 mb-0">@yield('page-title', 'Tableau de bord')</h1>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary me-2">{{ date('Y') }}</span>
                            <span class="text-muted">{{ now()->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </header>

                <!-- Flash Messages -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <!-- Content -->
                <main class="p-3">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Configuration globale
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Initialiser Select2
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: 'Sélectionner...'
            });
        });
    </script>

    @stack('scripts')
</body>

</html>