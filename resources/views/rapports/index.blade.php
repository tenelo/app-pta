{{-- resources/views/rapports/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Rapports PTBA')
@section('page-title', 'Rapports et Analyses')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="text-center mb-5">
            <h2 class="text-primary">Centre de Rapports PTBA</h2>
            <p class="text-muted lead">Générez et consultez vos rapports d'activités et de performances</p>
        </div>

        <div class="row">
            <!-- Rapport de synthèse annuelle -->
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="fas fa-file-contract fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">Synthèse Annuelle</h5>
                        <p class="card-text">
                            Rapport complet des activités, budgets et réalisations pour une année donnée.
                        </p>
                        <a href="{{ route('rapports.synthese') }}" class="btn btn-primary">
                            <i class="fas fa-chart-bar me-2"></i>Générer le rapport
                        </a>
                    </div>
                </div>
            </div>

            <!-- Rapport trimestriel -->
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="fas fa-calendar-alt fa-3x text-success"></i>
                        </div>
                        <h5 class="card-title">Rapport Trimestriel</h5>
                        <p class="card-text">
                            Analyse détaillée des réalisations et performances par trimestre.
                        </p>
                        <a href="{{ route('rapports.trimestriel') }}" class="btn btn-success">
                            <i class="fas fa-chart-line me-2"></i>Consulter
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tableau de bord analytique -->
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="fas fa-tachometer-alt fa-3x text-info"></i>
                        </div>
                        <h5 class="card-title">Tableau de Bord Analytique</h5>
                        <p class="card-text">
                            Indicateurs clés de performance et visualisations interactives.
                        </p>
                        <a href="{{ route('rapports.tableau-bord') }}" class="btn btn-info">
                            <i class="fas fa-analytics me-2"></i>Accéder
                        </a>
                    </div>
                </div>
            </div>

            <!-- Export et données -->
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="fas fa-download fa-3x text-warning"></i>
                        </div>
                        <h5 class="card-title">Exports et Données</h5>
                        <p class="card-text">
                            Téléchargez vos données en format Excel, PDF ou CSV.
                        </p>
                        <div class="dropdown">
                            <button class="btn btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-file-export me-2"></i>Exporter
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('rapports.synthese', ['format' => 'pdf']) }}">
                                        <i class="fas fa-file-pdf me-2"></i>Synthèse PDF
                                    </a></li>
                                <li><a class="dropdown-item" href="{{ route('rapports.trimestriel', ['format' => 'pdf']) }}">
                                        <i class="fas fa-file-pdf me-2"></i>Trimestriel PDF
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="#">
                                        <i class="fas fa-file-excel me-2"></i>Données Excel
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="card mt-5">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>Aperçu rapide {{ date('Y') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="border-end">
                            <h4 class="text-primary mb-1">{{ \App\Models\Activite::parAnnee(date('Y'))->count() }}</h4>
                            <small class="text-muted">Activités saisies</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-end">
                            <h4 class="text-success mb-1">{{ \App\Models\RealisationTrimestrielle::parAnnee(date('Y'))->count() }}</h4>
                            <small class="text-muted">Réalisations</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-end">
                            <h4 class="text-info mb-1">{{ number_format(\App\Models\Activite::parAnnee(date('Y'))->sum('cout_total_2023'), 0) }}M</h4>
                            <small class="text-muted">Budget prévu</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        @php
                        $budgetPrevu = \App\Models\Activite::parAnnee(date('Y'))->sum('cout_total_2023');
                        $budgetExecute = \App\Models\RealisationTrimestrielle::parAnnee(date('Y'))->sum('budget_execute');
                        $taux = $budgetPrevu > 0 ? ($budgetExecute / $budgetPrevu) * 100 : 0;
                        @endphp
                        <h4 class="text-warning mb-1">{{ number_format($taux, 1) }}%</h4>
                        <small class="text-muted">Taux d'exécution</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Rapports récents -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>Rapports récents
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Type de rapport</th>
                                <th>Période</th>
                                <th>Généré par</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><i class="fas fa-file-alt text-primary me-2"></i>Synthèse Annuelle</td>
                                <td>2023</td>
                                <td>{{ auth()->user()->nom_complet ?? 'Kouassi Marie-Claire' }}</td>
                                <td>{{ date('d/m/Y') }}</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-calendar-alt text-success me-2"></i>Rapport T4</td>
                                <td>T4 2023</td>
                                <td>{{ auth()->user()->nom_complet ?? 'Kouassi Marie-Claire' }}</td>
                                <td>{{ date('d/m/Y', strtotime('-1 day')) }}</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-file-excel text-warning me-2"></i>Export Excel</td>
                                <td>2023</td>
                                <td>{{ auth()->user()->nom_complet ?? 'Kouassi Marie-Claire' }}</td>
                                <td>{{ date('d/m/Y', strtotime('-2 days')) }}</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Animation des cartes au survol
        $('.card').hover(
            function() {
                $(this).addClass('shadow-lg').removeClass('shadow-sm');
            },
            function() {
                $(this).addClass('shadow-sm').removeClass('shadow-lg');
            }
        );
    });
</script>
@endpush